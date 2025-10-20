<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class AI_Quiz_Generator {

    /**
     * The single instance of the class.
     *
     * @var AI_Quiz_Generator
     */
    protected static $_instance = null;

    /**
     * Main AI_Quiz_Generator Instance.
     *
     * Ensures only one instance of AI_Quiz_Generator is loaded or can be loaded.
     *
     * @static
     * @return AI_Quiz_Generator - Main instance.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * AI_Quiz_Generator Constructor.
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {
        register_activation_hook(AIGQ_PLUGIN_FILE, [__CLASS__, 'activate']);
        register_deactivation_hook(AIGQ_PLUGIN_FILE, [__CLASS__, 'deactivate']);
    }

    /**
     * Plugin activation.
     */
    public static function activate() {
        aigq_create_custom_tables();
        aigq_add_custom_roles();
    }

    /**
     * Plugin deactivation.
     */
    public static function deactivate() {
        aigq_remove_custom_roles();
    }
}
