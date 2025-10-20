<?php

/**
 * This file handles the sending of email notifications for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Send an email notification to students when a quiz is published.
 *
 * This function is hooked into the 'transition_post_status' action.
 *
 * @param string  $new_status The new post status.
 * @param string  $old_status The old post status.
 * @param WP_Post $post       The post object.
 */
function aigq_send_quiz_notification($new_status, $old_status, $post) {
    if ('publish' === $new_status && 'publish' !== $old_status && 'quiz' === $post->post_type) {
        // Get the course and level of the quiz
        $course = wp_get_post_terms($post->ID, 'course');
        $level = wp_get_post_terms($post->ID, 'level');

        if ($course && $level) {
            $course_id = $course[0]->term_id;
            $level_id = $level[0]->term_id;

            // Get all students in the same course and level
            $args = [
                'role' => 'student',
                'meta_query' => [
                                    'relation' => 'AND',
                                    [
                                        'key' => 'course',
                                        'value' => $course_id,
                                        'compare' => 'IN',
                                    ],
                                    [
                                        'key' => 'level',
                                        'value' => $level_id,
                                        'compare' => 'IN',
                                    ],                ],
            ];
            $students = get_users($args);

            // Send an email to each student
            foreach ($students as $student) {
                $to = $student->user_email;
                $subject = __('New Quiz Available', 'aigq');
                $message = sprintf(
                    __('A new quiz, "%s", is now available in your portal.', 'aigq'),
                    get_the_title($post)
                );
                wp_mail($to, $subject, $message);
            }
        }
    }
}
add_action('transition_post_status', 'aigq_send_quiz_notification', 10, 3);
