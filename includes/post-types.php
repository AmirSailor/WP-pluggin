<?php

/**
 * This file registers the custom post types and taxonomies for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Register custom post types and taxonomies.
 *
 * This function is hooked into the 'init' action.
 */
function aigq_register_post_types() {
    // Register Lecture Post Type
    $lecture_labels = [
        'name' => _x('Lectures', 'Post Type General Name', 'aigq'),
        'singular_name' => _x('Lecture', 'Post Type Singular Name', 'aigq'),
        'menu_name' => __('Lectures', 'aigq'),
        'name_admin_bar' => __('Lecture', 'aigq'),
        'archives' => __('Lecture Archives', 'aigq'),
        'attributes' => __('Lecture Attributes', 'aigq'),
        'parent_item_colon' => __('Parent Lecture:', 'aigq'),
        'all_items' => __('All Lectures', 'aigq'),
        'add_new_item' => __('Add New Lecture', 'aigq'),
        'add_new' => __('Add New', 'aigq'),
        'new_item' => __('New Lecture', 'aigq'),
        'edit_item' => __('Edit Lecture', 'aigq'),
        'update_item' => __('Update Lecture', 'aigq'),
        'view_item' => __('View Lecture', 'aigq'),
        'view_items' => __('View Lectures', 'aigq'),
        'search_items' => __('Search Lecture', 'aigq'),
        'not_found' => __('Not found', 'aigq'),
        'not_found_in_trash' => __('Not found in Trash', 'aigq'),
        'featured_image' => __('Featured Image', 'aigq'),
        'set_featured_image' => __('Set featured image', 'aigq'),
        'remove_featured_image' => __('Remove featured image', 'aigq'),
        'use_featured_image' => __('Use as featured image', 'aigq'),
        'insert_into_item' => __('Insert into lecture', 'aigq'),
        'uploaded_to_this_item' => __('Uploaded to this lecture', 'aigq'),
        'items_list' => __('Lectures list', 'aigq'),
        'items_list_navigation' => __('Lectures list navigation', 'aigq'),
        'filter_items_list' => __('Filter lectures list', 'aigq'),
    ];
    $lecture_args = [
        'label' => __('Lecture', 'aigq'),
        'description' => __('Post Type for Lectures', 'aigq'),
        'labels' => $lecture_labels,
        'supports' => ['title', 'editor', 'author', 'custom-fields'],
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => true,
    ];
    register_post_type('lecture', $lecture_args);

    // Register Quiz Post Type
    $quiz_labels = [
        'name' => _x('Quizzes', 'Post Type General Name', 'aigq'),
        'singular_name' => _x('Quiz', 'Post Type Singular Name', 'aigq'),
        'menu_name' => __('Quizzes', 'aigq'),
        'name_admin_bar' => __('Quiz', 'aigq'),
        'archives' => __('Quiz Archives', 'aigq'),
        'attributes' => __('Quiz Attributes', 'aigq'),
        'parent_item_colon' => __('Parent Quiz:', 'aigq'),
        'all_items' => __('All Quizzes', 'aigq'),
        'add_new_item' => __('Add New Quiz', 'aigq'),
        'add_new' => __('Add New', 'aigq'),
        'new_item' => __('New Quiz', 'aigq'),
        'edit_item' => __('Edit Quiz', 'aigq'),
        'update_item' => __('Update Quiz', 'aigq'),
        'view_item' => __('View Quiz', 'aigq'),
        'view_items' => __('View Quizzes', 'aigq'),
        'search_items' => __('Search Quiz', 'aigq'),
        'not_found' => __('Not found', 'aigq'),
        'not_found_in_trash' => __('Not found in Trash', 'aigq'),
        'featured_image' => __('Featured Image', 'aigq'),
        'set_featured_image' => __('Set featured image', 'aigq'),
        'remove_featured_image' => __('Remove featured image', 'aigq'),
        'use_featured_image' => __('Use as featured image', 'aigq'),
        'insert_into_item' => __('Insert into quiz', 'aigq'),
        'uploaded_to_this_item' => __('Uploaded to this quiz', 'aigq'),
        'items_list' => __('Quizzes list', 'aigq'),
        'items_list_navigation' => __('Quizzes list navigation', 'aigq'),
        'filter_items_list' => __('Filter quizzes list', 'aigq'),
    ];
    $quiz_args = [
        'label' => __('Quiz', 'aigq'),
        'description' => __('Post Type for Quizzes', 'aigq'),
        'labels' => $quiz_labels,
        'supports' => ['title', 'author', 'custom-fields'],
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'menu_position' => 6,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => true,
    ];
    register_post_type('quiz', $quiz_args);

    // Register Course Taxonomy
    $course_labels = [
        'name' => _x('Courses', 'Taxonomy General Name', 'aigq'),
        'singular_name' => _x('Course', 'Taxonomy Singular Name', 'aigq'),
        'menu_name' => __('Course', 'aigq'),
    ];
    $course_args = [
        'labels' => $course_labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
        'show_in_rest' => true,
    ];
    register_taxonomy('course', ['lecture', 'quiz'], $course_args);

    // Register Level Taxonomy
    $level_labels = [
        'name' => _x('Levels', 'Taxonomy General Name', 'aigq'),
        'singular_name' => _x('Level', 'Taxonomy Singular Name', 'aigq'),
        'menu_name' => __('Level', 'aigq'),
    ];
    $level_args = [
        'labels' => $level_labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
        'show_in_rest' => true,
    ];
    register_taxonomy('level', ['lecture', 'quiz'], $level_args);
}
add_action('init', 'aigq_register_post_types', 0);