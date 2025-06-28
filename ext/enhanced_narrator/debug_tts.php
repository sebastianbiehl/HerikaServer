<?php
// Debug script to check TTS configuration for enhanced_narrator

// Load main configuration
$path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
require_once($path . "conf" . DIRECTORY_SEPARATOR . "conf.php");

echo "=== TTS Configuration Debug ===\n\n";

// Check TTS function setting
echo "1. TTS Function Setting:\n";
echo "   \$TTSFUNCTION = " . (isset($GLOBALS["TTSFUNCTION"]) ? "'{$GLOBALS["TTSFUNCTION"]}'" : "NOT SET") . "\n";
echo "   (Should be 'xtts', 'xtts-fastapi', or another TTS provider, not 'none')\n\n";

// Check if TTS file exists
if (isset($GLOBALS["TTSFUNCTION"]) && $GLOBALS["TTSFUNCTION"] != "none") {
    $ttsFile = $path . "tts" . DIRECTORY_SEPARATOR . "tts-" . $GLOBALS["TTSFUNCTION"] . ".php";
    echo "2. TTS File Check:\n";
    echo "   Looking for: $ttsFile\n";
    echo "   File exists: " . (file_exists($ttsFile) ? "YES" : "NO") . "\n\n";
}

// Check XTTS configuration if using XTTS
if (isset($GLOBALS["TTSFUNCTION"]) && strpos($GLOBALS["TTSFUNCTION"], "xtts") !== false) {
    echo "3. XTTS Configuration:\n";
    echo "   Endpoint: " . (isset($TTS["XTTS"]["endpoint"]) ? $TTS["XTTS"]["endpoint"] : "NOT SET") . "\n";
    echo "   Language: " . (isset($TTS["XTTS"]["language"]) ? $TTS["XTTS"]["language"] : "NOT SET") . "\n";
    echo "   Voice ID: " . (isset($TTS["XTTS"]["voiceid"]) ? $TTS["XTTS"]["voiceid"] : "NOT SET") . "\n\n";
    
    // Check if voice file exists
    if (isset($TTS["XTTS"]["voiceid"])) {
        $voiceFile = $path . "tts" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . $TTS["XTTS"]["voiceid"] . ".json";
        echo "   Voice file: $voiceFile\n";
        echo "   Voice file exists: " . (file_exists($voiceFile) ? "YES" : "NO") . "\n\n";
    }
}

// Check enhanced_narrator event types
echo "4. Enhanced Narrator Event Types:\n";
$eventTypes = ["innervoice", "selfgen", "inputtext_styled", "talkwithplayer", "autocomment", "eventcomment"];
foreach ($eventTypes as $event) {
    echo "   - $event\n";
}

echo "\n5. Diagnosis:\n";
if (!isset($GLOBALS["TTSFUNCTION"]) || $GLOBALS["TTSFUNCTION"] == "none") {
    echo "   ❌ TTS is disabled! Set \$TTSFUNCTION to 'xtts' or another TTS provider in conf.php\n";
} else {
    echo "   ✓ TTS is enabled with: {$GLOBALS["TTSFUNCTION"]}\n";
}

echo "\nTo fix 'generator none' error:\n";
echo "1. Edit " . $path . "conf/conf.php\n";
echo "2. Set: \$TTSFUNCTION=\"xtts\";\n";
echo "3. Configure XTTS settings:\n";
echo "   \$TTS[\"XTTS\"][\"endpoint\"]='http://your-xtts-server:port/';\n";
echo "   \$TTS[\"XTTS\"][\"language\"]='en';\n";
echo "   \$TTS[\"XTTS\"][\"voiceid\"]='your_voice_name';\n";
?>