<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the plugin's files.
require_once AIGQ_PLUGIN_DIR . '/includes/post-types.php';
require_once AIGQ_PLUGIN_DIR . '/includes/database.php';
require_once AIGQ_PLUGIN_DIR . '/includes/roles.php';
require_once AIGQ_PLUGIN_DIR . '/includes/interfaces.php';
require_once AIGQ_PLUGIN_DIR . '/includes/stt/google-speech-to-text.php';
require_once AIGQ_PLUGIN_DIR . '/includes/stt/openai-whisper.php';
require_once AIGQ_PLUGIN_DIR . '/includes/llm/google-gemini.php';
require_once AIGQ_PLUGIN_DIR . '/includes/llm/openai-gpt.php';
require_once AIGQ_PLUGIN_DIR . '/includes/api-loader.php';
require_once AIGQ_PLUGIN_DIR . '/includes/notifications.php';
require_once AIGQ_PLUGIN_DIR . '/includes/class-ai-quiz-generator.php';
require_once AIGQ_PLUGIN_DIR . '/lib/fpdf.php';