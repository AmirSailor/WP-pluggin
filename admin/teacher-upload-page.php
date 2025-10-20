<?php
/**
 * This file handles the creation of the teacher upload page for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add the admin menu for the teacher dashboard.
 *
 * This function is hooked into the 'admin_menu' action.
 */
function aigq_add_teacher_menu() {
    add_menu_page(
        __('Teacher Dashboard', 'aigq'),
        __('Teacher Dashboard', 'aigq'),
        'edit_posts', // Capability for teachers
        'aigq-teacher-dashboard',
        'aigq_render_teacher_dashboard_page',
        'dashicons-welcome-learn-more',
        30
    );
}
add_action('admin_menu', 'aigq_add_teacher_menu');

/**
 * Enqueue scripts for the teacher upload page.
 */
function aigq_enqueue_teacher_upload_scripts($hook) {
    if ('toplevel_page_aigq-teacher-dashboard' !== $hook) {
        return;
    }

    wp_enqueue_script(
        'aigq-teacher-upload',
        plugin_dir_url(__FILE__) . '../assets/js/teacher-upload.js',
        ['jquery'],
        '1.0.0',
        true
    );

    wp_localize_script(
        'aigq-teacher-upload',
        'aigq_ajax',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aigq-upload-audio'),
        ]
    );
}
add_action('admin_enqueue_scripts', 'aigq_enqueue_teacher_upload_scripts');

/**
 * Handle the AJAX audio upload.
 */
function aigq_upload_audio() {
    check_ajax_referer('aigq-upload-audio', 'nonce');

    if (isset($_FILES['lecture_audio'])) {
        // Handle the form submission
        $lecture_title = sanitize_text_field($_POST['lecture_title']);
        $lecture_course = intval($_POST['lecture_course']);
        $lecture_level = intval($_POST['lecture_level']);

        $file_type = wp_check_filetype($_FILES['lecture_audio']['name']);
        $allowed_types = ['audio/mpeg', 'audio/wav', 'audio/x-m4a'];
        if (!in_array($file_type['type'], $allowed_types)) {
            wp_send_json_error(['message' => __('Only MP3, WAV, and M4A files are allowed.', 'aigq')]);
        }

        // Upload the audio file
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attachment_id = media_handle_upload('lecture_audio', 0);

        if (is_wp_error($attachment_id)) {
            // Handle the error
            wp_send_json_error(['message' => $attachment_id->get_error_message()]);
        } else {
            // Create the lecture post
            $lecture_post = [
                'post_title' => $lecture_title,
                'post_status' => 'publish',
                'post_type' => 'lecture',
            ];
            $post_id = wp_insert_post($lecture_post);

            // Create the quiz post
            $quiz_post = [
                'post_title' => $lecture_title,
                'post_status' => 'publish',
                'post_type' => 'quiz',
            ];
            $quiz_id = wp_insert_post($quiz_post);

            // Link the quiz to the lecture
            add_post_meta($post_id, '_lecture_quiz_id', $quiz_id);
            add_post_meta($quiz_id, '_quiz_lecture_id', $post_id);

            // Set the taxonomies
            wp_set_post_terms($post_id, $lecture_course, 'course');
            wp_set_post_terms($post_id, $lecture_level, 'level');

            // Add the attachment to the post
            add_post_meta($post_id, '_lecture_audio_attachment_id', $attachment_id);

            // Schedule a cron event to process the audio file
            wp_schedule_single_event(time(), 'aigq_process_audio', [$post_id, $attachment_id]);

            // Send a success message
            wp_send_json_success(['message' => __('Your file is being processed. You will be redirected to the edit page shortly.', 'aigq'), 'redirect_url' => admin_url('post.php?post=' . $post_id . '&action=edit')]);
        }
    }
}
add_action('wp_ajax_aigq_upload_audio', 'aigq_upload_audio');

/**
 * Process the audio file in the background.
 */
function aigq_process_audio($post_id, $attachment_id) {
    // Get the services
    $stt_service = aigq_get_stt_service();
    if (is_null($stt_service)) {
        // Log an error or handle it gracefully
        return;
    }

    $llm_service = aigq_get_llm_service();
    if (is_null($llm_service)) {
        // Log an error
        return;
    }

    // Get the audio file path
    $audio_file_path = get_attached_file($attachment_id);

    // Get the transcript
    $transcript = $stt_service->transcribe($audio_file_path);

    // Get the quiz
    $quiz_data = $llm_service->generateQuiz($transcript);

    // Save the transcript and quiz data
    update_post_meta($post_id, '_lecture_transcript', $transcript);
    update_post_meta($post_id, '_lecture_quiz_data', $quiz_data);
}
add_action('aigq_process_audio', 'aigq_process_audio', 10, 2);

/**
 * Render the teacher dashboard page.
 */
function aigq_render_teacher_dashboard_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <h2><?php _e('Create New Lecture', 'aigq'); ?></h2>
        <form id="aigq-upload-form" method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Lecture Title', 'aigq'); ?></th>
                    <td><input type="text" name="lecture_title" size="40" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Course', 'aigq'); ?></th>
                    <td>
                        <?php
                        wp_dropdown_categories([
                            'taxonomy' => 'course',
                            'name' => 'lecture_course',
                            'show_option_none' => __('Select a course', 'aigq'),
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
                            'name' => 'lecture_level',
                            'show_option_none' => __('Select a level', 'aigq'),
                        ]);
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Audio File', 'aigq'); ?></th>
                    <td><input type="file" name="lecture_audio" /></td>
                </tr>
            </table>
            <?php submit_button(__('Upload and Generate Quiz', 'aigq')); ?>
        </form>
        <div id="aigq-upload-status" style="display:none;"></div>
    </div>
    <?php
}