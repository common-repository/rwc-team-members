<?php
/**
 * Plugin Name:       RWC Team Members
 * Plugin URI:        http://wordpress.org/plugins/rwc-team-members/
 * Description:       Showcase your team's talent and expertise with ease. Our team member plugin offers grid, slider, popup and filter layouts using a shortcode.
 * Version:           0.5
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Realwebcare
 * Author URI:        https://www.realwebcare.com/
 * Text Domain:       rwc-team-members
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * RWC Team Members Plugin
 *
 * Main plugin file that initializes and manages the "RWC Team Members" plugin.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */

// Define the plugin path
define('RWCTM_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('RWCTM_AUF', __FILE__);

/* Activation */
require_once RWCTM_PLUGIN_PATH . 'action/activate-plugin.php';
/* On Delete */
require_once RWCTM_PLUGIN_PATH . 'action/uninstall-plugin.php';
/* Process Team */
require_once RWCTM_PLUGIN_PATH . 'inc/init-team.php';