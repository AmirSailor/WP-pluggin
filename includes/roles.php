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
        'manager',
        __('Manager', 'aigq'),
        [
            'read' => true,
            'manage_ai_quiz' => true,
            'manage_options' => true,
            'edit_posts' => true,
            'manage_terms_course' => true,
            'edit_terms_course' => true,
            'delete_terms_course' => true,
            'assign_terms_course' => true,
            'manage_terms_level' => true,
            'edit_terms_level' => true,
            'delete_terms_level' => true,
            'assign_terms_level' => true,
            'edit_class' => true,
            'read_class' => true,
            'delete_class' => true,
            'edit_classes' => true,
            'edit_others_classes' => true,
            'publish_classes' => true,
            'read_private_classes' => true,
            'delete_classes' => true,
            'delete_private_classes' => true,
            'delete_published_classes' => true,
            'delete_others_classes' => true,
            'edit_private_classes' => true,
            'edit_published_classes' => true,
        ]
    );

    $admin_role = get_role('administrator');
    if ($admin_role) {
        $admin_role->add_cap('manage_ai_quiz');
    }

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
            'assign_terms_level' => true,
            'edit_class' => true,
            'read_class' => true,
            'delete_class' => true,
            'edit_classes' => true,
            'edit_others_classes' => false,
            'publish_classes' => true,
            'read_private_classes' => true,
            'delete_classes' => true,
            'delete_private_classes' => true,
            'delete_published_classes' => true,
            'delete_others_classes' => false,
            'edit_private_classes' => true,
            'edit_published_classes' => true,
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
    remove_role('manager');
    remove_role('teacher');
    remove_role('student');
}