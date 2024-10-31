<?php
/**
 * Team Settings Handling
 *
 * This file contains functions to manage team settings, including adding team members,
 * entering member details and social details, coloring the team using ColorPicker, and
 * configuring team settings for the "RWC Team Members" plugin.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Configure Team Settings
 *
 * This function handles the configuration of team settings, including layout and styling.
 */
if (!function_exists('rwctm_process_team_members')) {
	function rwctm_process_team_members() {
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
            // Display an error message or handle the case where permissions and nonce check failed
			wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
		} else {
			$sm = $ac = $cf = $sc = $smc = 1; $ed = 0;
			$s_media = '';
			$f_tips = '';
			// get the team name
			$our_team = isset($_POST['teamname']) ? sanitize_text_field($_POST['teamname']) : '';

			// make the first word uppercare and remove '_' by space
			$our_team_name = ucwords(str_replace('_', ' ', $our_team));

			// get total members count
			$mb_count = get_option( 'rwctm_memberCount' );
			// get activity for the team
			$activity_option = get_option($our_team.'_activity');
			// Sanitize the activity options
			$activity_option = rwctm_sanitize_activity_options($activity_option);
			// get settings options for the team
			$teamCombine = get_option($our_team.'_option');
			// Sanitize the team options
			$teamCombine = rwctm_sanitize_team_options($teamCombine);
			// get memberDetails1, memberDetails2, ... for the team
			$team_details = get_option($our_team);
			// get member details in an array
			$all_members = explode(', ', $team_details);
			// get global options for the team
			$global_options = get_option('rwctm_global_options');
			// Sanitize the global options
			$global_options = rwctm_sanitize_global_options($global_options);
			
			// check how many activities are available
			if(isset($activity_option['aicon1'])) {
				$activityNum = count($activity_option)/4;
			} else {
				$activityNum = count($activity_option)/2;
			}

			$template = isset($teamCombine['templ']) ? $teamCombine['templ'] : 'temp0';
			$checkValue = uniqid('yes');
			$siteurl = get_site_url();

			if(isset($global_options['wpedit']) && $global_options['wpedit'] == 'yes') {
				$editor = 'wysiwyg';
			} else {
				$editor = 'editor';
			}
			$general_fields = array(
				array(
					'name'				=> 'rwctm_option',
					'label'				=> __( 'Enable Team', 'rwc-team-members' ),
					'desc'				=> sprintf( __( 'Mark if you want to show <b>%s</b>', 'rwc-team-members' ), $our_team_name ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['enable']) ? $teamCombine['enable'] : 'yes',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> array(
											'our_team_name'	=> 'Your Team Name',
											'unq_team_name'	=> 'Unique Team Name'
										),
					'label'				=> array(
											__( 'Team Name', 'rwc-team-members' ),
											__( 'Modify Team Name', 'rwc-team-members' )
										),
					'desc'				=> array(
											__( 'Enter your team name here', 'rwc-team-members' ),
											__( 'Modify auto generated name if you want', 'rwc-team-members' )
										),
					'type'				=> 'multitext',
					'value'				=> array(
											isset($teamCombine['tmname']) ? $teamCombine['tmname'] : 'RWC Team Member',
											isset($our_team_name) ? $our_team_name : 'RWC Team Member'
										),
					'tips'				=> array(
											__( 'Here you can name as you wish. There is no problem if you give a name that is already used. This name will be used for display only.', 'rwc-team-members' ),
											__( 'This is an auto-generated team name generated when creating a team from a ready-made template. Auto generated team names must be unique. If you enter a name that is already in use, it will be saved in the database by adding an extra word to the name you entered. It will not be a problem. However, it is good practice to use an unique name.', 'rwc-team-members' )
										),
					'badge'				=> array(
											__( '', 'rwc-team-members' ),
											__( '', 'rwc-team-members' )
										),
				),
				array(
					'name'				=> 'shortcode_page_url',
					'label'				=> __( 'Shortcode Post/Page URLs:', 'rwc-team-members' ),
					'desc'				=> __( 'Enter One URL Per Line.', 'rwc-team-members' ),
					'placeholder'		=> __( 'Enter Shortcode Post/Page Urls', 'rwc-team-members' ),
					'type'				=> 'textarea',
					'value'				=> isset($teamCombine['spurl']) ? $teamCombine['spurl'] : '',
					'tips'				=> __( 'Enter the page URL(s) here where you want to show this team. By entering the page URL(s), the customized CSS will load only the specific team\'s custom CSS. In this way, file size of the custom CSS will reduce and the page load faster.', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'new_tab',
					'label'				=> __( 'Open Link in New Tab', 'rwc-team-members' ),
					'desc'				=> __( 'Mark if you want to open social media link in a new tab.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['nltab']) ? $teamCombine['nltab'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'hide_tmname',
					'label'				=> __( 'Hide the Team Name', 'rwc-team-members' ),
					'desc'				=> __( 'Completely hide the team name and the colored bars underneath the team name.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['hidetn']) ? $teamCombine['hidetn'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Team Name Margin', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> array(
						'tmname_top'	=> 'Margin Top',
						'tmname_bottom'	=> 'Margin Bottom'
					),
					'id'				=> 'tmname_margin',
					'label'				=> __( 'Team Name Margin (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Team name margin respectively for margin <b>top</b> and <b>bottom</b>. ', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0),
					'max'				=> array(500, 500),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['tntop']) ? $teamCombine['tntop'] : '0',
											isset($teamCombine['tnbtm']) ? $teamCombine['tnbtm'] : '0'
										),
					'tips'				=> array(),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Member Ribbons', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'enable_ribbon',
					'label'				=> __( 'Enable Member Ribbons', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you want to show ribbon for a member.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['enrib']) ? $teamCombine['enrib'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				/* array(
					'class'				=> 'rwctm_notice',
					'label'				=> __( 'For <strong>Ribbon Background Color</strong>, scroll up to <strong>Team Member Colors >> Member Ribbon Background</strong>. Search for <strong>Ribbon Font Color</strong> to change the ribbon text color.', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'span',
				) */
			);
			$media_fields = array(
				array(
					'id'				=> '',
					'label'				=> __( 'Member Image', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'enable_image',
					'label'				=> __( 'Enable Member Images', 'rwc-team-members' ),
					'desc'				=> __( 'Mark if you want to show member images.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['enimg']) ? $teamCombine['enimg'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> 'img-features',
					'type'				=> 'section',
					'html'				=> 'div',
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Image Properties', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( 'Image width and height are for thumbnail size. For image margin, you can select automatic margin or set it by entering margin value. For image opacity, enter a value from 1 to 100. 100 will work as &#34;opacity:1&#34; and 50 will work as &#34;opacity:0.5&#34;', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'image_size',
					'label'				=> __( 'Image Size', 'rwc-team-members' ),
					'desc'				=> __( 'Set the member thumbnail size.', 'rwc-team-members' ),
					'type'				=> 'select',
					'value'				=> isset($teamCombine['imsize']) ? $teamCombine['imsize'] : 'custom',
					'options'			=> array(
						'thumbnail'		=> 'Thumbnail Size',
						'medium'		=> 'Medium Size',
						'large'			=> 'Large Size',
						'full'			=> 'Full Size',
						'custom'		=> 'Custom Size'
					),
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> 'img-custom',
					'type'				=> 'section',
					'html'				=> 'div',
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Custom Image Properties', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( 'This will allow you to resize &amp; crop WordPress images uploaded via the media uploader on the fly. It relies on WP\'s native functions to resize the images, and checks if there is an already resized version of the image so that it won\'t be wasting your server\'s resources to regenerate the images.', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'image_width',
					'label'				=> __( 'Image Width', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the image thumbnail width (px)', 'rwc-team-members' ),
					'placeholder'		=> __( 'e.g. 300', 'rwc-team-members' ),
					'min'				=> 50,
					'max'				=> 2000,
					'step'				=> '1',
					'type'				=> 'number',
					'value'				=> isset($teamCombine['imgwid']) ? $teamCombine['imgwid'] : '250',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'image_height',
					'label'				=> __( 'Image Height', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the image thumbnail height (px)', 'rwc-team-members' ),
					'placeholder'		=> __( 'e.g. 300', 'rwc-team-members' ),
					'min'				=> 50,
					'max'				=> 2000,
					'step'				=> '1',
					'type'				=> 'number',
					'value'				=> isset($teamCombine['imghgt']) ? $teamCombine['imghgt'] : '250',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'class'				=> 'rwctm_notice',
					'label'				=> __( '<strong>Note:</strong> After adding or modifying image sizes, it is recommended to regenerate existing thumbnails using a plugin like <b>Regenerate Thumbnails</b>. Regenerating thumbnails provides an opportunity to remove any unnecessary image sizes that were previously generated. This can help <b>optimize your media library</b> and <b>reduce storage space</b> by eliminating redundant image variations.', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'span',
				),
				array(
					'type'				=> 'sectionend',
					'html'				=> 'div',
				),
				array(
					'name'				=> 'image_opacity',
					'label'				=> __( 'Image Opacity', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the opacity of the image between 0 and 100', 'rwc-team-members' ),
					'placeholder'		=> __( '20', 'rwc-team-members' ),
					'min'				=> 0,
					'max'				=> 100,
					'step'				=> '1',
					'type'				=> 'number',
					'value'				=> isset($teamCombine['imgopc']) ? $teamCombine['imgopc'] : '20',
					'tips'				=> __( '100 will remain the image transparent as it is. The lower the number, the dimmer the image.', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'type'				=> 'sectionend',
					'html'				=> 'div',
				)
			);
			$structure_fields = array(
				array(
					'name'				=> 'container_width',
					'label'				=> __( 'Container Width (in %)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the total container width of your team here.', 'rwc-team-members' ),
					'placeholder'		=> __( '1', 'rwc-team-members' ),
					'min'				=> 1,
					'max'				=> 100,
					'step'				=> '1',
					'type'				=> 'number',
					'value'				=> isset($teamCombine['cwidth']) ? $teamCombine['cwidth'] : '100',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> 'max-column',
					'type'				=> 'section',
					'html'				=> 'div',
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Number of Members per Row', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( 'If your team has many members, you can divide the members by row by selecting the number of columns from the dropdown list.', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'      		=> array(
											'max_column',
											'max_coltab',
											'max_colmob'
										),
					'label'     		=> array(
											__( 'Desktop', 'rwc-team-members' ),
											__( 'Tab', 'rwc-team-members' ),
											__( 'Mobile', 'rwc-team-members' )
										),
					'type'      		=> 'selectgroup',
					'value'   			=> array(
											isset($teamCombine['mxcold']) ? $teamCombine['mxcold'] : '4',
											isset($teamCombine['mxcolt']) ? $teamCombine['mxcolt'] : '4',
											isset($teamCombine['mxcolm']) ? $teamCombine['mxcolm'] : '4',
										),
					'options'   		=> array(
							array(
								'1'		=> __( '1 Column', 'rwc-team-members' ),
								'2'		=> __( '2 Columns', 'rwc-team-members' ),
								'3'    	=> __( '3 Columns', 'rwc-team-members' ),
								'4'    	=> __( '4 Columns', 'rwc-team-members' ),
								'5'    	=> __( '5 Columns', 'rwc-team-members' ),
								'6'    	=> __( '6 Columns', 'rwc-team-members' ),
								'7'    	=> __( '7 Columns', 'rwc-team-members' ),
								'8'    	=> __( '8 Columns', 'rwc-team-members' )
							),
							array(
								'1'		=> __( '1 Column', 'rwc-team-members' ),
								'2'		=> __( '2 Columns', 'rwc-team-members' ),
								'3'    	=> __( '3 Columns', 'rwc-team-members' ),
								'4'    	=> __( '4 Columns', 'rwc-team-members' )
							),
							array(
								'1'		=> __( '1 Column', 'rwc-team-members' ),
								'2'		=> __( '2 Columns', 'rwc-team-members' )
							)
					),
					'tips'				=> array(
											__( 'Select the number of members you would like to display per row for the desktop view. This setting applies to screen widths starting from 993px and above.', 'rwc-team-members' ),
											__( 'Specify the number of members to display per row in the tab view, targeting tablets with a width range of 481px to 992px.', 'rwc-team-members' ),
											__( 'Specify the number of members to display per row for the mobile view, which applies to screen widths of 480px and below.', 'rwc-team-members' )
										),
					'badge'				=> array()
				),
				array(
					'type'				=> 'sectionend',
					'html'				=> 'div',
				),
				array(
					'name'				=> 'img_margin_auto',
					'label'				=> __( 'Automatic Margin', 'rwc-team-members' ),
					'desc'				=> __( 'Set the image margin as &#34;<b>auto</b>&#34;.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['imauto']) ? $teamCombine['imauto'] : 'no',
					'tips'				=> __( 'For image margin, you can select automatic margin or set it by entering margin value', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> array(
						'imgmar_top'	=> 'Top',
						'imgmar_right'	=> 'Right',
						'imgmar_bottom'	=> 'Bottom',
						'imgmar_left'	=> 'Left'
					),
					'id'				=> 'image_margin',
					'label'				=> __( 'Image Margin (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Image margin respectively for margin top, right, bottom and left. ', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0, 0, 0),
					'max'				=> array(500, 500, 500, 500),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['imtop']) ? $teamCombine['imtop'] : '0',
											isset($teamCombine['imrgt']) ? $teamCombine['imrgt'] : '0',
											isset($teamCombine['imbtm']) ? $teamCombine['imbtm'] : '0',
											isset($teamCombine['imlft']) ? $teamCombine['imlft'] : '0'
										),
					'tips'				=> array(),
					'badge'				=> ''
				)
			);
			$layout_fields = array(
				array(
					'id'				=> '',
					'label'				=> __( 'Team Layout Type', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'    			=> 'marked_layout',
					'label'   			=> __( 'Layout Type for the Team', 'rwc-team-members' ),
					'desc'   			=> __( 'Select the Layout Type as per image <b>grid</b>, <b>slider</b>, <b>popup</b> and <b>expand</b>.', 'rwc-team-members' ),
					'type'    			=> 'radio',
					'value'         	=> isset($teamCombine['layout']) ? $teamCombine['layout'] : 'popup',
					'options' 			=> array(
							'grids'		=> 'Grids',
							'slide' 	=> 'Slider',
							'popup' 	=> 'Pop-up'
					),
					'img' 				=> true,
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> 'slider-option',
					'type'				=> 'section',
					'html'				=> 'div',
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Slider Properties', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( 'We offer an extensive range of options that empower you to fine-tune the carousel\'s behavior and appearance according to your team member\'s preferences. Below, we present some commonly used configurations that can be customized to suit your needs', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'slider_details',
					'label'				=> __( 'Enable Slider Member Details', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you want to show member details of each carousel.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['slmbd']) ? $teamCombine['slmbd'] : 'no',
					'tips'				=> __( 'When using the slider layout, you can include modal options to view detailed information about team members. Enable it to show member details. Disable to prevent displaying member details when clicking on their thumbnail.', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'slider_tshow',
					'label'				=> __( 'Slides to Show', 'rwc-team-members' ),
					'desc'				=> __( 'Number of slides to show at a time.', 'rwc-team-members' ),
					'placeholder'		=> __( '1', 'rwc-team-members' ),
					'min'				=> 1,
					'max'				=> 10,
					'step'				=> 'any',
					'type'				=> 'number',
					'value'				=> isset($teamCombine['stshow']) ? $teamCombine['stshow'] : '1',
					'tips'				=> __( 'By default, \'Slides to Show\' is set to 1, meaning that only one item will be visible at a time. However, you can adjust the value of \'Slides to Show\' to display multiple items simultaneously.', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'slider_tsped',
					'label'				=> __( 'Transition Speed', 'rwc-team-members' ),
					'desc'				=> __( 'The duration (in milliseconds) of the transition animation between carousel slides.', 'rwc-team-members' ),
					'placeholder'		=> __( '300', 'rwc-team-members' ),
					'min'				=> 100,
					'max'				=> 10000,
					'step'				=> 'any',
					'type'				=> 'number',
					'value'				=> isset($teamCombine['stsped']) ? $teamCombine['stsped'] : '300',
					'tips'				=> __( 'By default, speed is set to 300, which means the transition animation lasts 300 milliseconds (or 0.3 seconds). You can adjust this value to make the transition faster or slower according to your desired visual effect and timing.', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'slider_arrows',
					'label'				=> __( 'Enable Next/Prev Arrows', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you want to show Next/Prev arrows.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['slarow']) ? $teamCombine['slarow'] : 'yes',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'slider_aplay',
					'label'				=> __( 'Enable Auto Play', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you want to enables auto play of slides.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['saplay']) ? $teamCombine['saplay'] : 'no',
					'tips'				=> __( 'Please note that enabling auto-play mode will disable member details in the left popup. Proceed with this option only if you don\'t require member details in popup mode.', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> 'slider-apspeed',
					'type'				=> 'section',
					'html'				=> 'div',
				),
				array(
					'name'				=> 'sldaplay_speed',
					'label'				=> __( 'Auto play Change Interval', 'rwc-team-members' ),
					'desc'				=> __( 'Autoplay Speed in milliseconds. 1 second = 1000 milliseconds.', 'rwc-team-members' ),
					'placeholder'		=> __( '3000', 'rwc-team-members' ),
					'min'				=> 100,
					'max'				=> 10000,
					'step'				=> 'any',
					'type'				=> 'number',
					'value'				=> isset($teamCombine['sapspd']) ? $teamCombine['sapspd'] : '3000',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'type'				=> 'sectionend',
					'html'				=> 'div',
				),
				array(
					'name'				=> 'slider_vwdth',
					'label'				=> __( 'Enable Variable Width', 'rwc-team-members' ),
					'desc'				=> __( 'Enables automatic slide width calculation.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['svwdth']) ? $teamCombine['svwdth'] : 'no',
					'tips'				=> __( 'With \'Variable Width\' set to true, each item within the carousel can have its own unique width. This is useful when you have items with varying content lengths or when you want to create a visually dynamic carousel layout. Additionally, when using \'Variable Width\', other options like \'Slides to Show\' and \'Slides to Scroll\' may behave differently, as the number of items visible or scrolled might not correspond to a fixed number due to the variable widths of the items.', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'slider_zindx',
					'label'				=> __( 'zIndex Values for Slides', 'rwc-team-members' ),
					'desc'				=> __( 'Set the zIndex values for slides, useful for IE9 and lower.', 'rwc-team-members' ),
					'placeholder'		=> __( '1000', 'rwc-team-members' ),
					'min'				=> 0,
					'max'				=> 10000,
					'step'				=> 'any',
					'type'				=> 'number',
					'value'				=> isset($teamCombine['szindx']) ? $teamCombine['szindx'] : '1000',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'type'				=> 'sectionend',
					'html'				=> 'div',
				),
				array(
					'name'				=> 'popup_choice',
					'label'				=> __( 'Disable Pop-up', 'rwc-team-members' ),
					'desc'				=> __( 'Mark the checkbox if you don\'t want to show pop-up', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['popdir']) ? $teamCombine['popdir'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Team Layout Style', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'    			=> 'layout_style',
					'label'   			=> __( 'Team Showcase Style', 'rwc-team-members' ),
					'desc'   			=> __( 'Present your team elegantly with 28 visually appealing CSS formats.', 'rwc-team-members' ),
					'type'    			=> 'radio',
					'value'         	=> isset($teamCombine['lstyle']) ? $teamCombine['lstyle'] : 'lout1',
					'options' 			=> array(
							'lout1'		=> 'Layout 1',
							'lout2' 	=> 'Layout 2',	// 5
							'lout3' 	=> 'Layout 3',	// 9
							'lout4' 	=> 'Layout 4',	// 10
							'lout5' 	=> 'Layout 5',	// 11
							'lout6' 	=> 'Layout 6',	// 21
							'lout7' 	=> 'Layout 7',	// 22
							'lout8' 	=> 'Layout 8',	// 24
							'lout9' 	=> 'Layout 9',	// 25
							'lout10' 	=> 'Layout 10'	// 26
					),
					'img' 				=> true,
					'tips'				=> __( 'Team Showcase Snippets are predefined CSS styles for the RWC Team Members plugin, designed to display team members in various visually appealing formats on the front-end of your website.', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Team Layout Image Hover Effect', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'allow_imgeff',
					'label'				=> __( 'Enable Image Hover Effect', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you want an image hover effect when mouseover on thumbnail images.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['enihef']) ? $teamCombine['enihef'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				)
			);
			$filter_fields = array(
				array(
					'id'				=> '',
					'label'				=> __( 'Team Filter Alignment', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'enable_filter',
					'label'				=> __( 'Enable Filter Navigation', 'rwc-team-members' ),
					'desc'				=> __( 'Display the filter navigation based on the <b>member\'s department</b>.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['enfilt']) ? $teamCombine['enfilt'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> 'filter-nav',
					'type'				=> 'section',
					'html'				=> 'div',
				),
				array(
					'name'				=> 'filter_align',
					'label'				=> __( 'Filter Alignment', 'rwc-team-members' ),
					'desc'				=> __( 'Set the navigation alignment for the team member.', 'rwc-team-members' ),
					'type'				=> 'select',
					'value'				=> isset($teamCombine['flaln']) ? $teamCombine['flaln'] : 'center',
					'options'			=> array(
						'left'			=> 'Left',
						'center'		=> 'Center',
						'right'			=> 'Right'
					),
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'filmar_top',
					'label'				=> __( 'Margin Top (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter top space of filter navigation menu.', 'rwc-team-members' ),
					'placeholder'		=> __( '20', 'rwc-team-members' ),
					'min'				=> 0,
					'max'				=> 300,
					'step'				=> 1,
					'type'				=> 'number',
					'value'				=> isset($teamCombine['fmtop']) ? $teamCombine['fmtop'] : '0',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'filmar_bottom',
					'label'				=> __( 'Margin Bottom (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter bottom space of filter navigation menu.', 'rwc-team-members' ),
					'placeholder'		=> __( '20', 'rwc-team-members' ),
					'min'				=> 0,
					'max'				=> 300,
					'step'				=> 1,
					'type'				=> 'number',
					'value'				=> isset($teamCombine['fmbtm']) ? $teamCombine['fmbtm'] : '0',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'type'				=> 'sectionend',
					'html'				=> 'div',
				)
			);
			$thumbnail_fields = array(
				array(
					'id'				=> '',
					'label'				=> __( 'Member Thumbnail Alignment', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'thumbnail_align',
					'label'				=> __( 'Thumbnail Alignment', 'rwc-team-members' ),
					'desc'				=> __( 'Set alignment of the member\'s thumbnail.', 'rwc-team-members' ),
					'type'				=> 'select',
					'value'				=> isset($teamCombine['thmaln']) ? $teamCombine['thmaln'] : 'center',
					'options'			=> array(
						'left'			=> 'Left',
						'right'			=> 'Right',
						'center'		=> 'Center'
					),
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'thumbcap_align',
					'label'				=> __( 'Thumbnail Text Alignment', 'rwc-team-members' ),
					'desc'				=> __( 'Set text align for thumbnail content details.', 'rwc-team-members' ),
					'type'				=> 'select',
					'value'				=> isset($teamCombine['thtaln']) ? $teamCombine['thtaln'] : 'center',
					'options'			=> array(
						'left'			=> 'Left',
						'right'			=> 'Right',
						'center'		=> 'Center',
						'justify'		=> 'Justify'
					),
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Thumbnail Scale &amp; Shadow', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'enlarge_column',
					'label'				=> __( 'Enlarge Thumbnail on Hover', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you want to enlarge member thumbnail on mouseover.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['encol']) ? $teamCombine['encol'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				)
			);
			$display_fields = array(
				array(
					'id'				=> '',
					'label'				=> __( 'Thumbnail Items Show/Hide', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'thumb_desig',
					'label'				=> __( 'Hide Thumbnail Designation', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you don\'t want to show designation on thumbnail.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['thdsg']) ? $teamCombine['thdsg'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'thumb_depart',
					'label'				=> __( 'Hide Thumbnail Department', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you don\'t want to show department on thumbnail.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['thdpt']) ? $teamCombine['thdpt'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'thumb_smicon',
					'label'				=> __( 'Hide Thumbnail Social Icon', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you don\'t want to show social media icons on thumbnail.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['thsmic']) ? $teamCombine['thsmic'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Member Details Show/Hide', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'member_desig',
					'label'				=> __( 'Hide Member Designation', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you don\'t want to show designation on member details.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['mbdsg']) ? $teamCombine['mbdsg'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'member_depart',
					'label'				=> __( 'Hide Member Department', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you don\'t want to show department on member details.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['mbdpt']) ? $teamCombine['mbdpt'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'member_smicon',
					'label'				=> __( 'Hide Member Social Icon', 'rwc-team-members' ),
					'desc'				=> __( 'Check if you don\'t want to show social media icons on member details.', 'rwc-team-members' ),
					'type'				=> 'checkbox',
					'value'				=> isset($teamCombine['mbsmic']) ? $teamCombine['mbsmic'] : 'no',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
			);
			$font_fields = array(
				array(
					'id'				=> '',
					'label'				=> __( 'Font Family', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( 'In order to use a font family on your website, you need to make sure that the CSS file for that font is included and called properly in your website\'s HTML code. In this plugin you\'ll get an option in the global settings to link a font\'s CSS file or to use the plugin\'s default font. This can be a convenient way to add custom fonts to your website without having to manually add the CSS code yourself.', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'primary_font',
					'label'				=> __( 'Primary Font Family', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the primary font family here.', 'rwc-team-members' ),
					'placeholder'		=> __( '\'Roboto Condensed\', serif', 'rwc-team-members' ),
					'type'				=> 'text',
					'value'				=> isset($teamCombine['pgfont']) ? $teamCombine['pgfont'] : '',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'secondary_font',
					'label'				=> __( 'Secondary Font Family', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the secondary font family here.', 'rwc-team-members' ),
					'placeholder'		=> __( '\'Open Sans\', sans-serif', 'rwc-team-members' ),
					'type'				=> 'text',
					'value'				=> isset($teamCombine['sgfont']) ? $teamCombine['sgfont'] : '',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Font Size', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( 'Specify the font sizes for various attributes of the members. You can enter the font size for desktop, tablet, and mobile views individually. The desktop setting will be effective for screen widths of 993px and above. The tablet view will target devices with a width range of 481px to 992px. The mobile view, on the other hand, will apply to screen widths of 480px and below.', 'rwc-team-members' ),
					'html'				=> 'h3',
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Team Name &amp; Filter Navigation', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> array(
						'tm_name_font'	=> 'Desktop',
						'tab_tm_name'	=> 'Tab',
						'mob_tm_name'	=> 'Mobile'
					),
					'id'				=> 'tm_name_font',
					'label'				=> __( 'Team Name Font Size (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the font size of the team name for desktop, tab and mobile view.', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0, 0),
					'max'				=> array(200, 200, 200),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['tnfont']) ? $teamCombine['tnfont'] : '',
											isset($teamCombine['tnftab']) ? $teamCombine['tnftab'] : '',
											isset($teamCombine['tnfmob']) ? $teamCombine['tnfmob'] : ''
										),
					'tips'				=> array(),
					'badge'				=> ''
				),
				array(
					'name'				=> array(
						'nav_font_size'	=> 'Desktop',
						'nav_font_tab'	=> 'Tab',
						'nav_font_mob'	=> 'Mobile'
					),
					'id'				=> 'nav_font_size',
					'label'				=> __( 'Navigation Font Size (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the font size of the filter navigation for desktop, tab and mobile view.', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0, 0),
					'max'				=> array(200, 200, 200),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['navfsz']) ? $teamCombine['navfsz'] : '',
											isset($teamCombine['nvfntb']) ? $teamCombine['nvfntb'] : '',
											isset($teamCombine['nvfnmb']) ? $teamCombine['nvfnmb'] : ''
										),
					'tips'				=> array(),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Thumbnail Font Size', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> array(
						'mn_thumb_font'	=> 'Desktop',
						'mn_thmfnt_tab'	=> 'Tab',
						'mn_thmfnt_mob'	=> 'Mobile'
					),
					'id'				=> 'mn_thumb_font',
					'label'				=> __( 'Member Name Font Size (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the font size of the member name on the thumbnail for desktop, tab and mobile view.', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0, 0),
					'max'				=> array(200, 200, 200),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['mntfs']) ? $teamCombine['mntfs'] : '',
											isset($teamCombine['mntft']) ? $teamCombine['mntft'] : '',
											isset($teamCombine['mntfm']) ? $teamCombine['mntfm'] : ''
										),
					'tips'				=> array(),
					'badge'				=> ''
				),
				array(
					'name'				=> array(
						'dp_thumb_font'	=> 'Desktop',
						'dp_thmfnt_tab'	=> 'Tab',
						'dp_thmfnt_mob'	=> 'Mobile'
					),
					'id'				=> 'dp_thumb_font',
					'label'				=> __( 'Department Font Size (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the font size of the member department on the thumbnail for desktop, tab and mobile view.', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0, 0),
					'max'				=> array(200, 200, 200),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['dptfs']) ? $teamCombine['dptfs'] : '',
											isset($teamCombine['dptft']) ? $teamCombine['dptft'] : '',
											isset($teamCombine['dptfm']) ? $teamCombine['dptfm'] : ''
										),
					'tips'				=> array(),
					'badge'				=> ''
				),
				array(
					'name'				=> array(
						'dg_thumb_font'	=> 'Desktop',
						'dg_thmfnt_tab'	=> 'Tab',
						'dg_thmfnt_mob'	=> 'Mobile'
					),
					'id'				=> 'dg_thumb_font',
					'label'				=> __( 'Designation Font Size (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the font size of the member designation on the thumbnail for desktop, tab and mobile view.', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0, 0),
					'max'				=> array(200, 200, 200),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['dgnfs']) ? $teamCombine['dgnfs'] : '',
											isset($teamCombine['dgtft']) ? $teamCombine['dgtft'] : '',
											isset($teamCombine['dgtfm']) ? $teamCombine['dgtfm'] : ''
										),
					'tips'				=> array(),
					'badge'				=> ''
				),
				array(
					'name'				=> array(
						'bio_thumb_fnt'	=> 'Desktop',
						'bio_thmfn_tab'	=> 'Tab',
						'bio_thmfn_mob'	=> 'Mobile'
					),
					'id'				=> 'bio_thumb_fnt',
					'label'				=> __( 'Short Bio Font Size (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the font size of the member short bio on the thumbnail for desktop, tab and mobile view.', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0, 0),
					'max'				=> array(200, 200, 200),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['sbfsz']) ? $teamCombine['sbfsz'] : '',
											isset($teamCombine['sbfst']) ? $teamCombine['sbfst'] : '',
											isset($teamCombine['sbfsm']) ? $teamCombine['sbfsm'] : ''
										),
					'tips'				=> array(),
					'badge'				=> ''
				),
				array(
					'name'				=> array(
						'sm_thumb_font'	=> 'Desktop',
						'sm_thmfnt_tab'	=> 'Tab',
						'sm_thmfnt_mob'	=> 'Mobile'
					),
					'id'				=> 'sm_thumb_font',
					'label'				=> __( 'Social Icon Font Size (in px)', 'rwc-team-members' ),
					'desc'				=> __( 'Enter the font size of the social icon on the thumbnail for desktop, tab and mobile view.', 'rwc-team-members' ),
					'type'				=> 'numgroup',
					'min'				=> array(0, 0, 0),
					'max'				=> array(200, 200, 200),
					'step'				=> '1',
					'value'				=> array(
											isset($teamCombine['smifs']) ? $teamCombine['smifs'] : '',
											isset($teamCombine['smfst']) ? $teamCombine['smfst'] : '',
											isset($teamCombine['smfsm']) ? $teamCombine['smfsm'] : ''
										),
					'tips'				=> array(),
					'badge'				=> ''
				)
			);
			$color_fields = array(
				array(
					'id'				=> '',
					'label'				=> __( 'Team Name', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'team_name_color',
					'label'				=> __( 'Team Name Color', 'rwc-team-members' ),
					'desc'				=> sprintf( __( 'Set the font color of the <b>%s</b> name.', 'rwc-team-members' ), $our_team_name ),
					'placeholder'		=> __( '#000000', 'rwc-team-members' ),
					'type'				=> 'color',
					'value'				=> isset($teamCombine['tnmcl']) ? $teamCombine['tnmcl'] : '#000000',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'tmnm_mark_color',
					'label'				=> __( 'Team Name Marked Color', 'rwc-team-members' ),
					'desc'				=> sprintf( __( 'Set the each first letter font color of the <b>%s</b> name.', 'rwc-team-members' ), $our_team_name ),
					'placeholder'		=> __( '#ffc107', 'rwc-team-members' ),
					'type'				=> 'color',
					'value'				=> isset($teamCombine['tnflc']) ? $teamCombine['tnflc'] : '#ffc107',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'id'				=> '',
					'label'				=> __( 'Filter Navigation', 'rwc-team-members' ),
					'type'				=> 'title',
					'tips'				=> __( '', 'rwc-team-members' ),
					'html'				=> 'h4',
				),
				array(
					'name'				=> 'nav_bg_color',
					'label'				=> __( 'Navigation BG Color', 'rwc-team-members' ),
					'desc'				=> sprintf( __( 'Set the filter navigation background color of the <b>%s</b>.', 'rwc-team-members' ), $our_team_name ),
					'placeholder'		=> __( '#e9ecef', 'rwc-team-members' ),
					'type'				=> 'color',
					'value'				=> isset($teamCombine['nvbgcl']) ? $teamCombine['nvbgcl'] : '#e9ecef',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'nav_bg_active',
					'label'				=> __( 'Navigation Active BG Color', 'rwc-team-members' ),
					'desc'				=> sprintf( __( 'Set the filter navigation active background color of the <b>%s</b>.', 'rwc-team-members' ), $our_team_name ),
					'placeholder'		=> __( '#354664', 'rwc-team-members' ),
					'type'				=> 'color',
					'value'				=> isset($teamCombine['nbaccl']) ? $teamCombine['nbaccl'] : '#354664',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),	
				array(
					'name'				=> 'nav_font_color',
					'label'				=> __( 'Navigation Font Color', 'rwc-team-members' ),
					'desc'				=> sprintf( __( 'Set the filter navigation font color of the <b>%s</b>.', 'rwc-team-members' ), $our_team_name ),
					'placeholder'		=> __( '#1d233b', 'rwc-team-members' ),
					'type'				=> 'color',
					'value'				=> isset($teamCombine['nvfncl']) ? $teamCombine['nvfncl'] : '#1d233b',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
				array(
					'name'				=> 'nav_active_fnclr',
					'label'				=> __( 'Navigation Active Font Color', 'rwc-team-members' ),
					'desc'				=> sprintf( __( 'Set the filter navigation active font color of the <b>%s</b>.', 'rwc-team-members' ), $our_team_name ),
					'placeholder'		=> __( '#ffffff', 'rwc-team-members' ),
					'type'				=> 'color',
					'value'				=> isset($teamCombine['nfaccl']) ? $teamCombine['nfaccl'] : '#ffffff',
					'tips'				=> __( '', 'rwc-team-members' ),
					'badge'				=> ''
				),
			); ?>
			<input type="hidden" name="rwctm_process" value="process" />
			<input type="hidden" id="member-count" name="member_count" value="<?php echo esc_attr($mb_count); ?>" />
			<div id="teamcolumndiv">
				<div class="teamcolumnwrap">
					<h3><?php esc_html_e('Team Member Columns', 'rwc-team-members'); ?></h3>
					<div id="addButtons"><a href="#" class="button button-large" id="addMember"><?php esc_html_e('New Member', 'rwc-team-members'); ?></a></div>
					<div class="accordion-expand-holder">
						<button type="button" class="expand"><span class="dashicons dashicons-editor-expand"></span>&nbsp;<?php esc_html_e(' Expand all', 'rwc-team-members'); ?></button>
						<button type="button" class="collapse"><span class="dashicons dashicons-editor-contract"></span>&nbsp;<?php esc_html_e(' Collapse all', 'rwc-team-members'); ?></button>
					</div>
					<div id="sortable_column">
					<?php
					if(!empty($team_details)) {
						foreach($all_members as $option => $value) {
						$memberValue = get_option($value); ?>
						<!-- Start member_details -->
						<div id="rwctm-<?php echo esc_attr($ac); ?>" class="member_details">
							<h4 id="mcolumn<?php echo esc_attr($ac); ?>"><?php esc_html_e('Team Member ', 'rwc-team-members'); ?><?php echo esc_attr($ac); ?></h4>
							<?php if($memberValue['mdisp'] == 'show') { ?><span id="hideMemb<?php echo esc_attr($ac); ?>" class="column_hide"><span class="dashicons dashicons-fullscreen-alt"></span><?php } else { ?><span id="showMemb<?php echo esc_attr($ac); ?>" class="column_show"><span class="dashicons dashicons-fullscreen-exit-alt"></span><?php } ?><input type="hidden" name="hide_show[]" value="<?php echo esc_attr($memberValue['mdisp']); ?>" /></span>
							<span id="delMember"><span class="dashicons dashicons-trash"></span></span>
							<div id="accordion<?php echo esc_attr($ac); ?>" class="column_container">
								<h3 class="mtitle"><?php esc_html_e('Team Member Details', 'rwc-team-members'); ?></h3>
								<div class="element-input">
									<h4><?php esc_html_e('Member Name', 'rwc-team-members'); ?></h4>
									<label class="input-title"><?php esc_html_e('Member Name', 'rwc-team-members'); ?></label>
									<input type="text" name="member_name[]" class="medium" id="member_name" value="<?php echo esc_attr($memberValue['mname']); ?>" placeholder="John Doe" /><hr />
									<h4><?php esc_html_e('Member Detail Bio', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Add the Detail bio of the member it will show on the detail popup.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></h4><?php
									if($editor == 'wysiwyg') :
										$editor_settings = array(
											'teeny'         => TRUE,
											'tinymce'       => TRUE,
											'textarea_name' => 'member_desc[]',
											'textarea_rows' => 15,
											'editor_class'	=> 'rwctm-wp-editor',
											'media_buttons' => FALSE,
										);
										echo rwctm_get_wp_editor('teamcustom'.$ed, trim(html_entity_decode(wp_kses_post($memberValue['mdesc']))), $editor_settings);
										$ed++; ?>
										<div class="rwctm-editor-bottom"></div><?php
									else: ?>
										<textarea name="member_desc[]" class="medium" id="member_desc" cols="27" rows="10" placeholder="<?php esc_html_e('Enter Detail Bio', 'rwc-team-members'); ?>"><?php echo esc_textarea($memberValue['mdesc']); ?></textarea><?php
									endif; ?><hr />
									<h4><?php esc_html_e('Member Short Bio', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Enter a Short Description of the member it will show on the list view page.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></h4>
									<textarea name="short_bio[]" class="medium" id="short_bio" cols="27" rows="3" placeholder="<?php esc_html_e('Write Some Short Bio', 'rwc-team-members'); ?>"><?php echo esc_textarea($memberValue['msbio']); ?></textarea><hr />
									<h4><?php esc_html_e('Member Tag Line', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Enter the member tag line, it will show on the detail popup.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></h4>
									<textarea name="tag_line[]" class="medium" id="tag_line" cols="27" rows="2" placeholder="<?php esc_html_e('Enter Tag Line', 'rwc-team-members'); ?>"><?php echo esc_textarea($memberValue['tgline']); ?></textarea><hr />
									<h4><?php esc_html_e('Member Position', 'rwc-team-members'); ?></h4><?php
									if($activity_option) { ?>
										<label class="input-title mb-section"><?php esc_html_e('Department', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('By associating members with specific departments, you can easily filter and group members based on the department they belong to. For example, in a company, you might have departments such as \'Marketing\', \'Sales\', \'Engineering\', and \'Operations\'. By assigning employees to these departments, you could easily filter and group employees based on their department.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></label>
										<select id="memb-section" name="memb_department_<?php echo esc_attr($memberValue['mop']); ?>[]" multiple><?php
										for($i = 1; $i <= $activityNum; $i++) {
											if($activity_option['atype'.$i] == 'sector') {
												$departments = explode(',', $activity_option["aname".$i]);
												$currentdept = explode(',', $memberValue['mdept']);
												foreach($departments as $d_name) {
													$d_value = strtolower($d_name);
													echo '<option value="'.esc_attr($d_value).'"';if(in_array($d_value, $currentdept)) {echo ' selected';}echo '>'.esc_html($d_name).'</option>';
												}
											} $sm++;
										} $sm = 1; ?>
										</select><?php
									} else { printf(__('<label class="input-title">There are no departments!</label>', 'rwc-team-members')); }
									if($activity_option) { ?>
										<label class="input-title mb-status"><?php esc_html_e('Designation', 'rwc-team-members'); ?></label>
										<select id="memb-status" name="memb_designation_<?php echo esc_attr($memberValue['mop']); ?>[]" multiple><?php
										for($i = 1; $i <= $activityNum; $i++) {
											if($activity_option['atype'.$i] == 'status') {
												$positions = explode(',', $activity_option["aname".$i]);
												$currentpos = explode(',', $memberValue['mdsgn']);
												foreach($positions as $p_name) {
													$s_value = strtolower($p_name);
													echo '<option value="'.esc_attr($s_value).'"';if(in_array($s_value, $currentpos)) {echo ' selected';}echo '>'.esc_html($p_name).'</option>';
												}
											} $sm++;
										} $sm = 1; ?>
										</select><?php
									} else { printf(__('<label class="input-title">There are no designations!</label>', 'rwc-team-members')); } ?>
									<label class="input-title"><?php esc_html_e('Experience', 'rwc-team-members'); ?></label>
									<input name="memb_experience[]" type="text" class="medium" value="<?php echo esc_attr($memberValue['mexpn']); ?>" placeholder="<?php esc_attr_e('5 Years', 'rwc-team-members'); ?>" /><hr>
									<h4><?php esc_html_e('Member Info', 'rwc-team-members'); ?></h4>
									<label class="input-title"><?php esc_html_e('Email', 'rwc-team-members'); ?></label>
									<input name="memb_email[]" type="text" class="medium" value="<?php echo esc_html($memberValue['email']); ?>" placeholder="<?php esc_attr_e('example@gmail.com', 'rwc-team-members'); ?>" />
									<label class="input-title"><?php esc_html_e('Telephone', 'rwc-team-members'); ?></label>
									<input name="tele_code[]" type="text" class="in_small" value="<?php echo esc_attr($memberValue['tcode']); ?>" />&nbsp;-&nbsp;<input name="memb_telephone[]" type="number" class="in_small" value="<?php echo esc_attr($memberValue['mtele']); ?>" placeholder="<?php esc_attr_e('1234567890', 'rwc-team-members'); ?>" />
									<label class="input-title"><?php esc_html_e('Mobile', 'rwc-team-members'); ?></label>
									<input name="mobile_code[]" type="text" class="in_small" value="<?php echo esc_attr($memberValue['mcode']); ?>" />&nbsp;-&nbsp;<input name="memb_mobile[]" type="number" class="in_small" value="<?php echo esc_attr($memberValue['mmobe']); ?>" placeholder="<?php esc_attr_e('1234567890', 'rwc-team-members'); ?>" />
									<label class="input-title"><?php esc_html_e('Fax', 'rwc-team-members'); ?></label>
									<input name="fax_code[]" type="text" class="in_small" value="<?php echo esc_attr($memberValue['fcode']); ?>" />&nbsp;-&nbsp;<input name="memb_fax[]" type="number" class="in_small" value="<?php echo esc_attr($memberValue['mbfax']); ?>" placeholder="<?php esc_attr_e('1234567', 'rwc-team-members'); ?>" />
									<label class="input-title"><?php esc_html_e('Location', 'rwc-team-members'); ?></label>
									<input name="memb_location[]" id="memb_location" type="text" class="medium" value="<?php echo esc_attr($memberValue['mlocn']); ?>" />
									<label class="input-title"><?php esc_html_e('Personal Web URL', 'rwc-team-members'); ?></label>
									<input type="text" name="memb_weburl[]" class="medium" id="memb_weburl" value="<?php echo esc_url($memberValue['mwurl']); ?>" placeholder="<?php esc_attr_e('e.g. example.com', 'rwc-team-members'); ?>" /><hr />
									<h4><?php esc_html_e('Member Social Link (Only ID)', 'rwc-team-members'); ?></h4><?php
									if($activity_option) {
										for($i = 1; $i <= $activityNum; $i++) {
											if(isset($memberValue['aname'.$sm])) {
												$s_media = $memberValue['aname'.$sm];
												$a_link = esc_url($activity_option['alink'.$sm]) . '/';
											}
											if($activity_option['atype'.$i] == 'social') { ?>
												<label class="input-title double-text"><?php echo esc_html($activity_option['aname' . $i]); ?></label>
												<input type="text" class="medium in_double_one" name="activity_link[]" id="activity_link" value="<?php echo esc_url($a_link); ?>" placeholder="<?php esc_attr_e('Social Link', 'rwc-team-members'); ?>" disabled /><input name="activity_id[]" type="text" class="medium in_double_two" value="<?php echo esc_attr($s_media); ?>" placeholder="<?php esc_attr_e('Social ID', 'rwc-team-members'); ?>" /><?php
											}
											$sm++;
										}
										$sm = 1;
									} else {
										printf(__('<label class="input-title">There are no social activities!</label>', 'rwc-team-members'));
									} ?><hr />
									<h4><?php esc_html_e('Member Ribbon', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Enter ribbon text to make current member more attractive, like \'best\', \'new\', \'hot\' etc.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></h4>
									<label class="input-title"><?php esc_html_e('Ribbon Text', 'rwc-team-members'); ?></label>
									<input type="text" name="ribbon_text[]" class="medium" id="ribbon_text" value="<?php echo esc_attr($memberValue['rtext']); ?>" placeholder="<?php esc_html_e('e.g. Best', 'rwc-team-members'); ?>" />
								</div>
								<h3 class="mtitle"><?php esc_html_e('Team Member Colors', 'rwc-team-members'); ?></h3>
								<div class="element-input">
									<table>
										<!--Background Color -->
										<tr class="team-header">
											<td colspan="2"><?php esc_html_e('Background Colors', 'rwc-team-members'); ?></td>
										</tr>
										<tr class="team-input">
											<th><label class="input-title"><?php esc_html_e('Thumbnail Column BG', 'rwc-team-members'); ?></label></th>
											<td><input type="text" name="team_bg[]" class="team_bg" id="team_bg" value="<?php echo esc_attr($memberValue['mbgcl']); ?>" /></td>
										</tr>
										<tr class="team-input">
											<th><label class="input-title"><?php esc_html_e('Thumbnail Ribbon BG', 'rwc-team-members'); ?></label></th>
											<td><input type="text" name="ribbon_bg[]" class="ribbon_bg" id="ribbon_bg" value="<?php echo esc_attr($memberValue['rbncl']); ?>" /></td>
										</tr>
										<!--Social Color -->
										<tr class="team-header">
											<td colspan="2"><?php esc_html_e('Social Media Colors', 'rwc-team-members'); ?></td>
										</tr><?php
										if($activity_option) {
											for($i = 1; $i <= $activityNum; $i++) {
												if(isset($memberValue['aname'.$sm])) {
													$s_color = isset($memberValue['acolor'.$sm]) ? $memberValue['acolor'.$sm] : '';
												}
												if($activity_option['atype'.$i] == 'social') { ?>
												<tr class="team-input">
													<th><label class="input-title"><?php echo esc_attr($activity_option['aname'.$i]).' Font Color'; ?></label></th>
													<td><input type="text" name="sm_fn_color[]" class="sm_fn_color" id="social_color_<?php echo esc_attr($smc); ?>" value="<?php echo esc_attr($s_color); ?>" data-id="<?php echo esc_attr($smc); ?>" /></td>
												</tr><?php
												$smc++;
												} $sm++;
											} $sm = 1;
										} ?>
										<tr class="team-input">
											<th><label class="input-title"><?php esc_html_e('Social Icon Hover Color', 'rwc-team-members'); ?></label></th>
											<td><input type="text" name="sm_hover_colo[]" class="sm_hover_colo" id="sm_hover_colo" value="<?php echo esc_attr($memberValue['smhvr']); ?>" /></td>
										</tr>
									</table>
								</div>
								<h3 class="mtitle"><?php esc_html_e('Team Member Image', 'rwc-team-members'); ?></h3>
								<div class="element-input">
									<h4><?php esc_html_e('Member Image', 'rwc-team-members'); ?><a href="#" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Enter the image location or upload an image from your computer. If Member Image is enabled in General Settings under Team Member Settings, member image will be displayed and it is recommended that you set the image width before adding or updating the member image.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></h4>
									<div class="form-group rwctm-uploader">
										<label for="upload image" class="input-image"><?php esc_html_e('Upload Image', 'rwc-team-members'); ?></label>
										<input type="text" name="image_path[]" class="medium" value="<?php echo esc_url($memberValue['mbimg']); ?>" id="image_path" placeholder="<?php esc_html_e('Enter a URL / upload an image', 'rwc-team-members'); ?>">
									</div>
									<input type="hidden" name="hidden_path[]" value="<?php echo esc_url($memberValue['mbimg']); ?>" />
									<div id="show_upload_preview">
										<?php if(!empty($memberValue['mbimg'])) { ?>
											<label class="input-image"><?php esc_html_e('Preview', 'rwc-team-members'); ?></label>
											<img src="<?php echo esc_url($memberValue['mbimg']); ?>" alt="package image" class="preview_image">
											<span id="remove_image"></span>
										<?php } ?>
									</div><hr />
								</div>
								<input type="hidden" id="member-options<?php echo esc_attr($ac); ?>" name="team_members[]" value="<?php echo esc_attr($value); ?>" />
								<input type="hidden" id="member-id" name="member_id[]" value="<?php echo esc_attr($memberValue['mop']); ?>" />
								<input type="hidden" id="order-id" name="order_id[]" value="<?php echo esc_attr($memberValue['order']); ?>" />
							</div>	<!-- End of column_container -->
						</div>	<!-- End of member_details -->
					<?php
							$sm = 1; $ac++;
						}
					} else esc_html_e('No Members yet!', 'rwc-team-members'); ?>
					</div>	<!--//sortable_column -->
				</div>	<!--//teamcolumnwrap -->
			</div>	<!--//teamcolumndiv -->
			<div class="rwctm-clear"></div>
			<div id="settingcommon">
				<div id="rwctm-tabs">
					<span class="cs-inactive dashicons dashicons-arrow-up-alt2"></span>
					<span class="cs-active dashicons dashicons-arrow-down-alt2"></span>
					<ul>
						<li><a href="#general"><?php esc_html_e('General', 'rwc-team-members'); ?></a></li>
						<li><a href="#media"><?php esc_html_e('Media', 'rwc-team-members'); ?></a></li>
						<li><a href="#structure"><?php esc_html_e('Structure', 'rwc-team-members'); ?></a></li>
						<li><a href="#layout"><?php esc_html_e('Layout', 'rwc-team-members'); ?></a></li>
						<li><a href="#filter"><?php esc_html_e('Filter', 'rwc-team-members'); ?></a></li>
						<li><a href="#thumbnail"><?php esc_html_e('Thumbnail', 'rwc-team-members'); ?></a></li>
						<li><a href="#display"><?php esc_html_e('Show/Hide', 'rwc-team-members'); ?></a></li>
						<li><a href="#fonts"><?php esc_html_e('Font', 'rwc-team-members'); ?></a></li>
						<li><a href="#colors"><?php esc_html_e('Colors', 'rwc-team-members'); ?></a></li>
					</ul>
					<div class="common-settings"><?php
						// Note: The function rwctm_get_team_options() returns HTML content that is generated by the plugin
						// and does not contain user input, so escaping is not required in this context. ?>
						<!--General-->
						<div id="general" class="team-input">
							<h3><?php esc_html_e('General Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($general_fields); ?>
						</div>
						<!--//General-->
						<!--Media-->
						<div id="media" class="team-input">
							<h3><?php esc_html_e('Media Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($media_fields); ?>
						</div>
						<!--//Media-->
						<!--Structure-->
						<div id="structure" class="team-input">
							<h3><?php esc_html_e('Structure Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($structure_fields); ?>
						</div>
						<!--//Structure-->
						<!--Layout-->
						<div id="layout" class="team-input">
							<h3><?php esc_html_e('Layout Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($layout_fields); ?>
						</div>
						<!--//Layout-->
						<!--Filter-->
						<div id="filter" class="team-input">
							<h3><?php esc_html_e('Filter Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($filter_fields); ?>
						</div>
						<!--//Filter-->
						<!--Thumbnail-->
						<div id="thumbnail" class="team-input">
							<h3><?php esc_html_e('Thumbnail Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($thumbnail_fields); ?>
						</div>
						<!--//Thumbnail-->
						<!--Display-->
						<div id="display" class="team-input">
							<h3><?php esc_html_e('Display Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($display_fields); ?>
						</div>
						<!--//Display-->
						<!--Font-->
						<div id="fonts" class="team-input">
							<h3><?php esc_html_e('Font Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($font_fields); ?>
						</div>
						<!--//Font-->
						<!--Color-->
						<div id="colors" class="team-input">
							<h3><?php esc_html_e('Color Setup', 'rwc-team-members'); ?></h3>
							<?php echo rwctm_get_team_options($color_fields); ?>
						</div>
						<!--//Color-->
					</div>
				</div>
				<!--//tabs-->
			</div>
			<!--//settingcommon-->
			<div class="rwctm-clear"></div>
			<input type="hidden" id="submitted" name="submitted" value="<?php if(isset($teamCombine['subfm']) && $teamCombine['subfm']) { echo esc_attr($teamCombine['subfm']); } else { echo 'no'; } ?>" />
			<input type="hidden" name="our_team" value="<?php echo esc_attr($our_team); ?>" />
			<input type="hidden" name="checkbox_value" value="<?php echo esc_attr($checkValue); ?>" />
			<input type="hidden" name="template" value="<?php echo esc_attr($template); ?>" />
			<input type="hidden" name="action" value="rwctm_set_team_member_options">
			<input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>">
			<button type="submit" id="rwctm_save" class="button-primary"><?php esc_html_e('Save Members', 'rwc-team-members'); ?></button><?php
		}
		wp_die();
	}
}
add_action( 'wp_ajax_nopriv_rwctm_process_team_members', 'rwctm_process_team_members' );
add_action( 'wp_ajax_rwctm_process_team_members', 'rwctm_process_team_members' );
