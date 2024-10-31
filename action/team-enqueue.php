<?php
/**
 * Plugin Enqueue Functions
 *
 * This file contains functions responsible for enqueuing styles and scripts
 * for both the admin and front-end sections of the "RWC Team Members" plugin.
 * Proper enqueuing ensures that the required assets are loaded only where necessary,
 * enhancing performance and maintaining a seamless user experience.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Enqueue Admin Styles and Scripts
 *
 * Enqueues styles and scripts specific to the plugin's admin section.
 * These assets enhance the styling and functionality of the plugin's backend pages.
 */
if (!function_exists('rwctm_team_admin_enqueue')) {
	function rwctm_team_admin_enqueue() {
		if (wp_script_is('code-editor', 'registered') && function_exists('wp_enqueue_code_editor')) {
			wp_enqueue_code_editor(array('type' => 'text/html'));
			$rwctm_cm_settings['codeEditor_html'] = true;
		
			wp_enqueue_code_editor(array('type' => 'text/css'));
			$rwctm_cm_settings['codeEditor_css'] = true;
		}
		wp_register_script('selectize', plugins_url( '../assets/js/selectize.min.js', __FILE__ ), array('jquery'), '0.15.2', true);
		wp_register_script('rwctmjs', plugins_url( '../assets/js/rwctm-admin.min.js', __FILE__ ), array('jquery'), '0.5', true);
		wp_enqueue_script('selectize');
		wp_enqueue_script('rwctmjs');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_media();
		$nonce = wp_create_nonce('rwctm_ajax_action_nonce');
		wp_localize_script('rwctmjs', 'rwctmajax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'     => $nonce,
		) );
		wp_localize_script('jquery', 'rwctm_cm_settings', $rwctm_cm_settings);
		wp_enqueue_style('rwctmscone', plugins_url( '../assets/css/rwctm-style.min.css', __FILE__ ), '', '0.5');
		wp_enqueue_style('rwctmadmin', plugins_url( '../assets/css/rwctm-admin.min.css', __FILE__ ), '', '0.5');
		wp_enqueue_style('selectize', plugins_url( '../assets/css/selectize.min.css', __FILE__ ), '', '0.15.2');
		wp_enqueue_style('jquery-ui-rwctm', plugins_url( '../assets/css/jquery-ui-rwctm.css', __FILE__ ), '', '1.10.4');
		// Call the PHP file with CSS code
		require_once ( RWCTM_PLUGIN_PATH . 'assets/css/rwctm-custom.php' );
		// Read the contents of the PHP file
		$css_code = rwctm_set_custom_css('preview');
		// Set the path to the output file
		$output_path = RWCTM_PLUGIN_PATH . '/assets/css/rwctm-custom.css';
		// Write the CSS code to the output file
		file_put_contents($output_path, $css_code);
		// enqueue the CSS file
		wp_enqueue_style('rwctmcustom', plugins_url( '../assets/css/rwctm-custom.css', __FILE__ ), array(), '0.5');
	}
}
add_action('admin_enqueue_scripts', 'rwctm_team_admin_enqueue');

/**
 * Enqueue Front-End Styles and Scripts
 *
 * Enqueues styles and scripts that are required on the front-end of the website
 * where the plugin is active. These assets enhance the styling and interactivity
 * of the plugin's features when displayed on the website's pages.
 */
if (!function_exists('rwctm_team_frontend_enqueue')) {
	function rwctm_team_frontend_enqueue() {
		$rwctm_options = get_option('rwctm_global_options');
		/* Google Font */
		if(isset($rwctm_options['gdfont']) && $rwctm_options['gdfont'] == 'yes') {
			wp_enqueue_style('fonttmdfault', '//fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Roboto:wght@400;700&display=swap', array(), null);
		} else {
			if(isset($rwctm_options['gcfont']) && $rwctm_options['gcfont'] != '') {
				wp_enqueue_style('fonttmcustom', esc_url($rwctm_options['gcfont']), array(), null);
			}
		}
		wp_register_script('rwctm-team', plugins_url( '../assets/js/rwctm-team.js', __FILE__ ), array('jquery'), '0.5', true);
		wp_register_script('rwctm-slider', plugins_url( '../assets/js/rwctm-slider.js', __FILE__ ), array('jquery'), '1.8.1', true);
		wp_enqueue_script('rwctm-team');
		wp_enqueue_script('rwctm-slider');
		wp_enqueue_style('rwctmslider', plugins_url( '../assets/css/rwctm-slider.css', __FILE__ ), '', '1.8.1');
		wp_enqueue_style('rwctmstyle', plugins_url( '../assets/css/rwctm-style.css', __FILE__ ), '', '0.5');
		if(isset($rwctm_options['dsicon']) && $rwctm_options['dsicon'] == 'yes') {
			wp_enqueue_style( 'dashicons' );
		}
		// Get the current page ID
		$page_id = get_the_ID();
		// Call the PHP file with CSS code
		require_once ( RWCTM_PLUGIN_PATH . 'assets/css/rwctm-custom.php' );
		// passing true to get only the ids of the added pages
		$st_pageid = rwctm_set_custom_css('', true);
		// Iterate through the outer array
		foreach ($st_pageid as $subarray) {
			// Check if the current page ID exists in the inner array
			if (in_array($page_id, $subarray)) {
				// Generate the file name for the team
				$output_filename = 'rwctm-custom-' . $page_id . '.css';
				// Set the path to the output file
				$output_path = plugin_dir_path( RWCTM_AUF ) . '/assets/css/' . $output_filename;
				// Clear the file
				file_put_contents($output_path, '');
				// Read the contents of the PHP file
				$css_code = rwctm_set_custom_css($page_id);
				// Write the CSS code to the output file
				file_put_contents($output_path, $css_code);
				// enqueue the CSS file
				wp_enqueue_style('rwctmcustom-'.$page_id, plugins_url( 'assets/css/rwctm-custom-'.$page_id.'.css', RWCTM_AUF ), array(), '0.5');
			}
		}
	}
}
add_action('wp_enqueue_scripts', 'rwctm_team_frontend_enqueue');