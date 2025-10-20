<?php

/**
 * This file handles the creation of the admin management page for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}



/**
 * Render the students table.
 */
function aigq_render_students_table() {
    $students = get_users(['role' => 'student']);
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Username', 'aigq'); ?></th>
                <th><?php _e('Email', 'aigq'); ?></th>
                <th><?php _e('Classes', 'aigq'); ?></th>
                <th><?php _e('Actions', 'aigq'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($students)) : ?>
                <?php foreach ($students as $student) : ?>
                    <tr>
                        <td><?php echo esc_html($student->user_login); ?></td>
                        <td><?php echo esc_html($student->user_email); ?></td>
                        <td>
                            <?php
                            $classes = get_posts([
                                'post_type' => 'class',
                                'meta_query' => [
                                    [
                                        'key' => '_aigq_class_students',
                                        'value' => '"' . $student->ID . '"',
                                        'compare' => 'LIKE'
                                    ]
                                ]
                            ]);
                            if ($classes) {
                                foreach ($classes as $class) {
                                    echo esc_html($class->post_title) . '<br>';
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo get_edit_user_link($student->ID); ?>"><?php _e('Edit', 'aigq'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4"><?php _e('No students found.', 'aigq'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Render the teachers table.
 */
function aigq_render_teachers_table() {
    $teachers = get_users(['role' => 'teacher']);
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Username', 'aigq'); ?></th>
                <th><?php _e('Email', 'aigq'); ?></th>
                <th><?php _e('Classes', 'aigq'); ?></th>
                <th><?php _e('Actions', 'aigq'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($teachers)) : ?>
                <?php foreach ($teachers as $teacher) : ?>
                    <tr>
                        <td><?php echo esc_html($teacher->user_login); ?></td>
                        <td><?php echo esc_html($teacher->user_email); ?></td>
                        <td>
                            <?php
                            $classes = get_posts([
                                'post_type' => 'class',
                                'meta_query' => [
                                    [
                                        'key' => '_aigq_class_teachers',
                                        'value' => '"' . $teacher->ID . '"',
                                        'compare' => 'LIKE'
                                    ]
                                ]
                            ]);
                            if ($classes) {
                                foreach ($classes as $class) {
                                    echo esc_html($class->post_title) . '<br>';
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo get_edit_user_link($teacher->ID); ?>"><?php _e('Edit', 'aigq'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4"><?php _e('No teachers found.', 'aigq'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Render the quizzes table.
 */
function aigq_render_quizzes_table() {
    $quizzes = get_posts(['post_type' => 'quiz', 'posts_per_page' => -1]);
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Title', 'aigq'); ?></th>
                <th><?php _e('Author', 'aigq'); ?></th>
                <th><?php _e('Date', 'aigq'); ?></th>
                <th><?php _e('Actions', 'aigq'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($quizzes)) : ?>
                <?php foreach ($quizzes as $quiz) : ?>
                    <tr>
                        <td><?php echo esc_html($quiz->post_title); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('display_name', $quiz->post_author)); ?></td>
                        <td><?php echo esc_html(get_the_date('', $quiz)); ?></td>
                        <td>
                            <a href="<?php echo get_edit_post_link($quiz->ID); ?>"><?php _e('Edit', 'aigq'); ?></a>
                            <a href="<?php echo get_delete_post_link($quiz->ID); ?>"><?php _e('Delete', 'aigq'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4"><?php _e('No quizzes found.', 'aigq'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Render a taxonomy table.
 */
function aigq_render_taxonomy_table($taxonomy) {
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
    $taxonomy_obj = get_taxonomy($taxonomy);
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo esc_html($taxonomy_obj->labels->name); ?></h1>
        <a href="<?php echo admin_url('edit-tags.php?taxonomy=' . $taxonomy); ?>" class="page-title-action"><?php echo esc_html($taxonomy_obj->labels->add_new_item); ?></a>
        <hr class="wp-header-end">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Course ID', 'aigq'); ?></th>
                    <th><?php _e('Name', 'aigq'); ?></th>
                    <th><?php _e('Slug', 'aigq'); ?></th>
                    <th><?php _e('Count', 'aigq'); ?></th>
                    <?php if ($taxonomy == 'course') : ?>
                        <th><?php _e('Teachers', 'aigq'); ?></th>
                    <?php endif; ?>
                    <th><?php _e('Actions', 'aigq'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($terms)) : ?>
                    <?php foreach ($terms as $term) : ?>
                        <tr>
                            <td><?php echo esc_html($term->term_id); ?></td>
                            <td><?php echo esc_html($term->name); ?></td>
                            <td><?php echo esc_html($term->slug); ?></td>
                            <td><?php echo esc_html($term->count); ?></td>
                            <?php if ($taxonomy == 'course') : ?>
                                <td>
                                    <?php
                                    $teachers = get_term_meta($term->term_id, '_aigq_course_teachers', true);
                                    if (is_array($teachers)) {
                                        foreach ($teachers as $teacher_id) {
                                            $teacher = get_user_by('id', $teacher_id);
                                            if ($teacher) {
                                                echo esc_html($teacher->display_name) . '<br>';
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <a href="<?php echo get_edit_term_link($term->term_id, $taxonomy); ?>"><?php _e('Edit', 'aigq'); ?></a>
                                <a href="<?php echo wp_nonce_url(admin_url('edit-tags.php?action=delete&taxonomy=' . $taxonomy . '&tag_ID=' . $term->term_id), 'delete-tag_' . $term->term_id); ?>"><?php _e('Delete', 'aigq'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="<?php echo ($taxonomy == 'course') ? 6 : 5; ?>"><?php printf(__('No %s found.', 'aigq'), strtolower($taxonomy_obj->labels->name)); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
