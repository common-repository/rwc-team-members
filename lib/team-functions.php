<?php
/**
 * Admin Settings Handling
 *
 * This file contains a collection of functions to handle various settings and actions
 * done by the administrator for the "RWC Team Members" plugin. These functions
 * manage the addition, update, deletion, and manipulation of team-related settings and data.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check user capabilities and nonce for security in AJAX requests.
 *
 * This function is designed to be used at the beginning of AJAX handler functions
 * to ensure that the current user has the necessary capabilities and a valid nonce.
 * If the checks fail, it terminates the script and sends a JSON error response.
 *
 * @since 1.0.0
 */
if (!function_exists('rwctm_check_permissions_and_nonce')) {
    function rwctm_check_permissions_and_nonce() {
        // Check if the user has the necessary capability (e.g., manage_options)
        if ( ! current_user_can( 'manage_options' ) ) {
			// If the user does not have the required capability, return false
			return false;
        }

        // Create or verify nonce
        $nonce_action = 'rwctm_ajax_action_nonce';
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

        if (empty($nonce) || !wp_verify_nonce($nonce, $nonce_action)) {
			// If nonce is missing or verification fails, return false
            return false;
        }
		// Nonce verification passed, return true
        return true;
    }
}

/**
 * Add Activity for Our Team
 *
 * This function adds a new activity to the list of activities for a team.
 */
if (!function_exists('rwctm_add_activity_option')) {
	function rwctm_add_activity_option() {
        // Check permissions and nonce
        if (rwctm_check_permissions_and_nonce()) {
			$sm = 1;

			$activity_option = isset($_POST['activity_option']) ? sanitize_key($_POST['activity_option']) : '';
			// Note: $activity_link is an array of URLs. Individual elements are sanitized later in the loop.
			$activity_link = isset($_POST['activity_link']) ? array_map('sanitize_text_field', $_POST['activity_link']) : array();
			$awesome_icon = isset($_POST['awesome_icon']) ? array_map('sanitize_text_field', $_POST['awesome_icon']) : array();
			$activity_type = isset($_POST['activity_type']) ? array_map('sanitize_text_field', $_POST['activity_type']) : array();

			if($activity_option) {
				if(isset($_POST['activity_name'])) {
					$activity_name = array();
					foreach($_POST['activity_name'] as $key => $activity) {
						if($activity) {
							$activity_url = isset($activity_link[$key]) ? sanitize_url($activity_link[$key]) : '';
							$activity_name['aname'.$sm] = sanitize_text_field($activity);

							if('http' == parse_url($activity_url, PHP_URL_SCHEME)) {
								$activity_name['alink'.$sm] = str_replace('http://', 'https://', $activity_url);
							} else {
								$activity_name['alink'.$sm] = sanitize_url($activity_url);
							}

							$activity_name['aicon'.$sm] = sanitize_text_field($awesome_icon[$key]);
							$activity_name['atype'.$sm] = sanitize_text_field($activity_type[$key]);
							$sm++;
						} else {
							// If $activity is empty, set defaults
							$activity_name['aname'.$sm] = '';
							$activity_name['alink'.$sm] = '';
							$activity_name['aicon'.$sm] = '';
							$activity_name['atype'.$sm] = '';
							$sm++;
						}
					}

					// Validate and sanitize the option name before adding it
					$activity_option = sanitize_key($activity_option);

					// Add or update the option using update_option
					update_option($activity_option, $activity_name);
				}
			}
		} else {
			// Nonce verification failed, handle the error
            wp_send_json_error(array('message' => 'Nonce verification failed'));
            // Display an error message or handle the case where permissions and nonce check failed
            wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
        }
		wp_die();
	}
}
add_action('wp_ajax_nopriv_rwctm_add_activity_option', 'rwctm_add_activity_option');
add_action('wp_ajax_rwctm_add_activity_option', 'rwctm_add_activity_option');

/**
 * Update Activity for Our Team
 *
 * This function updates an existing activity for a team.
 */
if (!function_exists('rwctm_update_activity_option')) {
	function rwctm_update_activity_option() {
        // Check permissions and nonce
        if(rwctm_check_permissions_and_nonce()) {
			$sm = 1; $count_item = 0;
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

			// Note: $activity_link is an array of URLs. Individual elements are sanitized later in the loop.
			$activity_link = isset($_POST['activity_link']) ? array_map('sanitize_text_field', $_POST['activity_link']) : array();
			$awesome_icon = isset($_POST['awesome_icon']) ? array_map('sanitize_text_field', $_POST['awesome_icon']) : array();
			$activity_type = isset($_POST['activity_type']) ? array_map('sanitize_text_field', $_POST['activity_type']) : array();

			$activity_option = isset($_POST['activity_option']) ? sanitize_key($_POST['activity_option']) : '';
			$activity_lists = get_option($activity_option, 'default_value');

			if ( $activity_lists !== 'default_value' ) {
				if(isset($activity_lists['alink1'])) {
					if(isset($activity_lists['acate1'])) {
						$activityNum = count($activity_lists)/5;
					} else {
						$activityNum = count($activity_lists)/4;
					}
				} else {
					$activityNum = count($activity_lists)/2;
				}
			} else {
				$activityNum = 0;
			}

			$our_team = isset($_POST['our_team']) ? sanitize_text_field($_POST['our_team']) : '';
			$member_lists = get_option($our_team);
			$memberOptions = explode(', ', $member_lists);

			if(isset($_POST['activity_name'])) { $count_item = count($_POST['activity_name']); }

			if($count_item > 0) {
				$activity_value = isset($_POST['activity_value']) ? array_map('sanitize_text_field', $_POST['activity_value']) : array();
				$activity_color = isset($_POST['activity_color']) ? array_map('sanitize_text_field', $_POST['activity_color']) : array();

				$sn = 0; $ad = 1;
				$activity_name = array();

				foreach($_POST['activity_name'] as $key => $activity) {
					if($activity) {
						$activity_url = isset($activity_link[$sm - 1]) ? sanitize_url($activity_link[$sm - 1]) : '';

						if ($activity_url && 'http' == parse_url($activity_url, PHP_URL_SCHEME)) {
							$activity_url = str_replace('http://', 'https://', $activity_url);
						}

						$activity_name['aname'.$sm] = sanitize_text_field( $activity );
						$activity_name['alink'.$sm] = sanitize_url($activity_url);
						$activity_name['aicon'.$sm] = sanitize_text_field($awesome_icon[$sm - 1]);
						$activity_name['atype'.$sm] = sanitize_text_field($activity_type[$sm - 1]);
						$sm++;
					}
					foreach($memberOptions as $item => $option) {
						$memberItem = get_option($option);
						if(array_key_exists($key, $activity_lists)) {
							$memberItem['aname' . $ad] = isset($activity_value[$sn]) ? sanitize_text_field($activity_value[$sn]) : '';
							$memberItem['acolor' . $ad] = isset($activity_color[$sn]) ? sanitize_text_field($activity_color[$sn]) : '';
							update_option($option, $memberItem);
							$sn++;
						} else {
							$memberItem['aname'.$ad] = '';
							$memberItem['acolor'.$ad] = '';
							update_option($option, $memberItem);
						}
					}
					$ad++;
				}
				update_option($activity_option, $activity_name);
			} else {
				$activity_name[] = '';
				delete_option($activity_option);
			}
			foreach($memberOptions as $key => $option) {
				$memberItem = get_option($option);
				for($i = 1; $i <= $activityNum; $i++) {
					if($i > $count_item) {
						$member_key = 'aname'.$i;
						$color_key = 'acolor'.$i;
						unset($memberItem[$member_key]);
						unset($memberItem[$color_key]);
						update_option($option, $memberItem);
					}
				}
				$i = 1;
			}
		} else {
			// Nonce verification failed, handle the error
            wp_send_json_error(array('message' => 'Nonce verification failed'));
            // Display an error message or handle the case where permissions and nonce check failed
            wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
        }
		wp_die();
	}
}
add_action( 'wp_ajax_nopriv_rwctm_update_activity_option', 'rwctm_update_activity_option' );
add_action( 'wp_ajax_rwctm_update_activity_option', 'rwctm_update_activity_option' );

