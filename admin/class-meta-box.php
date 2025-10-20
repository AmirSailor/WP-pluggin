<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Add meta box for the class post type
function aigq_add_class_meta_box() {
    add_meta_box(
        'aigq_class_meta_box',
        __('Class Details', 'aigq'),
        'aigq_render_class_meta_box',
        'class',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'aigq_add_class_meta_box');

// Render the meta box
function aigq_render_class_meta_box($post) {
    wp_nonce_field('aigq_save_class_meta_box_data', 'aigq_class_meta_box_nonce');

    $course_id = get_post_meta($post->ID, '_aigq_class_course', true);
    $level_id = get_post_meta($post->ID, '_aigq_class_level', true);
    $teachers = get_post_meta($post->ID, '_aigq_class_teachers', true);
    $students = get_post_meta($post->ID, '_aigq_class_students', true);
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e('Course', 'aigq'); ?></th>
            <td>
                <?php
                wp_dropdown_categories([
                    'taxonomy' => 'course',
                    'name' => 'class_course',
                    'selected' => $course_id,
                    'show_option_none' => __('Select a course', 'aigq'),
                    'hide_empty' => 0,
                ]);
                ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Level', 'aigq'); ?></th>
            <td>
                <?php
                wp_dropdown_categories([
                    'taxonomy' => 'level',
                    'name' => 'class_level',
                    'selected' => $level_id,
                    'show_option_none' => __('Select a level', 'aigq'),
                    'hide_empty' => 0,
                ]);
                ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Teachers', 'aigq'); ?></th>
            <td>
                <select name="class_teachers[]" multiple="multiple" style="width:100%;">
                    <?php
                    $all_teachers = get_users(['role' => 'teacher']);
                    foreach ($all_teachers as $teacher) {
                        echo '<option value="' . $teacher->ID . '"' . (is_array($teachers) && in_array($teacher->ID, $teachers) ? ' selected' : '') . '>' . esc_html($teacher->display_name) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Students', 'aigq'); ?></th>
            <td>
                <select name="class_students[]" multiple="multiple" style="width:100%;">
                    <?php
                    $all_students = get_users(['role' => 'student']);
                    foreach ($all_students as $student) {
                        echo '<option value="' . $student->ID . '"' . (is_array($students) && in_array($student->ID, $students) ? ' selected' : '') . '>' . esc_html($student->display_name) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// Save the meta box data
function aigq_save_class_meta_box_data($post_id) {
    if (!isset($_POST['aigq_class_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['aigq_class_meta_box_nonce'], 'aigq_save_class_meta_box_data')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (isset($_POST['post_type']) && 'class' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['class_course'])) {
        update_post_meta($post_id, '_aigq_class_course', intval($_POST['class_course']));
    }
    if (isset($_POST['class_level'])) {
        update_post_meta($post_id, '_aigq_class_level', intval($_POST['class_level']));
    }
    if (isset($_POST['class_teachers'])) {
        update_post_meta($post_id, '_aigq_class_teachers', array_map('intval', $_POST['class_teachers']));
    }
    if (isset($_POST['class_students'])) {
        update_post_meta($post_id, '_aigq_class_students', array_map('intval', $_POST['class_students']));
    }
}
add_action('save_post', 'aigq_save_class_meta_box_data');
