<?php
/**
 * Admin Menu Setup
 *
 * This file contains functions for setting up the admin menu and submenu pages
 * for the "RWC Team Members" plugin. These pages allow administrators to
 * manage team member data, settings, and other plugin-related functionalities
 * within the WordPress admin dashboard.
 *
 * @uses  add_menu_page()	 - Adding a top-level menu page for our team plugin.
 * These functions takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * @uses  add_submenu_page() - Adding a submenu page for our team plugin.
 * The functions which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * Including other pages to make the plugin workable.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// ob_start(); // Start output buffering
if(!class_exists('RWCTM_Admin_Menu')) {
    class RWCTM_Admin_Menu {

        public function __construct() {
            add_action('admin_menu', array($this, 'rwctm_register_menu'));
        }

        public function rwctm_register_menu() {
            add_menu_page(
                'RWC Team Members',
                esc_html__('RWC Teams', 'rwc-team-members'),
                'manage_options',
                'rwctm-lists',
                array($this, 'rwctm_plugin_menu'),
                'dashicons-grid-view'
            );

            add_submenu_page(
                'rwctm-lists',
                esc_html__('RWCTM Lists', 'rwc-team-members'),
                esc_html__('All Teams', 'rwc-team-members'),
                'manage_options',
                'rwctm-lists',
                array($this, 'rwctm_plugin_menu')
            );

            add_submenu_page(
                'rwctm-lists',
                'RWCTM Template',
                esc_html__('Team Templates', 'rwc-team-members'),
                'manage_options',
                'rwctm-template',
                array($this, 'rwctm_template_page')
            );

            add_submenu_page(
                'rwctm-lists',
                'RWCTM Global',
                esc_html__('Settings', 'rwc-team-members'),
                'manage_options',
                'rwctm-settings',
                array($this, 'rwctm_global_page')
            );

            add_submenu_page(
                'rwctm-lists',
                'RWCTM Help',
                esc_html__('Help', 'rwc-team-members'),
                'manage_options',
                'rwctm-help',
                array($this, 'rwctm_help_page')
            );
        }

        /* Including Process File */
        function rwctm_plugin_menu() {
            if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'rwc-team-members' ) );
            }
            require_once ( RWCTM_PLUGIN_PATH . 'inc/process-team.php' );
        }

        /* Including Template File */
        function rwctm_template_page() {
            if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'rwc-team-members' ) );
            }
            require_once ( RWCTM_PLUGIN_PATH . 'templates/process-template.php' );
        }

        /* Including RWC Team Member Global Settings Page */
        function rwctm_global_page() {
            if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'rwc-team-members' ) );
            }
            require_once ( RWCTM_PLUGIN_PATH . 'options/global-settings.php' );
        }

        /* Including Help File */
        function rwctm_help_page() {
            if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'rwc-team-members' ) );
            }
            require_once ( RWCTM_PLUGIN_PATH . 'inc/rwctm-guide.php' );
        }
    }
}

new RWCTM_Admin_Menu();

/* Including other files to make the plugin workable. */
include ( RWCTM_PLUGIN_PATH . 'lib/team-functions.php' );
include ( RWCTM_PLUGIN_PATH . 'templates/template-options.php' );
include ( RWCTM_PLUGIN_PATH . 'options/team-settings.php' );
include ( RWCTM_PLUGIN_PATH . 'options/activity-settings.php' );
include ( RWCTM_PLUGIN_PATH . 'inc/display-team.php' );
include ( RWCTM_PLUGIN_PATH . 'inc/team-sidebar.php' );