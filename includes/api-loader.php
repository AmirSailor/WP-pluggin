<?php

/**
 * This file handles the loading of the API services for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the API service interfaces.
require_once plugin_dir_path(__FILE__) . 'interfaces.php';

/**
 * Get the Speech-to-Text service.
 *
 * @return SpeechToTextService|null The STT service instance, or null if not found.
 */
function aigq_get_stt_service() {
    $service_name = get_option('aigq_stt_service', 'google');
    $api_key = get_option('aigq_stt_api_key');

    if ($service_name === 'google') {
        require_once plugin_dir_path(__FILE__) . 'stt/google-speech-to-text.php';
        return new Google_Speech_To_Text_Service($api_key);
    } elseif ($service_name === 'openai') {
        require_once plugin_dir_path(__FILE__) . 'stt/openai-whisper.php';
        return new OpenAI_Whisper_Service($api_key);
    }

    return null;
}

function aigq_get_llm_service() {
    $service_name = get_option('aigq_llm_service', 'google');
    $api_key = get_option('aigq_llm_api_key');

    if ($service_name === 'google') {
        require_once plugin_dir_path(__FILE__) . 'llm/google-gemini.php';
        return new Google_Gemini_Service($api_key);
    } elseif ($service_name === 'openai') {
        require_once plugin_dir_path(__FILE__) . 'llm/openai-gpt.php';
        return new OpenAI_GPT_Service($api_key);
    }

    return null;
}
