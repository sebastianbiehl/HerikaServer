<?php
// Debug script to diagnose TTS issues

$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
require_once($path . "conf" . DIRECTORY_SEPARATOR . "conf.php");

echo "=== HerikaServer TTS Debug ===\n\n";

// 1. Check basic TTS configuration
echo "1. TTS Configuration:\n";
echo "   TTSFUNCTION: " . (isset($GLOBALS["TTSFUNCTION"]) ? $GLOBALS["TTSFUNCTION"] : "NOT SET") . "\n";
echo "   TTSENABLED: " . (isset($GLOBALS["TTSENABLED"]) ? ($GLOBALS["TTSENABLED"] ? "true" : "false") : "NOT SET") . "\n\n";

// 2. Check if TTS file exists
if (isset($GLOBALS["TTSFUNCTION"])) {
    $ttsFile = $path . "tts" . DIRECTORY_SEPARATOR . "tts-" . $GLOBALS["TTSFUNCTION"] . ".php";
    echo "2. TTS File:\n";
    echo "   Looking for: $ttsFile\n";
    echo "   Exists: " . (file_exists($ttsFile) ? "YES" : "NO") . "\n\n";
}

// 3. Check specific TTS provider settings
echo "3. TTS Provider Settings:\n";
if ($GLOBALS["TTSFUNCTION"] == "xtts" || $GLOBALS["TTSFUNCTION"] == "xtts-fastapi") {
    echo "   XTTS Endpoint: " . ($TTS["XTTS"]["endpoint"] ?? "NOT SET") . "\n";
    echo "   XTTS Language: " . ($TTS["XTTS"]["language"] ?? "NOT SET") . "\n";
    echo "   XTTS Voice ID: " . ($TTS["XTTS"]["voiceid"] ?? "NOT SET") . "\n";
} elseif ($GLOBALS["TTSFUNCTION"] == "11labs") {
    echo "   11Labs URL: " . ($TTS["11LABS"]["url"] ?? "NOT SET") . "\n";
    echo "   11Labs API Key: " . (isset($TTS["11LABS"]["apikey"]) ? "SET (hidden)" : "NOT SET") . "\n";
    echo "   11Labs Voice: " . ($TTS["11LABS"]["voiceid"] ?? "NOT SET") . "\n";
} elseif ($GLOBALS["TTSFUNCTION"] == "azure") {
    echo "   Azure Endpoint: " . ($TTS["AZURE"]["API_ENDPOINT"] ?? "NOT SET") . "\n";
    echo "   Azure API Key: " . (isset($TTS["AZURE"]["API_KEY"]) ? "SET (hidden)" : "NOT SET") . "\n";
}

echo "\n4. Other Relevant Settings:\n";
echo "   PLAYER_NAME: " . ($GLOBALS["PLAYER_NAME"] ?? "NOT SET") . "\n";
echo "   HERIKA_NAME: " . ($GLOBALS["HERIKA_NAME"] ?? "NOT SET") . "\n";

// 5. Check for any TTS-related errors in configuration
echo "\n5. Potential Issues:\n";
if (!isset($GLOBALS["TTSFUNCTION"]) || $GLOBALS["TTSFUNCTION"] == "none") {
    echo "   ❌ TTS is disabled (TTSFUNCTION = none or not set)\n";
} elseif (!file_exists($ttsFile)) {
    echo "   ❌ TTS file not found: $ttsFile\n";
} else {
    echo "   ✓ TTS appears to be configured\n";
}

// 6. Check if there's a custom actor setting
echo "\n6. Actor/Voice Settings:\n";
if (isset($GLOBALS["PLAYER_VOICE"])) {
    echo "   PLAYER_VOICE: " . $GLOBALS["PLAYER_VOICE"] . "\n";
} else {
    echo "   PLAYER_VOICE: NOT SET (might be the issue)\n";
}

echo "\n7. Database Driver:\n";
echo "   DBDRIVER: " . ($GLOBALS["DBDRIVER"] ?? "NOT SET") . "\n";

echo "\n=== Recommendations ===\n";
echo "1. Check if TTSFUNCTION is set correctly (not 'none')\n";
echo "2. Verify your TTS provider is running and accessible\n";
echo "3. Check error logs in /var/log/apache2/error.log or equivalent\n";
echo "4. Try setting a different TTSFUNCTION to test (e.g., if using xtts, try 11labs)\n";
echo "5. Make sure all required API keys and endpoints are configured\n";
?>