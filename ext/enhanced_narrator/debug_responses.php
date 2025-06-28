<?php
// Add this temporarily to chat_helper_functions.php in returnLines() function around line 315

// Add this right before the narrator check:
error_log("DEBUG: Response text: " . $responseTextUnmooded);
error_log("DEBUG: Contains 'The Narrator:': " . (strpos($responseTextUnmooded, "The Narrator:") !== false ? "YES" : "NO"));
error_log("DEBUG: TTS Function: " . $GLOBALS["TTSFUNCTION"]);

// The existing code:
if (strpos($responseTextUnmooded, "The Narrator:") !== false) {
    error_log("DEBUG: Skipping TTS for narrator");
    return;
}
?>