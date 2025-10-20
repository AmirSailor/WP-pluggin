<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Add teachers field to the "Add New Course" form
function aigq_add_course_teachers_field() {
    ?>
    <div class="form-field">
        <label for="course_teachers"><?php _e('Teachers', 'aigq'); ?></label>
        <select name="course_teachers[]" id="course_teachers" multiple="multiple" style="width: 100%;">
            <?php
            $teachers = get_users(['role' => 'teacher']);
            foreach ($teachers as $teacher) {
                echo '<option value="' . $teacher->ID . '">' . esc_html($teacher->display_name) . '</option>';
            }
            ?>
        </select>
        <p class="description"><?php _e('Assign teachers to this course.', 'aigq'); ?></p>
    </div>
    <?php
}
add_action('course_add_form_fields', 'aigq_add_course_teachers_field');

// Add teachers field to the "Edit Course" form
function aigq_edit_course_teachers_field($term) {
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="course_teachers"><?php _e('Teachers', 'aigq'); ?></label></th>
        <td>
            <select name="course_teachers[]" id="course_teachers" multiple="multiple" style="width: 100%;">
                <?php
                $teachers = get_users(['role' => 'teacher']);
                $selected_teachers = get_term_meta($term->term_id, '_aigq_course_teachers', true);
                foreach ($teachers as $teacher) {
                    echo '<option value="' . $teacher->ID . '"' . (is_array($selected_teachers) && in_array($teacher->ID, $selected_teachers) ? ' selected' : '') . '>' . esc_html($teacher->display_name) . '</option>';
                }
                ?>
            </select>
            <p class="description"><?php _e('Assign teachers to this course.', 'aigq'); ?></p>
        </td>
    </tr>
    <?php
}
add_action('course_edit_form_fields', 'aigq_edit_course_teachers_field');

// Save the teachers field
function aigq_save_course_teachers_field($term_id) {
    if (isset($_POST['course_teachers'])) {
        $teachers = array_map('intval', $_POST['course_teachers']);
        update_term_meta($term_id, '_aigq_course_teachers', $teachers);
    }
}
add_action('create_course', 'aigq_save_course_teachers_field');
add_action('edited_course', 'aigq_save_course_teachers_field');
