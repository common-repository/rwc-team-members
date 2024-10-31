<?php
/**
 * Layout Two - Slider View with Left Popup for Member Details
 *
 * This file contains the template for displaying the team members in a slider view
 * on the frontend using layout two. In this layout, team members are presented in a
 * slider format, and their details appear in a left popup if enabled from the admin options.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('rwctm_layout_two')) {
	function rwctm_layout_two( $t_name, $teamID, $t_activity, $t_combo, $tot_act, $t_lists, $m_options, $m_count, $flag ) {
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

		$slider_arrow = isset($t_combo['slarow']) && $t_combo['slarow'] != 'yes' ? 'false' : 'true';
		$slider_aplay = isset($t_combo['saplay']) && $t_combo['saplay'] != 'yes' ? 'false' : 'true';
		$slider_apspd = isset($t_combo['sapspd']) && $t_combo['sapspd'] ? $t_combo['sapspd'] : 3000;
		$slider_vwdth = isset($t_combo['svwdth']) && $t_combo['svwdth'] != 'yes' ? 'false' : 'true';
		$slider_tshow = isset($t_combo['stshow']) && $t_combo['stshow'] ? $t_combo['stshow'] : 1;
		$slider_tsped = isset($t_combo['stsped']) && $t_combo['stsped'] ? $t_combo['stsped'] : 300;
		$slider_zindx = isset($t_combo['szindx']) && $t_combo['szindx'] ? $t_combo['szindx'] : 1000;
		$slider_details = isset($t_combo['slmbd']) && $t_combo['slmbd'] == 'yes' ? 'yes' : 'no';

		$data = array('id' => $teamID, 'arrows' => $slider_arrow, 'aplay' => $slider_aplay, 'apspd' => $slider_apspd, 'vwdth' => $slider_vwdth, 'tshow' => $slider_tshow, 'tsped' => $slider_tsped, 'zindx' => $slider_zindx, 'slmbd' => $slider_details);

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
			<div id="<?php echo esc_attr($teamID); ?>" class="rwctm-container<?php if($slider_details == 'yes' && $slider_aplay != 'true') { ?> rwctm-popup-corner<?php } ?>"><?php
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

				<div class="rwctm-row rwctm-slider-container" data-id="<?php echo esc_attr($max_cold); ?>"><?php
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
								$rwctm_full_media = rwctm_aq_resize( $rwctm_full_media, 400, 400, true, true, true );
							} else {
								$featured_image = '<img width="'.$image_width.'" height="'.$image_height.'" src="'.$memberValue['mbimg'].'" class="attachment-medium size-medium rwctm-img-responsive" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
								$rwctm_full_media = $memberValue['mbimg'];
							}
						endif;
						if($t_combo['enimg'] == 'yes') :
							$thumb_output = $featured_image;
						else :
							$rwctm_full_media = '<img width="300" height="300" src="'.plugin_dir_url( __FILE__ ) . '../assets/images/300x300.png'.'" class="attachment-medium size-medium rwctm-img-responsive dummy-img" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
							$thumb_output = '<img width="300" height="250" src="'.plugin_dir_url( __FILE__ ) . '../assets/images/300x250.png'.'" class="attachment-medium size-medium rwctm-img-responsive dummy-img" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
						endif;

						if($memberValue['mdisp'] == "show") { ?>
						<div class="rwctm-member member-<?php echo esc_html($memberValue['mid']); ?><?php echo ' '.esc_attr($layout_style); ?><?php if($slider_details == 'yes' && $slider_aplay != 'true') { ?> rwctm-member-slider<?php } ?>" data-category="<?php echo esc_attr($category); ?>" data-mid="<?php echo esc_attr($dataid); ?>">
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
						</div>
						<?php
						}
					endforeach; ?>
				</div>
				<?php /* Modal */ ?>
				<?php if($slider_details == 'yes' && $slider_aplay != 'true') { ?>
					<div class="rwctm-slider-for <?php echo esc_attr($teamID); ?>"><?php
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

						$membid = $teamID.'-'.$key+1;
						$dataid = 'member-'.$key+1;
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
								$rwctm_full_media = rwctm_aq_resize( $rwctm_full_media, 400, 400, true, true, true );
							} else {
								$featured_image = '<img width="'.$image_width.'" height="'.$image_height.'" src="'.$memberValue['mbimg'].'" class="attachment-medium size-medium rwctm-img-responsive" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
								$rwctm_full_media = $memberValue['mbimg'];
							}
						endif;
						if($t_combo['enimg'] == 'yes') :
							$thumb_output = $featured_image;
						else :
							$rwctm_full_media = '<img width="300" height="300" src="'.plugin_dir_url( __FILE__ ) . '../assets/images/300x300.png'.'" class="attachment-medium size-medium rwctm-img-responsive dummy-img" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
							$thumb_output = '<img width="300" height="250" src="'.plugin_dir_url( __FILE__ ) . '../assets/images/300x250.png'.'" class="attachment-medium size-medium rwctm-img-responsive dummy-img" alt="'.$memberValue['mname'].'" decoding="async" loading="lazy">';
						endif; ?>
						<div id="member-<?php echo esc_html($memberValue['mid']); ?>" class="rwctm-modal-corner rwc-info-group">
							<div class="rwctm-overlay"></div>
							<div class="rwctm-slider-content member-<?php echo esc_html($memberValue['mid']); ?>">
								<div class="rwctm-widget-container">
									<div class="rwctm-modal-action">
										<a href="#" class="close-modal close-side-widget"></a>
									</div>
									<div class="rwctm-member-widget">
										<div class="rwctm-fullimg">
											<div class="rwctm-loading"></div><?php
											if($t_combo['enimg'] == 'yes') :
												echo '<img src="' . esc_url($rwctm_full_media) . '">';
											else :
												echo esc_html($rwctm_full_media);
											endif; ?>
										</div>
										<div class="rwctm-modal-body">
											<div class="rwctm-content-scrollbar">
												<?php /* Member Designation */ ?>
												<?php if(!empty($member_status) && (isset($t_combo['mbdsg']) && $t_combo['mbdsg'] != 'yes')) { ?>
												<span class="rwctm-designation"><?php
												foreach($member_status as $key => $position) {
													echo esc_attr(ucfirst($position));
													if($total_desg > $key) {
														echo ', ';
													}
												} ?></span>
												<?php } ?>

												<?php /* Member Name */ ?>
												<?php if(isset($memberValue['mname']) && $memberValue['mname']) { ?>
												<h2 class="rwctm-mbname"><?php echo esc_html($memberValue['mname']); ?></h2>
												<?php } ?>

												<?php /* Member Tagline */ ?>
												<?php if(isset($memberValue['tgline']) && $memberValue['tgline']) { ?>
												<span class="rwctm-tagline"><?php echo wp_kses_post(rwctm_display_wp_editor_content($memberValue['tgline'])); ?></span>
												<?php } ?>

												<?php /* Member Department */ ?>
												<?php if(!empty($member_depart) && (isset($t_combo['mbdpt']) && $t_combo['mbdpt'] != 'yes')) { ?>
													<span class="rwctm-department"><?php
														printf(__('<strong>Department:</strong> ', 'rwc-team-members'));
														foreach($member_depart as $key => $department) {
															echo esc_attr(ucfirst($department));
															if($total_dept > $key) {
																echo ', ';
															}
														} ?>
													</span>
												<?php } ?>

												<?php /* Member Experience */ ?>
												<?php if(isset($memberValue['mexpn']) && $memberValue['mexpn']) { ?>
												<span class="rwctm-experience"><?php if(isset($t_combo['mbdpt']) && $t_combo['mbdpt'] != 'yes') { echo '&nbsp;&nbsp;'; } ?><?php printf(__('<strong>Experience:</strong> ', 'rwc-team-members')); echo esc_html($memberValue['mexpn']); ?></span>
												<?php } ?>

												<?php /* Member Brief Description */ ?>
												<?php if(isset($memberValue['mdesc']) && $memberValue['mdesc']) { ?>
												<div class="rwctm-description"><?php echo wp_kses_post(rwctm_display_wp_editor_content($memberValue['mdesc'])); ?></div>
												<?php } ?>

												<div class="rwctm-contact-info">
													<?php /* Contact Details */ ?>
													<div class="rwctm-contact-details">
														<?php /* Telephone */ ?>
														<?php if (isset($memberValue['mtele']) && $memberValue['mtele']) { ?>
															<p class="rwctm-tel"><strong><?php esc_html_e('Tel: ', 'rwc-team-members'); ?></strong><a href="tel:<?php echo esc_html($memberValue['tcode'] . ' ' . $memberValue['mtele']); ?>"><?php echo esc_html($memberValue['tcode'] . ' ' . $memberValue['mtele']); ?></a></p>
														<?php } ?>

														<?php /* Mobile */ ?>
														<?php if (isset($memberValue['mmobe']) && $memberValue['mmobe']) { ?>
															<p class="rwctm-mob"><strong><?php esc_html_e('Mobile: ', 'rwc-team-members'); ?></strong><a href="tel:<?php echo esc_html($memberValue['mcode'] . ' ' . $memberValue['mmobe']); ?>"><?php echo esc_html($memberValue['mcode'] . ' ' . $memberValue['mmobe']); ?></a></p>
														<?php } ?>

														<?php /* Fax */ ?>
														<?php if (isset($memberValue['mbfax']) && $memberValue['mbfax']) { ?>
															<p class="rwctm-fax"><strong><?php esc_html_e('Fax: ', 'rwc-team-members'); ?></strong><a href="fax:<?php echo esc_html($memberValue['fcode'] . ' ' . $memberValue['mbfax']); ?>"><?php echo esc_html($memberValue['fcode'] . ' ' . $memberValue['mbfax']); ?></a></p>
														<?php } ?>

														<?php /* Email */ ?>
														<?php if (isset($memberValue['email']) && $memberValue['email']) { ?>
															<p class="rwctm-email"><strong><?php esc_html_e('Email: ', 'rwc-team-members'); ?></strong><a href="mailto:<?php echo esc_html($memberValue['email']); ?>"><?php echo esc_html($memberValue['email']); ?></a></p>
														<?php } ?>

														<?php /* Location */ ?>
														<?php if (isset($memberValue['mlocn']) && $memberValue['mlocn']) { ?>
															<p class="rwctm-loc"><strong><?php esc_html_e('Address: ', 'rwc-team-members'); ?></strong><?php echo esc_html($memberValue['mlocn']); ?></p>
														<?php } ?>

														<?php /* Website */ ?>
														<?php if (isset($memberValue['mwurl']) && $memberValue['mwurl']) { ?>
															<p class="rwctm-web"><strong><?php esc_html_e('Website: ', 'rwc-team-members'); ?></strong><a href="<?php echo esc_url($memberValue['mwurl']); ?>" target="_blank"><?php echo esc_html(rwctm_separate_domain_name($memberValue['mwurl'])); ?><span>&#129149;</span></a></p>
														<?php } ?>
													</div>

													<?php /* Social Icons */ ?>
													<?php if($t_activity && (isset($t_combo['mbsmic']) && $t_combo['mbsmic'] != 'yes')) { ?>
														<div class="rwctm-social-icons"><?php
															for($i = 1; $i <= $activityNum; $i++) {
																if(isset($memberValue['aname'.$sm])) {
																	$s_media = $memberValue['aname'.$sm];
																	$s_link = $t_activity['alink'.$sm].'/';
																	$s_icon = $t_activity['aicon'.$sm];
																}
																if($t_activity['atype'.$i] == 'social') {
																	$s_color = $memberValue['acolor'.$sm];
																	$b_color = rwctm_hex2rgba($s_color, 50); ?>
																	<span class="<?php echo esc_attr(str_replace([' ', '.', '&'], '_', $s_media)); ?>">
																		<a href="<?php echo esc_url($s_link . $s_media); ?>"<?php if ($t_combo['nltab'] == 'yes') { echo ' target="_blank"'; } ?><?php if (isset($s_color) && $s_color != '') { echo ' style="color:' . esc_attr($s_color) . ';border-color:' . esc_attr($b_color) . '"'; } ?>>
																			<i class="dashicons <?php echo esc_attr($s_icon); ?>" aria-hidden="true"></i>
																		</a>
																	</span><?php
																} $sm++;
															} $sm = 1; ?>
														</div>
													<?php } ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div><?php
					endforeach; ?>
					</div>
				<?php } ?>
				<?php /* // Modal */ ?>
			</div><?php
			wp_add_inline_script( 'rwctm-slider', rwctm_init_slider_carousel($data) );
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

