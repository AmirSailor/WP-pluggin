<?php

/**
 * This file handles the creation of the admin settings page for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add the admin menu for the plugin.
 *
 * This function is hooked into the 'admin_menu' action.
 */
function aigq_add_admin_menu() {
    add_menu_page(
        __('AI Quiz Generator', 'aigq'),
        __('AI Quiz Generator', 'aigq'),
        'manage_options',
        'aigq-settings',
        'aigq_render_settings_page',
        'dashicons-admin-generic',
        20
    );
}
add_action('admin_menu', 'aigq_add_admin_menu');

/**
 * Render the settings page.
 */
function aigq_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <?php
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
        ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=aigq-settings&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'aigq'); ?></a>
            <a href="?page=aigq-settings&tab=students" class="nav-tab <?php echo $active_tab == 'students' ? 'nav-tab-active' : ''; ?>"><?php _e('Students', 'aigq'); ?></a>
            <a href="?page=aigq-settings&tab=teachers" class="nav-tab <?php echo $active_tab == 'teachers' ? 'nav-tab-active' : ''; ?>"><?php _e('Teachers', 'aigq'); ?></a>
            <a href="?page=aigq-settings&tab=quizzes" class="nav-tab <?php echo $active_tab == 'quizzes' ? 'nav-tab-active' : ''; ?>"><?php _e('Quizzes', 'aigq'); ?></a>
            <a href="?page=aigq-settings&tab=courses" class="nav-tab <?php echo $active_tab == 'courses' ? 'nav-tab-active' : ''; ?>"><?php _e('Courses', 'aigq'); ?></a>
            <a href="?page=aigq-settings&tab=levels" class="nav-tab <?php echo $active_tab == 'levels' ? 'nav-tab-active' : ''; ?>"><?php _e('Levels', 'aigq'); ?></a>

            <a href="?page=aigq-settings&tab=quiz-results" class="nav-tab <?php echo $active_tab == 'quiz-results' ? 'nav-tab-active' : ''; ?>"><?php _e('Quiz Results', 'aigq'); ?></a>
        </h2>
        <?php
        if ($active_tab == 'settings') {
            ?>
            <form action="options.php" method="post">
                <?php
                settings_fields('aigq_settings');
                do_settings_sections('aigq-settings');
                submit_button();
                ?>
            </form>
            <?php
        } elseif ($active_tab == 'students') {
            aigq_render_students_table();
        } elseif ($active_tab == 'teachers') {
            aigq_render_teachers_table();
        } elseif ($active_tab == 'quizzes') {
            aigq_render_quizzes_table();
        } elseif ($active_tab == 'courses') {
            aigq_render_taxonomy_table('course');
        } elseif ($active_tab == 'levels') {
            aigq_render_taxonomy_table('level');
        } else {
            aigq_render_quiz_results_page();
        }
        ?>
    </div>
    <?php
}

/**
 * Register the settings.
 */
function aigq_register_settings() {
    register_setting('aigq_settings', 'aigq_stt_service');
    register_setting('aigq_settings', 'aigq_stt_api_key');
    register_setting('aigq_settings', 'aigq_llm_service');
    register_setting('aigq_settings', 'aigq_llm_api_key');

    add_settings_section(
        'aigq_api_settings_section',
        __('API Settings', 'aigq'),
        'aigq_api_settings_section_callback',
        'aigq-settings'
    );

    add_settings_field(
        'aigq_stt_service_field',
        __('Speech-to-Text Service', 'aigq'),
        'aigq_stt_service_field_render',
        'aigq-settings',
        'aigq_api_settings_section'
    );

    add_settings_field(
        'aigq_stt_api_key_field',
        __('STT API Key', 'aigq'),
        'aigq_stt_api_key_field_render',
        'aigq-settings',
        'aigq_api_settings_section'
    );

    add_settings_field(
        'aigq_llm_service_field',
        __('LLM Service', 'aigq'),
        'aigq_llm_service_field_render',
        'aigq-settings',
        'aigq_api_settings_section'
    );

    add_settings_field(
        'aigq_llm_api_key_field',
        __('LLM API Key', 'aigq'),
        'aigq_llm_api_key_field_render',
        'aigq-settings',
        'aigq_api_settings_section'
    );
}
add_action('admin_init', 'aigq_register_settings');

/**
 * Render the API settings section.
 */
function aigq_api_settings_section_callback() {
    echo __('Configure the API settings for the AI Quiz Generator plugin.', 'aigq');
}

/**
 * Render the STT service field.
 */
function aigq_stt_service_field_render() {
    $option = get_option('aigq_stt_service');
    ?>
    <select name="aigq_stt_service">
        <option value="google" <?php selected($option, 'google'); ?>><?php _e('Google Cloud Speech-to-Text', 'aigq'); ?></option>
        <option value="openai" <?php selected($option, 'openai'); ?>><?php _e('OpenAI Whisper', 'aigq'); ?></option>
    </select>
    <?php
}

/**
 * Render the STT API key field.
 */
function aigq_stt_api_key_field_render() {
    $option = get_option('aigq_stt_api_key');
    ?>
    <input type="password" name="aigq_stt_api_key" value="<?php echo esc_attr($option); ?>" />
    <?php
}

/**
 * Render the LLM service field.
 */
function aigq_llm_service_field_render() {
    $option = get_option('aigq_llm_service');
    ?>
    <select name="aigq_llm_service">
        <option value="google" <?php selected($option, 'google'); ?>><?php _e('Google Gemini', 'aigq'); ?></option>
        <option value="openai" <?php selected($option, 'openai'); ?>><?php _e('OpenAI (GPT-4/GPT-3.5)', 'aigq'); ?></option>
    </select>
    <?php
}

/**
 * Render the LLM API key field.
 */
function aigq_llm_api_key_field_render() {
    $option = get_option('aigq_llm_api_key');
    ?>
    <input type="password" name="aigq_llm_api_key" value="<?php echo esc_attr($option); ?>" />
    <?php
}
