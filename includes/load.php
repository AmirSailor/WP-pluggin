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
require_once AIGQ_PLUGIN_DIR . '/includes/registration.php';

// Admin files
require_once AIGQ_PLUGIN_DIR . '/admin/lecture-meta-box.php';
require_once AIGQ_PLUGIN_DIR . '/admin/management-page.php';
require_once AIGQ_PLUGIN_DIR . '/admin/quiz-results-page.php';
require_once AIGQ_PLUGIN_DIR . '/admin/settings-page.php';
require_once AIGQ_PLUGIN_DIR . '/admin/teacher-upload-page.php';
require_once AIGQ_PLUGIN_DIR . '/admin/user-profile-fields.php';
require_once AIGQ_PLUGIN_DIR . '/admin/taxonomy-fields.php';
require_once AIGQ_PLUGIN_DIR . '/admin/class-meta-box.php';