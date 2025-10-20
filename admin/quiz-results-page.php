<?php

/**
 * This file handles the creation of the quiz results page for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add the admin menu for the quiz results page.
 *
 * This function is hooked into the 'admin_menu' action.
 */
function aigq_add_quiz_results_menu() {
    add_submenu_page(
        'aigq-settings',
        __('Quiz Results', 'aigq'),
        __('Quiz Results', 'aigq'),
        'edit_posts', // Capability for teachers
        'aigq-quiz-results',
        'aigq_render_quiz_results_page'
    );
}
add_action('admin_menu', 'aigq_add_quiz_results_menu');

/**
 * Render the quiz results page.
 */
function aigq_render_quiz_results_page() {
    global $wpdb;
    $user_id = get_current_user_id();

    // Get the teacher's quizzes
    $args = [
        'post_type' => 'quiz',
        'author' => $user_id,
        'posts_per_page' => -1,
    ];
    $quizzes = new WP_Query($args);

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php if (isset($_GET['quiz_id'])) : ?>
            <?php
            $quiz_id = intval($_GET['quiz_id']);
            $table_name_attempts = $wpdb->prefix . 'quiz_attempts';
            $attempts = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name_attempts WHERE quiz_id = %d ORDER BY date_taken DESC",
                $quiz_id
            ));
            ?>
            <h2><?php printf(__('Results for %s', 'aigq'), get_the_title($quiz_id)); ?></h2>
            <a href="<?php echo admin_url('admin.php?page=aigq-quiz-results'); ?>"><?php _e('&laquo; Back to all quizzes', 'aigq'); ?></a>
            <?php if ($attempts) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Student', 'aigq'); ?></th>
                            <th><?php _e('Score', 'aigq'); ?></th>
                            <th><?php _e('Date Taken', 'aigq'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attempts as $attempt) : ?>
                            <?php
                            $student = get_user_by('id', $attempt->user_id);
                            ?>
                            <tr>
                                <td><?php echo $student ? esc_html($student->display_name) : __('User not found', 'aigq'); ?></td>
                                <td><?php echo esc_html($attempt->score); ?></td>
                                <td><?php echo esc_html($attempt->date_taken); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('No results for this quiz yet.', 'aigq'); ?></p>
            <?php endif; ?>

        <?php else : ?>
            <h2><?php _e('Your Quizzes', 'aigq'); ?></h2>
            <?php if ($quizzes->have_posts()) : ?>
                <ul>
                    <?php while ($quizzes->have_posts()) : $quizzes->the_post(); ?>
                        <li>
                            <a href="<?php echo add_query_arg('quiz_id', get_the_ID()); ?>">
                                <?php the_title(); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php _e('You have not created any quizzes yet.', 'aigq'); ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}
