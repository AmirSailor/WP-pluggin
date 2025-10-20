<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class OpenAI_Whisper_Service implements SpeechToTextService {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function transcribe($audio_file_path) {
        try {
            $api_url = 'https://api.openai.com/v1/audio/transcriptions';

            $boundary = wp_generate_password(24);
            $headers  = [
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
                'Authorization' => 'Bearer ' . $this->api_key,
            ];

            $body = '--' . $boundary . "\r\n";
            $body .= 'Content-Disposition: form-data; name="file"; filename="' . basename($audio_file_path) . "\"\r\n";
            $body .= "Content-Type: audio/mpeg\r\n\r\n";
            $body .= file_get_contents($audio_file_path) . "\r\n";
            $body .= '--' . $boundary . "\r\n";
            $body .= "Content-Disposition: form-data; name=\"model\"\r\n\r\n";
            $body .= "whisper-1\r\n";
            $body .= '--' . $boundary . '--';

            $response = wp_remote_post($api_url, [
                'headers' => $headers,
                'body' => $body,
            ]);

            if (is_wp_error($response)) {
                return $response->get_error_message();
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            return $data['text'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
