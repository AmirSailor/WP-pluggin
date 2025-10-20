<?php

/**
 * This file handles the addition of custom fields to the user profile page for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add custom fields to the user profile page.
 *
 * This function is hooked into the 'show_user_profile' and 'edit_user_profile' actions.
 *
 * @param WP_User $user The user object.
 */
function aigq_add_user_profile_fields($user) {
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <h3><?php _e('AI Quiz Generator Settings', 'aigq'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="course"><?php _e('Courses', 'aigq'); ?></label></th>
            <td>
                <select name="course[]" id="course" multiple="multiple" style="min-width: 200px;">
                    <?php
                    $courses = get_terms(['taxonomy' => 'course', 'hide_empty' => false]);
                    $user_courses = get_user_meta($user->ID, 'course', false);
                    foreach ($courses as $course) {
                        echo '<option value="' . $course->term_id . '"' . (in_array($course->term_id, $user_courses) ? ' selected' : '') . '>' . $course->name . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="level"><?php _e('Levels', 'aigq'); ?></label></th>
            <td>
                <select name="level[]" id="level" multiple="multiple" style="min-width: 200px;">
                    <?php
                    $levels = get_terms(['taxonomy' => 'level', 'hide_empty' => false]);
                    $user_levels = get_user_meta($user->ID, 'level', false);
                    foreach ($levels as $level) {
                        echo '<option value="' . $level->term_id . '"' . (in_array($level->term_id, $user_levels) ? ' selected' : '') . '>' . $level->name . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'aigq_add_user_profile_fields');
add_action('edit_user_profile', 'aigq_add_user_profile_fields');

/**
 * Save the custom fields on the user profile page.
 */
function aigq_save_user_profile_fields($user_id) {
    if (!current_user_can('manage_options')) {
        return;
    }

    delete_user_meta($user_id, 'course');
    if (isset($_POST['course'])) {
        foreach ($_POST['course'] as $course) {
            add_user_meta($user_id, 'course', intval($course));
        }
    }

    delete_user_meta($user_id, 'level');
    if (isset($_POST['level'])) {
        foreach ($_POST['level'] as $level) {
            add_user_meta($user_id, 'level', intval($level));
        }
    }
}
add_action('personal_options_update', 'aigq_save_user_profile_fields');
add_action('edit_user_profile_update', 'aigq_save_user_profile_fields');
