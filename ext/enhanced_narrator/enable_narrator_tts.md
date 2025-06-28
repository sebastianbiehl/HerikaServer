# Enabling TTS for Narrator Responses

By default, HerikaServer skips TTS generation for any response containing "The Narrator:" to prevent the narrator's voice from being spoken aloud in-game. This is intentional behavior.

If you want to enable TTS for narrator responses, you have these options:

## Option 1: Modify chat_helper_functions.php (Quick Fix)

Edit `/lib/chat_helper_functions.php` around line 315:

```php
// Original code:
if (strpos($responseTextUnmooded, "The Narrator:") !== false) { // Force not impersonating the narrator.
    return;
}

// Change to:
if (strpos($responseTextUnmooded, "The Narrator:") !== false && !isset($GLOBALS["ALLOW_NARRATOR_TTS"])) {
    return;
}
```

Then add to your `conf.php`:
```php
$GLOBALS["ALLOW_NARRATOR_TTS"] = true;
```

## Option 2: Remove "The Narrator:" from Enhanced Narrator Prompts

The enhanced_narrator plugin's prompts don't actually include "The Narrator:", so they should work. The issue might be that some base game events are being prepended with "The Narrator:" automatically.

## Option 3: Use a Different Actor Name

Instead of having responses that include "The Narrator:", use a different format like:
- "*Inner Voice*:" 
- "*Thoughts*:"
- Or just the content without any prefix

## Why This Happens

The game's data_functions.php automatically prepends "The Narrator:" to these event types:
- info* events
- death* events  
- funcret* events
- location* events
- background chat events
- book events

Then chat_helper_functions.php skips TTS for any response containing "The Narrator:".

## Recommended Solution

For the enhanced_narrator plugin, ensure your prompts don't generate responses containing "The Narrator:". The plugin's current prompts use formats like:
- `(*Player* thinks to themselves)`
- `*Player's thoughts*:`

These should not trigger the narrator check and should generate TTS normally.