/**
 * Edit Team Name and Check Uniqueness
 *
 * This function handles the update of a team's name while ensuring its uniqueness.
 *
 * @param string  $edited_team - The name of the team that needs to be edited.
 * @param string  $our_team - Edited name of the team.
 * @return string - The edited team name.
 */
if (!function_exists('rwctm_edit_our_team')) {
	function rwctm_edit_our_team($edited_team, $our_team) {
        // Check if the user has the necessary capability (e.g., manage_options)
        if (!current_user_can('manage_options')) {
            // If the user does not have the required capability, terminate and display an error message.
            wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
        } else {
			if($our_team && $our_team != $edited_team) {
				$all_teams = get_option('rwctm_teamLists');
				$team_lists = explode(', ', $all_teams);

				// Array to hold the new team names
				$new_all_teams = array();

				if (is_array($team_lists) && !empty($team_lists)) {
					foreach($team_lists as $key => $value) {
						if($value == $edited_team) {
							if(in_array($our_team, $team_lists, true)) {
								// If the new name is not unique, add a prefix
								$our_team = 'another_' . sanitize_text_field($our_team);
							}
							// Use the new name
							$new_all_teams[$key] = sanitize_text_field($our_team);
						} else {
							// Keep existing team names
							$new_all_teams[$key] = $value;
						}
					}
				}

				$new_all_teams = implode(', ', $new_all_teams);

				// Update team list with the new names
				update_option('rwctm_teamLists', $new_all_teams);

				// Transfer the values associated with the old team name to the new name
				$edited_team_value = get_option($edited_team);
				if($edited_team_value) {
					delete_option($edited_team);
					add_option($our_team, $edited_team_value);
				}

				$edited_activity_value = get_option($edited_team.'_activity');
				if($edited_activity_value) {
					delete_option($edited_team.'_activity');
					add_option($our_team.'_activity', $edited_activity_value);
				}

				$edited_option_value = get_option($edited_team.'_option');
				if($edited_option_value) {
					delete_option($edited_team.'_option');
					add_option($our_team.'_option', $edited_option_value);
				}

				// Return the edited team name
				return $our_team;
			} else {
				// If no changes were made, return the original team name
				return $edited_team;
			}
		}
	}
}

/**
 * Delete Team Members
 *
 * This function deletes team members that are no longer part of the updated team.
 *
 * @param string $our_team   The slug of the team being edited.
 * @param array  $new_lists  The updated list of team members.
 */
if (!function_exists('rwctm_delete_team_members')) {
	function rwctm_delete_team_members($our_team, $new_lists) {
        // Check if the user has the necessary capability (e.g., manage_options)
        if (!current_user_can('manage_options')) {
            // If the user does not have the required capability, terminate and display an error message.
            wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
        } else {
			$old_member_lists = get_option($our_team);
			$memberOptions = explode(', ', $old_member_lists);
			$member_diff = array_diff($memberOptions, $new_lists);

			if(!empty($member_diff)) {
				foreach($member_diff as $delpack) {
					delete_option($delpack);
				}
			}
		}
	}
}

/**
 * Update team member's option name in the database.
 *
 * This function updates an option name for a specific team member in the database.
 *
 * @param string $our_team      The name of the team.
 * @param string $member_lists  Lists of the member option name.
 * @return string               The updated option name.
 */
if (!function_exists('rwctm_update_our_team')) {
	function rwctm_update_our_team($our_team, $member_lists) {
        // Check if the user has the necessary capability (e.g., manage_options)
        if (!current_user_can('manage_options')) {
            // If the user does not have the required capability, terminate and display an error message.
            wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
        } else {
			$member_count = get_option('rwctm_memberCount', 0);

			if ($member_count === 0) {
				$member_count = 1;
				add_option('rwctm_memberCount', $member_count);
			} else {
				$member_count++;
				update_option('rwctm_memberCount', $member_count);
			}

			$optionName = 'rwctm_memberOptions' . $member_count;

			// Delete existing member options with the same prefix
			$memb_option = get_option($optionName, 0);
			if($memb_option !== 0) {
				foreach ( wp_load_alloptions() as $option => $value ) {
					if ( strpos( $option, $optionName ) === 0 ) {
						delete_option( $optionName );
					}
				}
			}

			if(!isset($member_lists) || empty($member_lists)) {
				$member_lists = $optionName;
				add_option($our_team, $member_lists);
			} else {
				$member_lists = $member_lists . ', ' . $optionName;
				update_option($our_team, $member_lists);
			}
			return $optionName;
		}
	}
}

/**
 * Recursively escapes and sanitizes input data.
 *
 * This function can be used to escape and sanitize input data, especially in cases where
 * the input may be an array. It utilizes the WordPress function esc_attr() for attribute
 * escaping and applies it recursively to handle arrays.
 *
 * @param mixed $input The input data to be escaped.
 * @return mixed Escaped and sanitized input data.
 */
if (!function_exists('rwctm_escape_input')) {
	function rwctm_escape_input($input) {
		// Check if the user has the necessary capability (e.g., manage_options)
		if (!current_user_can('manage_options')) {
			// If the user does not have the required capability, terminate and display an error message.
			wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
		} else {
			return is_array($input) ? array_map('rwctm_escape_input', $input) : esc_attr($input);
		}
	}
}

/**
 * Generate HTML input fields based on an array of settings.
 *
 * This function generates HTML input fields based on an array of settings.
 *
 * @param array $settings - Array of settings for generating input fields.
 * @return string - The HTML markup for the input fields.
 */
