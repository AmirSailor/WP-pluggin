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
        $lecture_class = intval($_POST['lecture_class']);

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

            // Associate the lecture with the class
            add_post_meta($post_id, '_aigq_lecture_class', $lecture_class);

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

// AJAX handler to get students for a class
function aigq_get_students_for_class() {
    if (isset($_POST['class_id'])) {
        $class_id = intval($_POST['class_id']);
        $class_students = get_post_meta($class_id, '_aigq_class_students', true);
        if (is_array($class_students)) {
            echo '<ul>';
            foreach ($class_students as $student_id) {
                $student = get_user_by('id', $student_id);
                if ($student) {
                    echo '<li>' . esc_html($student->display_name) . '</li>';
                }
            }
            echo '</ul>';
        } else {
            echo '<p>' . __('No students found in this class.', 'aigq') . '</p>';
        }
    }
    wp_die();
}
add_action('wp_ajax_aigq_get_students_for_class', 'aigq_get_students_for_class');

/**
 * Render the teacher dashboard page.
 */
function aigq_render_teacher_dashboard_page() {
    $user_id = get_current_user_id();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'create_lecture';
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=aigq-teacher-dashboard&tab=create_lecture" class="nav-tab <?php echo $active_tab == 'create_lecture' ? 'nav-tab-active' : ''; ?>"><?php _e('Create New Lecture', 'aigq'); ?></a>
            <a href="?page=aigq-teacher-dashboard&tab=your_lectures" class="nav-tab <?php echo $active_tab == 'your_lectures' ? 'nav-tab-active' : ''; ?>"><?php _e('Your Lectures', 'aigq'); ?></a>
            <a href="?page=aigq-teacher-dashboard&tab=your_quizzes" class="nav-tab <?php echo $active_tab == 'your_quizzes' ? 'nav-tab-active' : ''; ?>"><?php _e('Your Quizzes', 'aigq'); ?></a>
            <a href="?page=aigq-teacher-dashboard&tab=your_classes" class="nav-tab <?php echo $active_tab == 'your_classes' ? 'nav-tab-active' : ''; ?>"><?php _e('Your Classes', 'aigq'); ?></a>
            <a href="?page=aigq-teacher-dashboard&tab=your_students" class="nav-tab <?php echo $active_tab == 'your_students' ? 'nav-tab-active' : ''; ?>"><?php _e('Your Students', 'aigq'); ?></a>
        </h2>

        <?php if ($active_tab == 'create_lecture') : ?>
            <form id="aigq-upload-form" method="post" enctype="multipart/form-data">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Lecture Title', 'aigq'); ?></th>
                        <td><input type="text" name="lecture_title" size="40" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Class', 'aigq'); ?></th>
                        <td>
                            <select name="lecture_class">
                                <option value=""><?php _e('Select a class', 'aigq'); ?></option>
                                <?php
                                                                        $classes = get_posts([
                                                                            'post_type' => 'class',
                                                                            'posts_per_page' => -1,
                                                                            'meta_query' => [
                                                                                [
                                                                                    'key' => '_aigq_class_teachers',
                                                                                    'value' => $user_id,                                            'compare' => 'LIKE'
                                        ]
                                    ]
                                ]);
                                if ($classes) {
                                    foreach ($classes as $class) {
                                        echo '<option value="' . $class->ID . '">' . esc_html($class->post_title) . '</option>';
                                    }
                                }
                                ?>
                            </select>
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

        <?php elseif ($active_tab == 'your_lectures') : ?>
            <h2><?php _e('Your Lectures', 'aigq'); ?> <a href="<?php echo admin_url('post-new.php?post_type=lecture'); ?>" class="page-title-action"><?php _e('Add New Lecture', 'aigq'); ?></a></h2>
            <?php
            $lectures = get_posts(['post_type' => 'lecture', 'author' => $user_id, 'posts_per_page' => -1]);
            if ($lectures) {
                echo '<ul>';
                foreach ($lectures as $lecture) {
                    echo '<li><a href="' . get_edit_post_link($lecture->ID) . '">' . esc_html($lecture->post_title) . '</a></li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . __('You have not created any lectures yet.', 'aigq') . '</p>';
            }
            ?>

        <?php elseif ($active_tab == 'your_quizzes') : ?>
            <h2><?php _e('Your Quizzes', 'aigq'); ?> <a href="<?php echo admin_url('post-new.php?post_type=quiz'); ?>" class="page-title-action"><?php _e('Add New Quiz', 'aigq'); ?></a></h2>
            <?php
            $quizzes = get_posts(['post_type' => 'quiz', 'author' => $user_id, 'posts_per_page' => -1]);
            if ($quizzes) {
                echo '<ul>';
                foreach ($quizzes as $quiz) {
                    echo '<li><a href="' . get_edit_post_link($quiz->ID) . '">' . esc_html($quiz->post_title) . '</a></li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . __('You have not created any quizzes yet.', 'aigq') . '</p>';
            }
            ?>

        <?php elseif ($active_tab == 'your_classes') : ?>
            <h2><?php _e('Your Classes', 'aigq'); ?></h2>
            <?php
            $teacher_classes = get_posts([
                'post_type' => 'class',
                'posts_per_page' => -1,
                'meta_query' => [
                    [
                        'value' => $user_id,
                        'compare' => 'LIKE'
                    ]
                ]
            ]);
            if ($teacher_classes) {
                echo '<ul>';
                foreach ($teacher_classes as $class) {
                    echo '<li>' . esc_html($class->post_title) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . __('You are not assigned to any classes yet.', 'aigq') . '</p>';
            }
            ?>

        <?php elseif ($active_tab == 'your_students') : ?>
            <h2><?php _e('Your Students', 'aigq'); ?></h2>
            <div id="teacher-students-container">
                <div id="teacher-classes-col">
                    <h3><?php _e('Your Classes', 'aigq'); ?></h3>
                    <ul>
                        <?php
                        $teacher_classes = get_posts([
                            'post_type' => 'class',
                            'posts_per_page' => -1,
                            'meta_query' => [
                                [
                                    'key' => '_aigq_class_teachers',
                                    'value' => $user_id,
                                    'compare' => 'LIKE'
                                ]
                            ]
                        ]);
                        if ($teacher_classes) {
                            foreach ($teacher_classes as $class) {
                                echo '<li><a href="#" data-class-id="' . $class->ID . '">' . esc_html($class->post_title) . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div id="teacher-students-col">
                    <h3><?php _e('Students in Class', 'aigq'); ?></h3>
                    <div id="students-list"></div>
                </div>
            </div>
            <script>
            jQuery(document).ready(function($) {
                $('#teacher-classes-col a').on('click', function(e) {
                    e.preventDefault();
                    var classId = $(this).data('class-id');
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'aigq_get_students_for_class',
                            class_id: classId
                        },
                        success: function(response) {
                            $('#students-list').html(response);
                        }
                    });
                });
            });
            </script>
            <style>
                #teacher-students-container { display: flex; }
                #teacher-classes-col { width: 30%; padding-right: 20px; }
                #teacher-students-col { width: 70%; }
            </style>
        <?php endif; ?>
    </div>
    <?php
}