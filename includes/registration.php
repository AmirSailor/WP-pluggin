<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Add role selection field to registration form
function aigq_add_role_selection_to_registration_form() {
    ?>
    <p>
        <label for="aigq_role"><?php _e('Register as', 'aigq'); ?><br />
            <select name="aigq_role" id="aigq_role">
                <option value="student"><?php _e('Student', 'aigq'); ?></option>
                <option value="teacher"><?php _e('Teacher', 'aigq'); ?></option>
            </select>
        </label>
    </p>
    <?php
}
add_action('register_form', 'aigq_add_role_selection_to_registration_form');

// Save the selected role
function aigq_save_role_on_registration($user_id) {
    if (isset($_POST['aigq_role'])) {
        $role = sanitize_text_field($_POST['aigq_role']);
        $user = new WP_User($user_id);
        $user->set_role($role);
    }
}
add_action('user_register', 'aigq_save_role_on_registration');

// Redirect user after registration
function aigq_registration_redirect($redirect_to) {
    if (isset($_POST['aigq_role'])) {
        $role = sanitize_text_field($_POST['aigq_role']);
        if ($role == 'teacher') {
            return admin_url('admin.php?page=aigq-teacher-dashboard');
        } else {
            return home_url('/student-portal'); // Assuming you have a student portal page with this slug
        }
    }
    return $redirect_to;
}
add_filter('registration_redirect', 'aigq_registration_redirect');
