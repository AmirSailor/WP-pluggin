<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

interface SpeechToTextService {
    public function transcribe($audio_file_path);
}

interface LLMService {
    public function generateQuiz($text);
}
