# Player Voice TTS Explanation

## The Issue
HerikaServer doesn't generate TTS for player input by default. The system is designed to:
1. Accept player text input
2. Process it
3. Generate NPC responses with TTS

Your typed input is never voiced because the game assumes you're already speaking/roleplaying.

## Why *roleplay* Syntax Might Not Generate Voice

The `*roleplay*` feature translates your input into your character's speaking style, but:
- It changes the text style
- It still processes as player input
- Player input doesn't trigger TTS generation

## Potential Solutions

### Option 1: Modify HerikaServer Core (Not Recommended)
You would need to modify the core flow to generate TTS for player input, which would require significant changes.

### Option 2: Use Inner Voice Instead
Instead of `*roleplay* Hello there`, use:
- `*Hello there*` - This triggers inner voice (narrator)
- The narrator responses DO get TTS

### Option 3: Create a Player Voice Event Type
Create a new event type that specifically generates TTS for player speech:

```php
// In prompts.php
$PROMPTS["player_voice"] = [
    "cue" => [
        "Echo what the player said: {$GLOBALS["PLAYER_NAME"]} says: "
    ],
    "player_request" => [""],
    "extra" => ["force_tokens_max" => 100]
];
```

Then in context.php, add a trigger like `*voice*` that changes the event to "player_voice".

### Option 4: Use Existing TTS Outside HerikaServer
Since you want to voice YOUR input, you might want to:
1. Use a separate TTS system for your character's voice
2. Or modify the game client to send player dialogue to TTS

## The Design Philosophy

HerikaServer assumes:
- NPCs need TTS (they're AI-generated)
- Players don't need TTS (they're human-controlled)
- The narrator needs special handling (no TTS by default)

Your use case (voicing player input) goes against this design, which is why it's not working out of the box.