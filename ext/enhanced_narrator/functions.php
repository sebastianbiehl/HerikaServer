<?php

/*
 * Enhanced Narrator Plugin - Functions
 * 
 * Adds functions that can be called by the AI to trigger narrator functionality
 */

// Function to trigger inner voice/thoughts
$GLOBALS["F_NAMES"]["WebCmdInnerVoice"] = "InnerVoice";
$GLOBALS["F_TRANSLATIONS"]["WebCmdInnerVoice"] = "Generate an inner voice thought or reflection for the player character";

$GLOBALS["FUNCTIONS"][] = [
    "name" => $GLOBALS["F_NAMES"]["WebCmdInnerVoice"],
    "description" => $GLOBALS["F_TRANSLATIONS"]["WebCmdInnerVoice"],
    "parameters" => [
        "type" => "object",
        "properties" => [
            "context" => [
                "type" => "string",
                "description" => "Context or trigger for the inner voice (optional)",
                "enum" => ["current_situation", "recent_events", "surroundings", "emotions", "memories"]
            ]
        ],
        "required" => [],
    ],
];

$GLOBALS["ENABLED_FUNCTIONS"][] = "WebCmdInnerVoice";

// Function to trigger character observation
$GLOBALS["F_NAMES"]["WebCmdObserve"] = "Observe";
$GLOBALS["F_TRANSLATIONS"]["WebCmdObserve"] = "Make the player character observe and comment on their surroundings or situation";

$GLOBALS["FUNCTIONS"][] = [
    "name" => $GLOBALS["F_NAMES"]["WebCmdObserve"],
    "description" => $GLOBALS["F_TRANSLATIONS"]["WebCmdObserve"],
    "parameters" => [
        "type" => "object",
        "properties" => [
            "focus" => [
                "type" => "string",
                "description" => "What to focus the observation on",
                "enum" => ["environment", "people", "objects", "atmosphere", "general"]
            ]
        ],
        "required" => ["focus"],
    ],
];

$GLOBALS["ENABLED_FUNCTIONS"][] = "WebCmdObserve";

// Server-side function handlers
$GLOBALS["FUNCSERV"]["WebCmdInnerVoice"] = function() {
    global $gameRequest, $returnFunction, $db, $request;
    
    // Trigger inner voice response
    $context = isset($returnFunction[2]) ? $returnFunction[2] : "current_situation";
    
    // Create a pseudo-event for inner voice
    $innerVoiceData = "Inner voice triggered via function call. Context: $context";
    
    // Log the function call
    $db->insert(
        'eventlog',
        array(
            'ts' => $gameRequest[1],
            'gamets' => $gameRequest[2],
            'type' => 'function_innervoice',
            'data' => $innerVoiceData,
            'sess' => (php_sapi_name()=="cli")?'cli':'web',
            'localts' => time()
        )
    );
    
    // Set return value
    $returnFunction[3] = "Inner voice activated";
    $gameRequest[3] = $innerVoiceData;
};

$GLOBALS["FUNCSERV"]["WebCmdObserve"] = function() {
    global $gameRequest, $returnFunction, $db, $request;
    
    // Trigger observation response
    $focus = isset($returnFunction[2]) ? $returnFunction[2] : "general";
    
    // Create observation data
    $observationData = "Player observes: $focus";
    
    // Log the function call
    $db->insert(
        'eventlog',
        array(
            'ts' => $gameRequest[1],
            'gamets' => $gameRequest[2],
            'type' => 'function_observe',
            'data' => $observationData,
            'sess' => (php_sapi_name()=="cli")?'cli':'web',
            'localts' => time()
        )
    );
    
    // Set return value
    $returnFunction[3] = "Observation made";
    $gameRequest[3] = $observationData;
};

// Custom prompts for function returns
$GLOBALS["PROMPTS"]["afterfunc"]["cue"]["WebCmdInnerVoice"] = "({$GLOBALS["PLAYER_NAME"]} has an inner thought) *{$GLOBALS["PLAYER_NAME"]} thinks*: {$GLOBALS["TEMPLATE_DIALOG"]}";

$GLOBALS["PROMPTS"]["afterfunc"]["cue"]["WebCmdObserve"] = "({$GLOBALS["PLAYER_NAME"]} makes an observation) *{$GLOBALS["PLAYER_NAME"]} observes*: {$GLOBALS["TEMPLATE_DIALOG"]}";

// Function return handling
$GLOBALS["FUNCRET"]["WebCmdInnerVoice"] = function($gameRequest) {
    $GLOBALS["FORCE_MAX_TOKENS"] = 60;
    return ["argName" => "context"];
};

$GLOBALS["FUNCRET"]["WebCmdObserve"] = function($gameRequest) {
    $GLOBALS["FORCE_MAX_TOKENS"] = 75;
    return ["argName" => "focus"];
};

?>