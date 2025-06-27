<?php

/*
 * Enhanced Narrator Plugin - Prompts
 * 
 * Adds new prompt types for:
 * - Inner voice narrator (player's thoughts)
 * - Character speech style translation
 * - Self-generation functionality
 * - Automatic PC commentary
 */

// Load configuration
require_once(__DIR__ . DIRECTORY_SEPARATOR . "config.base.php");
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "config.php")) {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . "config.php");
}

// Check if plugin is enabled
if (!isset($GLOBALS["enhanced_narrator_settings"]["enabled"]) || !$GLOBALS["enhanced_narrator_settings"]["enabled"]) {
    return; // Plugin disabled, don't add prompts
}

// Inner voice narrator - player's thoughts
$PROMPTS["innervoice"] = [
    "cue" => $GLOBALS["enhanced_narrator_settings"]["inner_voice_prompts"] ?: [
        "({$GLOBALS["PLAYER_NAME"]} thinks to themselves) *{$GLOBALS["PLAYER_NAME"]}'s thoughts*: "
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["innervoice"] ?: 75]
];

// Character speech style translation
$PROMPTS["inputtext_styled"] = [
    "cue" => [
        $GLOBALS["enhanced_narrator_settings"]["roleplay_prompt"] ?: 
        "Translate {$GLOBALS["PLAYER_NAME"]}'s words into their characteristic speaking style and personality. Consider their background, class, and recent experiences. Write it as: \"{$GLOBALS["PLAYER_NAME"]}: [translated speech]\" $MAXIMUM_WORDS"
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["roleplay"] ?: 100]
];

// Self-generation for asterisk syntax
$PROMPTS["selfgen"] = [
    "cue" => $GLOBALS["enhanced_narrator_settings"]["selfgen_prompts"] ?: [
        "Generate a brief action or thought for {$GLOBALS["PLAYER_NAME"]} in the current situation. Write as: *{$GLOBALS["PLAYER_NAME"]} [action/thought]* $MAXIMUM_WORDS"
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["selfgen"] ?: 50]
];

// Automatic PC commentary system (random thoughts)
$PROMPTS["autocomment"] = [
    "cue" => [
        "({$GLOBALS["PLAYER_NAME"]} makes an observation about the current situation) *{$GLOBALS["PLAYER_NAME"]} thinks*: ",
        "({$GLOBALS["PLAYER_NAME"]} reflects on recent events) *{$GLOBALS["PLAYER_NAME"]}'s thoughts*: ",
        "({$GLOBALS["PLAYER_NAME"]} notices something about their surroundings) *{$GLOBALS["PLAYER_NAME"]} observes*: ",
        "({$GLOBALS["PLAYER_NAME"]} has a random thought) *{$GLOBALS["PLAYER_NAME"]} ponders*: ",
        "({$GLOBALS["PLAYER_NAME"]} makes a mental note) *{$GLOBALS["PLAYER_NAME"]} thinks to themselves*: ",
        "({$GLOBALS["PLAYER_NAME"]} considers their next move) *{$GLOBALS["PLAYER_NAME"]} contemplates*: ",
        "({$GLOBALS["PLAYER_NAME"]} remembers something) *{$GLOBALS["PLAYER_NAME"]} recalls*: "
    ],
    "player_request" => [""],
    "extra" => [
        "force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["autocomment"] ?: 60,
        "dontuse" => !($GLOBALS["enhanced_narrator_auto"]["enabled"] ?? true)
    ]
];

// Event-based PC commentary (kills, interactions, etc.)
$PROMPTS["eventcomment"] = [
    "cue" => [
        "({$GLOBALS["PLAYER_NAME"]} reacts to what just happened) *{$GLOBALS["PLAYER_NAME"]}'s reaction*: ",
        "({$GLOBALS["PLAYER_NAME"]} comments on the recent event) *{$GLOBALS["PLAYER_NAME"]} thinks*: ",
        "({$GLOBALS["PLAYER_NAME"]} processes what occurred) *{$GLOBALS["PLAYER_NAME"]}'s thoughts*: ",
        "({$GLOBALS["PLAYER_NAME"]} reflects on the outcome) *{$GLOBALS["PLAYER_NAME"]} considers*: ",
        "({$GLOBALS["PLAYER_NAME"]} has a thought about what just transpired) *{$GLOBALS["PLAYER_NAME"]} muses*: ",
        "({$GLOBALS["PLAYER_NAME"]} makes a mental note about the event) *{$GLOBALS["PLAYER_NAME"]} notes*: "
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["eventcomment"] ?: 50]
];

// Location-specific automatic thoughts  
$PROMPTS["location_thoughts"] = [
    "cue" => [
        "({$GLOBALS["PLAYER_NAME"]} takes in the new surroundings) *{$GLOBALS["PLAYER_NAME"]} observes*: ",
        "({$GLOBALS["PLAYER_NAME"]} reflects on arriving at this place) *{$GLOBALS["PLAYER_NAME"]} thinks*: ",
        "({$GLOBALS["PLAYER_NAME"]} notices something about this location) *{$GLOBALS["PLAYER_NAME"]} notes*: ",
        "({$GLOBALS["PLAYER_NAME"]} remembers something about this place) *{$GLOBALS["PLAYER_NAME"]} recalls*: "
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["location_thoughts"] ?: 60]
];

// Quest-specific inner thoughts
$PROMPTS["quest_thoughts"] = [
    "cue" => [
        "({$GLOBALS["PLAYER_NAME"]} considers the new quest) *{$GLOBALS["PLAYER_NAME"]} thinks*: ",
        "({$GLOBALS["PLAYER_NAME"]} weighs the implications) *{$GLOBALS["PLAYER_NAME"]} ponders*: ",
        "({$GLOBALS["PLAYER_NAME"]} reflects on the task ahead) *{$GLOBALS["PLAYER_NAME"]}'s thoughts*: ",
        "({$GLOBALS["PLAYER_NAME"]} makes plans) *{$GLOBALS["PLAYER_NAME"]} strategizes*: "
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["quest_thoughts"] ?: 55]
];

// Enhanced combat end commentary with inner voice
$PROMPTS["combatend_inner"] = [
    "cue" => [
        "({$GLOBALS["PLAYER_NAME"]} reflects on the battle) *{$GLOBALS["PLAYER_NAME"]} thinks*: ",
        "({$GLOBALS["PLAYER_NAME"]} catches their breath and considers the fight) *{$GLOBALS["PLAYER_NAME"]}'s thoughts*: ",
        "({$GLOBALS["PLAYER_NAME"]} looks at the aftermath) *{$GLOBALS["PLAYER_NAME"]} observes*: ",
        "({$GLOBALS["PLAYER_NAME"]} feels the adrenaline fading) *{$GLOBALS["PLAYER_NAME"]} reflects*: "
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["combatend_inner"] ?: 50]
];

// Talk with Player - Direct communication to the player character/narrator
$PROMPTS["talkwithplayer"] = [
    "cue" => [
        $GLOBALS["enhanced_narrator_settings"]["talkwithplayer_prompt"] ?: 
        "{$GLOBALS["HERIKA_NAME"]} responds directly to the real-life player who is controlling {$GLOBALS["PLAYER_NAME"]}. This is out-of-character communication between the player and their character. Respond as {$GLOBALS["HERIKA_NAME"]} talking to their player: "
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => $GLOBALS["enhanced_narrator_tokens"]["talkwithplayer"] ?: 80]
];

?>