<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class OpenAI_GPT_Service implements LLMService {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function generateQuiz($text) {
        try {
            $api_url = 'https://api.openai.com/v1/chat/completions';

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
                    'Authorization' => 'Bearer ' . $this->api_key,
                ],
                'body' => json_encode([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]),
            ]);

            if (is_wp_error($response)) {
                return $response->get_error_message();
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            return $data['choices'][0]['message']['content'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
