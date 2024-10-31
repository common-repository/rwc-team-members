<?php
/**
 * Team Activities Handling
 *
 * This file contains functions to manage various team activities and details for the
 * "RWC Team Members" plugin. These functions handle activities such as team
 * department, designation, social media names, and links for team members.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('rwctm_process_activity_option')) {
	function rwctm_process_activity_option() {
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
		}
		$i = $ao = 1;
		$our_team = isset( $_POST['teamname'] ) ? sanitize_text_field( $_POST['teamname'] ) : '';
		$activity_option = get_option($our_team.'_activity', 'default_value');

		// Sanitize the activity options
		$activity_option = rwctm_sanitize_activity_options($activity_option);
		
		if($activity_option !== 'default_value') {
			if(isset($activity_option['aicon1'])) {
				$activityNum = count($activity_option)/4;
			} else {
				$activityNum = count($activity_option)/2;
			}
		} else {
			$activityNum = 0;
		}
		if($activityNum == 1 && $activityNum > 0) {
			$rmv_id = 'remDisable';
		} else {
			$rmv_id = 'remActivity';
		}

		$member_lists = get_option($our_team);
		$memberOptions = explode(', ', $member_lists); ?>
		<input type="hidden" name="process_activity" value="activity" />
		<div id="form-messages">
			<button type="button" class="rwctm_close">
				<span aria-hidden="true"><a><i class="dashicons dashicons-dismiss greencross"></i></a></span>
			</button>
			<i class="start-icon dashicons dashicons-yes-alt"></i>
			<?php printf(__( '<strong>Well done!</strong> You successfully Updated Team Activities.', 'rwc-team-members' )); ?>
		</div>
		<div id="tablenamediv"><?php
		/* Edit activities if available */
		if($activity_option !== 'default_value') { ?>
			<div id="teamactivitydiv">
				<div class="teamactivitywrap">
					<h3><?php esc_html_e('Activity Details', 'rwc-team-members'); ?></h3>
					<table id="activity_edititem" cellspacing="0">
						<thead>
							<tr class="smheader">
								<th><?php esc_html_e('Activity Name', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Enter different activities that describe your team members. These could include department names (e.g., Finance), job titles (e.g., Account Manager), skills (e.g., PHP), or even their social media profiles (e.g., Facebook). Providing accurate activity names will help organize and categorize your team effectively.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></th>
								<th><?php esc_html_e('Activity URL', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Exclusive to social media activities, use the \'Activity Link\' field to input the corresponding profile link. This will allow easy access to their social media profiles and enhance connectivity within the team.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></th>
								<th><?php esc_html_e('Dashicons Class', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip rwctm_left dashicons dashicons-info" rel="<?php esc_html_e('To set up Dashicons you just need to put the icon class in the below text box that corresponds to the icon you are trying to display; for example if you want to display the camera icon you can add the \'dashicons-camera\' class. Visit \'developer.wordpress.org/resource/dashicons/\' to learn more about Dashicons.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></th>
								<th><?php esc_html_e('Type', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip rwctm_left dashicons dashicons-info" rel="<?php esc_html_e('There are 4 types of activities. Select \'Sector\' for Member Category/Department, \'Status\' for Member Designation, \'Social\' for social media activities of the member and \'Skill\' for member skills on different platforms. ', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></th>
								<th><?php esc_html_e('Actions', 'rwc-team-members'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php for($i = 1; $i <= $activityNum; $i++) { ?>
							<tr class="activitybody">
								<td><span><?php esc_html_e('Name:', 'rwc-team-members'); ?></span><input type="text" name="activity_name[<?php echo esc_attr('aname' . $i); ?>]" value="<?php echo esc_attr($activity_option['aname' . $i]); ?>" placeholder="<?php esc_html_e('Enter Activity Name', 'rwc-team-members'); ?>" size="15" required /><?php
									foreach($memberOptions as $option => $value) {
										$memberItem = get_option($value);

										// Sanitize the member options
										$memberItem = rwctm_escape_team_member_data($memberItem); ?>

										<input type="hidden" name="activity_value[]" value="<?php echo esc_attr($memberItem['aname'.$ao]); ?>"><?php
									} $ao++; ?>
								</td>
								<td><span><?php esc_html_e('https://', 'rwc-team-members'); ?></span><input type="text" name="activity_link[]" value="<?php echo esc_url(trim(str_replace(array('http://', 'https://'), '', $activity_option['alink' . $i]), '/')); ?>" placeholder="<?php esc_html_e('Enter Activity URL', 'rwc-team-members'); ?>" size="25" /></td>
								<td><input type="text" name="awesome_icon[]" value="<?php echo esc_attr($activity_option['aicon' . $i]); ?>" placeholder="<?php esc_html_e('Enter Icon Class', 'rwc-team-members'); ?>" size="15" /><span><?php esc_html_e('Icon', 'rwc-team-members'); ?></span></td>
								<td>
									<select name="activity_type[]" id="activity_type">
										<?php if($activity_option['atype'.$i] == 'social') { ?>
										<option value="sector"><?php esc_html_e('Sector', 'rwc-team-members'); ?></option>
										<option value="status"><?php esc_html_e('Status', 'rwc-team-members'); ?></option>
										<option value="social" selected="selected"><?php esc_html_e('Social', 'rwc-team-members'); ?></option>
										<?php } elseif($activity_option['atype'.$i] == 'sector') { ?>
										<option value="sector" selected="selected"><?php esc_html_e('Sector', 'rwc-team-members'); ?></option>
										<option value="status"><?php esc_html_e('Status', 'rwc-team-members'); ?></option>
										<option value="social"><?php esc_html_e('Social', 'rwc-team-members'); ?></option>
										<?php } elseif($activity_option['atype'.$i] == 'status') { ?>
										<option value="sector"><?php esc_html_e('Sector', 'rwc-team-members'); ?></option>
										<option value="status" selected="selected"><?php esc_html_e('Status', 'rwc-team-members'); ?></option>
										<option value="social"><?php esc_html_e('Social', 'rwc-team-members'); ?></option>
										<?php } else { ?>
										<option value="sector" selected="selected"><?php esc_html_e('Sector', 'rwc-team-members'); ?></option>
										<option value="status"><?php esc_html_e('Status', 'rwc-team-members'); ?></option>
										<option value="social"><?php esc_html_e('Social', 'rwc-team-members'); ?></option>
										<?php } ?>
									</select>
								</td>
								<td><span id="<?php echo esc_attr($rmv_id); ?>"></span></td>
								<!-- <td><span id="remActivity"></span></td> -->
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<input type="button" id="editactivity" class="button-primary" value="<?php esc_html_e('Add New', 'rwc-team-members'); ?>" />
				</div>
			</div>
			<input type="hidden" name="our_team" value="<?php echo esc_attr($our_team); ?>" />
			<input type="hidden" name="activity_option" value="<?php echo esc_attr($our_team.'_activity'); ?>" />
			<input type="hidden" name="action" value="rwctm_update_activity_option">
			<input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>">
			<button type="submit" id="rwctm_upactivity" class="button-primary"><?php esc_html_e('Update Activity', 'rwc-team-members'); ?></button>
		<?php
		/* if no activity available */
		} else { ?>
			<div id="teamactivitydiv">
				<div class="teamactivitywrap">
					<h3><?php esc_html_e('Activity Details', 'rwc-team-members'); ?></h3>
					<table id="activity_edititem" cellspacing="0">
						<thead>
							<tr class="smheader">
								<th><?php esc_html_e('Activity Name', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip" rel="<?php esc_html_e('Enter your team social links in the text box. A social is a distinctive characteristic of a good or service that sets it apart from similar items. Means of providing benefits to customers.', 'rwc-team-members'); ?>"></a></th>
								<th><?php esc_html_e('Activity URL', 'rwc-team-members'); ?></th>
								<th><?php esc_html_e('Dashicons Class', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip rwctm_left" rel="<?php esc_html_e('To set up Dashicons you just need to put the icon class in the below text box that corresponds to the icon you are trying to display; for example if you want to display the camera icon you can add the \'dashicons-camera\' class. Visit \'developer.wordpress.org/resource/dashicons/\' to learn more about Dashicons.', 'rwc-team-members'); ?>"></a></th>
								<th><?php esc_html_e('Type', 'rwc-team-members'); ?></th>
								<th><?php esc_html_e('Actions', 'rwc-team-members'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr class="activitybody">
								<td><span><?php esc_html_e('Name:', 'rwc-team-members'); ?></span><input type="text" name="activity_name[]" value="" placeholder="<?php esc_html_e('Enter Activity Name', 'rwc-team-members'); ?>" size="15" required /></td>
								<td><span><?php esc_html_e('https://', 'rwc-team-members'); ?></span><input type="text" name="activity_link[]" value="" placeholder="<?php esc_html_e('Enter Activity URL', 'rwc-team-members'); ?>" size="30" /></td>
								<td><input type="text" name="awesome_icon[]" value="" placeholder="<?php esc_html_e('Enter Icon Class', 'rwc-team-members'); ?>" size="15" /><span><?php esc_html_e('Icon', 'rwc-team-members'); ?></span></td>
								<td>
									<select name="activity_type[]" id="activity_type">
										<option value="sector" selected="selected"><?php esc_html_e('Sector', 'rwc-team-members'); ?></option>
										<option value="status"><?php esc_html_e('Status', 'rwc-team-members'); ?></option>
										<option value="social"><?php esc_html_e('Social', 'rwc-team-members'); ?></option>
									</select>
								</td>
								<td><span id="remActivity"></span></td>
							</tr>
						</tbody>
					</table>
					<input type="button" id="editactivity" class="button-primary" value="<?php esc_html_e('Add New', 'rwc-team-members'); ?>" />
				</div>
			</div>
			<input type="hidden" name="activity_option" value="<?php echo esc_attr($our_team.'_activity'); ?>" />
			<input type="hidden" name="action" value="rwctm_add_activity_option">
			<button type="submit" id="rwctm_addactivity" class="button-primary"><?php esc_html_e('Add Activity', 'rwc-team-members'); ?></button>
		<?php } ?>
		</div>
		<div class="rwctm-clear"></div>
		<div class="team_list">
			<p class="activity_notice"><?php esc_html_e('*** You can reorder activity by dragging with the mouse ***', 'rwc-team-members'); ?></p>
		</div>
	<?php
		wp_die();
	}
}
add_action( 'wp_ajax_nopriv_rwctm_process_activity_option', 'rwctm_process_activity_option' );
add_action( 'wp_ajax_rwctm_process_activity_option', 'rwctm_process_activity_option' );
