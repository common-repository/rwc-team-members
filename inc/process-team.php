<?php
/**
 * Team Management Landing Page
 *
 * This file contains the landing page for team management in the admin panel
 * of the "RWC Team Members" plugin. On this page, administrators can view
 * the list of all created teams and perform various actions such as previewing teams,
 * editing team activities, editing team settings, deleting teams, and duplicating teams.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$team_list = get_option('rwctm_teamLists');
$team_ids = get_option('rwctm_teamIDs');
$flag = 0; ?>
<div class="wrap">
	<div id="add_new_team" class="postbox-container">
	<h2 class="main-header"><?php esc_html_e('RWC Team Members', 'rwc-team-members'); ?> <a href="<?php echo esc_url(admin_url("admin.php?page=rwctm-template")); ?>" id="new_team" class="add-new-h2"><?php esc_html_e('Add Template', 'rwc-team-members'); ?></a><span id="rwctm-loading-image"></span></h2>
	<?php
	/* Display Team Lists*/
	if($team_list) {
		$team_lists = explode(', ', $team_list);
		$active_lists = rwctm_published_teams_count($team_lists);
		$inactive_lists = count($team_lists) - rwctm_published_teams_count($team_lists);
		$id_lists = explode(', ', $team_ids); ?>
		<div class="team_list">
            <ul class="subsubsub">
                <li class="all"><?php printf(__('All <span class="count">%s</span>', 'rwc-team-members'), absint(count($team_lists))); ?></li>
                <li class="publish"><?php printf(__('Active <span class="count">%s</span>', 'rwc-team-members'), absint($active_lists)); ?></li>
                <li class="unpublish"><?php printf(__('Inactive <span class="count">%s</span>', 'rwc-team-members'), absint($inactive_lists)); ?></li>
            </ul><br>
			<form id='rwctm_edit_form' method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">
				<?php wp_nonce_field( 'rwctm_action', 'rwctm_action_nonce' ); ?>
				<!-- <input type="hidden" name="rwctm_edit_process" value="<?php //echo esc_attr('editprocess'); ?>" /> -->
				<table id="rwctm_list" class="form-table">
					<div id="form-messages">
						<button type="button" class="rwctm_close">
							<span aria-hidden="true"><a><i class="dashicons dashicons-dismiss greencross"></i></a></span>
						</button>
						<i class="start-icon dashicons dashicons-yes-alt"></i>
						<?php printf(__('<strong>Well done!</strong> You successfully Updated Team Member Settings.', 'rwc-team-members'));	?>
					</div>
					<thead>
						<tr>
							<th><?php esc_html_e('ID', 'rwc-team-members'); ?></th>
							<th><?php esc_html_e('Team Name', 'rwc-team-members'); ?></th>
							<th><?php esc_html_e('Shortcode', 'rwc-team-members'); ?></th>
							<th><?php esc_html_e('Template', 'rwc-team-members'); ?></th>
							<th><?php esc_html_e('Status', 'rwc-team-members'); ?></th>
						</tr>
					</thead>
			<?php
			foreach($team_lists as $key => $list) {
				$list = sanitize_text_field($list);
				$team_name = ucwords(str_replace('_', ' ', $list));
				$team_options = get_option($list);
				$team_activity = get_option($list.'_activity');
				$common_options = get_option($list.'_option');
				$team_details = explode(', ', $team_options);
				$details_count = count($team_details);
				$listID = $id_lists[$key];

				// Iterate through each key-value pair in the $common_options array to escape and sanitize the data
				$common_options = rwctm_sanitize_team_options($common_options);

				$team_nicename = (isset($common_options['tmname']) && $common_options['tmname'] != '') ? $common_options['tmname'] : $team_name;
				$teamId = $key+1;
				$t_templ = 'temp0';
				if($team_activity) {
					if(get_option($list) && $details_count > 0) {
						$flag = 1;
						if(isset($common_options['templ']) && $common_options['templ'] != '') {
							$t_templ = $common_options['templ'];
						} ?>
						<tbody id="rwctm_<?php echo esc_attr($list); ?>" class="team_todo">
							<tr <?php if($teamId % 2 == 0) { echo 'class="alt"'; } ?> data-row-id="<?php echo esc_attr($list); ?>">
                            	<td><?php echo esc_attr($teamId); ?></td>
								<td class="team_name" id="<?php echo esc_attr($list); ?>">
									<div id="set_team"><?php echo esc_attr($team_nicename); ?>
									<span id="copy_team" class="duplicate_team" rel="<?php esc_html_e('Duplicate Team', 'rwc-team-members'); ?>" data-count="<?php echo esc_attr($details_count); ?>" data-id="<?php echo esc_attr($list); ?>"></span></div>
									<span class="process_team" data-id="<?php echo esc_attr($list); ?>" data-count="<?php echo esc_attr($details_count); ?>"><?php esc_html_e('Edit Members', 'rwc-team-members'); ?></span>
									<span id="edit_activity" class="rwctm_activity" data-id="<?php echo esc_attr($list); ?>"><?php esc_html_e('Edit Activity', 'rwc-team-members'); ?></span>
									<span id="view_team" class="view_team" data-id="<?php echo esc_attr($listID); ?>" data-team="<?php echo esc_attr($list); ?>"><?php esc_html_e('Preview', 'rwc-team-members'); ?></span>
									<span id="remTeam" class="rwctm_remove" data-id="<?php echo esc_attr($list); ?>"><?php esc_html_e('Delete', 'rwc-team-members'); ?></span>
								</td>
								<td class="shortcode">
                                    <div class="ot_tooltip">
                                		<input id="rwctmInput-<?php echo esc_attr($teamId); ?>" type="text" name="rwctm_shortcode" class="rwctm_shortcode" value="<?php echo esc_html('[rwc-team-members id="'.$listID.'"]'); ?>" onclick="copyShortcode(<?php echo esc_js($teamId); ?>)" onmouseout="outFunc()">
                                        <span class="ot_tooltip_text" id="rwctmTooltip-<?php echo esc_attr($teamId); ?>"><?php esc_html_e('Click to Copy Shortcode!', 'rwc-team-members'); ?></span>
                                    </div>
                                </td>
								<td>
									<div class="temp_choice">
										<select name="rwctm_template" class="rwctm_template" id="rwctm_template" data-id="<?php echo esc_attr($list); ?>">
											<option value="temp1"<?php if($t_templ == 'temp1') { ?> selected<?php } ?>><?php esc_html_e('Template 1', 'rwc-team-members'); ?></option>
											<option value="temp2"<?php if($t_templ == 'temp2') { ?> selected<?php } ?>><?php esc_html_e('Template 2', 'rwc-team-members'); ?></option>
											<option value="temp3"<?php if($t_templ == 'temp3') { ?> selected<?php } ?>><?php esc_html_e('Template 3', 'rwc-team-members'); ?></option>
											<option value="temp4"<?php if($t_templ == 'temp4') { ?> selected<?php } ?>><?php esc_html_e('Template 4', 'rwc-team-members'); ?></option>
											<option value="temp5"<?php if($t_templ == 'temp5') { ?> selected<?php } ?>><?php esc_html_e('Template 5', 'rwc-team-members'); ?></option>
											<option value="temp6"<?php if($t_templ == 'temp6') { ?> selected<?php } ?>><?php esc_html_e('Template 6', 'rwc-team-members'); ?></option>
											<option value="temp7"<?php if($t_templ == 'temp7') { ?> selected<?php } ?>><?php esc_html_e('Template 7', 'rwc-team-members'); ?></option>
											<option value="temp8"<?php if($t_templ == 'temp8') { ?> selected<?php } ?>><?php esc_html_e('Template 8', 'rwc-team-members'); ?></option>
											<option value="temp9"<?php if($t_templ == 'temp9') { ?> selected<?php } ?>><?php esc_html_e('Template 9', 'rwc-team-members'); ?></option>
											<option value="temp10"<?php if($t_templ == 'temp10') { ?> selected<?php } ?>><?php esc_html_e('Template 10', 'rwc-team-members'); ?></option>
										</select>
                                	</div>
								</td>
								<td class="rwctm-status"><?php if(isset($common_options['enable']) && $common_options['enable'] == 'yes') { printf(__('<span class="status active">Active</span>', 'rwc-team-members')); } else { printf(__('<span class="status inactive">Inactive</span>', 'rwc-team-members')); } ?></td>
							</tr>
						</tbody>
					<?php
					}
				} else { 
					$flag = 0; ?>
					<tbody id="rwctm_<?php echo esc_attr($list); ?>">
						<tr <?php if($teamId % 2 == 0) { echo 'class="alt"'; } ?> data-row-id="<?php echo esc_attr($list); ?>">
                            <td><?php echo esc_attr($teamId); ?></td>
							<td class="team_name" id="<?php echo esc_attr($list); ?>">
								<div id="set_team"><?php echo esc_attr($team_nicename); ?></div>
								<span id="edit_activity" class="rwctm_activity" data-id="<?php echo esc_attr($list); ?>"><?php esc_html_e('Add Activity', 'rwc-team-members'); ?></span>
								<span id="remTeam" class="rwctm_remove" data-id="<?php echo esc_attr($list); ?>"><?php esc_html_e('Delete', 'rwc-team-members'); ?></span>
							</td>
							<td class="rwctm_notice"><span><?php printf(__('Mouseover on the team name in the left and clicked on <strong>Add Activity</strong> link. To get started you have to add some social links first. After that, you will be able to setup team columns. After that you will get the <strong>SHORTCODE</strong> here.', 'rwc-team-members')); ?></span></td>
							<td class="no_templates"><span class="temp_none"></span></td>
							<td><?php printf(__('<span class="status inactive">Inactive</span>', 'rwc-team-members')); ?></td>
						</tr>
					</tbody>
				<?php
				}
			} ?>
				</table>
				<?php if($team_list && $flag == 1) { ?>
				<div class="regen_shortcode"><input type="button" name="regen_shortcode" class="button-primary" value="<?php esc_html_e('Regenerate Shortcode', 'rwc-team-members'); ?>" /></div>
				<?php } ?>
			</form>
		</div>
	<?php
	/* If no team available */
	} else {
		$flag = 0; ?>
		<div class="team_list">
			<p class="get_started"><?php
				printf(__('
					Welcome to our plugin, %1$s! It looks like you haven\'t added any teams yet. Don\'t worry, we\'ve got you covered! Just click on the <a href="%2$s"><strong>Add Template</strong></a> button to get started. You\'ll find 10 ready-made templates to choose from. Simply select one and click on the <strong>Create Team</strong> button to instantly create your team!<br /><br />If you have any questions or need further assistance beyond what\'s covered in the help <a href="%3$s"><strong>page</strong></a>, please don\'t hesitate to <a href="%4$s" target="_blank"><strong>contact us</strong></a> via the WordPress support thread. We\'re here to provide you with the support you need.', 'rwc-team-members'),
					'<strong>RWC Team Members</strong>',
					esc_url(admin_url("admin.php?page=rwctm-template")),
					esc_url(admin_url("admin.php?page=rwctm-help")),
					esc_url("https://wordpress.org/support/plugin/rwc-team-members/")
				); ?>
			</p>
		</div>
	<?php } ?>
	</div>
	<?php if($team_list && $flag == 1) { ?>
	<div id="rwctm-narration" class="postbox-container code">
		<div id="rwctmusage-note" class="rwctmusage-maincontent">
			<div class="rwctm"><?php
				printf(__('
					<h3>About Version 0.1</h3>
					<p><strong>Please read the following information carefully:</strong></p>
					<blockquote>
						<p>In the <strong>General Settings</strong> for each team, you\'ll find a crucial feature: <strong>Shortcode Post/Page URLs</strong>. It\'s vital to add the page URL of the team\'s display location. This ensures your team displays correctly based on its settings. This step keeps your Custom CSS file tailored to the team, leading to faster loading and a more streamlined look. This setting is pivotal - without it, your customization won\'t take effect. Make sure to implement this feature to achieve the desired team display and style.</p>
						<cite>%1$s <span class="cite-last-name">%2$s</span></cite>
						<div class="blockquote-author-image"></div>
					</blockquote>
					<p>There is a <a href="%3$s">YouTube video</a> available that explains how the Team Members plugin works. If you have any trouble understanding, feel free to <a href="%4$s" target="_blank">Contact Us</a> at any time.</p>
					<p class="likeit">We greatly value your feedback! Please spare a moment to rate your recent experience with our products/services. Your input is highly appreciated and helps us improve. Thank you for your support!<a target="_blank" href="%5$s">&#9733;&#9733;&#9733;&#9733;&#9733;</a></p>', 'rwc-team-members'),
					'Realwebcare',
					'ADMIN',
					esc_url(admin_url("admin.php?page=rwctm-help")),
					esc_url("https://wordpress.org/support/plugin/rwc-team-members/"),
					esc_url("https://wordpress.org/support/plugin/rwc-team-members/reviews/?filter=5/#new-post")
				); ?>
			</div>
		</div>
	</div>
	<div id="rwctm-sidebar" class="postbox-container code">
		<div id="rwctmusage-note" class="rwctmusage-sidebar">
			<div class="rwctm"><?php
				printf(__('
				<h3>Code Usage Instruction</h3>
				<p class="rwctm-first">To display a team shortcode in a WordPress post or page, you need to access the post or page editor in the WordPress dashboard. Here\'s how:</p>
				<ol>
					<li>Go to Posts or Pages, depending on where you want to display the team.</li>
					<li>Either create a new post or page, or edit an existing one.</li>
					<li>Switch to the visual editor if it\'s not already active.</li>
					<li>Locate the spot in the post or page where you want to display the team.</li>
					<li>Paste the following shortcode into the editor:
						<pre><code>[rwc-team-members</span> <span class="rwctm-built_in">id</span>=<span class="rwctm-string">"SHORTCODE_ID"</span>]</code></pre>
						Replace "SHORTCODE_ID" with the actual id of the team that you want to display.
					</li>
					<li>Save or publish the post or page.</li>
				</ol>
				<p>Once you\'ve saved or published the post or page, the team shortcode will be processed and the team will be displayed on the front end of your site.</p>', 'rwc-team-members')); ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>