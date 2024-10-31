<?php
/**
 * Global Settings Handling
 *
 * This file contains functions to manage global options, custom CSS, and import/export
 * team data in JSON format for the "RWC Team Members" plugin. These functions
 * provide administrators with tools to customize the plugin's behavior and appearance.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div id="rwctm_global_settings" class="wrap">
	<h2><?php esc_html_e( 'RWC Team Member Global Settings', 'rwc-team-members'); ?></h2><?php

	/* Calling a function to set RWC team global options. */
	if(isset($_POST['rwctm_global']) && $_POST['rwctm_global'] == "process") {
		if(wp_verify_nonce( $_POST['rwctm_action_nonce'], 'rwctm_action')) {
			if( isset( $_POST['rwctm_process'] ) ) {
				rwctm_set_global_options();
				printf('<div id="message" class="updated"><p><strong>%s</strong></p></div>', esc_html__('Global Settings Updated.', 'rwc-team-members'));
			}
			// Optimizing database by deleting unnecessary package options.
			if(isset($_POST['rwctm_optimize'])) {
				rwctm_unuseful_member_options();
				printf('<div id="message" class="updated"><p><strong>%s</strong></p></div>', esc_html__('Hurray! You don\'t have anymore unnecessary team options to clear!', 'rwc-team-members'));
			}
		} else {
			// Nonce verification failed, handle the error or redirect
			wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
		}
	}
	if(isset($_POST['rwctm_custom']) && $_POST['rwctm_custom'] == "custcss") {
		if(wp_verify_nonce( $_POST['rwctm_ccss_action_nonce'], 'rwctm_ccss_action' )) {
			if( isset( $_POST['rwctm_ccss'] ) ) {
				rwctm_set_global_options();
				printf('<div id="message" class="updated"><p><strong>%s</strong></p></div>', esc_html__('RWC Team Member Custom CSS Updated.', 'rwc-team-members'));
			}
		} else {
			// Nonce verification failed, handle the error or redirect
			wp_die(__('You do not have sufficient permissions to access this page, or the nonce verification failed.', 'rwc-team-members'));
		}
	} ?>
	<div id="rwctm-tabs" class="rwctm_global" style="visibility:hidden">
		<ul>
			<li><a href="#options"><?php esc_html_e('Options', 'rwc-team-members'); ?></a></li>
			<li><a href="#custcss"><?php esc_html_e('Custom Style', 'rwc-team-members'); ?></a></li>
			<li><a href="#export"><?php esc_html_e('Export Team', 'rwc-team-members'); ?></a></li>
			<li><a href="#import"><?php esc_html_e('Import Team', 'rwc-team-members'); ?></a></li>
		</ul>

		<!-- Get Global Options -->
		<div id="options">
			<h2><?php esc_html_e( 'Global Settings', 'rwc-team-members' ); ?></h2>
			<form id="rwctm_template_form" method="post" action=""><?php
				wp_nonce_field( 'rwctm_action', 'rwctm_action_nonce' );
				$rwctm_options = get_option('rwctm_global_options', 'default_value');

				// Sanitize the global options
				$rwctm_options = rwctm_sanitize_global_options($rwctm_options); ?>

				<input type="hidden" name="rwctm_global_options_nonce" value="<?php echo esc_attr( wp_create_nonce( 'rwctm_global_options_nonce' ) ); ?>" />
				<input type="hidden" name="rwctm_global" value="process" />
				<div class="rwctm_options">
					<label class="input-check"><?php esc_html_e('Enable WP Post Editor for HTML Feature Type', 'rwc-team-members'); ?><a href="<?php esc_url('#'); ?>" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Instead of worrying about the alignment and organization of text, quotes, and images, enable WP default post editor and you can focus on creating. Most of the users prefer to write on it.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></label>
					<label for="editor_choice" class="rwctm_global_check">
					<input type="checkbox" name="wp_post_editor" class="tickbox" id="wp_post_editor" value="yes" <?php if(isset($rwctm_options['wpedit']) && $rwctm_options['wpedit'] == "yes") echo "checked"; ?>>
					</label>
				</div>
				<div id="htmleditor" class="rwctm_options">
					<label class="input-check"><?php esc_html_e('Enable HTML Editor as the Default Post Editor', 'rwc-team-members'); ?><a href="<?php esc_url('#'); ?>" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('It is useful when you have only a few users and most of them agree to your choice to set HTML editor as the default post editor in WordPress. The advantage is that you will have the visual editor in the background which you can choose to use anytime.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></label>
					<label for="html_editor" class="rwctm_global_check">
					<input type="checkbox" name="html_editor" class="tickbox" id="html_editor" value="yes" <?php if(isset($rwctm_options['htmled']) && $rwctm_options['htmled'] == "yes") echo "checked"; ?>>
					</label>
				</div>
				<div class="rwctm_options">
					<label class="input-check"><?php esc_html_e('Enable Plugin Default Google Font', 'rwc-team-members'); ?>:</label>
					<label for="default_font" class="rwctm_global_check">
					<input type="checkbox" name="default_font" class="tickbox" id="default_font" value="yes" <?php if(isset($rwctm_options['gdfont']) && $rwctm_options['gdfont'] == "yes") echo('checked="checked"'); ?>/>
					</label>
				</div>
				<div id="user_choice_font">
					<div class="rwctm_options">
						<label class="input-title"><?php
							printf(__('
								Enter <a class="global-link" href="%1s" target="_blank">Google Fonts</a> URL link<a href="%2s" class="rwctm_tooltip dashicons dashicons-info" rel="If you want your website to stand out from the others, then using custom fonts like Google Fonts will get you there. Google Web Designer lets you select a font from a list of common fonts or from Google Fonts. You can use hundreds of open-source fonts available through Google Fonts at no cost."><span class="rwctm_ttarrow"></span></a><span class="dashicons dashicons-megaphone"></span><a href="%3s" class="rwctm_tooltip rwctm_note" rel="Go to Google Fonts. Search for your preferred font that you\'d like to use on your team and click on it from the list. Click on the \'Get Font\' button at the top right corner. Repeat this process to select as many fonts as you want. Finally, click on \'Get Embed Code\' from the right side of the screen. Configure the font (style, weight, etc.) from the left panel. From the right panel, copy the URL under \'Embed code in the <head> of your html\'. Copy the URL within the href attribute (e.g., //fonts.googleapis.com/css2?family=Open+Sans&family=Roboto:wght@400;700&display=swap). In your plugin\'s settings, paste the copied URL into the \'Enter Google Fonts URL link\' text box. After that, under \'CSS class\' on the bottom, copy the font-family declaration (e.g., &quot;Roboto&quot;, sans-serif;). Paste the font-family declaration into the Primary or Secondary font family text box in the plugin\'s settings. That\'s all! Your chosen Google Fonts should now be integrated into your website through the plugin."><i class="attention" title="Add a font from Google Fonts"></i></a>', 'rwc-team-members'),
								esc_url("https://fonts.google.com/"),
								esc_url("#"),
								esc_url("#")
							); ?>
						</label>
						<input type="text" name="custom_font" class="medium" id="custom_font" value="<?php echo esc_attr(isset($rwctm_options['gcfont']) ? $rwctm_options['gcfont'] : ''); ?>" placeholder="<?php esc_attr_e('//fonts.googleapis.com/css?family=Roboto+Condensed:400,700', 'rwc-team-members'); ?>" />
					</div>
                    <div class="rwctm_options">
                        <label class="input-title"><?php esc_html_e('Enter Primary Font Family', 'rwc-team-members'); ?><a href="<?php esc_url('#'); ?>" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Each price table you create has it\'s own primary font family settings under \'RWC Team Members Settings &raquo; Font Settings\'. If you leave that blank, only then primary font family entered here will work.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></label>
                        <input type="text" name="primary_font" class="medium" id="primary_font" value="<?php echo esc_attr(isset($rwctm_options['prfont']) ? $rwctm_options['prfont'] : ''); ?>" placeholder="<?php esc_attr_e("'Roboto Condensed', serif", 'rwc-team-members'); ?>">
                    </div>
                    <div class="rwctm_options">
                        <label class="input-title"><?php esc_html_e('Enter Secondary Font Family', 'rwc-team-members'); ?><a href="<?php esc_url('#'); ?>" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('Each price table you create has it\'s own secondary font family settings under \'RWC Team Members Settings &raquo; Font Settings\'. If you leave that blank, only then secondary font family entered here will work.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></label>
                        <input type="text" name="secondary_font" class="medium" id="secondary_font" value="<?php echo esc_attr(isset($rwctm_options['scfont']) ? $rwctm_options['scfont'] : ''); ?>" placeholder="<?php esc_attr_e("'Open Sans', sans-serif", 'rwc-team-members'); ?>">
                    </div>
				</div>
				<div class="rwctm_options">
					<label class="input-check"><?php esc_html_e('Enable Dashicons in Theme', 'rwc-team-members'); ?>:</label>
					<label for="dashicons" class="rwctm_global_check">
					<input type="checkbox" name="dash_icons" class="tickbox" id="dash_icons" value="yes" <?php if(isset($rwctm_options['dsicon']) && $rwctm_options['dsicon'] == "yes") echo('checked="checked"'); ?>/>
					</label>
				</div>
				<div class="rwctm_options">
					<label class="input-check"><?php esc_html_e('Add View Port Meta in the Header', 'rwc-team-members'); ?>:</label>
					<label for="view_port" class="rwctm_global_check">
					<input type="checkbox" name="view_port" class="tickbox" id="view_port" value="yes" <?php if(isset($rwctm_options['vwport']) && $rwctm_options['vwport'] == "yes") echo('checked="checked"'); ?>/>
					</label>
				</div>
				<div class="rwctm_options">
					<label class="input-check"><?php esc_html_e('Clear Database on Delete', 'rwc-team-members'); ?>:<a href="<?php esc_url('#'); ?>" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('If you decide not to use the plugin anymore, you can leave this option checked. By doing this, all the data stored in the database will be deleted along with the deletion of the plugin.', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></label>
					<label for="clear_db" class="rwctm_global_check">
					<input type="checkbox" name="clear_db" class="tickbox" id="clear_db" value="yes" <?php if(isset($rwctm_options['clrdb']) && $rwctm_options['clrdb'] == "yes") echo('checked="checked"'); ?>/>
					</label>
				</div>
				<div class="rwctm_options">
					<label for="optimize_data" class="input-check"><?php esc_html_e('Remove Unnecessary Team Option', 'rwc-team-members'); ?>:<a href="<?php esc_url('#'); ?>" class="rwctm_tooltip dashicons dashicons-info" rel="<?php esc_html_e('While customizing the team, some unnecessary team options may be created, which can make the database heavier. By clicking on this button, without any hassle, unnecessary options can be removed from the database right away!', 'rwc-team-members'); ?>"><span class="rwctm_ttarrow"></span></a></label>
                	<input type="submit" id="rwctm_optimize" name="rwctm_optimize" class="button-secondary" value="<?php esc_html_e('Optimize RWC Team Members', 'rwc-team-members'); ?>">
					</label>
				</div><?php
				if(isset($rwctm_options) && $rwctm_options != '') { ?>
					<input type="submit" id="rwctm_process" name="rwctm_process" class="button-primary" value="<?php esc_attr_e('Update Changes', 'rwc-team-members'); ?>"><?php
				} else { ?>
					<input type="submit" id="rwctm_process" name="rwctm_process" class="button-primary" value="<?php esc_attr_e('Save Changes', 'rwc-team-members'); ?>"><?php
				} ?>
				<input type="hidden" name="rwctm_custom_css" value="<?php echo esc_textarea(isset($rwctm_options['rtmcss']) ? $rwctm_options['rtmcss'] : ''); ?>">
			</form>
		</div>

		<!-- Display Custom CSS Editor Box -->
		<div id="custcss">
			<form id="custom_css_form" method="post" action=""><?php
				wp_nonce_field( 'rwctm_ccss_action', 'rwctm_ccss_action_nonce' );
				$rwctm_options = get_option('rwctm_global_options', 'default_value');

				// Sanitize the global options
				$rwctm_options = rwctm_sanitize_global_options($rwctm_options); ?>

				<input type="hidden" name="rwctm_global_options_nonce" value="<?php echo esc_attr( wp_create_nonce( 'rwctm_global_options_nonce' ) ); ?>" />
				<input type="hidden" name="rwctm_custom" value="custcss">
                <div class="rwctm_custom_css">
                    <label class="label-check"><h2><?php esc_html_e( 'RWC Team Member Custom CSS', 'rwc-team-members' ); ?></h2></label>
                    <textarea id="rwctm-fancy-textarea" class="rwctm-wp-editor" name="rwctm_custom_css"><?php echo esc_textarea(isset($rwctm_options['rtmcss']) ? $rwctm_options['rtmcss'] : ''); ?></textarea>
                    <p><input type="submit" id="rwctm_ccss" name="rwctm_ccss"  class="button-primary" value="<?php esc_html_e( 'Save Changes', 'rwc-team-members' ) ?>"></p>
                    <p class="get_started"><?php esc_html_e('Please add all your custom CSS here and avoid modifying the core plugin CSS, since that\'ll make upgrading the plugin problematic. Your custom CSS will be loaded after the plugin\'s stylesheets, which means that your rules will take precedence. Just add your CSS here for what you want to change.', 'rwc-team-members'); ?></p>
                </div>
				<input type="hidden" name="wp_post_editor" value="<?php echo esc_attr($rwctm_options['wpedit']); ?>">
				<input type="hidden" name="html_editor" value="<?php echo esc_attr($rwctm_options['htmled']); ?>">
				<input type="hidden" name="default_font" value="<?php echo esc_attr($rwctm_options['gdfont']); ?>">
				<input type="hidden" name="custom_font" value="<?php echo esc_attr($rwctm_options['gcfont']); ?>">
				<input type="hidden" name="primary_font" value="<?php echo esc_attr($rwctm_options['prfont']); ?>">
				<input type="hidden" name="secondary_font" value="<?php echo esc_attr($rwctm_options['scfont']); ?>">
				<input type="hidden" name="font_awesome" value="<?php echo esc_attr($rwctm_options['fasome']); ?>">
				<input type="hidden" name="view_port" value="<?php echo esc_attr($rwctm_options['vwport']); ?>">
				<input type="hidden" name="clear_db" value="<?php echo esc_attr($rwctm_options['clrdb']); ?>">
			</form>
		</div>

		<!-- Export Team Data as JSON -->
		<div id="export"><?php
			$team_lists = get_option('rwctm_teamLists');
			$list_array = explode(', ', $team_lists);
			$temp_option = 'rwctm_teamLists, rwctm_teamIDs, rwctm_IDsSerial, rwctm_memberCount, ' . $team_lists;
			foreach($list_array as $list) {
				$team_options = get_option($list);
				$merge_option  = $temp_option . ', ' . $team_options . ', ' . $list . '_activity' . ', ' . $list . '_option';
				$temp_option = $merge_option;
			}
			if (!isset($_POST['rwctm_export'])) { ?>
                <div id="icon-tools" class="icon32"><br /></div>
                <h2><?php esc_html_e('Export', 'rwc-team-members') ?></h2>
                <p><?php printf(__('When you click <strong>Download Export File</strong> button, WordPress will generate a JSON file for you to save to your computer.', 'rwc-team-members')); ?></p>
                <p><?php printf(__('This backup file contains all the configuration and setting options of your teams. Note that it do <b>NOT</b> contain posts, pages, or any relevant data, just your all team options.', 'rwc-team-members')); ?></p>
                <p><?php esc_html_e('After exporting, you can either use the backup file to restore your settings on this site again or another WordPress site.', 'rwc-team-members') ?></p>
                <form method="post" id="export-rwctm">
                    <p class="submit">
                        <?php wp_nonce_field('rwctm-export'); ?>
                        <input type="submit" name="rwctm_export" id="rwctm_export" class="button button-primary" value="<?php esc_html_e('Download Export File', 'rwc-team-members') ?>" />
                    </p>
                </form><?php
			} elseif (check_admin_referer('rwctm-export')) {
				$blogname = str_replace(" ", "", get_option('blogname'));
				$date = date("m-d-Y");
				$json_name = $blogname."-".$date; // Namming the filename will be generated.
			
				$option_lists = explode(', ', $merge_option);
				$options = array();

				foreach($option_lists as $oplist) {
					$options[$oplist] = get_option($oplist);
				}
				$json_file = json_encode($options);
			
				ob_clean();
				echo $json_file;
				header("Content-Type: text/json; charset=" . get_option( 'blog_charset'));
				header("Content-Disposition: attachment; filename=$json_name.json");
				exit();
			} ?>
		</div>

		<!-- Import Team Data from JSON -->
		<div id="import">
            <div id="icon-tools" class="icon32"><br /></div>
            <h2><?php esc_html_e('Import', 'rwc-team-members') ?></h2><?php

			if (isset($_FILES['rwctm_import']) && check_admin_referer('rwctm-import')) {
				$file_error = $_FILES['rwctm_import']['error'];

				if ($file_error !== UPLOAD_ERR_OK) {
					wp_die("Error during file upload. Error code: $file_error");
				}

				$file_name = sanitize_file_name($_FILES['rwctm_import']['name']);
				$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
				$file_size = $_FILES['rwctm_import']['size'];

				// Check file extension and size
				if (strtolower($file_ext) === 'json' && $file_size < 500000) {
					$upload_dir = wp_upload_dir(); // You may use another secure directory
					$target_file = trailingslashit($upload_dir['path']) . $file_name;

					// Move the uploaded file to a secure location
					$file_info = wp_handle_upload($_FILES['rwctm_import'], array('test_form' => false));
					if ($file_info && !isset($file_info['error'])) {
						$encode_options = file_get_contents($target_file);
						$option_lists = json_decode($encode_options, true);

						// Validate and update options
						if (is_array($option_lists)) {
							foreach ($option_lists as $key => $options) {
								foreach ($options as $option_key => $value) {
									// Sanitize and validate option key and value as needed
									$sanitized_option_key = sanitize_key($option_key);
									$sanitized_value = sanitize_text_field($value);

									update_option($sanitized_option_key, $sanitized_value);
								}
							}

							printf(__('<div class="updated"><p>All options are imported successfully.</p></div>', 'rwc-team-members'));

						} else {
							printf(__('<div class="error"><p>Invalid JSON file format.</p></div>', 'rwc-team-members'));
						}
					} else {
						printf(__('<div class="error"><p>Error moving the uploaded file to a secure location.</p></div>', 'rwc-team-members'));
					}
				} else {
					printf(__('<div class="error"><p>Invalid file or file size too big.</p></div>', 'rwc-team-members'));
				}
			} ?>
            <p><?php printf(__('Choose a json file to upload that you backup before, then click <strong>Upload file and import</strong>. Wordpress do the rest for you.', 'rwc-team-members')); ?></p>
            <form method="post" enctype="multipart/form-data" id="import-rwctm">
                <?php wp_nonce_field('rwctm-import'); ?>
                <p>
                    <label for="rwctm_upload"><?php esc_html_e('Choose a file from your computer:', 'rwc-team-members') ?></label> <?php esc_html_e('(Maximum size: 450 MB)', 'rwc-team-members') ?>
                    <input type="file" id="rwctm_upload" name="rwctm_import" size="25" />
                </p>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Upload file and import', 'rwc-team-members') ?>" />
                </p>
            </form>
		</div>
	</div>
	<?php
	$sidebar_content = rwctm_sidebar('rwctm-tools');
	if ($sidebar_content !== null) {
		echo wp_kses_post($sidebar_content);
	} else {
		// Handle the case where t4bnt_sidebar() returns null
		echo ''; // or provide an alternative content
	} ?>
</div>