if (!function_exists('rwctm_get_team_options')) {
	function rwctm_get_team_options($settings = array()) {
		// Check if the user has the necessary capability (e.g., manage_options)
		if (!current_user_can('manage_options')) {
			// If the user does not have the required capability, terminate and display an error message.
			wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
		} else {
			$input_field = ''; $i = 0;
			foreach($settings as $key => $inputs) {
				$escaped_type	= isset($inputs['type']) ? rwctm_escape_input($inputs['type']) : '';
				$escaped_html	= isset($inputs['html']) ? rwctm_escape_input($inputs['html']) : '';
				$escaped_id		= isset($inputs['id']) ? rwctm_escape_input($inputs['id']) : '';
				$escaped_class	= isset($inputs['class']) ? rwctm_escape_input($inputs['class']) : '';
				$escaped_name	= isset($inputs['name']) ? rwctm_escape_input($inputs['name']) : '';
				$escaped_badge	= isset($inputs['badge']) ? rwctm_escape_input($inputs['badge']) : '';
				$escaped_label	= isset($inputs['label']) ? rwctm_escape_input($inputs['label']) : '';
				$escaped_tips	= isset($inputs['tips']) ? rwctm_escape_input($inputs['tips']) : '';
				$escaped_value	= isset($inputs['value']) ? rwctm_escape_input($inputs['value']) : '';
				$escaped_desc	= isset($inputs['desc']) ? rwctm_escape_input($inputs['desc']) : '';
				$escaped_img	= isset($inputs['img']) ? rwctm_escape_input($inputs['img']) : '';
				$escaped_min	= isset($inputs['min']) ? rwctm_escape_input($inputs['min']) : '';
				$escaped_max	= isset($inputs['max']) ? rwctm_escape_input($inputs['max']) : '';
				$escaped_step	= isset($inputs['step']) ? rwctm_escape_input($inputs['step']) : '';
				$escaped_optns	= isset($inputs['options']) ? rwctm_escape_input($inputs['options']) : '';
				$escaped_phold	= isset($inputs['placeholder']) ? rwctm_escape_input($inputs['placeholder']) : '';

				if($escaped_type == 'title') {
					$input_field .= '
					<'.$escaped_html;if($escaped_id) {$input_field .= ' id="'.$escaped_id.'"';}if($escaped_class) {$input_field .= ' class="'.$escaped_class.'"';}$input_field .= '>'.htmlspecialchars_decode($escaped_label, ENT_QUOTES).'';if($escaped_tips) {$input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>';}$input_field .= '</'.$escaped_html.'>';
				} elseif($escaped_type == 'section') {
					$input_field .= '
					<'.$escaped_html;if($escaped_id){$input_field .= ' id="'.$escaped_id;}$input_field .= '">';
				} elseif($escaped_type == 'sectionend') {
					$input_field .= '
					</'.$escaped_html.'>';
				} elseif($escaped_type == 'checkbox') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
					<input type="checkbox" name="'.$escaped_name.'" class="tickbox" id="'.$escaped_name.'" value="yes"';if($escaped_value == 'yes') { $input_field .= ' checked="checked"'; } $input_field .= '>
					<span class="check_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'radio') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>';
					$img = $escaped_img;
					if($img) {$input_field .= '<div class="layout-style">';}
					foreach($escaped_optns as $key => $option) {
						if($img){$input_field .= '<label class="labelexpanded '.$key.'">';} $input_field .= '
						<input type="radio" name="'.$escaped_name.'" class="radiobox" id="'.$escaped_name.'_'.$key.'" value="'.$key.'"';if($escaped_value == $key) { $input_field .= ' checked'; } $input_field .= '>';if($img){$input_field .= '<div class="radio-btns '.$key.'">';if($key == 'rb-1' || $key == 'rb-2' || $key == 'rb-3' || $key == 'rb-4' || $key == 'rb-5'){$input_field .= '<div class="'.$key.'"></div>';} $input_field .= '<i class="dashicons dashicons-yes-alt"></i><span>'.$option.'</span></div></label>';} else{ $input_field .= $option; }
						$i++;
					} $i = 0;
					if($img) {$input_field .= '</div>';} $input_field .= '
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'textarea') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
					<textarea name="'.$escaped_name.'" class="medium" id="'.$escaped_name.'" cols="55" rows="5" placeholder="'.$escaped_phold.'">'.$escaped_value.'</textarea>
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'select') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
					<select name="'.$escaped_name.'" id="'.$escaped_name.'">';
					foreach($escaped_optns as $key => $option) { $input_field .= '
						<option value="'.$key.'"';if($escaped_value == $key) { $input_field .= ' selected'; } $input_field .= '>'.$option.'</option>';
					} $input_field .= '
					</select>
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'multiselect') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
					<select name="'.$escaped_name.'[]" id="'.$escaped_name.'" size="10" multiple>';
					foreach($escaped_optns as $key => $option) { $input_field .= '
						<option value="'.$key.'"';
						foreach($escaped_value as $value) {
							if($value == $key) { $input_field .= ' selected'; }
						}
						$input_field .= '>'.$option.'</option>';
					} $input_field .= '
					</select>
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'number') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
					<input type="number" name="'.$escaped_name.'" class="medium" id="'.$escaped_name.'" value="'.$escaped_value.'" min="'.$escaped_min.'" max="'.$escaped_max.'" step="'.$escaped_step.'" placeholder="'.$escaped_phold.'">
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'wysiwyg') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>';
					$editor_settings = array(
						'teeny'         => true,
						'textarea_name' => $escaped_name,
						'textarea_rows' => 10,
						'editor_class'	=> 'rwctm-wp-editor'
					);
					$input_field .=  rwctm_get_wp_editor($escaped_value, 'teamcustom', $editor_settings); $input_field .=  '
					<div class="rwctm-editor-bottom"></div>
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'editor') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
					<textarea id="rwctm-fancy-textarea" class="rwctm-wp-editor" name="'.$escaped_name.'">' . sanitize_textarea_field($escaped_value) . '</textarea>'; $input_field .=  '
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'color') {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
					<div class="wp-color-picker-field">
					<input type="text" name="'.$escaped_name.'" class="medium rwctm-color-picker" id="'.$escaped_name.'" value="'.$escaped_value.'" placeholder="'.$escaped_phold.'">
					</div>
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				}  elseif($escaped_type == 'textgroup') {
					$input_field .= '
				<div class="team_options '.$escaped_id.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>';
					foreach($escaped_name as $key => $name) {
						$values = $escaped_value; $input_field .= '
						<input type="text" name="'.$key.'" class="medium" id="'.$key.'" value="'.$values[$i].'" placeholder="'.$name.'">';
						$i++;
					} $i = 0; $input_field .= '
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'numgroup') {
					$input_field .= '
				<div class="team_options '.$escaped_id.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES).'</label>';
					$values = $escaped_value;
					$min = $escaped_min;
					$max = $escaped_max;
					$tipses = $escaped_tips;
					foreach($escaped_name as $key => $name) {	$input_field .= '
						<label class="label-check numgroup">'.$name; if(isset($tipses[$i]) && $tipses[$i] != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$tipses[$i].'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
						<input type="number" name="'.$key.'" class="medium" id="'.$key.'" value="'.$values[$i].'" min="'.$min[$i].'" max="'.$max[$i].'" step="'.$escaped_step.'" placeholder="'.$name.'">';
						$i++;
					} $i = 0; $input_field .= '
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'selectgroup') {
					$labels = $escaped_label;
					$options_key = $escaped_optns;
					$values = $escaped_value;
					$tipses = $escaped_tips;
					$badges = $escaped_badge;
					foreach($escaped_name as $key => $name) {
							$input_field .= '
						<div class="team_options '.$name.'">';
							if(isset($badges[$i]) && $badges[$i]){$input_field .= '<span class="rwtm-badge">'.$badges[$i].'</span>';} $input_field .= '
							<label class="label-check">'.htmlspecialchars_decode($labels[$i], ENT_QUOTES); if($tipses[$i] != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$tipses[$i].'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
							<select name="'.$name.'" id="'.$name.'">';
							foreach($options_key[$key] as $okey => $value) { $input_field .= '
								<option value="'.$okey.'"';if($values[$i] == $okey) { $input_field .= ' selected'; } $input_field .= '>'.$value.'</option>';
							} $input_field .= '
							</select>
						</div>';
						$i++;
					} $i = 0;
				} elseif($escaped_type == 'radiogroup') {
					$labels = $escaped_label;
					$options_key = $escaped_optns;
					$tipses = $escaped_tips;
					$badges = $escaped_badge;
					foreach($labels as $key => $label) {
						$input_field .= '
						<div class="team_options '.$escaped_name.'">';
							if(isset($badges[$i]) && $badges[$i] != ''){$input_field .= '<span class="rwtm-badge">'.$badges[$i].'</span>';} $input_field .= '
							<label class="label-check">'.htmlspecialchars_decode($label, ENT_QUOTES); if(isset($tipses[$i]) && $tipses[$i] != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$tipses[$i].'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>';
							$img = $escaped_img;
							if($img) {$input_field .= '<div class="layout-style style-group">';}
							foreach($options_key[$key] as $okey => $value) {
								if($img){$input_field .= '<label class="labelexpanded ">';} $input_field .= '
								<input type="radio" name="'.$escaped_name.'" class="radiobox" id="'.$okey.'" value="'.$okey.'"';if($escaped_value == $okey) { $input_field .= ' checked'; } $input_field .= '>';if($img){$input_field .= '<div class="radio-btns btn-group '.$okey.'">'; $input_field .= '<i class="dashicons dashicons-yes-alt"></i><span>'.$value.'</span></div></label>';} else{ $input_field .= $value; }
							} if($img) {$input_field .= '</div>';} $input_field .= '
						</div>';
						$i++;
					} $i = 0;
				} elseif($escaped_type == 'multitext') {
					$labels = $escaped_label;
					$details = $escaped_desc;
					$values = $escaped_value;
					$tipses = $escaped_tips;
					$badges = $escaped_badge;
					foreach($escaped_name as $key => $name) {
						$input_field .= '
						<div class="team_options">';
							if($badges[$i] != '') { $input_field .= '<span class="rwtm-badge">'.$badges[$i].'</span>'; } $input_field .= '
							<label class="label-title">'.htmlspecialchars_decode($labels[$i], ENT_QUOTES); if($tipses[$i] != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$tipses[$i].'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
							<input type="text" name="'.$key.'" class="medium" id="'.$key.'" value="'.$values[$i].'" placeholder="'.$name.'">
							<span class="input_note">'.htmlspecialchars_decode($details[$i], ENT_QUOTES).'</span>
						</div>';
						$i++;
					} $i = 0;
				} elseif($escaped_type == 'multicheck') {
					$input_field .= '
				<div class="team_options '.$escaped_id.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>';
					foreach($escaped_name as $key => $name) {
						$values = $escaped_value; $input_field .= '
						<div id="'.$key.'" class="multicheck-item">
						<input type="checkbox" name="'.$key.'" class="tickbox mcheck" value="on"';if($values[$i] == 'on') { $input_field .= ' checked="checked"'; } $input_field .= '>'.$name.'</div>';
						$i++;
						if($i % 4 == 0 && $i != count($escaped_name)) {$input_field .= '<br><label class="label-check">&nbsp;</label>';}
					} $i = 0; $input_field .= '
					<span class="check_note multi">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				} elseif($escaped_type == 'mediabox') {
					$input_field .= '
				<div class="upload-input">
					<div class="team_options '.$escaped_name.' rwctm-uploader">';
						if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
						<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
						<input type="text" name="'.$escaped_name.'" class="medium" id="'.$escaped_name.'" value="'.$escaped_value.'" placeholder="'.$escaped_phold.'">';
						/*<input type="hidden" name="hidden_path" value="'.$escaped_value.'">*/
						$input_field .= '<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
					</div>
					<div id="show_upload_preview" class="team_options">';
						if(!empty($escaped_value)) {$input_field .= '
							<label class="label-check input-image">' . esc_html__('Preview', 'rwc-team-members') . '</label>
							<img src="'.$escaped_value.'" alt="member image" class="preview_image">
							<span id="remove_image"></span>';
						}$input_field .= '
					</div>
				</div>';
				} else {
					$input_field .= '
				<div class="team_options '.$escaped_name.'">';
					if($escaped_badge != ''){$input_field .= '<span class="rwtm-badge">'.$escaped_badge.'</span>';} $input_field .= '
					<label class="label-check">'.htmlspecialchars_decode($escaped_label, ENT_QUOTES); if($escaped_tips != '') { $input_field .= '<a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="'.$escaped_tips.'"><span class="rwctm_ttarrow"></span></a>'; } $input_field .= '</label>
					<input type="text" name="'.$escaped_name.'" class="medium" id="'.$escaped_name.'" value="'.$escaped_value.'" placeholder="'.$escaped_phold.'">
					<span class="input_note">'.htmlspecialchars_decode($escaped_desc, ENT_QUOTES).'</span>
				</div>';
				}
			}
			return $input_field;
		}
	}
}

