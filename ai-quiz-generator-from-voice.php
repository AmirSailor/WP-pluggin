<?php
/**
 * Plugin Name: AI Quiz Generator from Voice
 * Description: A WordPress plugin to generate quizzes from audio lectures using AI.
 * Version: 1.0.1
 * Author: AmirSailor
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

define( 'AIGQ_VERSION', '1.1.0' );
define( 'AIGQ_PLUGIN', __FILE__ );
define( 'AIGQ_PLUGIN_BASENAME', plugin_basename( AIGQ_PLUGIN ) );
define( 'AIGQ_PLUGIN_NAME', trim( dirname( AIGQ_PLUGIN_BASENAME ), '/' ) );
define( 'AIGQ_PLUGIN_DIR', untrailingslashit( dirname( AIGQ_PLUGIN ) ) );
define( 'AIGQ_PLUGIN_FILE', __FILE__ );

require_once AIGQ_PLUGIN_DIR . '/includes/load.php';

// Initialize the plugin.
function aigq() {
    return AI_Quiz_Generator::instance();
}
aigq();
