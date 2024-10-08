<?php
/*
Plugin Name: Admin To-Do Widget
Description: A widget that displays a to-do list in the admin dashboard.
Version: 1.2
Author: Toni Quinonero
*/

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-atw-widget-display.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-atw-task-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-atw-ajax-handler.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-atw-settings.php';

// Enqueue scripts and styles
function atw_enqueue_scripts($hook) {
    if ('index.php' !== $hook) {
        return;
    }
    wp_enqueue_style('atw-styles', plugins_url('includes/css/atw-styles.css', __FILE__));
    wp_enqueue_script('atw-ajax', plugins_url('includes/js/atw-ajax.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('atw-ajax', 'atw_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('atw_ajax_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'atw_enqueue_scripts');

// Add dashboard widget
function atw_add_dashboard_widget() {
    wp_add_dashboard_widget('atw_todo_widget', 'To-Do List', 'atw_display_dashboard_widget');
}
add_action('wp_dashboard_setup', 'atw_add_dashboard_widget');

// Initialize classes
$atw_ajax_handler = new ATW_Ajax_Handler();
if (is_admin()) {
    $atw_settings = new ATW_Settings();
}

// Display dashboard widget
function atw_display_dashboard_widget() {
    $widget_display = new ATW_Widget_Display();
    $widget_display->display_widget();
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'atw_delete_stored_tasks');

function atw_delete_stored_tasks() {
    // Get the option name for tasks
    $option_name = 'atw_tasks'; // Default option name
    $options = get_option('atw_options', array());

    // Check if user-specific lists are enabled
    if (!empty($options['user_specific_lists'])) {
        // Delete user-specific tasks for all users
        $users = get_users();
        foreach ($users as $user) {
            delete_option('atw_tasks_user_' . $user->ID);
        }
    } else {
        // Delete the shared tasks option
        delete_option($option_name);
    }
}
