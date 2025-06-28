<?php

/*
 * Enhanced Narrator Plugin - Context Processing
 * 
 * Handles special syntax and input processing:
 * - *wrapped* syntax for inner voice narrator
 * - * syntax for self-generation
 * - Character speech style translation
 * - Automatic commentary triggers
 */

// Access global variables
global $db, $gameRequest;

// Check if required variables are available
if (!isset($db) || !is_object($db) || !isset($gameRequest) || !is_array($gameRequest)) {
    return; // Required variables not ready, skip processing
}

// Load configuration
require_once(__DIR__ . DIRECTORY_SEPARATOR . "config.base.php");
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "config.php")) {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . "config.php");
}

// Check if plugin is enabled
if (!isset($GLOBALS["enhanced_narrator_settings"]["enabled"]) || !$GLOBALS["enhanced_narrator_settings"]["enabled"]) {
    return; // Plugin disabled, don't process anything
}

// Start session for tracking automatic commentary timing
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Only process input text events
if (($gameRequest[0] == "inputtext") || ($gameRequest[0] == "inputtext_s")) {
    
    $originalInput = $gameRequest[3];
    $processedInput = trim($originalInput);
    
    // Check for special syntax patterns - ORDER MATTERS!
    
    // 1. Handle *roleplay* syntax for character speech style translation (MUST BE FIRST)
    if (preg_match('/^\*roleplay\*\s*(.+)$/i', $processedInput, $matches)) {
        $gameRequest[0] = "inputtext_styled";
        $gameRequest[3] = trim($matches[1]);
        
        // Log the event
        $db->insert(
            'eventlog',
            array(
                'ts' => $gameRequest[1],
                'gamets' => $gameRequest[2],
                'type' => 'inputtext_styled',
                'data' => "Speech style translation: " . $gameRequest[3],
                'sess' => (php_sapi_name()=="cli")?'cli':'web',
                'localts' => time()
            )
        );
    }
    // 2. Handle *player* or *talk* syntax for real player communicating with their character
    elseif (preg_match('/^\*(player|talk)\*\s*(.+)$/i', $processedInput, $matches)) {
        $gameRequest[0] = "talkwithplayer";
        $gameRequest[3] = trim($matches[2]);
        
        // IMPORTANT: Skip the normal dialogue target addition - this is the real player talking to their character
        $GLOBALS["ENHANCED_NARRATOR_SKIP_DIALOGUE_TARGET"] = true;
        
        // Log the event
        $db->insert(
            'eventlog',
            array(
                'ts' => $gameRequest[1],
                'gamets' => $gameRequest[2],
                'type' => 'talkwithplayer',
                'data' => "Real player communication with character: " . $gameRequest[3],
                'sess' => (php_sapi_name()=="cli")?'cli':'web',
                'localts' => time()
            )
        );
    }
    // 3. Handle single * for self-generation
    elseif ($processedInput === '*') {
        $gameRequest[0] = "selfgen";
        $gameRequest[3] = "";
        
        // IMPORTANT: Skip the normal dialogue target addition since this is inner voice
        $GLOBALS["ENHANCED_NARRATOR_SKIP_DIALOGUE_TARGET"] = true;
        
        // Log the event
        $db->insert(
            'eventlog',
            array(
                'ts' => $gameRequest[1],
                'gamets' => $gameRequest[2],
                'type' => 'selfgen',
                'data' => "Self-generation triggered",
                'sess' => (php_sapi_name()=="cli")?'cli':'web',
                'localts' => time()
            )
        );
    }
    // 4. Handle *wrapped* syntax for inner voice narrator (MUST BE LAST)
    elseif (preg_match('/^\*(.+?)\*$/', $processedInput, $matches)) {
        $gameRequest[0] = "innervoice";
        $gameRequest[3] = trim($matches[1]);
        
        // IMPORTANT: Skip the normal dialogue target addition since this is inner voice
        $GLOBALS["ENHANCED_NARRATOR_SKIP_DIALOGUE_TARGET"] = true;
        
        // Log the event
        $db->insert(
            'eventlog',
            array(
                'ts' => $gameRequest[1],
                'gamets' => $gameRequest[2],
                'type' => 'innervoice',
                'data' => "Inner voice triggered: " . $gameRequest[3],
                'sess' => (php_sapi_name()=="cli")?'cli':'web',
                'localts' => time()
            )
        );
    }
    // 5. Normal input - no automatic translation
    else {
        // Keep normal flow without automatic style translation
        // This was removed so translation only happens when explicitly requested
    }
}

// Enhanced automatic commentary system with multiple triggers

