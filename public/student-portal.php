<?php

/**
 * This file handles the display of the student portal for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add the shortcode for the student portal.
 *
 * This function is hooked into the 'add_shortcode' function.
 *
 * @return string The HTML for the student portal.
 */
function aigq_student_portal_shortcode() {
    if (!is_user_logged_in()) {
        return __('You must be logged in to view this content.', 'aigq');
    }

    $user = wp_get_current_user();
    if (!in_array('student', (array) $user->roles)) {
        return __('You do not have permission to view this content.', 'aigq');
    }

    ob_start();

    // Get the student's courses and levels
    $student_courses = get_user_meta($user->ID, 'course', true);
    $student_levels = get_user_meta($user->ID, 'level', true);

    // Get the quizzes for the student
    $args = [
        'post_type' => 'quiz',
        'post_status' => 'publish',
        'tax_query' => [
            'relation' => 'AND',
            [
                'taxonomy' => 'course',
                'field' => 'term_id',
                'terms' => $student_courses,
                'operator' => 'IN',
            ],
            [
                'taxonomy' => 'level',
                'field' => 'term_id',
                'terms' => $student_levels,
                'operator' => 'IN',
            ],
        ],
    ];
    $quizzes = new WP_Query($args);

    if ($quizzes->have_posts()) {
        echo '<ul>';
        while ($quizzes->have_posts()) {
            $quizzes->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo __('No quizzes available for you at the moment.', 'aigq');
    }

    echo '<h2>' . __('Quiz History', 'aigq') . '</h2>';

    global $wpdb;
    $table_name_attempts = $wpdb->prefix . 'quiz_attempts';
    $attempts = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name_attempts WHERE user_id = %d ORDER BY date_taken DESC",
        $user->ID
    ));

    if ($attempts) {
        echo '<table>';
        echo '<thead><tr><th>' . __('Quiz', 'aigq') . '</th><th>' . __('Score', 'aigq') . '</th><th>' . __('Date Taken', 'aigq') . '</th></tr></thead>';
        echo '<tbody>';
        foreach ($attempts as $attempt) {
            $quiz_title = get_the_title($attempt->quiz_id);
            echo '<tr>';
            echo '<td>' . esc_html($quiz_title) . '</td>';
            echo '<td>' . esc_html($attempt->score) . '</td>';
            echo '<td>' . esc_html($attempt->date_taken) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo __('You have not taken any quizzes yet.', 'aigq');
    }

    return ob_get_clean();
}
add_shortcode('aigq_student_portal', 'aigq_student_portal_shortcode');