/**
 * Update Team Member Details, Activities, Colors, and Settings
 *
 * This function updates various details, activities, colors, and settings for a team member.
 */
if (!function_exists('rwctm_set_team_member_options')) {
	function rwctm_set_team_member_options() {
        // Check permissions and nonce
        if(rwctm_check_permissions_and_nonce()) {
			// Ensure this is a POST request
			if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
				wp_die();
			}

			$ac = $as = 0;

			// Sanitize and validate user inputs
			$our_team = isset($_POST['our_team']) && $_POST['our_team'] ? sanitize_text_field($_POST['our_team']) : '';
			$unq_team_name = isset($_POST['unq_team_name']) && $_POST['unq_team_name'] ? trim(preg_replace('/[^A-Za-z0-9-\w_]+/', '_', sanitize_text_field( $_POST['unq_team_name'] ))) : $our_team;

			// Editing team name
			$our_team = rwctm_edit_our_team($our_team, $unq_team_name);

			// Array of team settings with array keys and inputs name
			$member_common_texts = array(
				'templ' => 'template', 'tmname' => 'our_team_name', 'spurl' => 'shortcode_page_url', 'imsize' => 'image_size', 'imgwid' => 'image_width', 'imghgt' => 'image_height', 'imtop' => 'imgmar_top', 'imrgt' => 'imgmar_right', 'imbtm' => 'imgmar_bottom', 'imlft' => 'imgmar_left', 'tntop' => 'tmname_top', 'tnbtm' => 'tmname_bottom', 'imgopc' => 'image_opacity', 'vbody' => 'video_body', 'cwidth' => 'container_width', 'flaln' => 'filter_align', 'fmtop' => 'filmar_top', 'fmbtm' => 'filmar_bottom', 'mxcold' => 'max_column', 'mxcolt' => 'max_coltab', 'mxcolm' => 'max_colmob', 'stshow' => 'slider_tshow', 'stsped' => 'slider_tsped', 'szindx' => 'slider_zindx', 'sapspd' => 'sldaplay_speed', 'thmaln' => 'thumbnail_align', 'thtaln' => 'thumbcap_align', 'pgfont' => 'primary_font', 'sgfont' => 'secondary_font', 'tnfont' => 'tm_name_font', 'tnftab' => 'tab_tm_name', 'tnfmob' => 'mob_tm_name', 'navfsz' => 'nav_font_size', 'nvfntb' => 'nav_font_tab', 'nvfnmb' => 'nav_font_mob', 'mntfs' => 'mn_thumb_font', 'mntft' => 'mn_thmfnt_tab', 'mntfm' => 'mn_thmfnt_mob', 'dptfs' => 'dp_thumb_font', 'dptft' => 'dp_thmfnt_tab', 'dptfm' => 'dp_thmfnt_mob', 'dgnfs' => 'dg_thumb_font', 'dgtft' => 'dg_thmfnt_tab', 'dgtfm' => 'dg_thmfnt_mob', 'sbfsz' => 'bio_thumb_fnt', 'sbfst' => 'bio_thmfn_tab', 'sbfsm' => 'bio_thmfn_mob', 'smifs' => 'sm_thumb_font', 'smfst' => 'sm_thmfnt_tab', 'smfsm' => 'sm_thmfnt_mob', 'tnmcl' => 'team_name_color', 'tnflc' => 'tmnm_mark_color', 'nvbgcl' => 'nav_bg_color', 'nbaccl' => 'nav_bg_active', 'nvfncl' => 'nav_font_color', 'nfaccl' => 'nav_active_fnclr'
			);
			
			// Sanitizing input values and put the values in an array.
			foreach($member_common_texts as $key => $value) {
				if( isset( $_POST[$value] ) ) {
					if($key == 'spurl') {
						$textarea_content = isset($_POST[$value]) ? $_POST[$value] : array();
						$lines = explode("\n", $textarea_content);

						// Sanitize each line in the textarea separately
						$cleaned_urls = array_map(
							function ($line) {
								return esc_url_raw(sanitize_text_field(trim($line)));
							},
							$lines
						);

						$optionValue_text[$key] = implode("\n", $cleaned_urls);
					} elseif ($key == 'sgfont' || $key == 'pgfont') {
						$font_family = isset($_POST[$value]) ? $_POST[$value] : array();
						$font_family = str_replace('"', "'", $font_family);
						$optionValue_text[$key] = wp_unslash($font_family);
					} else {
						$optionValue_text[$key] = sanitize_text_field( $_POST[$value] );
					}
				}
			}

			// Define the allowed options for layout_style
			$allowed_layout_styles = [
				'lout1', 'lout2', 'lout3', 'lout4', 'lout5', 'lout6', 'lout7', 'lout8', 'lout9', 'lout10'
			];

			// Declaring variables for input check values of team settings
			$rwctm_option = isset($_POST['rwctm_option']) && $_POST['rwctm_option'] === 'yes' ? 'yes' : 'no';
			$new_tab = isset($_POST['new_tab']) && $_POST['new_tab'] === 'yes' ? 'yes' : 'no';
			$hide_tmname = isset($_POST['hide_tmname']) && $_POST['hide_tmname'] === 'yes' ? 'yes' : 'no';
			$enable_ribbon = isset($_POST['enable_ribbon']) && $_POST['enable_ribbon'] === 'yes' ? 'yes' : 'no';
			$enable_image = isset($_POST['enable_image']) && $_POST['enable_image'] === 'yes' ? 'yes' : 'no';
			$img_margin_auto = isset($_POST['img_margin_auto']) && $_POST['img_margin_auto'] === 'yes' ? 'yes' : 'no';
			/* Slider */
			$slider_details = isset($_POST['slider_details']) && $_POST['slider_details'] === 'yes' ? 'yes' : 'no';
			$slider_arrows = isset($_POST['slider_arrows']) && $_POST['slider_arrows'] === 'yes' ? 'yes' : 'no';
			$slider_aplay = isset($_POST['slider_aplay']) && $_POST['slider_aplay'] === 'yes' ? 'yes' : 'no';
			$slider_vwdth = isset($_POST['slider_vwdth']) && $_POST['slider_vwdth'] === 'yes' ? 'yes' : 'no';

			$popup_choice = isset($_POST['popup_choice']) && $_POST['popup_choice'] === 'yes' ? 'yes' : 'no';
			$allow_imgeff = isset($_POST['allow_imgeff']) && $_POST['allow_imgeff'] === 'yes' ? 'yes' : 'no';
			$enable_filter = isset($_POST['enable_filter']) && $_POST['enable_filter'] === 'yes' ? 'yes' : 'no';
			$enlarge_column = isset($_POST['enlarge_column']) && $_POST['enlarge_column'] === 'yes' ? 'yes' : 'no';
			$thumb_desig = isset($_POST['thumb_desig']) && $_POST['thumb_desig'] === 'yes' ? 'yes' : 'no';
			$thumb_depart = isset($_POST['thumb_depart']) && $_POST['thumb_depart'] === 'yes' ? 'yes' : 'no';
			$thumb_smicon = isset($_POST['thumb_smicon']) && $_POST['thumb_smicon'] === 'yes' ? 'yes' : 'no';
			/* Member */
			$member_desig = isset($_POST['member_desig']) && $_POST['member_desig'] === 'yes' ? 'yes' : 'no';
			$member_depart = isset($_POST['member_depart']) && $_POST['member_depart'] === 'yes' ? 'yes' : 'no';
			$member_smicon = isset($_POST['member_smicon']) && $_POST['member_smicon'] === 'yes' ? 'yes' : 'no';
			/* Radio */
			$marked_layout = isset($_POST['marked_layout']) && in_array($_POST['marked_layout'], ['grids', 'slide', 'popup']) ? sanitize_text_field($_POST['marked_layout']) : 'grids';
			$layout_style = isset($_POST['layout_style']) && in_array($_POST['layout_style'], $allowed_layout_styles) ? sanitize_text_field($_POST['layout_style']) : 'lout1';
			/* Submit */
			$submitted = (isset($_POST['submitted']) && $_POST['submitted'] == 'yes') ? sanitize_text_field($_POST['submitted']) : 'yes';
			
			// team option name
			$team_option = $our_team.'_option';
			
			// Storing all the checked input values in an array
			$optionValue_check = array(
				'enable' => $rwctm_option, 'nltab' => $new_tab, 'hidetn' => $hide_tmname, 'enihef' => $allow_imgeff, 'enfilt' => $enable_filter, 'encol' => $enlarge_column, 'enrib' => $enable_ribbon, 'layout' => $marked_layout, 'popdir' => $popup_choice, 'lstyle' => $layout_style, 'enimg' => $enable_image, 'imauto' => $img_margin_auto, 'slmbd' => $slider_details, 'slarow' => $slider_arrows, 'saplay' => $slider_aplay, 'svwdth' => $slider_vwdth, 'thdsg' => $thumb_desig, 'thdpt' => $thumb_depart, 'thsmic' => $thumb_smicon, 'mbdsg' => $member_desig, 'mbdpt' => $member_depart, 'mbsmic' => $member_smicon, 'subfm' => $submitted
			);

			// Merging all the input values in one array
			$optionValue = array_merge($optionValue_text, $optionValue_check);

			/* Updating all the common settings in the database by serealizing
			* Here the option part ended
			*/
			update_option($team_option, $optionValue);

			// Deleting members if not available while updating
			if (isset($_POST['team_members'])) {
				$sanitized_team_members = array_map('sanitize_text_field', $_POST['team_members']);
				rwctm_delete_team_members($our_team, $sanitized_team_members);
			} else {
				rwctm_delete_team_members($our_team, ''); // Provide a default value if $_POST['team_members'] is not set
			}

			$member_count = isset($_POST['member_count']) ? absint($_POST['member_count']) : '';
			$member_id = isset($_POST['member_id']) ? array_map('sanitize_text_field', $_POST['member_id']) : array();
			$order_id = isset($_POST['order_id']) ? array_map('sanitize_text_field', $_POST['order_id']) : array();
			$member_name = isset($_POST['member_name']) ? array_map('sanitize_text_field', $_POST['member_name']) : array();
			$hidden_path = isset($_POST['hidden_path']) ? array_map('sanitize_text_field', $_POST['hidden_path']) : array();

			// Array of member details with array keys and inputs name
			$member_text_options = array(
				'mdisp' => 'hide_show', 'mname' => 'member_name', 'mdesc' => 'member_desc', 'msbio' => 'short_bio', 'tgline' => 'tag_line', 'mdept' => 'memb_department_', 'mdsgn' => 'memb_designation_', 'mexpn' => 'memb_experience', 'email' => 'memb_email', 'tcode' => 'tele_code', 'mtele' => 'memb_telephone', 'mcode' => 'mobile_code', 'mmobe' => 'memb_mobile', 'fcode' =>'fax_code', 'mbfax' =>'memb_fax', 'mlocn' =>'memb_location', 'mwurl' =>'memb_weburl', 'rtext' => 'ribbon_text', 'mbgcl' => 'team_bg', 'rbncl' => 'ribbon_bg', 'smhvr' => 'sm_hover_colo', 'mbimg' => 'image_path'
			);

			// Storing activity values by declaring variables
			$activity_id = isset($_POST['activity_id']) ? array_map('sanitize_text_field', $_POST['activity_id']) : array();
			$sm_fn_color = isset($_POST['sm_fn_color']) ? array_map('sanitize_text_field', $_POST['sm_fn_color']) : array();
			$activity_skill = isset($_POST['activity_skill']) ? array_map('sanitize_text_field', $_POST['activity_skill']) : array();
			$checkbox_value = isset($_POST['checkbox_value']) ? sanitize_text_field($_POST['checkbox_value']) : '';

			// Our team activity name
			$team_activity = $our_team.'_activity';

			// Retrieving our team activity options
			$activity_items = get_option($team_activity);
			$temp = ''; $member_order = array();

			// Define the allowed HTML tags for member description and other fields that require HTML content
			$allowed_html = array(
				'b' => array(), 'br' => array(), 'dd' => array(), 'dl' => array(), 'dt' => array(), 'em' => array(), 'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(), 'hr' => array(), 'tr' => array(), 'code' => array(), 'thead' => array(), 'tbody' => array(), 'strike' => array(), 'strong' => array(), 'i' => array( 'class' => array() ), 'p' => array( 'class' => array() ), 'li' => array( 'class' => array() ), 'abbr' => array( 'title' => array() ), 'cite' => array( 'title' => array() ), 'blockquote' => array( 'cite' => array() ), 'q' => array( 'cite' => array(), 'title' => array() ), 'ul' => array( 'class' => array(), 'style' => array() ), 'table' => array( 'class' => array(), 'id' => array() ), 'del' => array('datetime' => array(), 'title' => array() ), 'ol' => array( 'class' => array(), 'start' => array(), 'type' => array() ), 'span' => array( 'class' => array(), 'title' => array(), 'style' => array() ), 'td' => array( 'colspan' => array(), 'rowspan' => array(), 'class' => array() ), 'th' => array( 'colspan' => array(), 'rowspan' => array(), 'class' => array() ), 'div' => array( 'class' => array(), 'id' => array(), 'style' => array() ), 'a' => array( 'href' => array(), 'title' => array(), 'class' => array(), 'rel' => array(), 'target' => array() ), 'img' => array( 'alt' => array(), 'class' => array(), 'height' => array(), 'src' => array(), 'width' => array() ),
			);

			// Processing each column of the team
			if(isset($_POST['team_members'])) {
				foreach($_POST['team_members'] as $key => $member) {
					$ak = 1;
					$member_order[] = sanitize_text_field($member);
					$optionName = 'rwctm_memberOptions' . sanitize_text_field($member_id[$key]);

					if(get_option($optionName) !== null && !empty(get_option($optionName))) {
						$member_count = $member_count + 1;
						update_option('rwctm_memberCount', $member_count);
					}

					// Processing member activities
					foreach($activity_items as $akey => $item) {
						if($akey == 'aname'.$ak) {
							if($activity_items['atype'.$ak] == 'social') {
								if($activity_id[$ac]) { $member_activity[$akey] = sanitize_text_field( $activity_id[$ac] ); }
								else { $member_activity[$akey] = ''; }
								if($sm_fn_color[$ac]) { $social_color['acolor'.$ak] = sanitize_text_field( $sm_fn_color[$ac] ); }
								else { $social_color['acolor'.$ak] = ''; }
								$ac++;
							} elseif($activity_items['atype'.$ak] == 'skill') {
								if($activity_skill[$as]) { $member_activity[$akey] = sanitize_text_field( $activity_skill[$as] ); }
								else { $member_activity[$akey] = ''; }
								$as++;
							} else {
								$ak++;
							}
							// $ac++; $as++;
						} else { 
							if($akey == 'atype'.$ak) {
								$ak++;
							}
						}
					}

					// Storing all the member deatils in an array
					$memberOptions_text = array();
					foreach($member_text_options as $mkey => $value) {
						// $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
						if( isset( $_POST[$value] ) && ($mkey != 'mdept' || $mkey != 'mdsgn') ) {
							// $membValue = isset($_POST[$value]) ? array_map('sanitize_text_field', $_POST[$value]) : array();
							$membValue = isset($_POST[$value]) ? array_map('wp_unslash', $_POST[$value]) : array();

							if($mkey == 'mwurl') {
								$memberOptions_text[$mkey] = esc_url_raw( $membValue[$key] );
							} elseif (in_array($mkey, ['mdesc', 'msbio', 'tgline', 'mqlty', 'pmemb', 'maoex'])) {
								$memberOptions_text[$mkey] = wp_kses($membValue[$key], $allowed_html);
							} else {
								$memberOptions_text[$mkey] = sanitize_text_field( $membValue[$key] );
							}
						} else {	/* For mdept & mdsgn */
							$value = $value.$member_id[$key];
							if( isset( $_POST[$value] ) ) {
								// $membValue = isset($_POST[$value]) ? sanitize_text_field($_POST[$value]) : '';
								$membValue = isset($_POST[$value]) ? array_map('sanitize_text_field', $_POST[$value]) : array();
								$membValue = implode(',', $membValue);
								$memberOptions_text[$mkey] = sanitize_text_field( $membValue );
							}
						}
					}
					$memberOptions_extra = array( 'mop' => $member_id[$key], 'mid' => $key + 1, 'order' => $order_id[$key] );

					// Merging all the member arrays in one array
					$mergeMembers = array_merge($memberOptions_extra, $memberOptions_text, $member_activity, $social_color);

					// Updating all the member details in the database by serializing
					update_option($member, $mergeMembers);
				}
			}

			// Reordering members according to member order and updating again
			$team_lists = implode(', ', $member_order);
			update_option($our_team, $team_lists);
		} else {
			// Nonce verification failed, handle the error
            wp_send_json_error(array('message' => 'Nonce verification failed'));
            // Display an error message or handle the case where permissions and nonce check failed
            wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
        }
		wp_die();
	}
}
add_action( 'wp_ajax_nopriv_rwctm_set_team_member_options', 'rwctm_set_team_member_options' );
add_action( 'wp_ajax_rwctm_set_team_member_options', 'rwctm_set_team_member_options' );

