<?php

/**
 * This file handles the loading of the custom quiz template for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Load the quiz template.
 *
 * This function is hooked into the 'template_include' filter.
 *
 * @param string $template The path to the template file.
 * @return string The path to the new template file.
 */
function aigq_load_quiz_template($template) {
    if (is_singular('quiz')) {
        $new_template = plugin_dir_path(__FILE__) . '../templates/single-quiz.php';
        if (file_exists($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'aigq_load_quiz_template');

/**
 * Enqueue scripts for the quiz page.
 */
function aigq_enqueue_quiz_scripts() {
    if (is_singular('quiz')) {
        wp_enqueue_script(
            'aigq-quiz-submission',
            plugin_dir_url(__FILE__) . '../assets/js/quiz-submission.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script(
            'aigq-quiz-submission',
            'aigq_ajax',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aigq-quiz-submission'),
            ]
        );
    }
}
add_action('wp_enqueue_scripts', 'aigq_enqueue_quiz_scripts');

/**
 * Handle the AJAX quiz submission.
 */
function aigq_submit_quiz() {
    check_ajax_referer('aigq-quiz-submission', 'nonce');

    $quiz_id = intval($_POST['quiz_id']);
    parse_str($_POST['answers'], $answers);
    $answers = $answers['aigq_answers'];

    global $wpdb;
    $user_id = get_current_user_id();
    $lecture_id = get_post_meta($quiz_id, '_quiz_lecture_id', true);
    $quiz_data_json = get_post_meta($lecture_id, '_lecture_quiz_data', true);
    $quiz_data = json_decode($quiz_data_json, true);
    $quiz = $quiz_data['quiz'];

    $score = 0;

    // Insert the attempt
    $table_name_attempts = $wpdb->prefix . 'quiz_attempts';
    $wpdb->query($wpdb->prepare(
        "INSERT INTO $table_name_attempts (quiz_id, user_id, score, date_taken) VALUES (%d, %d, %d, %s)",
        $quiz_id,
        $user_id,
        0, // Placeholder
        current_time('mysql')
    ));
    $attempt_id = $wpdb->insert_id;

    // Check the answers
    $table_name_answers = $wpdb->prefix . 'quiz_answers';
    foreach ($quiz as $index => $question) {
        $is_correct = false;
        if (isset($answers[$index]) && $answers[$index] === $question['answer']) {
            $score++;
            $is_correct = true;
        }

        // Insert the answer
        $wpdb->query($wpdb->prepare(
            "INSERT INTO $table_name_answers (attempt_id, question_id, selected_answer, is_correct) VALUES (%d, %d, %s, %d)",
            $attempt_id,
            $index,
            $answers[$index],
            $is_correct
        ));
    }

    // Update the score
    $wpdb->query($wpdb->prepare(
        "UPDATE $table_name_attempts SET score = %d WHERE attempt_id = %d",
        $score,
        $attempt_id
    ));

    // Prepare the results HTML
    $html = '<h2>' . __('Your Results', 'aigq') . '</h2>';
    $html .= '<p>' . sprintf(__('You scored %d out of %d.', 'aigq'), $score, count($quiz)) . '</p>';

    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_aigq_submit_quiz', 'aigq_submit_quiz');
