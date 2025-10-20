<?php

/**
 * This file handles the creation of custom database tables for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Create custom database tables on plugin activation.
 *
 * This function is hooked into the plugin activation hook.
 */
function aigq_create_custom_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name_attempts = $wpdb->prefix . 'quiz_attempts';
    $sql_attempts = "CREATE TABLE $table_name_attempts (
        attempt_id bigint(20) NOT NULL AUTO_INCREMENT,
        quiz_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        score int(11) NOT NULL,
        date_taken datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY  (attempt_id)
    ) $charset_collate;";

    $table_name_answers = $wpdb->prefix . 'quiz_answers';
    $sql_answers = "CREATE TABLE $table_name_answers (
        answer_id bigint(20) NOT NULL AUTO_INCREMENT,
        attempt_id bigint(20) NOT NULL,
        question_id bigint(20) NOT NULL,
        selected_answer text NOT NULL,
        is_correct boolean NOT NULL,
        PRIMARY KEY  (answer_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_attempts);
    dbDelta($sql_answers);
}
