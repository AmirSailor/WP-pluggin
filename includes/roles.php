<?php

/**
 * This file handles the creation and removal of custom user roles for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Create custom user roles on plugin activation.
 *
 * This function is hooked into the plugin activation hook.
 */
function aigq_add_custom_roles() {
    add_role(
        'teacher',
        __('Teacher', 'aigq'),
        [
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'publish_posts' => true,
            'upload_files' => true,
            'edit_published_posts' => true,
            'delete_published_posts' => true,
            'edit_lectures' => true,
            'edit_others_lectures' => false,
            'delete_lectures' => true,
            'delete_others_lectures' => false,
            'publish_lectures' => true,
            'read_private_lectures' => true,
            'edit_quizzes' => true,
            'edit_others_quizzes' => false,
            'delete_quizzes' => true,
            'delete_others_quizzes' => false,
            'publish_quizzes' => true,
            'read_private_quizzes' => true,
        ]
    );

    add_role(
        'student',
        __('Student', 'aigq'),
        [
            'read' => true,
        ]
    );
}

/**
 * Remove custom user roles on plugin deactivation.
 */
function aigq_remove_custom_roles() {
    remove_role('teacher');
    remove_role('student');
}