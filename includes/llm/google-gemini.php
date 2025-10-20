<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Google_Gemini_Service implements LLMService {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function generateQuiz($text) {
        try {
            $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $this->api_key;

            $prompt = 'You are an expert educator\'s assistant. Based on the following transcript, perform two tasks:
1.  Generate a concise summary of about 150 words.
2.  Create a 10-question multiple-choice quiz.

For the quiz, strictly follow this JSON format:
{
  "summary": "Your generated summary here...",
  "quiz": [
    {
      "question": "The question text?",
      "options": [
        "Option A",
        "Option B",
        "Option C",
        "Option D"
      ],
      "answer": "Option C"
    }
  ]
}

Transcript:
' . $text;

            $response = wp_remote_post($api_url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                ]),
            ]);

            if (is_wp_error($response)) {
                return $response->get_error_message();
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            return $data['candidates'][0]['content']['parts'][0]['text'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
