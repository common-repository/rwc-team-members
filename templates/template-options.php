<?php
/**
 * Template Options Handling
 *
 * This file contains functions to handle template options for the "RWC Team Members" plugin.
 * The functions in this file allow administrators to generate a team instantly and switch between
 * existing teams using a streamlined interface.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Generate Instant Team
 *
 * This function generates a new team instantly when the respective button is clicked.
 * The team creation process includes basic details and default settings.
 */
if (!function_exists('rwctm_activate_template')) {
	function rwctm_activate_template() {
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
			$template_number = isset($_POST['tempcount']) ? sanitize_text_field($_POST['tempcount']) : '';
			$all_teams = get_option('rwctm_teamLists');
			$team_id = $id_count = 1;
			$team_lists = explode(', ', $all_teams);
			$count_copy = count($team_lists) + $template_number;
			$our_team = 'rwctm_team_template_' . $count_copy . rand(1,1000);
			$activity_option = $our_team.'_activity';
			$team_option = $our_team.'_option';
			$sm = 1;

			$template_activity = array('Facebook', 'Twitter', 'Linkedin', 'Human Resources', 'Finance and Accounting', 'Sales and Marketing', 'Information Technology', 'Customer Service', 'Administration', 'Public Relations', 'Data Analytics', 'Creative and Design', 'Business Development', 'HR Manager', 'Recruitment Specialist', 'Chief Financial Officer', 'Accountant', 'Sales Manager', 'Sales Representative', 'IT Manager', 'Software Developer', 'Network Administrator', 'Service Manager', 'Support Specialist', 'Office Manager', 'Executive Assistant', 'PR Manager', 'Data Analyst', 'Data Engineer', 'Creative Director', 'Graphic Designer', 'Partnership Manager', 'Strategic Planner');
			$activity_link = array('facebook.com', 'twitter.com', 'linkedin.com', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$awesome_icon = array('dashicons-facebook', 'dashicons-twitter', 'dashicons-linkedin', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$activity_type = array('social', 'social', 'social', 'sector', 'sector', 'sector', 'sector', 'sector', 'sector', 'sector', 'sector', 'sector', 'sector', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status', 'status');
			$activity_values = array( 'aname1' => array('fb1', 'fb2', 'fb3', 'fb4', 'fb5', 'fb6', 'fb7', 'fb8'), 'aname2' => array('tw1', 'tw2', 'tw3', 'tw4', 'tw5', 'tw6', 'tw7', 'tw8'), 'aname3' => array('li1', 'li2', 'li3', 'li4', 'li5', 'li6', 'li7', 'li8') );

			$member_desc1 = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum accumsan non lacus semper mollis. Aenean porta risus at lobortis euismod. Sed et ligula eget odio faucibus cursus. Sed fringilla diam ut nisl condimentum ultrices. Etiam laoreet fermentum elit, quis feugiat turpis tempor vitae. Vivamus eu efficitur nunc, sit amet malesuada neque. Nam posuere felis a ex tincidunt ullamcorper. Suspendisse ex nulla, mattis sed bibendum quis, interdum a ex. Duis vel mauris ante. Nulla sit amet tellus faucibus, auctor orci et, tincidunt ligula.';
			$member_desc2 = 'In sit amet aliquet orci. Donec nec fermentum tellus. Proin eleifend porta iaculis. Nulla placerat dignissim tincidunt. Morbi varius commodo dignissim. Donec faucibus, odio eu convallis ultrices, mauris massa tristique risus, ut rhoncus metus nisi sit amet nisi. Nunc dictum sed leo non posuere. Duis quam nibh, mattis non laoreet id, volutpat sit amet eros. Duis aliquet metus vitae maximus volutpat. Etiam vestibulum arcu eu ultrices mattis. Maecenas justo lacus, tempus sed tortor nec, malesuada semper nunc. Cras in eros diam. Proin rutrum, purus vel porta sollicitudin, lorem dui imperdiet est, nec luctus dui nisl non nulla.';
			$short_bio1 = 'Aenean porta risus at lobortis euismod.';
			$short_bio2 = 'Nulla rhoncus metus nisi sit amet nisi.';
			$tagline1 = 'Lorem ipsum dolor sit amet.';
			$tagline2 = 'Etiam laoreet fermentum elit.';
			$mbimg_url = 'https://www.realwebcare.com/images/our-team/model-';

			if(!isset($all_teams)) {
				add_option('rwctm_teamLists', $our_team);
				add_option('rwctm_teamIDs', $team_id);
				add_option('rwctm_IDsSerial', $id_count);
			} elseif(empty($all_teams)){
				update_option('rwctm_teamLists', $our_team);
				update_option('rwctm_teamIDs', $team_id);
				update_option('rwctm_IDsSerial', $id_count);
			} else {
				if(in_array($our_team, $team_lists)) {
					$new_our_team = 'another_' . $our_team;
					$our_team_lists = $all_teams . ', ' . $new_our_team;
					update_option('rwctm_teamLists', $our_team_lists);
				} else {
					$our_team_lists = $all_teams . ', ' . $our_team;
					update_option('rwctm_teamLists', $our_team_lists);
				}
				$team_id = get_option('rwctm_teamIDs');
				$id_count = get_option('rwctm_IDsSerial') + 1;
				$our_team_ids = $team_id . ', ' . $id_count;
				update_option('rwctm_teamIDs', $our_team_ids);
				update_option('rwctm_IDsSerial', $id_count);
			}

			$member_common_options = array(
				'tmname' => 'Our Awesome Team '.$id_count.'', 'spurl' => '', 'imsize' => 'custom', 'imgwid' => '300', 'imghgt' => '300', 'popdir' => '', 'imtop' => '', 'imrgt' => '', 'imbtm' => '', 'imlft' => '', 'tntop' => '50', 'tnbtm' => '30', 'imgopc' => '', 'vbody' => '', 'cwidth' => '100', 'flaln' => '', 'fmtop' => '', 'fmbtm' => '', 'mxcold' => '4', 'mxcolt' => '2', 'mxcolm' => '1', 'stshow' => '3', 'stsped' => '300', 'szindx' => '1000', 'sapspd' => '', 'thmaln' => 'left', 'thtaln' => 'center', 'pgfont' => '', 'sgfont' => '', 'tnfont' => '', 'tnftab' => '', 'tnfmob' => '', 'navfsz' => '', 'nvfntb' => '', 'nvfnmb' => '', 'mntfs' => '', 'mntft' => '', 'mntfm' => '', 'dptfs' => '', 'dptft' => '', 'dptfm' => '', 'dgnfs' => '', 'dgtft' => '', 'dgtfm' => '', 'sbfsz' => '', 'sbfst' => '', 'sbfsm' => '', 'smifs' => '', 'smfst' => '', 'smfsm' => '', 'tnmcl' => '#34495e', 'tnflc' => '#ff9f43', 'nvbgcl' => '#ffffff', 'nbaccl' => '#ffffff', 'nvfncl' => '#34495e', 'nfaccl' => '#22313f', 'enable' => 'yes', 'nltab' => 'yes', 'hidetn' => 'no', 'enfilt' => 'no', 'encol' => 'no', 'inbdr' => 'no', 'enrib' => 'no', 'layout' => 'grids', 'lstyle' => 'lout1', 'enimg' => 'yes', 'imauto' => 'yes', 'slmbd' => '', 'slarow' => 'yes', 'saplay' => '', 'svwdth' => '', 'thdsg' => '', 'thdpt' => '', 'thsmic' => '', 'mbdsg' => '', 'mbdpt' => '', 'mbsmic' => '', 'subfm' => ''
			);

			$member_individual_options = array(
				'mdisp' => array('show', 'show', 'show', 'show', 'show', 'show', 'show', 'show'),
				'mname' => array('John McKenzie', 'Michael Siller', 'Brad Ortiz', 'Richard Lewis', 'Andrew Johnson', 'Robert Clark', 'James Martinez', 'William Smith'),
				'mdesc' => array($member_desc1, $member_desc2, $member_desc1, $member_desc2, $member_desc1, $member_desc2, $member_desc1, $member_desc2),
				'msbio' => array($short_bio1, $short_bio2, $short_bio1, $short_bio2, $short_bio1, $short_bio2, $short_bio1, $short_bio2),
				'tgline' => array($tagline1, $tagline2, $tagline1, $tagline2, $tagline1, $tagline2, $tagline1, $tagline2),
				'mdept' => array('human resources', 'finance and accounting', 'sales and marketing', 'information technology', 'customer service', 'administration', 'public relations', 'data analytics'),
				'mdsgn' => array('hr manager', 'accountant', 'sales manager', 'it manager', 'service manager', 'office manager', 'pr manager', 'data analyst'),
				'mexpn' => array('5 Years', '3 Years', '4 Years', '5 Years', '2 Years', '1 Year', '4 Years', '2 Years'),
				'email' => array('ex1@gmail.com', 'ex2@gmail.com', 'ex3@gmail.com', 'ex4@gmail.com', 'ex5@gmail.com', 'ex6@gmail.com', 'ex7@gmail.com', 'ex8@gmail.com'),
				'tcode' => array('+555', '+555', '+555', '+555', '+555', '+555', '+555', '+555'),
				'mtele' => array('1234567', '9876543', '5551234', '7890123', '4567890', '2345678', '8765432', '3210987'),
				'mcode' => array('+555', '+555', '+555', '+555', '+555', '+555', '+555', '+555'),
				'mmobe' => array('5556789', '1234567', '8765432', '2345678', '9876543', '6789012', '3210987', '7892345'),
				'fcode' => array('+555', '+555', '+555', '+555', '+555', '+555', '+555', '+555'),
				'mbfax' => array('1234567', '9876543', '5551234', '7890123', '4567890', '2345678', '8765432', '3210987'),
				'mlocn' => array('1234 Elm Street, Springfield, CA 12345', '5678 Maple Avenue, Riverside, NY 67890', '9876 Oak Lane, Lakeside, TX 45678', '4321 Pine Road, Mountainview, AZ 34567', '7890 Birch Court, Meadowville, FL 56789', '2468 Cedar Lane, Woodland, IL 23456', '13579 Willow Drive, Brookside, WA 78901', '8642 Walnut Street, Harborville, NC 34568'),
				'mwurl' => array('example.com', '', '', 'example.com', '', 'example.com', '', 'example.com'),
				'rtext' => array('VIP', '', 'NEW', '', 'Elite', '', 'BEST', ''),
				'mbgcl' => array('#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'),
				'rbncl' => array('#ee5253', '#ff9f43', '#2574a9', '#48b56a', '#ee5253', '#ff9f43', '#2574a9', '#48b56a'),
				'smhvr' => array('#34495e', '#34495e', '#34495e', '#34495e', '#34495e', '#34495e', '#34495e', '#34495e'),
				'mbimg' => array($mbimg_url.'7-500x500.jpg', $mbimg_url.'3-500x500.jpg', $mbimg_url.'4-500x500.jpg', $mbimg_url.'1-500x500.jpg', $mbimg_url.'5-500x500.jpg', $mbimg_url.'2-500x500.jpg', $mbimg_url.'8-500x500.jpg', $mbimg_url.'6-500x500.jpg'),
				'aname1' => array('', '', '', '', '', '', '', ''),
				'aname2' => array('', '', '', '', '', '', '', ''),
				'aname3' => array('', '', '', '', '', '', '', ''),
				'acolor1' => array('#5567a9', '#5567a9', '#5567a9', '#5567a9', '#5567a9', '#5567a9', '#5567a9', '#5567a9'),
				'acolor2' => array('#08c1f3', '#08c1f3', '#08c1f3', '#08c1f3', '#08c1f3', '#08c1f3', '#08c1f3', '#08c1f3'),
				'acolor3' => array('#1f8dd6', '#1f8dd6', '#1f8dd6', '#1f8dd6', '#1f8dd6', '#1f8dd6', '#1f8dd6', '#1f8dd6')
			);

			if($template_number == 10) {
				$member_options_color = array( 'templ' => 'temp10', 'lstyle' => 'lout10' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} elseif($template_number == 9) {
				$member_options_color = array( 'templ' => 'temp9', 'lstyle' => 'lout9', 'thtaln' => 'left' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} elseif($template_number == 8) {
				$member_options_color = array( 'templ' => 'temp8', 'lstyle' => 'lout8', 'thtaln' => 'left' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} elseif($template_number == 7) {
				$member_options_color = array( 'templ' => 'temp7', 'lstyle' => 'lout7' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} elseif($template_number == 6) {
				$member_options_color = array( 'templ' => 'temp6', 'lstyle' => 'lout6', 'imghgt' => '400' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} elseif($template_number == 5) {
				$member_options_color = array( 'templ' => 'temp5', 'lstyle' => 'lout5' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} elseif($template_number == 4) {
				$member_options_color = array( 'templ' => 'temp4', 'lstyle' => 'lout4' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} elseif($template_number == 3) {
				$member_options_color = array( 'templ' => 'temp3', 'lstyle' => 'lout3' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} elseif($template_number == 2) {
				$member_options_color = array( 'templ' => 'temp2', 'lstyle' => 'lout2' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			} else {
				$member_options_color = array( 'templ' => 'temp1', 'lstyle' => 'lout1' );
				$member_details_color = array();
				$member_options = array_merge($member_common_options, $member_options_color);
				$member_details = array_merge($member_individual_options, $member_details_color);
			}

			/* Generating Activity */
			foreach($template_activity as $key => $activity) {
				if($activity) {
					$activity_name['aname'.$sm] = sanitize_text_field( $activity );
					$activity_name['alink'.$sm] = esc_url( $activity_link[$key] );
					$activity_name['aicon'.$sm] = sanitize_text_field( $awesome_icon[$key] );
					$activity_name['atype'.$sm] = sanitize_text_field( $activity_type[$key] );
					$sm++;
				} else {
					$activity_name['aname'.$sm] = '';
					$activity_name['alink'.$sm] = '';
					$activity_name['aicon'.$sm] = '';
					$activity_name['atype'.$sm] = '';
					$sm++;
				}
			}
			add_option($activity_option, $activity_name);
			/* Generating Team Options */
			foreach($member_options as $key => $option) {
				$optionValue[$key] = sanitize_text_field( $option );
			}
			add_option($team_option, $optionValue);
			/* Generating Team Lists */
			for($mn = 0; $mn < 8; $mn++) {
				$member_lists = get_option($our_team);
				$optionName = rwctm_update_our_team($our_team, $member_lists);
				$member_count = get_option('rwctm_memberCount');
				$new_member_lists = get_option($our_team);
				$memberOptions = explode(', ', $new_member_lists);
				$list_count = count($memberOptions);
				foreach($member_details as $mkey => $value) {
					$memberOptions_text[$mkey] = sanitize_text_field( $value[$mn] );
				}
				foreach($activity_values as $skey => $svalue) {
					$activityValues_text[$skey] = sanitize_text_field( $svalue[$mn] );
				}
				$member_details_top = array( 'mop' => $member_count, 'mid' => $mn+1, 'order' => $list_count );
				$mergeMembers = array_merge($member_details_top, $memberOptions_text, $activityValues_text);
				add_option($optionName, $mergeMembers);
			}
		}
	}
}
add_action( 'wp_ajax_nopriv_rwctm_activate_template', 'rwctm_activate_template' );
add_action( 'wp_ajax_rwctm_activate_template', 'rwctm_activate_template' );

/**
 * Switch Between Existing Teams
 *
 * This function enables administrators to switch between existing teams using a dropdown menu.
 * When a team is selected from the dropdown, the layout of the template is dynamically changed
 * to match the selected team's template, providing an immediate preview of the team's layout.
 */
if (!function_exists('rwctm_setup_selected_template')) {
	function rwctm_setup_selected_template() {
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
			$template = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : '';
			$team_name = isset($_POST['teamname']) ? sanitize_text_field($_POST['teamname']) : '';
			$option_name = $team_name.'_option';
			$team_option = get_option($option_name);
			$member_lists = get_option($team_name);
			$memberOptions = explode(', ', $member_lists);

			$member_details = array(
				'mbgcl' => array('#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'),
				'rbncl' => array('#ee5253', '#ff9f43', '#2574a9', '#48b56a', '#ee5253', '#ff9f43', '#2574a9', '#48b56a'),
				'smhvr' => array('#34495e', '#34495e', '#34495e', '#34495e', '#34495e', '#34495e', '#34495e', '#34495e')
			);

			if($template == 'temp1') {
				$member_options = array( 'templ' => 'temp1', 'lstyle' => 'lout1' );
			} elseif($template == 'temp2') {
				$member_options = array( 'templ' => 'temp2', 'lstyle' => 'lout2' );
			} elseif($template == 'temp3') {
				$member_options = array( 'templ' => 'temp3', 'lstyle' => 'lout3' );
			} elseif($template == 'temp4') {
				$member_options = array( 'templ' => 'temp4', 'lstyle' => 'lout4' );
			} elseif($template == 'temp5') {
				$member_options = array( 'templ' => 'temp5', 'lstyle' => 'lout5' );
			} elseif($template == 'temp6') {
				$member_options = array( 'templ' => 'temp6', 'lstyle' => 'lout6' );
			} elseif($template == 'temp7') {
				$member_options = array( 'templ' => 'temp7', 'lstyle' => 'lout7' );
			} elseif($template == 'temp8') {
				$member_options = array( 'templ' => 'temp8', 'lstyle' => 'lout8' );
			} elseif($template == 'temp9') {
				$member_options = array( 'templ' => 'temp9', 'lstyle' => 'lout9' );
			} elseif($template == 'temp10') {
				$member_options = array( 'templ' => 'temp10', 'lstyle' => 'lout10' );
			} else {
				$member_options = array( 'templ' => 'temp1', 'lstyle' => 'lout1' );
			}
			foreach($memberOptions as $key => $option) {
				$member_value = get_option($option);
				foreach($member_details as $mkey => $value) {
					$memberValues_text[$mkey] = sanitize_text_field( $value[$key] );
				}
				$mergeMembers = array_merge($memberValues_text, $member_value);
				update_option($option, $mergeMembers);
			}
			$mergeOptions = array_merge($team_option, $member_options);

			update_option($option_name, $mergeOptions);
			wp_die();
		}
	}
}
add_action( 'wp_ajax_nopriv_rwctm_setup_selected_template', 'rwctm_setup_selected_template' );
add_action( 'wp_ajax_rwctm_setup_selected_template', 'rwctm_setup_selected_template' );