if (!function_exists('rwctm_init_slider_carousel')) {
	function rwctm_init_slider_carousel($settings = array()) {
		$result = '';
		$result .= '(function ($) {';
			if($settings['slmbd'] == 'yes' && $settings['aplay'] != 'true') { $result .= '
			var $sliderFor = $("#'.$settings['id'].' .rwctm-slider-for");
			$sliderFor.otslider({
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
				fade: true,
				asNavFor: "#'.$settings['id'].' .rwctm-slider-container"
			});';
			}
			$result .= '
			var $sliderContainer = $("#'.$settings['id'].' .rwctm-slider-container");
			$sliderContainer.otslider({';
				if($settings['slmbd'] == 'yes' && $settings['aplay'] != 'true') { $result .= '
				asNavFor: "#'.$settings['id'].' .rwctm-slider-for",';
				} $result .= '
				focusOnSelect: true,';
				if($settings['arrows'] == 'false') { $result .= '
				arrows: false,'; }
				if($settings['aplay'] == 'true') { $result .= '
				autoplay: true,';
				if($settings['apspd'] != 3000) { $result .= '
				autoplaySpeed: '.$settings['apspd'].',';
				}}
				if($settings['vwdth'] == 'true') { $result .= '
				variableWidth: true,'; }
				if($settings['tshow'] != 1) { $result .= '
				slidesToShow: '.$settings['tshow'].','; }
				if($settings['tsped'] != 300) { $result .= '
				speed: '.$settings['tsped'].','; }
				if($settings['zindx'] != 1000) { $result .= '
				zIndex: '.$settings['zindx'].','; } $result .= '
				responsive: [
					{
						breakpoint: 1025,
						settings: {
							slidesToScroll: 1,
							slidesToShow: 3
						}
					},
					{
						breakpoint: 993,
						settings: {
							slidesToScroll: 1,
							slidesToShow: 2
						}
					},
					{
						breakpoint: 601,
						settings: {
							slidesToScroll: 1,
							slidesToShow: 1
						}
					}
				]
			});';

			if($settings['slmbd'] == 'yes' && $settings['aplay'] != 'true') { $result .= '
			$sliderContainer.on("click", ".rwctm-slide", function () {
				var isVisible = parseFloat($sliderFor.find(".rwctm-slider-content").css("opacity")) > 0;
				$sliderFor.find(".rwctm-track, .rwctm-list").css("transform", "none");
				// Hide the details if they are visible
				if (isVisible) {
				} else {
					var clickedIndex = $(this).data("rwctm-index");
					// Close any other open pop-ups
					$sliderFor.find(".rwc-info-group").removeClass("isActive");
					$sliderFor.find(".rwc-info-group").addClass("isActive");
					// Wait for a short delay to ensure proper initialization
					setTimeout(function () {
						// Update details slider and navigate to the clicked index
						$sliderFor.find(".rwc-info-group").addClass("isActive");
						$sliderFor.otslider("otsliderGoTo", clickedIndex);
					}, 10);
				}
			});

			// Close button click event
			$sliderFor.on("click", ".close-side-widget", function (e) {
				e.preventDefault();
				$sliderFor.closest(".rwc-info-group").removeClass("isActive");
			});

			$sliderFor.on("click", ".rwctm-overlay", function(e) {
				e.preventDefault();
				$sliderFor.find(".rwc-info-group").removeClass("isActive");
			});';
			} $result .= '
		})(jQuery);';
		return $result;
	}
}