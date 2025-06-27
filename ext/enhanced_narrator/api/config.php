<?php

header('Content-Type: application/json');

$pluginPath = dirname(__DIR__);

// Always load the base configuration first
require_once("$pluginPath/config.base.php");

// Then load the custom config if it exists, otherwise create it from base
if (!file_exists("$pluginPath/config.php")) {
    copy("$pluginPath/config.base.php", "$pluginPath/config.php");
}

function escapeString($string) {
    return str_replace(['\\', '"'], ['\\\\', '\\"'], $string);
}

// Read config data from the file (GET request)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['defaults']) && $_GET['defaults'] === 'true') {
        // Return default configuration
        require_once("$pluginPath/config.base.php");
    } else {
        require_once("$pluginPath/config.base.php");
        if (file_exists("$pluginPath/config.php")) {
            require_once("$pluginPath/config.php");
        }
    }
    
    // Prepare the response using the loaded configuration
    $configData = array(
        "enhanced_narrator_settings" => $GLOBALS["enhanced_narrator_settings"],
        "enhanced_narrator_events" => $GLOBALS["enhanced_narrator_events"],
        "enhanced_narrator_auto" => $GLOBALS["enhanced_narrator_auto"],
        "enhanced_narrator_tokens" => $GLOBALS["enhanced_narrator_tokens"]
    );
    
    // Return the config data as JSON
    echo json_encode($configData);
}

// Update config data and write it back to the config.php file (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Decode JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            throw new Exception("Failed to decode JSON input: " . json_last_error_msg());
        }

        // Build the new config content string
        $newConfig = "<?php\n";
        $newConfig .= "// Enhanced Narrator Plugin Configuration\n";
        $newConfig .= "// This file overrides values from config.base.php\n\n";
        
        // Inner Voice Narrator Settings
        if (isset($input['enhanced_narrator_settings'])) {
            $settings = $input['enhanced_narrator_settings'];
            $newConfig .= "\$GLOBALS[\"enhanced_narrator_settings\"] = array(\n";
            $newConfig .= "    \"enabled\" => " . ($settings['enabled'] ? 'true' : 'false') . ",\n";
            
            // Inner voice prompts array
            $newConfig .= "    \"inner_voice_prompts\" => array(\n";
            foreach ($settings['inner_voice_prompts'] as $prompt) {
                $newConfig .= "        \"" . escapeString($prompt) . "\",\n";
            }
            $newConfig .= "    ),\n";
            
            // Roleplay prompt
            $newConfig .= "    \"roleplay_prompt\" => \"" . escapeString($settings['roleplay_prompt']) . "\",\n";
            
            // Selfgen prompts array
            $newConfig .= "    \"selfgen_prompts\" => array(\n";
            foreach ($settings['selfgen_prompts'] as $prompt) {
                $newConfig .= "        \"" . escapeString($prompt) . "\",\n";
            }
            $newConfig .= "    ),\n";
            
            // Direct prompt and Talk with player prompts
            $newConfig .= "    \"directprompt_prompt\" => \"" . escapeString($settings['directprompt_prompt'] ?? '') . "\",\n";
            $newConfig .= "    \"talkwithplayer_prompt\" => \"" . escapeString($settings['talkwithplayer_prompt'] ?? '') . "\"\n";
            $newConfig .= ");\n\n";
        }
        
        // Event Commentary Settings
        if (isset($input['enhanced_narrator_events'])) {
            $events = $input['enhanced_narrator_events'];
            $newConfig .= "\$GLOBALS[\"enhanced_narrator_events\"] = array(\n";
            foreach ($events as $eventName => $eventConfig) {
                $newConfig .= "    \"$eventName\" => array(\n";
                $newConfig .= "        \"enabled\" => " . ($eventConfig['enabled'] ? 'true' : 'false') . ",\n";
                $newConfig .= "        \"chance\" => " . intval($eventConfig['chance']) . "";
                if (isset($eventConfig['use_inner_voice'])) {
                    $newConfig .= ",\n        \"use_inner_voice\" => " . intval($eventConfig['use_inner_voice']);
                }
                $newConfig .= "\n    ),\n";
            }
            $newConfig .= ");\n\n";
        }
        
        // Automatic Commentary Settings
        if (isset($input['enhanced_narrator_auto'])) {
            $auto = $input['enhanced_narrator_auto'];
            $newConfig .= "\$GLOBALS[\"enhanced_narrator_auto\"] = array(\n";
            $newConfig .= "    \"enabled\" => " . ($auto['enabled'] ? 'true' : 'false') . ",\n";
            $newConfig .= "    \"base_chance\" => " . intval($auto['base_chance']) . ",\n";
            $newConfig .= "    \"max_chance\" => " . intval($auto['max_chance']) . ",\n";
            $newConfig .= "    \"min_interval\" => " . intval($auto['min_interval']) . ",\n";
            $newConfig .= "    \"conversation_chance\" => " . intval($auto['conversation_chance']) . "\n";
            $newConfig .= ");\n\n";
        }
        
        // Token Limits
        if (isset($input['enhanced_narrator_tokens'])) {
            $tokens = $input['enhanced_narrator_tokens'];
            $newConfig .= "\$GLOBALS[\"enhanced_narrator_tokens\"] = array(\n";
            foreach ($tokens as $tokenType => $limit) {
                $newConfig .= "    \"$tokenType\" => " . intval($limit) . ",\n";
            }
            $newConfig .= ");\n\n";
        }

        // Write configuration
        $configFile = "$pluginPath/config.php";
        
        if (!is_writable($pluginPath)) {
            throw new Exception("Plugin directory is not writable");
        }
        
        if (file_exists($configFile) && !is_writable($configFile)) {
            throw new Exception("Configuration file exists but is not writable");
        }

        $success = (file_put_contents($configFile, $newConfig) !== false);
        if (!$success) {
            throw new Exception("Failed to write configuration file");
        }

        // Clear the configuration cache
        clearstatcache(true, $configFile);
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Configuration saved successfully',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

?>