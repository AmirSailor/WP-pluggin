<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Google_Speech_To_Text_Service implements SpeechToTextService {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function transcribe($audio_file_path) {
        try {
            $api_url = 'https://speech.googleapis.com/v1/speech:recognize?key=' . $this->api_key;

            $audio_content = base64_encode(file_get_contents($audio_file_path));

            $response = wp_remote_post($api_url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'config' => [
                        'encoding' => 'LINEAR16',
                        'sampleRateHertz' => 16000,
                        'languageCode' => 'en-US',
                    ],
                    'audio' => [
                        'content' => $audio_content,
                    ],
                ]),
            ]);

            if (is_wp_error($response)) {
                return $response->get_error_message();
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            return $data['results'][0]['alternatives'][0]['transcript'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
