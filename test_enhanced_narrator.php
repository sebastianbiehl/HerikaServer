<?php
// Test script to verify enhanced_narrator functionality

error_reporting(E_ALL);
ini_set('display_errors', 1);

$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;

// Simulate being called from main.php
$GLOBALS["PLAYER_NAME"] = "TestPlayer";
$GLOBALS["HERIKA_NAME"] = "TestHerika";
$GLOBALS["DBDRIVER"] = "sqlite3";

// Load required files
require_once($path . "conf" . DIRECTORY_SEPARATOR . "conf.php");
require_once($path . "lib" . DIRECTORY_SEPARATOR . "{$GLOBALS["DBDRIVER"]}.class.php");

// Initialize database
$db = new sql();

echo "=== Testing Enhanced Narrator Plugin ===\n\n";

// Test 1: Check if plugin files exist
echo "1. Plugin Files Check:\n";
$pluginPath = $path . "ext" . DIRECTORY_SEPARATOR . "enhanced_narrator" . DIRECTORY_SEPARATOR;
$requiredFiles = ["context.php", "prompts.php", "functions.php", "config.base.php"];
foreach ($requiredFiles as $file) {
    echo "   $file: " . (file_exists($pluginPath . $file) ? "✓ EXISTS" : "✗ MISSING") . "\n";
}

// Test 2: Load configuration
echo "\n2. Loading Configuration:\n";
require_once($pluginPath . "config.base.php");
echo "   Plugin enabled: " . ($GLOBALS["enhanced_narrator_settings"]["enabled"] ? "YES" : "NO") . "\n";

// Test 3: Load prompts
echo "\n3. Loading Prompts:\n";
require_once($pluginPath . "prompts.php");
$promptTypes = ["innervoice", "selfgen", "inputtext_styled", "talkwithplayer"];
foreach ($promptTypes as $type) {
    echo "   $type: " . (isset($GLOBALS["PROMPTS"][$type]) ? "✓ LOADED" : "✗ MISSING") . "\n";
}

// Test 4: Test context processing
echo "\n4. Testing Context Processing:\n";

// Simulate different inputs
$testCases = [
    ["inputtext", "123", "456", "*I wonder what's next*"],
    ["inputtext", "123", "456", "*"],
    ["inputtext", "123", "456", "*roleplay* Hello there"],
    ["inputtext", "123", "456", "*player* How are you feeling?"],
    ["inputtext", "123", "456", "Normal text"]
];

foreach ($testCases as $test) {
    $gameRequest = $test;
    $originalType = $gameRequest[0];
    $originalText = $gameRequest[3];
    
    // Clear any previous flags
    unset($GLOBALS["ENHANCED_NARRATOR_SKIP_DIALOGUE_TARGET"]);
    
    // Include context.php to process the request
    include($pluginPath . "context.php");
    
    echo "\n   Input: '$originalText'\n";
    echo "   Original type: $originalType -> New type: {$gameRequest[0]}\n";
    echo "   Skip dialogue target: " . (isset($GLOBALS["ENHANCED_NARRATOR_SKIP_DIALOGUE_TARGET"]) && $GLOBALS["ENHANCED_NARRATOR_SKIP_DIALOGUE_TARGET"] ? "YES" : "NO") . "\n";
    
    if ($gameRequest[0] != $originalType) {
        echo "   ✓ EVENT TYPE CHANGED\n";
    } else {
        echo "   ✗ No change\n";
    }
}

echo "\n5. Checking Prompt Content:\n";
if (isset($GLOBALS["PROMPTS"]["innervoice"]["cue"][0])) {
    echo "   Inner voice prompt: " . substr($GLOBALS["PROMPTS"]["innervoice"]["cue"][0], 0, 50) . "...\n";
}

echo "\n=== Summary ===\n";
echo "If all event types show 'No change', the plugin is not working correctly.\n";
echo "Check that:\n";
echo "1. The plugin is in the correct directory\n";
echo "2. Global variables are properly set\n";
echo "3. No syntax errors are preventing execution\n";
?>