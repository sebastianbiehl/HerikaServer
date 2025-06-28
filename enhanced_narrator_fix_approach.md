# Enhanced Narrator - Fundamental Fix Needed

## The Current Broken Approach

1. **context.php changes event types AFTER request.php runs**
   - request.php selects prompts for "inputtext" 
   - context.php changes to "innervoice" 
   - But prompts were already selected for "inputtext"

2. **Custom event types aren't handled by main flow**
   - We create "innervoice", "inputtext_styled" etc.
   - But the main HerikaServer flow doesn't know what to do with these

3. **Automatic commentary timing is wrong**
   - Events get processed but no commentary triggers
   - Interval checks happen but nothing changes

## Better Approach Options

### Option 1: Override Existing Prompts (Simpler)
Instead of creating new event types, modify the existing "inputtext" prompts based on syntax:

```php
// In prompts.php - detect syntax and override prompts
if (preg_match('/\*roleplay\*/', $gameRequest[3])) {
    $PROMPTS["inputtext"]["cue"] = ["Translate speech style..."];
}
```

### Option 2: Use Player Request Override (MinAI approach)
Use the existing `player_request` system to override what gets sent:

```php
// Override the player request content
$PROMPTS["inputtext"]["player_request"] = ["Custom narrator prompt"];
```

### Option 3: Early Event Type Change
Move the event type detection to prompts.php instead of context.php:

```php
// In prompts.php - change event type BEFORE request.php runs
if (preg_match('/\*wrapped\*/', $gameRequest[3])) {
    $gameRequest[0] = "innervoice";
}
```

## Recommendation

Use Option 3 - move the syntax detection to prompts.php so it happens BEFORE request.php processes the events.