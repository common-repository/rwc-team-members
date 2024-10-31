<?php
/**
 * This file is responsible for handling various actions related to the
 * initialization of the RWC Team Members plugin. It performs the following tasks:
 *
 * 1. Adding action links to the plugin on the WordPress plugins page.
 * 2. Integration of a plugin update checker to check for updates.
 * 3. Setting up the admin menu if the current user is an administrator.
 * 4. Initialization of various plugin functions via 'init-functions.php'.
 * 5. Enqueuing CSS and JS files using 'team-enqueue.php'.
 * 6. Aqua Resizer: Resizes WordPress images on the fly
 * 7. Adding a shortcode for the team using 'team-shortcode.php'.
 *
 * @param string $plugin The execution key for the RWC Team Members plugin.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Add plugin action links
 * Hook into the plugin action links to add custom links
 * This filter allows us to modify the action links displayed on the Plugins page for our plugin
 * We're adding our custom function 'rwctm_plugin_actions' to be called when this filter is triggered
**/
add_filter( 'plugin_action_links_' . plugin_basename(RWCTM_AUF), 'rwctm_plugin_actions' );

/* Admin Menu */
if(is_admin()) {
	require_once RWCTM_PLUGIN_PATH . 'inc/admin-menu.php';
}

/* Initial Functions */
require_once RWCTM_PLUGIN_PATH . 'action/init-functions.php';
/* Enqueue CSS & JS */
require_once RWCTM_PLUGIN_PATH . 'action/team-enqueue.php';
/* Aqua Resizer */
require_once RWCTM_PLUGIN_PATH . 'class/rwctm_aq_resizer.php';
/* Shortcode */
require_once RWCTM_PLUGIN_PATH . 'action/team-shortcode.php';