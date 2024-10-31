<?php
/**
 * Plugin Uninstall Functions
 *
 * This file contains functions to ensure proper removal of plugin options and data
 * from the database when the "RWC Team Members" plugin is deleted. These functions
 * are crucial to maintain a clean uninstallation process and ensure no residual data remains
 * after the plugin is removed.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Remove Plugin Options on Uninstall
 *
 * Handles the removal of plugin options and data from the database when the plugin
 * is completely uninstalled. This ensures that no unnecessary data remains in the
 * WordPress database after the plugin is deleted.
 */
if (!function_exists('rwctm_plugin_deactivate_options')) {
	function rwctm_plugin_deactivate_options() {
		$rwctm_globals = get_option('rwctm_global_options', 'default_value');
		if ( $rwctm_globals !== 'default_value' ) {
			$clear_db = isset($rwctm_globals['clrdb']) ? $rwctm_globals['clrdb'] : 'no';

			if($clear_db === 'yes') {
				$all_teams = get_option('rwctm_teamLists', 'default_team');

				if($all_teams !== 'default_team') {
					$team_lists = explode(', ', $all_teams);

					foreach($team_lists as $list) {
						$member_lists = get_option($list);

						if(isset($member_lists) && $member_lists) {
							$team_members = explode(', ', $member_lists);

							foreach($team_members as $member) {
								delete_option($member);
							}
						}

						delete_option($list);

						delete_option($list . '_activity');
						delete_option($list . '_option');
					}

					delete_option('rwctm_teamLists');
					delete_option('rwctm_memberCount');
					delete_option('rwctm_teamIDs');
					delete_option('rwctm_IDsSerial');
				}
				delete_option('rwctm_global_options');
				delete_option('external_updates-rwc-team-members');
			}
		}
	}
}
add_action( 'rwctm_plugin_deactivation_check', 'rwctm_plugin_deactivate_options' );

if (!function_exists('rwctm_plugin_deactivate')) {
	/* Unregistering plugin deletion hooks */
	function rwctm_plugin_deactivate(){
		do_action( 'rwctm_plugin_deactivation_check' );
	}
}
register_uninstall_hook( RWCTM_AUF, 'rwctm_plugin_deactivate' );