// 1. Event-based commentary (combat, locations, quests, etc.)
if (in_array($gameRequest[0], ["location", "combatend", "quest", "lockpicked", "afterattack", "bleedout", "book"])) {
    
    // Get event configuration
    $eventConfig = $GLOBALS["enhanced_narrator_events"][$gameRequest[0]] ?? null;
    
    // Skip if event is disabled
    if (!$eventConfig || !($eventConfig["enabled"] ?? true)) {
        // Event disabled, continue with normal processing
    } else {
        $commentChance = $eventConfig["chance"] ?? 25;
        $commentType = "eventcomment"; // Default
        
        // Use specific prompt types for certain events
        switch ($gameRequest[0]) {
            case "location":
                $commentType = "location_thoughts";
                break;
            case "quest":
                $commentType = "quest_thoughts";
                break;
        }
        
        if (rand(1, 100) <= $commentChance) {
        // Store the original event for processing after commentary
        $GLOBALS["ENHANCED_NARRATOR_ORIGINAL_EVENT"] = [
            'type' => $gameRequest[0],
            'data' => $gameRequest[3],
            'request' => isset($request) ? $request : null
        ];
        
        // Switch to appropriate commentary type
        $gameRequest[0] = $commentType;
        $gameRequest[3] = "Recent event: " . $GLOBALS["ENHANCED_NARRATOR_ORIGINAL_EVENT"]["data"];
        
        // The appropriate prompts will be used from prompts.php based on the event type
        
        // Log the event
        $db->insert(
            'eventlog',
            array(
                'ts' => $gameRequest[1],
                'gamets' => $gameRequest[2],
                'type' => $commentType,
                'data' => "Auto-commentary on: " . $GLOBALS["ENHANCED_NARRATOR_ORIGINAL_EVENT"]["type"],
                'sess' => (php_sapi_name()=="cli")?'cli':'web',
                'localts' => time()
            )
        );
    }
    }
}

// 2. Special handling for combat end - prefer inner voice
if ($gameRequest[0] == "combatend") {
    $combatConfig = $GLOBALS["enhanced_narrator_events"]["combatend"] ?? null;
    $innerVoiceChance = $combatConfig["use_inner_voice"] ?? 50;
    
    // Use configured chance for inner voice instead of regular combat commentary
    if (rand(1, 100) <= $innerVoiceChance) {
        $gameRequest[0] = "combatend_inner";
        // The combatend_inner prompts will be used from prompts.php
        
        // Log the event
        $db->insert(
            'eventlog',
            array(
                'ts' => $gameRequest[1],
                'gamets' => $gameRequest[2],
                'type' => 'combatend_inner',
                'data' => "Inner voice combat commentary",
                'sess' => (php_sapi_name()=="cli")?'cli':'web',
                'localts' => time()
            )
        );
    }
}

// 3. Interval-based automatic thoughts
// Trigger based on time intervals and interaction frequency
if (!in_array($gameRequest[0], ["innervoice", "selfgen", "eventcomment", "autocomment", "combatend_inner", "inputtext_styled", "directprompt", "talkwithplayer"])) {
    
    $autoConfig = $GLOBALS["enhanced_narrator_auto"] ?? array();
    
    // Skip if automatic commentary is disabled
    if (!($autoConfig["enabled"] ?? true)) {
        // Auto commentary disabled, skip
    } else {
        // Check if enough time has passed for automatic thoughts
        $lastAutoComment = isset($_SESSION['last_auto_comment']) ? $_SESSION['last_auto_comment'] : 0;
        $currentTime = time();
        $timeSinceLastComment = $currentTime - $lastAutoComment;
        
        // Use configured values
        $baseChance = $autoConfig["base_chance"] ?? 8;
        $maxChance = $autoConfig["max_chance"] ?? 15;
        $minInterval = $autoConfig["min_interval"] ?? 60;
        
        // Base chance increases over time
        if ($timeSinceLastComment > 300) { // 5 minutes
            $baseChance = $maxChance;
        } elseif ($timeSinceLastComment > 120) { // 2 minutes  
            $baseChance = min($maxChance, $baseChance + 4);
        } elseif ($timeSinceLastComment < $minInterval) {
            $baseChance = 0; // Don't spam - wait minimum interval
        }
    
    if ($baseChance > 0 && rand(1, 100) <= $baseChance) {
        // Store original for later
        $GLOBALS["ENHANCED_NARRATOR_DEFERRED_EVENT"] = [
            'type' => $gameRequest[0],
            'data' => $gameRequest[3],
            'request' => isset($request) ? $request : null
        ];
        
        // Switch to auto commentary
        $gameRequest[0] = "autocomment";
        $gameRequest[3] = "";
        // The autocomment prompts will be used from prompts.php
        
        // Update last comment time
        $_SESSION['last_auto_comment'] = $currentTime;
        
        // Log the event
        $db->insert(
            'eventlog',
            array(
                'ts' => $gameRequest[1],
                'gamets' => $gameRequest[2],
                'type' => 'autocomment',
                'data' => "Automatic commentary triggered (interval-based)",
                'sess' => (php_sapi_name()=="cli")?'cli':'web',
                'localts' => time()
            )
        );
    }
    }
}

// 4. Special triggers for other events
if ($gameRequest[0] == "inputtext" || $gameRequest[0] == "inputtext_s") {
    $autoConfig = $GLOBALS["enhanced_narrator_auto"] ?? array();
    $conversationChance = $autoConfig["conversation_chance"] ?? 10;
    
    // Configurable chance for spontaneous thought during conversations
    if (($autoConfig["enabled"] ?? true) && rand(1, 100) <= $conversationChance) {
        // Store the conversation for context
        $GLOBALS["ENHANCED_NARRATOR_CONVERSATION_CONTEXT"] = $gameRequest[3];
        
        // Don't interrupt the conversation, but log potential for follow-up commentary
        $db->insert(
            'eventlog',
            array(
                'ts' => $gameRequest[1],
                'gamets' => $gameRequest[2],
                'type' => 'potential_followup',
                'data' => "Conversation context for potential follow-up: " . substr($gameRequest[3], 0, 100),
                'sess' => (php_sapi_name()=="cli")?'cli':'web',
                'localts' => time()
            )
        );
    }
}

?>