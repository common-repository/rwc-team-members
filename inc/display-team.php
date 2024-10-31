<?php
/**
 * Team Preview Function
 *
 * This file contains a function to display a preview of the team in the admin panel
 * for the "RWC Team Members" plugin. The preview provides administrators with
 * a visual representation of how the team member information will be displayed on the frontend.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('rwctm_view_team_member')) {
	function rwctm_view_team_member() {
		// Check if the user has the necessary capability (e.g., manage_options)
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
		}
		// Get the nonce from the AJAX request data
		$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

		// Verify the nonce
		if (!wp_verify_nonce($nonce, 'rwctm_ajax_action_nonce')) {
			// Nonce verification failed, handle the error
			wp_send_json_error(array('message' => 'Nonce verification failed'));
			wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
		} else {
			$i = 1;
			$preview_team = isset( $_POST['teamname'] ) ? sanitize_text_field( $_POST['teamname'] ) : '';
			$teamId = isset( $_POST['teamid'] ) ? sanitize_text_field( $_POST['teamid'] ) : '';
			$team_lists = get_option($preview_team);
			$memberOptions = explode(', ', $team_lists);
			$optionCount = count($memberOptions); ?>
			<style type="text/css">
				*,*:after,*:before{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:0;margin:0}
			</style>
			<div id="teamdisplaydiv">
				<h3><span id="editTeam" class="process_team button button-large" data-id="<?php echo esc_attr($preview_team); ?>" data-count="<?php echo esc_attr($optionCount); ?>"><?php esc_html_e('Edit Team', 'rwc-team-members'); ?></span></h3>
				<?php echo do_shortcode('[rwc-team-members id="'.esc_attr($teamId).'"]'); ?>
			</div><?php
			$message = sprintf( __( '<p class="get_started"><strong>&#8270;&#8270;</strong> The preview might look a bit different from the actual results because of how the website\'s design varies between the front-end and admin panel. The preview shows how the team will appear on the website after it\'s published.<br><br><strong>&#8270;&#8270;</strong> To use the plugin, insert the shortcode in your theme where it will work correctly.<br><br><strong>&#8270;&#8270; Pop-ups and sliders won\'t function here; they\'ll only work on the live website.</strong>', 'rwc-team-members'), '0.1' );

			echo wp_kses_post( $message );
			wp_die();
		}
	}
}
add_action( 'wp_ajax_nopriv_rwctm_view_team_member', 'rwctm_view_team_member' );
add_action( 'wp_ajax_rwctm_view_team_member', 'rwctm_view_team_member' );