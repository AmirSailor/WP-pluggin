<?php

/**
 * This file handles the creation of the lecture meta box for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add the meta box for the lecture post type.
 *
 * This function is hooked into the 'add_meta_boxes' action.
 */
function aigq_add_lecture_meta_box() {
    add_meta_box(
        'aigq_lecture_meta_box',
        __('Lecture Details', 'aigq'),
        'aigq_render_lecture_meta_box',
        'lecture',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'aigq_add_lecture_meta_box');

/**
 * Render the lecture meta box.
 */
function aigq_render_lecture_meta_box($post) {
    // Get the saved data
    $transcript = get_post_meta($post->ID, '_lecture_transcript', true);
    $quiz_data_json = get_post_meta($post->ID, '_lecture_quiz_data', true);
    $quiz_data = json_decode($quiz_data_json, true);

    // Security nonce
    wp_nonce_field('aigq_save_lecture_meta_box_data', 'aigq_lecture_meta_box_nonce');

    ?>
    <div class="wrap">
        <h2><?php _e('Transcript', 'aigq'); ?></h2>
        <textarea name="aigq_transcript" rows="10" class="widefat"><?php echo esc_textarea($transcript); ?></textarea>

        <h2><?php _e('Summary', 'aigq'); ?></h2>
        <textarea name="aigq_summary" rows="5" class="widefat"><?php echo esc_textarea($quiz_data['summary']); ?></textarea>

        <h2><?php _e('Quiz', 'aigq'); ?></h2>
        <div id="aigq-quiz-container">
            <?php if (isset($quiz_data['quiz'])) : ?>
                <?php foreach ($quiz_data['quiz'] as $index => $question) : ?>
                    <div class="aigq-question">
                        <h4><?php printf(__('Question %d', 'aigq'), $index + 1); ?></h4>
                        <p>
                            <label><?php _e('Question:', 'aigq'); ?></label>
                            <input type="text" name="aigq_quiz[<?php echo $index; ?>][question]" value="<?php echo esc_attr($question['question']); ?>" class="regular-text" style="width:100%;" />
                        </p>
                        <p>
                            <label><?php _e('Options:', 'aigq'); ?></label>
                        </p>
                        <ul>
                            <?php foreach ($question['options'] as $o_index => $option) : ?>
                                <li>
                                    <input type="text" name="aigq_quiz[<?php echo $index; ?>][options][<?php echo $o_index; ?>]" value="<?php echo esc_attr($option); ?>" class="regular-text" />
                                    <input type="radio" name="aigq_quiz[<?php echo $index; ?>][answer]" value="<?php echo esc_attr($option); ?>" <?php checked($question['answer'], $option); ?> />
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" id="aigq-add-question" class="button"><?php _e('Add Question', 'aigq'); ?></button>
    </div>
    <script>
        jQuery(document).ready(function($) {
            var questionIndex = <?php echo isset($quiz_data['quiz']) ? count($quiz_data['quiz']) : 0; ?>;
            $('#aigq-add-question').on('click', function() {
                var newQuestion = '<div class="aigq-question">' +
                    '<h4>Question ' + (questionIndex + 1) + '</h4>' +
                    '<p><label>Question:</label><input type="text" name="aigq_quiz[' + questionIndex + '][question]" style="width:100%;" /></p>' +
                    '<p><label>Options:</label></p>' +
                    '<ul>' +
                    '<li><input type="text" name="aigq_quiz[' + questionIndex + '][options][0]" /><input type="radio" name="aigq_quiz[' + questionIndex + '][answer]" value="" /></li>' +
                    '<li><input type="text" name="aigq_quiz[' + questionIndex + '][options][1]" /><input type="radio" name="aigq_quiz[' + questionIndex + '][answer]" value="" /></li>' +
                    '<li><input type="text" name="aigq_quiz[' + questionIndex + '][options][2]" /><input type="radio" name="aigq_quiz[' + questionIndex + '][answer]" value="" /></li>' +
                    '<li><input type="text" name="aigq_quiz[' + questionIndex + '][options][3]" /><input type="radio" name="aigq_quiz[' + questionIndex + '][answer]" value="" /></li>' +
                    '</ul>' +
                    '</div>';
                $('#aigq-quiz-container').append(newQuestion);
                questionIndex++;
            });
        });
    </script>
    <?php
}

/**
 * Save the meta box data.
 */
function aigq_save_lecture_meta_box_data($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['aigq_lecture_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['aigq_lecture_meta_box_nonce'], 'aigq_save_lecture_meta_box_data')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'lecture' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Sanitize and save the data.
    if (isset($_POST['aigq_transcript'])) {
        update_post_meta($post_id, '_lecture_transcript', sanitize_textarea_field($_POST['aigq_transcript']));
    }

    if (isset($_POST['aigq_summary']) || isset($_POST['aigq_quiz'])) {
        $quiz_data = [];
        if (isset($_POST['aigq_summary'])) {
            $quiz_data['summary'] = sanitize_textarea_field($_POST['aigq_summary']);
        }

        if (isset($_POST['aigq_quiz'])) {
            $quiz_data['quiz'] = array_map(function($question) {
                return [
                    'question' => sanitize_text_field($question['question']),
                    'options' => array_map('sanitize_text_field', $question['options']),
                    'answer' => sanitize_text_field($question['answer']),
                ];
            }, $_POST['aigq_quiz']);
        }

        update_post_meta($post_id, '_lecture_quiz_data', json_encode($quiz_data));
    }
}
add_action('save_post', 'aigq_save_lecture_meta_box_data');
