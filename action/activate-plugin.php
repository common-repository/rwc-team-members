<?php
/**
 * IMPORTANT: Plugin Activation Defaults
 *
 * This file is crucial for setting up default options in the database
 * when the plugin is activated. It ensures that input and textarea
 * fields won't display any error or warning messages. Modifying
 * this file without proper understanding may lead to unexpected
 * behavior of the plugin. Make sure to proceed with caution.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('rwctm_plugin_activation_values')) {
	function rwctm_plugin_activation_values() {
		$rwctm_globals = get_option('rwctm_global_options', 'default_value');

		//  Get the current time and set it as an option when the plugin is activated.
		$get_activation_time = strtotime("now");
		add_option('rwctm_activation_time', $get_activation_time );

		// Checking global options exist or not
		if ( $rwctm_globals === 'default_value' ) {
			// The option does not exist. Set default values here
			$global_options = array('wpedit' => 'yes', 'htmled' => 'yes', 'gdfont' => 'yes', 'gcfont' => '', 'prfont' => '', 'scfont' => '', 'dsicon' => 'yes', 'vwport' => '', 'clrdb' => '', 'rtmcss' => '');

			add_option('rwctm_global_options', $global_options);
		} else {
			// Checking values of team member global settings
			$wp_post_editor = isset($rwctm_globals['wpedit']) && $rwctm_globals['wpedit'] === 'yes' ? 'yes' : 'yes';
			$html_editor = isset($rwctm_globals['htmled']) && $rwctm_globals['htmled'] === 'yes' ? 'yes' : 'yes';
			$default_font = isset($rwctm_globals['gdfont']) && $rwctm_globals['gdfont'] === 'yes' ? 'yes' : 'yes';
			$dash_icons = isset($rwctm_globals['dsicon']) && $rwctm_globals['dsicon'] === 'yes' ? 'yes' : 'yes';
			$view_port = isset($rwctm_globals['vwport']) && $rwctm_globals['vwport'] === 'yes' ? 'yes' : 'no';
			$clear_db = isset($rwctm_globals['clrdb']) && $rwctm_globals['clrdb'] === 'yes' ? 'yes' : 'no';

			$custom_font = (isset($rwctm_globals['gcfont']) && $rwctm_globals['gcfont']) ? sanitize_text_field($rwctm_globals['gcfont']) : '';
			$primary_font = (isset($rwctm_globals['prfont']) && $rwctm_globals['prfont']) ? sanitize_text_field($rwctm_globals['prfont']) : '';
			$secondary_font = (isset($rwctm_globals['scfont']) && $rwctm_globals['scfont']) ? sanitize_text_field($rwctm_globals['scfont']) : '';
			$rwctm_custom = (isset($rwctm_globals['rtmcss']) && $rwctm_globals['rtmcss']) ? esc_textarea($rwctm_globals['rtmcss']) : '';

			$global_options = array('wpedit' => $wp_post_editor, 'htmled' => $html_editor, 'gdfont' => $default_font, 'gcfont' => $custom_font, 'prfont' => $primary_font, 'scfont' => $secondary_font, 'dsicon' => $dash_icons, 'vwport' => $view_port, 'clrdb' => $clear_db, 'rtmcss' => $rwctm_custom);

			update_option('rwctm_global_options', $global_options);
		}

		$all_teams = get_option('rwctm_teamLists', 'default_value');
		$team_activity = $team_combine = $member_columns = $memberOptions = array();
		if ( $all_teams !== 'default_value' ) {
			// The option exists
			$team_lists = explode(', ', $all_teams);
			$act=0;
			foreach($team_lists as $key => $the_team) {
				if($the_team) {
					$team_activity = get_option($the_team.'_activity', 'activity_not_exists');
					$team_combine = get_option($the_team.'_option');
					$member_columns = get_option($the_team, null);
					if ( $member_columns !== null ) {
						// The option exists
						$memberOptions = explode(', ', $member_columns);
					}
				}
				if ( $team_activity !== 'activity_not_exists' ) {
					// The activity exists
					$actnum = count($team_activity)/4;
				} else {
					$actnum = 0;
				}
				for($act=1; $act <= $actnum; $act++) {
					$activity_name['aname'.$act] = '';
					$activity_name['alink'.$act] = '';
					$activity_name['aicon'.$act] = '';
					$activity_name['atype'.$act] = '';
				}
				$act=0; $mbgcl = $rbncl = $smhvr = '';
				$final_activities = array_merge($activity_name, $team_activity);
				//Updating all the features
				update_option($the_team.'_activity', $final_activities);

				$common_options = array(
					'tmname' => '', 'spurl' => '', 'imsize' => 'custom', 'imgwid' => '300', 'imghgt' => '300', 'popdir' => '', 'imtop' => '', 'imrgt' => '', 'imbtm' => '', 'imlft' => '', 'tntop' => '50', 'tnbtm' => '30', 'imgopc' => '', 'vbody' => '', 'cwidth' => '100', 'flaln' => '', 'fmtop' => '', 'fmbtm' => '', 'mxcold' => '4', 'mxcolt' => '2', 'mxcolm' => '1', 'stshow' => '3', 'stsped' => '300', 'szindx' => '1000', 'sapspd' => '', 'thmaln' => 'left', 'thtaln' => 'center', 'pgfont' => '', 'sgfont' => '', 'tnfont' => '', 'tnftab' => '', 'tnfmob' => '', 'navfsz' => '', 'nvfntb' => '', 'nvfnmb' => '', 'mntfs' => '', 'mntft' => '', 'mntfm' => '', 'dptfs' => '', 'dptft' => '', 'dptfm' => '', 'dgnfs' => '', 'dgtft' => '', 'dgtfm' => '', 'sbfsz' => '', 'sbfst' => '', 'sbfsm' => '', 'smifs' => '', 'smfst' => '', 'smfsm' => '', 'tnmcl' => '#34495e', 'tnflc' => '#ff9f43', 'nvbgcl' => '#ffffff', 'nbaccl' => '#ffffff', 'nvfncl' => '#34495e', 'nfaccl' => '#22313f', 'enable' => 'yes', 'nltab' => 'yes', 'hidetn' => 'no', 'enfilt' => 'no', 'encol' => 'no', 'inbdr' => 'no', 'enrib' => 'no', 'layout' => 'grids', 'lstyle' => 'lout1', 'enimg' => 'yes', 'imauto' => 'yes', 'slmbd' => '', 'slarow' => 'yes', 'saplay' => '', 'svwdth' => '', 'thdsg' => '', 'thdpt' => '', 'thsmic' => '', 'mbdsg' => '', 'mbdpt' => '', 'mbsmic' => '', 'subfm' => '', 'templ' => '', 'enihef' => 'no'
				);

				if (is_array($team_combine)) {
					$final_options = array_merge($common_options, $team_combine);
				} else {
					$final_options = $common_options; // Set a default value
				}

				//Updating all the common options
				update_option($the_team.'_option', $final_options);

				foreach($memberOptions as $key => $option) {
					$member_values = get_option($option);
					if(!isset($member_values['mbgcl'])) {
						$mbgcl = $member_values['mbgcl'];
					}
					if(!isset($member_values['rbncl'])) {
						$rbncl = $member_values['rbncl'];
					}
					if(!isset($member_values['smhvr'])) {
						$smhvr = $member_values['smhvr'];
					}
					$member_column_details = array('mdisp' => '', 'mname' => '', 'mdesc' => '', 'msbio' => '', 'tgline' => '', 'mdept' => '', 'mdsgn' => '', 'mexpn' => '', 'email' => '', 'tcode' => '', 'mtele' => '', 'mcode' => '', 'mmobe' => '', 'fcode' => '', 'mbfax' => '', 'mlocn' => '', 'mwurl' => '', 'rtext' => '', 'mbgcl' => $mbgcl, 'rbncl' => $rbncl,  'smhvr' => $smhvr, 'mbimg' => '');

					for($act=1; $act <= $actnum; $act++) {
						$activity_values['aname'.$act] = '';
						$activity_values['acolor'.$act] = '';
					}
					$member_details = array_merge($member_column_details, $activity_values);
					$final_member_details = array_merge($member_details, $member_values);
					//Updating all the columns options
					update_option($option, $final_member_details);
				}
			}
		}
	}
}
add_action( 'rwctm_plugin_activation_options', 'rwctm_plugin_activation_values' );

if (!function_exists('rwctm_plugin_activate')) {
	/* Registering plugin activation hooks */
	function rwctm_plugin_activate() {
		do_action( 'rwctm_plugin_activation_options' );
	}
}
register_activation_hook( RWCTM_AUF, 'rwctm_plugin_activate' );