/**
 * Delete Selected Team by Clearing Team Activities and Member Details
 *
 * This function deletes a selected team by clearing its activities and member details.
 */
if (!function_exists('rwctm_delete_selected_team')) {
	function rwctm_delete_selected_team() {
        // Check permissions and nonce
        if(rwctm_check_permissions_and_nonce()) {
			// Validate and sanitize the input
			$selected_team = isset( $_POST['teamname'] ) ? sanitize_text_field( $_POST['teamname'] ) : '';

			// Check if the selected team is not empty
			if ( ! empty( $selected_team ) ) {
				$our_team_lists = get_option('rwctm_teamLists');
				$team_id_lists = get_option('rwctm_teamIDs');

				// Delete member options if they exist
				$member_lists = get_option($selected_team);
				if($member_lists) {
					$member_options = explode(', ', $member_lists);
					foreach($member_options as $option) {
						delete_option($option);
					}
				}

				// Delete selected team options
				delete_option($selected_team);

				$selected_team_activities = get_option($selected_team.'_activity');
				$selected_team_option = get_option($selected_team.'_option');

				if(isset($selected_team_activities)) {
					delete_option($selected_team.'_activity');
				}
				if(isset($selected_team_option)) {
					delete_option($selected_team.'_option');
				}

				// Update team lists and IDs
				$our_team_lists = explode(', ', $our_team_lists);
				$team_id_lists = explode(', ', $team_id_lists);

				$key = array_search($selected_team, $our_team_lists);

				$our_team_diff = array_diff($our_team_lists, array($selected_team));
				if($our_team_diff) {
					$new_our_team_lists = implode(', ', $our_team_diff);
					unset($team_id_lists[$key]);
					$new_team_id_lists = implode(', ', $team_id_lists);
					update_option('rwctm_teamLists', $new_our_team_lists);
					update_option('rwctm_teamIDs', $new_team_id_lists);
				} else {
					delete_option('rwctm_teamLists');
					delete_option('rwctm_memberCount');
					delete_option('rwctm_teamIDs');
					delete_option('rwctm_IDsSerial');
				}
			}
			// Send response and terminate
			wp_send_json_success();
		} else {
			// Nonce verification failed, handle the error
            wp_send_json_error(array('message' => 'Nonce verification failed'));
            // Display an error message or handle the case where permissions and nonce check failed
            wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
        }
	}
}
add_action( 'wp_ajax_nopriv_rwctm_delete_selected_team', 'rwctm_delete_selected_team' );
add_action( 'wp_ajax_rwctm_delete_selected_team', 'rwctm_delete_selected_team' );

