# Enhanced Narrator Plugin

This plugin enhances the HerikaServer/CHIM experience by adding an inner voice narrator system and advanced player character interaction features.

## Features

### 1. Inner Voice Narrator
- The narrator now acts as the player character's inner voice/thoughts rather than an external narrator
- No need to say "hey narrator" - the system automatically detects when to use inner voice

### 2. Special Syntax
- **`*wrapped text*`** - Triggers inner voice narrator with the wrapped content
- **`*`** (single asterisk) - Triggers self-generation for spontaneous actions/thoughts
- **`*roleplay* text`** - Translates the text into character's speaking style and personality

### 3. Character Speech Style Translation
- Use `*roleplay* your text here` to translate input into character's speaking style
- Takes into account character background, class, and recent experiences
- No longer automatic - only triggered when explicitly requested

### 4. Enhanced Automatic Commentary System
- **Interval-based Commentary**: Intelligent timing (8-15% chance based on time since last comment)
  - Minimum 1 minute between automatic thoughts
  - Higher chance after 2+ minutes of silence
  - Respects natural conversation flow
- **Event-based Commentary**: Specific triggers for different events:
  - **Combat**: 40% chance for inner voice thoughts after battles
  - **Location**: 30% chance for location-specific observations  
  - **Quests**: 35% chance for quest-related planning thoughts
  - **Actions**: 20% chance for lockpicking, attacks, etc.
  - **Books**: 15% chance for reading-related thoughts
- **Combat Enhancement**: 50% chance to use inner voice instead of regular combat commentary

### 5. AI Functions
- **InnerVoice()** - AI can trigger inner voice thoughts
- **Observe()** - AI can make the character observe their surroundings

## Usage Examples

### Inner Voice Syntax
```
*I wonder what's behind that door*
```
Result: Generates player character's inner thoughts about the door.

### Self-Generation
```
*
```
Result: Generates a spontaneous action or thought for the character.

### Character Speech Style Translation
```
*roleplay* Hello there, friend
```
Result: Translates into character's speaking style, e.g., "Greetings, traveler. Well met on this fine day."

### Normal Speech (unchanged)
```
Hello there, friend
```
Result: Regular dialogue without style translation - processed normally by HerikaServer.

## Technical Details

### Plugin Structure
- `prompts.php` - Defines new prompt types for inner voice, style translation, etc.
- `context.php` - Handles input processing and special syntax detection
- `functions.php` - Adds AI-callable functions for narrator control
- `manifest.json` - Plugin metadata

### Event Types Added
- `innervoice` - Player's inner thoughts (triggered by `*wrapped*` syntax)
- `inputtext_styled` - Character speech style translation (triggered by `*roleplay*` syntax)  
- `selfgen` - Self-generated actions/thoughts (triggered by `*` syntax)
- `autocomment` - Automatic interval-based commentary
- `eventcomment` - Generic event-based commentary
- `location_thoughts` - Location-specific automatic thoughts
- `quest_thoughts` - Quest-related automatic thoughts  
- `combatend_inner` - Inner voice combat commentary

### Configuration
The plugin works automatically once installed. Commentary frequency can be adjusted by modifying the random chance percentages in `context.php`:

- **Interval-based commentary**: 8-15% chance (time-dependent)
- **Combat events**: 40% chance  
- **Location changes**: 30% chance
- **Quest events**: 35% chance
- **General actions**: 20% chance
- **Book reading**: 15% chance
- **Inner voice combat**: 50% chance

## Installation

1. Ensure the plugin is in `/ext/enhanced_narrator/`
2. Restart HerikaServer
3. The plugin will automatically load and be available
4. Open the configuration page at `/ext/enhanced_narrator/config.html` to customize settings

## Configuration

The plugin includes a web-based configuration interface where you can customize:

- **Inner Voice Prompts**: Customize the prompts used for `*wrapped*` syntax
- **Roleplay Translation Prompt**: Customize how `*roleplay*` translates speech
- **Self-Generation Prompts**: Customize prompts for `*` syntax  
- **Event Commentary**: Enable/disable and adjust chances for different game events
- **Automatic Commentary**: Configure timing intervals and chances
- **Token Limits**: Adjust maximum response lengths for different response types

Access the configuration at: `http://your-server/HerikaServer/ext/enhanced_narrator/config.html`

## Compatibility

This plugin is designed to work alongside existing HerikaServer functionality and should not interfere with normal NPC interactions or other plugins. All features can be individually disabled through the configuration interface.