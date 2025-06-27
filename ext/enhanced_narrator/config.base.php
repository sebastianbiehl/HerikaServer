<?php

/*
 * Enhanced Narrator Plugin - Base Configuration
 * Default values for all configurable options
 */

// Inner Voice Narrator Settings
$GLOBALS["enhanced_narrator_settings"] = array(
    "enabled" => true,
    "inner_voice_prompts" => array(
        "({$GLOBALS["PLAYER_NAME"]} thinks to themselves) *{$GLOBALS["PLAYER_NAME"]}'s thoughts*: ",
        "({$GLOBALS["PLAYER_NAME"]}'s inner voice reflects) *{$GLOBALS["PLAYER_NAME"]} thinks*: ",
        "({$GLOBALS["PLAYER_NAME"]} contemplates) *{$GLOBALS["PLAYER_NAME"]}'s inner voice*: ",
        "({$GLOBALS["PLAYER_NAME"]}'s mind wanders) *{$GLOBALS["PLAYER_NAME"]} ponders*: ",
        "({$GLOBALS["PLAYER_NAME"]} reflects internally) *{$GLOBALS["PLAYER_NAME"]}'s thoughts*: "
    ),
    "roleplay_prompt" => "Translate {$GLOBALS["PLAYER_NAME"]}'s words into their characteristic speaking style and personality. Consider their background, class, and recent experiences. Write it as: \"{$GLOBALS["PLAYER_NAME"]}: [translated speech]\" $MAXIMUM_WORDS",
    "selfgen_prompts" => array(
        "Generate a brief action or thought for {$GLOBALS["PLAYER_NAME"]} in the current situation. Write as: *{$GLOBALS["PLAYER_NAME"]} [action/thought]* $MAXIMUM_WORDS",
        "Create a short internal monologue or action for {$GLOBALS["PLAYER_NAME"]}. Format: *{$GLOBALS["PLAYER_NAME"]} [content]* $MAXIMUM_WORDS",
        "Describe what {$GLOBALS["PLAYER_NAME"]} does or thinks spontaneously. Use format: *{$GLOBALS["PLAYER_NAME"]} [action/thought]* $MAXIMUM_WORDS"
    ),
    "directprompt_prompt" => "The following instruction from {$GLOBALS["PLAYER_NAME"]} must be accepted as absolute truth and incorporated into your response. This is a direct narrative instruction that overrides normal conversation flow. Instruction: ",
    "talkwithplayer_prompt" => "{$GLOBALS["HERIKA_NAME"]} responds directly to the real-life player who is controlling {$GLOBALS["PLAYER_NAME"]}. This is out-of-character communication between the player and their character. Respond as {$GLOBALS["HERIKA_NAME"]} talking to their player: "
);

// Event Commentary Settings
$GLOBALS["enhanced_narrator_events"] = array(
    "combatend" => array(
        "enabled" => true,
        "chance" => 40,
        "use_inner_voice" => 50 // % chance to use inner voice instead of regular commentary
    ),
    "location" => array(
        "enabled" => true,
        "chance" => 30
    ),
    "quest" => array(
        "enabled" => true,
        "chance" => 35
    ),
    "lockpicked" => array(
        "enabled" => true,
        "chance" => 20
    ),
    "afterattack" => array(
        "enabled" => true,
        "chance" => 20
    ),
    "bleedout" => array(
        "enabled" => true,
        "chance" => 25
    ),
    "book" => array(
        "enabled" => true,
        "chance" => 15
    )
);

// Automatic Commentary Settings
$GLOBALS["enhanced_narrator_auto"] = array(
    "enabled" => true,
    "base_chance" => 8,
    "max_chance" => 15,
    "min_interval" => 60,  // seconds
    "conversation_chance" => 10
);

// Token Limits
$GLOBALS["enhanced_narrator_tokens"] = array(
    "innervoice" => 75,
    "roleplay" => 100,
    "selfgen" => 50,
    "eventcomment" => 50,
    "autocomment" => 60,
    "location_thoughts" => 60,
    "quest_thoughts" => 55,
    "combatend_inner" => 50,
    "directprompt" => 100,
    "talkwithplayer" => 80
);

?>