/**
 * Copy an Existing team and Make a Duplicate
 *
 * This function duplicates an existing team to create a new one.
 */
if (!function_exists('rwctm_copy_existed_team')) {
	function rwctm_copy_existed_team() {
        // Check permissions and nonce
        if(rwctm_check_permissions_and_nonce()) {
			$member_count = get_option('rwctm_memberCount') + 1;
			$count = 1;
			$our_team = isset( $_POST['teamname'] ) ? sanitize_text_field( $_POST['teamname'] ) : '';
			if($our_team) {
				$all_teams = get_option('rwctm_teamLists');
				$team_lists = explode(', ', $all_teams);
				$count_copy = count($team_lists) + 1;
				$new_our_team = $our_team . '_copy_' . $count_copy . rand(1,100);
				if(in_array($our_team, $team_lists)) {
					$our_team_lists = $all_teams . ', ' . $new_our_team;
					update_option('rwctm_teamLists', $our_team_lists);
				} else {
					$our_team_lists = $all_teams . ', ' . $our_team;
					update_option('rwctm_teamLists', $our_team_lists);
				}
				$team_ids = get_option('rwctm_teamIDs');
				$ids_serial = get_option('rwctm_IDsSerial') + 1;
				$our_team_ids = $team_ids . ', ' . $ids_serial;
				update_option('rwctm_teamIDs', $our_team_ids);
				update_option('rwctm_IDsSerial', $ids_serial);
				$team_activities = get_option($our_team.'_activity');
				$new_team_activities = $new_our_team . '_activity';
				// Adding duplicate team activities
				add_option($new_team_activities, $team_activities);
				$teamCombine = get_option($our_team.'_option');
				$new_team_option = $new_our_team . '_option';
				$team_subfm_option = array( 'tmname' => 'Our Awesome Team '.$ids_serial, 'subfm' => 'no' );
				$merge_options = array_merge($teamCombine, $team_subfm_option);
				// Adding duplicate team options
				add_option($new_team_option, $merge_options);
				$member_lists = get_option($our_team);
				$memberOptions = explode(', ', $member_lists);
				foreach($memberOptions as $key => $option) {
					$member_value = get_option($option);
					foreach($member_value as $mkey => $value) {
						if($mkey == 'mop') {
							$copy_member_value[$mkey] = $member_count;
						} elseif($mkey == 'mid') {
							$copy_member_value[$mkey] = $count;
							$count++;
						} else {
							$copy_member_value[$mkey] = $value;
						}
					}
					$new_memb_name = 'rwctm_memberOptions' . $member_count;
					$new_memberOptions[$key] = $new_memb_name;
					add_option($new_memb_name, $copy_member_value);
					$member_count++;
				}
				$new_memb_options = implode(', ', $new_memberOptions);
				add_option($new_our_team, $new_memb_options);
				$member_count = $member_count - 1;
				update_option('rwctm_memberCount', $member_count);
			}
		} else {
			// Nonce verification failed, handle the error
            wp_send_json_error(array('message' => 'Nonce verification failed'));
            // Display an error message or handle the case where permissions and nonce check failed
            wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
        }
	}
}
add_action( 'wp_ajax_nopriv_rwctm_copy_existed_team', 'rwctm_copy_existed_team' );
add_action( 'wp_ajax_rwctm_copy_existed_team', 'rwctm_copy_existed_team' );

