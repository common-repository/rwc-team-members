<?php
/**
 * Layout One - Grid View
 *
 * This file contains the template for displaying the team members in a grid view
 * on the frontend using layout one. In this layout, team member details are not shown,
 * and team members are presented in a visually appealing grid format.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('rwctm_layout_one')) {
	function rwctm_layout_one( $t_name, $teamID, $t_activity, $t_combo, $tot_act, $t_lists, $m_options, $m_count, $flag ) {
		$i = 1; $j = 0;
		$current_page_url = get_permalink();
		$allowed_tags = array(
			'img' => array(
				'src' => true,
				'width' => true,
				'height' => true,
				'class' => true,
				'alt' => true,
				'decoding' => true,
				'loading' => true,
			),
		);

		// // Sanitize the team options
		$t_combo = rwctm_sanitize_team_options($t_combo);

		// Sanitize the activity options
		$t_activity = rwctm_sanitize_activity_options($t_activity);

		if(isset($t_combo['spurl']) && $t_combo['spurl'] === '') {
			printf(__('
				<div class="rwctm_notice warning">To ensure proper display and styling of your team, go to the team <a href="%1s" target="_blank"><strong>lists page</strong></a>. Mouseover the team name, click on <strong>Edit Members</strong>, and scroll down to the <strong>General Settings</strong> tab. Locate the <strong>Shortcode Post/Page URLs</strong> section and paste the page URL (<strong>%2s</strong>) there. Update the settings to achieve the desired team display and style.</div>', 'rwc-team-members'),
				esc_url(admin_url('admin.php?page=rwctm-lists')),
				esc_url($current_page_url)
			);
		} else {
			// Check if entered URLs are valid
			$entered_urls = explode("\n", $t_combo['spurl']);
			$valid_urls = array();

			foreach ($entered_urls as $url) {
				$url = esc_url_raw(trim($url));

				// Use a regular expression for more strict URL validation
				if (preg_match('/^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([\/\w.-]*)*\/?$/', $url)) {
					$valid_urls[] = $url;
				}
			}

			if (empty($valid_urls)) {
				// No valid URLs entered
				$validation_message = '<div class="rwctm_notice warning">%s</div>';
				$validation_text = esc_html__('Please enter valid URLs for the team member shortcode. Each URL should be on a new line.', 'rwc-team-members');
				printf($validation_message, $validation_text);
			} else {
				// Check if the current page URL exists in the entered URLs
				if (!in_array($current_page_url, $valid_urls)) {
					// Display a message about including the current page URL
					$missing_current_page_message = '<div class="rwctm_notice warning">%s</div>';
					$missing_current_page_text = wp_kses(
					sprintf(
						__('It is necessary to include the current page URL for the team member shortcode. Please add the current page URL to the list of <strong>Shortcode Post/Page URLs</strong> to ensure proper display and styling. Go to the team <a href="%s" target="_blank"><strong>lists page</strong></a>. Mouseover the team name, click on <strong>Edit Members</strong>, and scroll down to the <strong>General Settings</strong> tab. Locate the <strong>Shortcode Post/Page URLs</strong> section and paste the page URL (<strong>%s</strong>) there.', 'rwc-team-members'),
						esc_url(admin_url('admin.php?page=rwctm-lists')),
						esc_url($current_page_url)
					), 'post');
	
					// Using wp_kses_post to echo HTML safely
					printf($missing_current_page_message, wp_kses_post($missing_current_page_text));
				} else {
					// Valid URLs entered
				}
			}
		}

		/* Preparing structural settings of the different parts of the member. */
		$size = isset($t_combo['imsize']) && $t_combo['imsize'] != '' ? $t_combo['imsize'] : 'thumbnail';
		if(isset($t_combo['imgwid']) && $t_combo['imgwid']) { $image_width = (int)$t_combo['imgwid']; } else { $image_width = 300; }
		if(isset($t_combo['imghgt']) && $t_combo['imghgt']) { $image_height = (int)$t_combo['imghgt']; } else { $image_height = 300; }
		if(isset($t_combo['mxcold']) && $t_combo['mxcold']) { $max_cold = (int)$t_combo['mxcold']; } else { $max_cold = 4; }
		if(isset($t_combo['lstyle']) && $t_combo['lstyle']) {
			$layout_style = $t_combo['lstyle'];
			$layout_num = (int)filter_var($t_combo['lstyle'], FILTER_SANITIZE_NUMBER_INT);
		} else {
			$layout_style = 'lout1';
			$layout_num = 1;
		}

		$dp = $sm = 1;
		$team_name = explode(' ', $t_combo['tmname']);
		$tc = count($team_name) - 1;
		// check how many activities are available
		if(isset($t_activity['aicon1'])) {
			$activityNum = count($t_activity)/4;
		} else {
			$activityNum = count($t_activity)/2;
		}
		/* check if the team is enable */
		if(!empty($t_lists) && $t_combo['enable'] == 'yes' && $flag == 1) { ?>
			<div id="<?php echo esc_attr($teamID); ?>" class="rwctm-container rwctm-slider-container"><?php
				if ($t_combo['hidetn'] != 'yes') { ?>
					<h2 class="rwctm_tm_name"><?php
					foreach ($team_name as $key => $name) {
						echo '<span>' . esc_attr(substr($name, 0, 1)) . '</span>' . esc_attr(substr($name, 1));
						if ($key != $tc) { 
							echo ' ';
						}
					} ?></h2><?php
				} ?>
				<?php if(isset($t_combo['enfilt']) && $t_combo['enfilt'] == 'yes') : ?>
				<!-- Filter -->
				<div class="rwctm-team-filter-wrap">
					<span class="rwctm-filter-btn rwctm-active-filter" data-filter="all"><?php esc_attr_e('All','rwc-team-members'); ?></span><?php
					for($i = 1; $i <= $activityNum; $i++) {
						if($t_activity['atype'.$i] == 'sector') {
							$filters = explode(', ', $t_activity['aname'.$i]);
							foreach($filters as $tag) { ?>
								<span class="rwctm-filter-btn" data-filter="<?php echo esc_attr(strtolower($tag)); ?>"><?php echo esc_html($tag); ?></span><?php
							}
						} $dp++;
					} $dp = 1; ?>
				</div>
				<!-- /filter -->
				<?php endif; ?>

				<div class="rwctm-row" data-id="<?php echo esc_attr($max_cold); ?>"><?php
					foreach($m_options as $key => $value) :
						$memberValue = get_option($value);

						// Sanitize the member options
						$memberValue = rwctm_escape_team_member_data($memberValue);

						$category = isset($memberValue['mdept']) ? $memberValue['mdept'] : 'Unknown';
						if(isset($memberValue['mdept']) && $memberValue['mdept']) {
							$member_depart = explode(',', $memberValue['mdept']);
						} else {
							$member_depart = array();
						}
						if (is_array($member_depart) && !empty($member_depart)) {
							$total_dept = count($member_depart) - 1;
						} else {
							$total_dept = 0;
						}
						if(isset($memberValue['mdsgn']) && $memberValue['mdsgn']) {
							$member_status = explode(',', $memberValue['mdsgn']);
						} else {
							$member_status = array();
						}
						if (is_array($member_status) && !empty($member_status)) {
							$total_desg = count($member_status) - 1;
						} else {
							$total_desg = 0;
						}

						$membid = $teamID . '-' . ((int)$key + 1);
						$dataid = 'member-' . ((int)$key + 1);
						if($t_combo['enimg'] == 'yes') :
							/* FEATURED THUMBNAILS */
							$image_id = rwctm_get_attachment_id_by_url($memberValue['mbimg']);

							if($image_id) {
								$rwctm_full_media = wp_get_attachment_url( $image_id, 'full' );

								if($t_combo['imsize'] == 'custom') {
									$awesome_thumb = rwctm_aq_resize( $rwctm_full_media, $image_width, $image_height, true, true, true );
									$featured_image = '<img width="'.$image_width.'" height="'.$image_height.'" src="'.$awesome_thumb.'" class="attachment-medium size-medium rwctm-img-responsive" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
								} else {
									$featured_image = wp_get_attachment_image( $image_id, $size, false, array('class' => 'attachment-medium size-medium rwctm-img-responsive') );
								}
							} else {
								$featured_image = '<img width="'.$image_width.'" height="'.$image_height.'" src="'.$memberValue['mbimg'].'" class="attachment-medium size-medium rwctm-img-responsive" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
							}
						endif;
						if($t_combo['enimg'] == 'yes') :
							$thumb_output = $featured_image;
						else :
							$rwctm_full_media = '<img width="300" height="300" src="'.plugin_dir_url( __FILE__ ) . '../assets/images/300x300.png'.'" class="attachment-medium size-medium rwctm-img-responsive dummy-img" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
							$thumb_output = '<img width="300" height="250" src="'.plugin_dir_url( __FILE__ ) . '../assets/images/300x250.png'.'" class="attachment-medium size-medium rwctm-img-responsive dummy-img" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
						endif;

						if($memberValue['mdisp'] == "show") { ?>
						<div class="rwctm-member member-<?php echo esc_html($memberValue['mid']); ?><?php echo ' ' . esc_attr($layout_style); ?>" data-category="<?php echo esc_attr($category); ?>">
							<div class="rwctm-member-list"><?php
								/* Ribbon Start */
								if(isset($t_combo['enrib']) && $t_combo['enrib'] == 'yes') {
									if ($memberValue['rtext']) {
										echo '<div class="rwctm-ribbon ribbon_color-' . esc_html($memberValue['mid']) . '"><a href="#" id="rwctm-ribbon">' . esc_html(ucfirst($memberValue['rtext'])) . '</a></div>';
									}								
								} ?>
								<?php /* Thumbnail Details */ ?>
								<div class="rwctm-img-details">
									<figure>
										<div class="rwctm-member-thumb">
											<?php echo wp_kses($thumb_output, $allowed_tags); ?>
											<?php if($layout_num > 5 && $layout_num < 8) { ?>
												<?php /* Thumbnail Social Icon */ ?>
												<?php if($t_activity && (isset($t_combo['thsmic']) && $t_combo['thsmic'] != 'yes')) { ?>
													<div class="social-thumb"><?php
														for($i = 1; $i <= $activityNum; $i++) {
															if(isset($memberValue['aname'.$sm])) {
																$s_media = $memberValue['aname'.$sm];
																$s_link = $t_activity['alink'.$sm].'/';
																$s_icon = $t_activity['aicon'.$sm];
															}
															if($t_activity['atype'.$i] == 'social') { ?>
																<span class="<?php echo esc_attr(str_replace([' ', '.', '&'], '_', $s_media)); ?>">
																	<a href="<?php echo esc_url($s_link . $s_media); ?>"<?php if ($t_combo['nltab'] == 'yes') { echo ' target="_blank"'; } ?>>
																		<i class="dashicons <?php echo esc_attr($s_icon); ?>" aria-hidden="true"></i>
																	</a>
																</span><?php
															} $sm++;
														} $sm = 1; ?>
													</div>
												<?php } ?>
											<?php } ?>
										</div>
										<figcaption>
											<div class="rwctm-member-info">
												<div class="rwctm-member-status">
													<?php /* Thumbnail Department */ ?>
													<?php if(!empty($member_depart) && (isset($t_combo['thdpt']) && $t_combo['thdpt'] != 'yes')) { ?>
													<span class="depart-thumb"><?php
													foreach($member_depart as $key => $cats) {
														echo esc_attr(ucfirst($cats));
														if($total_dept > $key) {
															echo ', ';
														}
													} ?>
													</span>
													<?php } ?>

													<?php /* Thumbnail Name */ ?>
													<?php if(isset($memberValue['mname']) && $memberValue['mname']) { ?>
													<h3 class="name-thumb"><?php echo esc_html($memberValue['mname']); ?></h3>
													<?php } ?>

													<?php /* Thumbnail Designation */ ?>
													<?php if(!empty($member_status) && (isset($t_combo['thdsg']) && $t_combo['thdsg'] != 'yes')) { ?>
													<span class="desig-thumb"><?php
														foreach($member_status as $key => $position) {
															echo esc_attr(ucfirst($position));
															if($total_desg > $key) {
																echo ', ';
															}
														} ?>
													</span>
													<?php } ?>
												</div>

												<div class="rwctm-member-bio">
													<?php /* Thumbnail Short Bio */ ?>
													<?php if(isset($memberValue['msbio']) && $memberValue['msbio']) { ?>
														<div class="rwctm-short-bio"><?php echo wp_kses_post(rwctm_display_wp_editor_content($memberValue['msbio'])); ?></div>
													<?php } ?>

													<?php if($layout_num < 6 || $layout_num > 7) { ?>
														<?php /* Thumbnail Social Icon */ ?>
														<?php if($t_activity && (isset($t_combo['thsmic']) && $t_combo['thsmic'] != 'yes')) { ?>
															<div class="social-thumb"><?php
																for($i = 1; $i <= $activityNum; $i++) {
																	if(isset($memberValue['aname'.$sm])) {
																		$s_media = $memberValue['aname'.$sm];
																		$s_link = $t_activity['alink'.$sm].'/';
																		$s_icon = $t_activity['aicon'.$sm];
																	}
																	if($t_activity['atype'.$i] == 'social') { ?>
																		<span class="<?php echo esc_attr(str_replace([' ', '.', '&'], '_', $s_media)); ?>">
																			<a href="<?php echo esc_url($s_link . $s_media); ?>"<?php if ($t_combo['nltab'] == 'yes') { echo ' target="_blank"'; } ?>>
																				<i class="dashicons <?php echo esc_attr($s_icon); ?>" aria-hidden="true"></i>
																			</a>
																		</span><?php
																	} $sm++;
																} $sm = 1; ?>
															</div>
														<?php } ?>
													<?php } ?>
												</div>
											</div>
										</figcaption>
									</figure>
								</div>
								<?php /* // Thumbnail Details */ ?>
							</div>
						</div><?php
						}
					endforeach; ?>
				</div>
			</div><?php
			/* if team is not enable */
		} else {
			printf(__('
				<div class="rwctm_notice error">Your team\'s display option is currently inactive! This means your team members aren\'t visible. To change this, follow these steps:</div>
				<div class="rwctm_notice warning">
					<ol>
						<li>Go to <strong>RWC Teams</strong> in your WordPress dashboard.</li>
						<li>Hover your mouse cursor over the team name you wish to work on.</li>
						<li>Click on <strong>Edit Members</strong>. This will take you to the team editing page.</li>
						<li>Scroll down to the <strong>General</strong> tab under <strong>Team Member Settings</strong>.</li>
						<li>Enable the option <strong>Enable Team</strong> to proudly showcase your team members on your page.</li>
					</ol>
				</div>
				<p class="rwctm_notice info">If you need any help, our support team is <a href="%s" target="_blank"><strong>here</strong></a> for you. Thank you for choosing the RWC Team Members plugin to enhance your team\'s presence!</p>', 'rwc-team-members'),
				esc_url("https://wordpress.org/support/plugin/rwc-team-members/")
			);
		}
	}
}