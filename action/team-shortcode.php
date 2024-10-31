<?php
/**
 * Team Shortcode Functions
 *
 * This file contains functions to define and handle the [rwc-team-members] shortcode
 * for displaying team member information on the front-end of the website.
 * The shortcode allows users to customize how the team members are displayed
 * and provides a seamless way to integrate the team's information into posts or pages.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Team Shortcode Function
 *
 * Handles the rendering of the [rwc-team-members] shortcode. This function generates and returns
 * the HTML markup for displaying team member information based on shortcode attributes.
 * Users can customize the display of team members using attributes like "category" or "layout."
 */
if (!function_exists('rwctm_team_members_shortcode')) {
	function rwctm_team_members_shortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'id' => 1
		), $atts, 'rwc-team-members' );

		// Sanitize and validate $id here
		$id = absint( $atts['id'] );

		if ( $id < 1 ) {
			return "<p class=\"rwctm_notice\">" . esc_html__('Invalid team ID!', 'rwc-team-members') . "</p>";
		}

		$total_act = $flag = 0;

		$all_teams = get_option('rwctm_teamLists');
		$team_ids = get_option('rwctm_teamIDs');

		if($all_teams != '') {
			$tm_array = explode(', ', $all_teams);
			$ids_array = explode(', ', $team_ids);

			$key = array_search($id, $ids_array);

			// Validate and sanitize the key
			if ($key !== false && isset($tm_array[$key])) {
				$flag = 1;
				// $our_team = $tm_array[$key];
				$our_team = sanitize_text_field($tm_array[$key]);
			} else {
				$message = sprintf( __( '<h3 class="rwctm_notice error">Invalid team ID!</h3><p class="rwctm_notice warning">It looks like you haven\'t added any teams yet. Don\'t worry, we\'ve got you covered! Login to your dashboard and click on the <a href="%1s" target="_blank"><strong>Add Template</strong></a> button to get started. You\'ll find <strong>10 ready-made templates</strong> to choose from. Simply select one and click on the <strong>Create Team</strong> button to instantly create your team!</p><p class="rwctm_notice info">If you have any questions or need further assistance beyond what\'s covered in the help <a href="%2s" target="_blank"><strong>page</strong></a>, please don\'t hesitate to <a href="%3s" target="_blank"><strong>contact us</strong></a> via the WordPress support thread. We\'re here to provide you with the support you need.', 'rwc-team-members' ),
				esc_url(admin_url("admin.php?page=rwctm-template")),
				esc_url(admin_url("admin.php?page=rwctm-help")),
				esc_url("https://wordpress.org/support/plugin/rwc-team-members/")
				);
				return wp_kses_post($message);
			}

			$teamID = $our_team ? strtolower($our_team) . '-' .$id : '';

			$tm_act = get_option($our_team.'_activity');
			$common = get_option($our_team.'_option');

			if($tm_act) {
				$total_act = count($tm_act)/4;
			}

			$tm_lists = get_option($our_team);
			$tmOptions = explode(', ', $tm_lists);
			$mb_count = count($tmOptions);

			ob_start();
			if($common['layout'] === 'popup') {
				require_once ( RWCTM_PLUGIN_PATH . 'layout/layout-three.php' );
				echo rwctm_layout_three($our_team, $teamID, $tm_act, $common, $total_act, $tm_lists, $tmOptions, $mb_count, $flag );
			} elseif($common['layout'] === 'slide') {
				require_once ( RWCTM_PLUGIN_PATH . 'layout/layout-two.php' );
				echo rwctm_layout_two($our_team, $teamID, $tm_act, $common, $total_act, $tm_lists, $tmOptions, $mb_count, $flag );
			} else {
				require_once ( RWCTM_PLUGIN_PATH . 'layout/layout-one.php' );
				echo rwctm_layout_one($our_team, $teamID, $tm_act, $common, $total_act, $tm_lists, $tmOptions, $mb_count, $flag );
			}
		} else {
			echo "<p class=\"rwctm_notice\">" . esc_html__('You didn\'t add any teams yet!', 'rwc-team-members') . "</p>";
		}
		return ob_get_clean();
	}
}
add_shortcode('rwc-team-members', 'rwctm_team_members_shortcode');