/**
 * Regenerate Shortcodes IDs
 *
 * This function regenerates shortcode IDs for consistency and integrity.
 */
if (!function_exists('rwctm_regenerate_shortcode')) {
	function rwctm_regenerate_shortcode() {
        // Check permissions and nonce
        if(rwctm_check_permissions_and_nonce()) {
			$all_teams = get_option('rwctm_teamLists');
			delete_option('rwctm_teamIDs');
			delete_option('rwctm_IDsSerial');

			$team_lists = explode(', ', $all_teams);
			$team_id = 1;
			$temp = '';
			$id_count = count($team_lists);
			foreach($team_lists as $key => $list) {
				if($id_count > 1) {
					$our_team_ids = $temp . $team_id;
					$temp = $our_team_ids . ', ';
					$team_id++;
				} else {
					$our_team_ids = $team_id;
				}
			}
			add_option('rwctm_teamIDs', $our_team_ids);
			add_option('rwctm_IDsSerial', $id_count);
		} else {
			// Nonce verification failed, handle the error
            wp_send_json_error(array('message' => 'Nonce verification failed'));
            // Display an error message or handle the case where permissions and nonce check failed
            wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
        }
	}
}
add_action( 'wp_ajax_nopriv_rwctm_regenerate_shortcode', 'rwctm_regenerate_shortcode' );
add_action( 'wp_ajax_rwctm_regenerate_shortcode', 'rwctm_regenerate_shortcode' );

/**
 * Set Global Options for All Teams
 *
 * This function sets global options that apply to all teams.
 */
if (!function_exists('rwctm_set_global_options')) {
	function rwctm_set_global_options() {
		// Check if the user has the necessary capability (e.g., manage_options)
		if ( ! current_user_can( 'manage_options' ) ) {
			// If the user does not have the required capability, terminate and display an error message.
			wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
		} else {
			// Nonce verification
			if (!isset($_POST['rwctm_global_options_nonce']) || !wp_verify_nonce($_POST['rwctm_global_options_nonce'], 'rwctm_global_options_nonce')) {
				// Nonce verification failed, terminate and display an error message.
				wp_die(__('Security check failed. Please refresh the page and try again.', 'rwc-team-members'));
			}
			// Validate and sanitize inputs
			$wp_post_editor = isset( $_POST['wp_post_editor'] ) && $_POST['wp_post_editor'] === 'yes' ? 'yes' : 'no';
			$html_editor = isset( $_POST['html_editor'] ) && $_POST['html_editor'] === 'yes' ? 'yes' : 'no';
			$default_font = isset( $_POST['default_font'] ) && $_POST['default_font'] === 'yes' ? 'yes' : 'no';
			$dash_icons = isset( $_POST['dash_icons'] ) && $_POST['dash_icons'] === 'yes' ? 'yes' : 'no';
			$view_port = isset( $_POST['view_port'] ) && $_POST['view_port'] === 'yes' ? 'yes' : 'no';
			$clear_db = isset( $_POST['clear_db'] ) && $_POST['clear_db'] === 'yes' ? 'yes' : 'no';

			$custom_font = isset( $_POST['custom_font'] ) ? sanitize_text_field( $_POST['custom_font'] ) : '';
			$primary_font = isset( $_POST['primary_font'] ) ? sanitize_css_font_family( $_POST['primary_font'] ) : '';
			$secondary_font = isset( $_POST['secondary_font'] ) ? sanitize_css_font_family( $_POST['secondary_font'] ) : '';

			$rwctm_custom = isset($_POST['rwctm_custom_css']) ? sanitize_textarea_field($_POST['rwctm_custom_css']) : '';

			// Storing all the checked input values in an array
			$global_options = array(
				'wpedit'	=> $wp_post_editor,
				'htmled'	=> $html_editor,
				'gdfont'	=> $default_font,
				'gcfont'	=> $custom_font,
				'prfont'	=> $primary_font,
				'scfont'	=> $secondary_font,
				'dsicon'	=> $dash_icons,
				'vwport'	=> $view_port,
				'clrdb'		=> $clear_db,
				'rtmcss'	=> $rwctm_custom
			);

			// Update or add the global options
			if ( get_option( 'rwctm_global_options' ) !== false ) {
				update_option('rwctm_global_options', $global_options);
			} else {
				add_option('rwctm_global_options', $global_options);
			}
		}
	}
}

/**
 * Sanitize font family strings for CSS
 */
if (!function_exists('sanitize_css_font_family')) {
    function sanitize_css_font_family($font) {
        $font = strip_tags($font); // Remove HTML tags
        $font = stripslashes($font); // Remove slashes
        $font = html_entity_decode($font, ENT_QUOTES, 'UTF-8'); // Decode HTML entities

        // Remove unwanted characters (anything that is not alphanumeric, comma, space, or dash)
        $font = preg_replace('/[^a-zA-Z0-9, \-"]/','', $font);

        // Ensure proper formatting with quotes around font names with spaces
        $font_families = explode(',', $font);
        $sanitized_families = array();
        
        foreach ($font_families as $family) {
            $family = trim($family);
            if (strpos($family, ' ') !== false && strpos($family, '"') === false) {
                $family = '"' . $family . '"';
            }
            $sanitized_families[] = $family;
        }

        return implode(', ', $sanitized_families);
    }
}

/**
 * Find How Many Teams are Published
 *
 * This function counts and returns the number of published teams.
 */
if (!function_exists('rwctm_published_teams_count')) {
	function rwctm_published_teams_count($team_lists) {
		// Check if the user has the necessary capability (e.g., manage_options)
		if (!current_user_can('manage_options')) {
			// If the user does not have the required capability, terminate and display an error message.
			wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
		} else {
			$count = 0;
			foreach($team_lists as $key => $list) {
				$common_options = get_option($list.'_option');
				if($common_options) {
					if(isset($common_options['enable']) && $common_options['enable'] == 'yes') {
						$count++;
					}
				}
			}
			return $count;
		}
	}
}

/**
 * Find Unuseful Member Options and Delete Them
 *
 * This function identifies and deletes unused member options from the database.
 */
if (!function_exists('rwctm_unuseful_member_options')) {
	function rwctm_unuseful_member_options() {
		// Check if the user has the necessary capability (e.g., manage_options)
		if (!current_user_can('manage_options')) {
			// If the user does not have the required capability, terminate and display an error message.
			wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
		} else {
			$all_teams = get_option('rwctm_teamLists');
			$team_lists = explode(', ', $all_teams);
			$temp = '';
			foreach($team_lists as $key => $team) :				 // $team = My_Team_WordPress
				$team_options = get_option($team);
				$team_options_list = $temp . $team_options;
				$temp = $team_options_list.', ';
			endforeach;
			$total_team_options = explode(', ', $team_options_list);
			/* counting memberOptions1-1000 to check
			 * if any unuseful member exist or not */
			for($i = 1; $i <= 1000; $i++) {
				$member_option = 'rwctm_memberOptions'.$i;
				if(get_option($member_option) == true && !in_array($member_option, $total_team_options)) {
					delete_option($member_option);
				}
			}
		}
	}
}

/**
 * Get WP Editor for Team Member Description
 *
 * This function returns the WordPress editor for editing a team member's description.
 */
if (!function_exists('rwctm_get_wp_editor')) {
	function rwctm_get_wp_editor( $editor_id, $content = '', $options = array() ) {
		// Check if the user has the necessary capability (e.g., manage_options)
		if (!current_user_can('manage_options')) {
			// If the user does not have the required capability, terminate and display an error message.
			wp_die(__('You do not have sufficient permissions to access this page.', 'rwc-team-members'));
		} else {
            // Start output buffering
            ob_start();

			// Ensure $content is a string
			$content = is_null($content) ? '' : $content;

			wp_editor( $content, $editor_id, $options );
			$editor_output = ob_get_clean(); // Get and clear the buffer
            ob_start();
            \_WP_Editors::enqueue_scripts();
            $enqueue_scripts = ob_get_clean();

            ob_start();
            print_footer_scripts();
            $footer_scripts = ob_get_clean();

            ob_start();
            \_WP_Editors::editor_js();
            $editor_js = ob_get_clean();

            // Combine all parts and return as a single string
            $temp = $editor_output . $enqueue_scripts . $footer_scripts . $editor_js;
            return $temp;
		}
	}
}