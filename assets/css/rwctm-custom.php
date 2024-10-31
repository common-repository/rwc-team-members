<?php
/**
 * Custom Styles
 *
 * This file generating custom styles for the "RWC Team Members" plugin.
 * These styles are designed to customize the appearance of the plugin's frontend
 * output and ensure seamless integration with the overall website design.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('rwctm_set_custom_css')) {
	function rwctm_set_custom_css($page_id, $st_pageid = false) {
		$tt_act = $mwidth = $twidth = $dwidth = 0;
		$custom_css = '';
		$sm = $sk = 1;

		$all_post_urls = $scode_pageid = $post_id = array();
		$current_post_id = $page_id;

		// Fetch options from the database
		$our_team_lists = get_option('rwctm_teamLists');
		$team_id_lists = get_option('rwctm_teamIDs');

		// Escape user input data
		$our_team_lists = esc_html($our_team_lists);
		$team_id_lists = esc_html($team_id_lists);

		// Convert options to arrays
		$our_team_lists = explode(', ', $our_team_lists);
		$team_id_lists = explode(', ', $team_id_lists);

		// Fetch global options
		$rwctm_options = get_option('rwctm_global_options', 'default_value');

		// Sanitize the global options
		$rwctm_options = rwctm_sanitize_global_options($rwctm_options);

		// Decode custom CSS
		$rwctm_custom = isset($rwctm_options['rtmcss']) ? htmlspecialchars_decode($rwctm_options['rtmcss']) : '';

		if(isset($rwctm_options['gdfont']) && $rwctm_options['gdfont'] != 'yes') {
			$global_font1 = isset($rwctm_options['prfont']) ? $rwctm_options['prfont'] : '';
			$global_font2 = isset($rwctm_options['scfont']) ? $rwctm_options['scfont'] : '';
		} else {
			$global_font1 = $global_font2 = '';
		}

		foreach($our_team_lists as $key => $tm_memb) :
			$id = $team_id_lists[$key];
			$teamID = strtolower($tm_memb) . '-' .$id;
			$t_combo = get_option($tm_memb.'_option');

			// Iterate through each key-value pair in the $t_combo array to escape and sanitize the data
			$t_combo = rwctm_sanitize_team_options($t_combo);

			$all_post_urls = isset($t_combo['spurl']) ? explode( "\n", $t_combo['spurl'] ) : array();
			foreach($all_post_urls as $url) {
				if($url != '') {
					$post_id[] = url_to_postid( $url );
				} else {
					$post_id[] = 0;
				}
			}
			$scode_pageid[] = $post_id;
			if(in_array($current_post_id, $post_id) || $current_post_id == 'preview') :

				// Get the activity options from the database
				$t_activity = get_option($tm_memb.'_activity');

				// Check if the retrieved data is an array and not empty
				if (is_array($t_activity) && !empty($t_activity)) {
					// Loop through each activity key-value pair
					foreach ($t_activity as $key => $value) {	
						// Check if the key starts with 'alink'		
						if (strpos($key, 'alink') === 0) {
							// Use esc_url() for URLs
							$t_activity[$key] = esc_url($value);
						} else {
							// Use esc_html() for general HTML content
							$t_activity[$key] = esc_html($value);
						}
					}
					// Calculate the total number of activities
					$tt_act = count($t_activity)/4;
				}

				$m_lists = get_option($tm_memb);
				$m_options = explode(', ', $m_lists);
				// print_r($m_options);
				$m_count = count($m_options);
				$i = 1; $j = 0;

				/* Preparing structure settings of the different parts of the team. */
				$bottom_padding = isset($t_combo['tbpad']) ? (int)$t_combo['tbpad']+1 : 3;
				if(isset($t_combo['cwidth']) && $t_combo['cwidth']) { $container_width = (int)$t_combo['cwidth']; } else { $container_width = 100; }
				if(isset($t_combo['mxcold']) && $t_combo['mxcold']) { $max_cold = (int)$t_combo['mxcold']; } else { $max_cold = 4; }
				if(isset($t_combo['mxcolt']) && $t_combo['mxcolt']) { $max_colt = (int)$t_combo['mxcolt']; } else { $max_colt = 2; }
				if(isset($t_combo['mxcolm']) && $t_combo['mxcolm']) { $max_colm = (int)$t_combo['mxcolm']; } else { $max_colm = 1; }

				if(isset($t_combo['imauto']) && $t_combo['imauto'] != 'yes') {
					if(isset($t_combo['imtop']) && $t_combo['imtop']) { $imtop = $t_combo['imtop']; } else { $imtop = 0; }
					if(isset($t_combo['imrgt']) && $t_combo['imrgt']) { $imrgt = $t_combo['imrgt']; } else { $imrgt = 0; }
					if(isset($t_combo['imbtm']) && $t_combo['imbtm']) { $imbtm = $t_combo['imbtm']; } else { $imbtm = 0; }
					if(isset($t_combo['imlft']) && $t_combo['imlft']) { $imlft = $t_combo['imlft']; } else { $imlft = 0; }
					$padding = $imtop.'px '.$imrgt.'px '.$imbtm.'px '.$imlft.'px';
				}

				if(isset($t_combo['tntop'])) { $tntop = $t_combo['tntop']; } else { $tntop = ''; }
				if(isset($t_combo['tnbtm'])) { $tnbtm = $t_combo['tnbtm']; } else { $tnbtm = ''; }
				if($tntop == '' && $tnbtm == '') {
					$tnmargin_total = '';
				} elseif($tntop != '' && $tnbtm == '') {
					$tnmargin_total = $tntop;
				} elseif($tntop == '' && $tnbtm != '') {
					$tnmargin_total = $tnbtm;
				} else {
					$tnmargin_total = $tntop + $tnbtm;
				}
				if($tnmargin_total != '') {
					if($tntop == $tnbtm) {
						$tn_margin = $tntop.'px auto';
					} elseif(($tntop == 0 || $tntop == '') && $tnbtm != 0) {
						$tn_margin = '0 auto '.$tnbtm.'px';
					} elseif($tntop != 0 && ($tnbtm == 0 || $tnbtm == '')) {
						$tn_margin = $tntop.'px auto 0';
					} else {
						$tn_margin = $tntop.'px auto '.$tnbtm.'px';
					}
				} else {
					$tn_margin = '';
				}

				if(isset($t_combo['lstyle']) && $t_combo['lstyle']) {
					$layout_style = $t_combo['lstyle'];
				} else {
					$layout_style = 'lout1';
				}

				/* member calculation to set margin left at the last 2 or 3 members */
				$before_last = (int)($m_count/$max_cold) * $max_cold + 1;
				$have_last = (int)($m_count % $max_cold);

				/* Calculating responsive width of the team for both tablet and mobile view.
				* Here, we are calculating responsive width for member column.
				**/
				if($t_combo) {	// option is available
					$tdesk_container = $container_width;
					$ttabs_container = $container_width;
					$tmobe_container = $container_width;

					/* Calculating total width for desktop
					* auto calculation is on
					* @param int $m_count - how many columns we have
					* @param int $max_cold - number of column per row
					* @param int $dwidth - width for desktop
					**/
					if($m_count > $max_cold) {
						$dwidth = ($tdesk_container)/$max_cold . '%';
					} else {
						$dwidth = ($tdesk_container)/$m_count . '%';
					}
					$m_left = $m_right = 0;

					/* Calculating total width for tablet and mobile
					* @param int $twidth - width for tablet
					* @param int $mwidth - width for mobile
					**/
					if($m_count > $max_colt) {
						$twidth = ($ttabs_container)/$max_colt . '%';
					} else {
						$twidth = ($ttabs_container)/$m_count . '%';
					}
					if($m_count > $max_colm) {
						$mwidth = ($tmobe_container)/$max_colm . '%';
					} else {
						$mwidth = ($tmobe_container)/$m_count . '%';
					}
					$m_left_rp = $m_right_rp = 0;
				}

				/* Custom CSS for all Layout */
				/* if((isset($t_combo['pgfont']) && $t_combo['pgfont']) || $global_font1) {

				} */
				if(isset($t_combo['lstyle'])) {
					/* For Layout Style 1 */
					if($layout_style == 'lout1') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption{left:25px;right:25px;top:25px;bottom:25px;width:auto;opacity:0;-webkit-backface-visibility:hidden;-moz-backface-visibility:hidden;backface-visibility:hidden;-webkit-transform:scale(.6);-moz-transform:scale(.6);-ms-transform:scale(.6);-o-transform:scale(.6);transform:scale(.6);-webkit-transition:-webkit-transform .2s,opacity .2s;-moz-transition:-moz-transform .2s,opacity .2s;-webkit-transition:opacity .2s,-webkit-transform .2s;transition:opacity .2s,-webkit-transform .2s;-moz-transition:transform .2s,opacity .2s,-moz-transform .2s;-o-transition:opacity .2s,-o-transform .2s;transition:transform .2s,opacity .2s;transition:transform .2s,opacity .2s,-webkit-transform .2s,-moz-transform .2s,-o-transform .2s;transition:transform .2s,opacity .2s,-webkit-transform .2s,-moz-transform .2s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption::before{content:"";display:contents;height:100%;vertical-align:middle;width:1px}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details:hover figcaption{-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1);opacity:1}'.PHP_EOL;
					}
					/* For Layout Style 5 */
					if($layout_style == 'lout2') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figcaption{top:0;left:0;bottom:0;right:0;height:100%;background:transparent!important;padding:0}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figcaption::before{content:"";position:absolute;top:50%;bottom:50%;left:49.5%;width:2px;display:block;opacity:0;background-color:rgba(255, 255, 255, 0);-webkit-transition:all 0.4s;-o-transition:all 0.4s;-moz-transition:all 0.4s;transition:all 0.4s;-webkit-transition-delay:0.6s;-moz-transition-delay:0.6s;-o-transition-delay:0.6s;transition-delay:0.6s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figcaption .rwctm-member-info{height:100%;display:table}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure .rwctm-member-status,#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure .rwctm-member-bio{display:table-cell;vertical-align:middle;height:100%;overflow:hidden;width:50%;position:relative;margin:0;padding:20px;opacity:0;-webkit-transition:opacity 0.45s,-webkit-transform 0.45s;transition:opacity 0.45s,-webkit-transform 0.45s,-moz-transform 0.45s,-o-transform 0.45s,transform 0.45s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure .rwctm-member-status{background:rgba(2, 116, 190, 0.8);-webkit-transform:translate3d(50%, 0%, 0);-moz-transform:translate3d(50%, 0%, 0);transform:translate3d(50%, 0%, 0);-webkit-transition-delay:0s;-moz-transition-delay:0s;-o-transition-delay:0s;transition-delay:0s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover figcaption .rwctm-member-bio{background:rgba(0, 0, 0, 0.7)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure .rwctm-member-bio{-webkit-transform:translate3d(-50%, 0%, 0);-moz-transform:translate3d(-50%, 0%, 0);transform:translate3d(-50%, 0%, 0);-webkit-transition-delay:0s;-moz-transition-delay:0s;-o-transition-delay:0s;transition-delay:0s;}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover img{opacity:0.7}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover figcaption .rwctm-member-status,#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover figcaption .rwctm-member-bio{-webkit-transform:translate3d(0%, 0%, 0);-moz-transform:translate3d(0%, 0%, 0);transform:translate3d(0%, 0%, 0);-webkit-transition-delay:0.5s;-moz-transition-delay:0.5s;-o-transition-delay:0.5s;transition-delay:0.5s;opacity:1}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover  figcaption::before{top:0;bottom:0;opacity:1;-webkit-transition-delay:0s;-moz-transition-delay:0s;-o-transition-delay:0s;transition-delay:0s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figcaption::after{left:25%}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .social-thumb>span>a{color:#fff}'.PHP_EOL;
					}
					/* For Layout Style 9 */
					if($layout_style == 'lout3') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figcaption{opacity:0;top:0;height:100%}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details:hover figcaption{opacity:1}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info{padding:20px;top:0;height:100%;-webkit-transform:translateY(0);-moz-transform:translateY(0);-ms-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .rwctm-member-info::before,#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .rwctm-member-info::after{content:"";position:absolute;top:0;right:0;bottom:0;left:0;opacity:0;-webkit-transition:opacity 0.6s, -webkit-transform 0.6s;transition:opacity 0.6s, -webkit-transform 0.6s;-o-transition:opacity 0.6s, transform 0.6s;transition:opacity 0.6s, transform 0.6s;transition:opacity 0.6s, transform 0.6s, -webkit-transform 0.6s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .rwctm-member-info::before{border-top:1px solid #fff;border-bottom:1px solid #fff;-webkit-transform:scale(0,1);-moz-transform:scale(0,1);-ms-transform:scale(0,1);-o-transform:scale(0,1);transform:scale(0,1)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .rwctm-member-info::after{border-right:1px solid #fff;border-left:1px solid #fff;-webkit-transform:scale(1,0);-moz-transform:scale(1,0);-ms-transform:scale(1,0);-o-transform:scale(1,0);transform:scale(1,0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover .rwctm-member-info::before,#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover .rwctm-member-info::after{opacity:1;-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure .rwctm-info-wrapper{position:relative;top:50%;-webkit-transform:translateY(-50%);-moz-transform:translateY(-50%);-ms-transform:translateY(-50%);-o-transform:translateY(-50%);transform:translateY(-50%);z-index:2}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-status, #'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-bio{-webkit-transform:scale(0);-moz-transform:scale(0);-ms-transform:scale(0);-o-transform:scale(0);transform:scale(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-member-status, #'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-member-bio{-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1)}'.PHP_EOL;
					}
					/* For Layout Style 10 */
					if($layout_style == 'lout4') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figcaption{outline:7px double #0274be;outline-offset:3px;opacity:0;-webkit-transform:skewY(10deg) scale(0.8);-moz-transform:skewY(10deg) scale(0.8);-ms-transform:skewY(10deg) scale(0.8);-o-transform:skewY(10deg) scale(0.8);transform:skewY(10deg) scale(0.8);padding:0 10px;position:absolute;top:25px;left:25px;width:-webkit-calc(100% - 50px);width:-moz-calc(100% - 50px);width:calc(100% - 50px);height:-webkit-calc(100% - 50px);height:-moz-calc(100% - 50px);height:calc(100% - 50px)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-img-details figcaption{opacity:0.9;-webkit-transform:skewY(0) scale(1);-moz-transform:skewY(0) scale(1);-ms-transform:skewY(0) scale(1);-o-transform:skewY(0) scale(1);transform:skewY(0) scale(1)}'.PHP_EOL;
					}
					/* For Layout Style 11 */
					if($layout_style == 'lout5') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figcaption{background:transparent!important;top:0;padding:0;height:100%}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .rwctm-member-info{height:100%}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .rwctm-member-bio .rwctm-bio-wrapper{position:relative;top:50%;-webkit-transform:translateY(-50%);-moz-transform:translateY(-50%);-ms-transform:translateY(-50%);-o-transform:translateY(-50%);transform:translateY(-50%)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-status{background:rgba(2, 116, 190, 0.8);position:absolute;bottom:25px;width:90%;left:5%;padding:10px 60px 10px 25px;color:#fff;-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;-moz-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-bio{background:rgba(2, 116, 190, 0.8);width:100%;height:100%;position:absolute;top:0;left:-100%;padding:30px 35px;-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;-moz-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details:hover .rwctm-member-status{left:100%}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details:hover .rwctm-member-bio{left:0}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-status:after{content:"\f345";font-family:"dashicons";font-weight:900;width:30px;height:30px;line-height:31px;position:absolute;top:50%;right:10px;background:#20211c;font-size:26px;color:#fff;text-align:center;-webkit-transform:translateY(-50%);-moz-transform:translateY(-50%);-ms-transform:translateY(-50%);-o-transform:translateY(-50%);transform:translateY(-50%)}'.PHP_EOL;
					}
					/* For Layout Style 21 */
					if($layout_style == 'lout6') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .rwctm-member-thumb{position:relative;overflow:hidden}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-thumb .social-thumb{position:absolute;bottom:0;width:100%;-webkit-transform:translateY(-50%);-moz-transform:translateY(-50%);-ms-transform:translateY(-50%);-o-transform:translateY(-50%);transform:translateY(-50%)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-member-thumb .social-thumb{bottom:50%;-webkit-transform:translateY(0);-moz-transform:translateY(0);-ms-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0);z-index:2}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-thumb .social-thumb span a{display:inline-block;width:40px;height:40px;margin:0;padding:0;-webkit-border-radius:50%;border-radius:50%;background:#00558d;font-size:17px;font-weight:700;line-height:41px;color:#fff}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-thumb .social-thumb span a .dashicons{position:relative;top:50%;-webkit-transform:translateY(-50%);-moz-transform:translateY(-50%);-ms-transform:translateY(-50%);-o-transform:translateY(-50%);transform:translateY(-50%)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure figcaption{height:100px;background:#0274be;padding:25px 0;border-left:5px solid #00558d}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details:hover figure figcaption{background:#1f8dd6}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption .rwctm-member-info{padding:0 20px;top:0;-webkit-transform:translateY(0);-moz-transform:translateY(0);-ms-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .rwctm-member-status{text-transform:capitalize}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-member-info .rwctm-member-status{color:#ecdfbd;margin-bottom:20px}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-bio{position:absolute;left:0;right:0;padding:0 20px;opacity:0;-webkit-transform:scale(0);-moz-transform:scale(0);-ms-transform:scale(0);-o-transform:scale(0);transform:scale(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-member-bio{opacity:1;';if(isset($t_combo['thdpt']) && $t_combo['thdpt'] == 'yes') { $custom_css .= 'top:45px;'; } else { $custom_css .= 'top:60px;'; } $custom_css .= '-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1)}'.PHP_EOL;
					}
					/* For Layout Style 22 */
					if($layout_style == 'lout7') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .rwctm-member-thumb{position:relative;overflow:hidden}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-thumb .social-thumb{width:80%;position:absolute;top:75%;left:10%;padding:8px 0 0;margin:0;text-align:center;border:2px solid #fff;opacity:0;-webkit-transform:scale(0.8);-moz-transform:scale(0.8);-ms-transform:scale(0.8);-o-transform:scale(0.8);transform:scale(0.8);z-index:1}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-member-thumb .social-thumb{opacity:1;-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-thumb .social-thumb span{display:inline-block;padding:0 5px}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-thumb .social-thumb span a{color:#ffffff}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure .rwctm-member-thumb::after{content:"";width:100%;height:100%;position:absolute;top:0;left:0;border:none;background:-webkit-gradient(linear, left top, right top, from(rgba(32, 84, 139, 0.5)), to(rgba(236, 236, 1, 0.4)));background:-webkit-linear-gradient(left, rgba(32, 84, 139, 0.5), rgba(236, 236, 1, 0.4));background:-moz-linear-gradient(left, rgba(32, 84, 139, 0.5), rgba(236, 236, 1, 0.4));background:-o-linear-gradient(left, rgba(32, 84, 139, 0.5), rgba(236, 236, 1, 0.4));background:linear-gradient(to right, rgba(32, 84, 139, 0.5), rgba(236, 236, 1, 0.4));opacity:0;-webkit-transition:all 0.4s ease 0s;-o-transition:all 0.4s ease 0s;-moz-transition:all 0.4s ease 0s;transition:all 0.4s ease 0s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-img-details figure .rwctm-member-thumb::after{opacity:1}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption{position:relative;background:transparent;padding:20px 5px}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption .rwctm-member-info{top:0;-webkit-transform:translateY(0);-moz-transform:translateY(0);-ms-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-status::after{content:"";display:block;width:20%;margin:15px auto;border-bottom:1px solid #222}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info h3{color:#222}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .desig-thumb,#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .depart-thumb{color:#808080}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .rwctm-short-bio p{color:#666}'.PHP_EOL;
					}
					/* For Layout Style 24 & 25 */
					if($layout_style == 'lout8' || $layout_style == 'lout9') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption{position:relative;background:transparent;padding:20px 0}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption .rwctm-member-info{top:0;-webkit-transform:translateY(0);-moz-transform:translateY(0);-ms-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info h3{color:#0274be}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .desig-thumb,#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .depart-thumb{color:#aaa}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .rwctm-short-bio p{color:#666}'.PHP_EOL;
					}
					/* For Layout Style 25 */
					if($layout_style == 'lout9') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .social-thumb span > a{border:2px solid #8a8a8a;font-size:20px!important;width:35px;height:35px;border-radius:50%;line-height:34px;text-align:center}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info h3{color:#222}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .desig-thumb,#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .depart-thumb{color:#0274be}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .social-thumb>span>a .dashicons{position:relative;top:50%;-webkit-transform:translateY(-50%);-moz-transform:translateY(-50%);-ms-transform:translateY(-50%);-o-transform:translateY(-50%);transform:translateY(-50%)}'.PHP_EOL;
					}
					/* For Layout Style 26 */
					if($layout_style == 'lout10') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure{padding:40px 15px 50px;border:1px solid #ebebeb;background-color:transparent;-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;-moz-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure img{border:10px solid #f8f8f8;-webkit-border-radius:50%;border-radius:50%;width:auto}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover{background:#0274be}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover img{border-color:#095281}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption{position:relative;padding:20px 0;background:transparent!important}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption .rwctm-member-info{top:0;-webkit-transform:translateY(0);-moz-transform:translateY(0);-ms-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption .rwctm-member-status .name-thumb::after{content:"";width:34px;display:block;position:relative;top:15px;border-bottom:3px solid #1f8dd6;margin:auto;-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;-moz-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover figcaption .rwctm-member-status .name-thumb::after{border-color:#fff;top:5px}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption .rwctm-member-status .desig-thumb{padding-top:30px}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover figcaption .rwctm-member-status .desig-thumb{-webkit-transform:scale(0);-moz-transform:scale(0);-ms-transform:scale(0);-o-transform:scale(0);transform:scale(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption .rwctm-member-bio{position:absolute;top:45px;width:100%;opacity:0;height:0;-webkit-transform:scale(0);-moz-transform:scale(0);-ms-transform:scale(0);-o-transform:scale(0);transform:scale(0)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover figcaption .rwctm-member-bio .social-thumb span > a{margin-top:10px}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover figcaption .rwctm-member-bio{opacity:1;height:auto;-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1)}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info h3{color:#222}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .desig-thumb,#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .depart-thumb{color:#808080}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-member-info h3{color:#fff}'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list:hover .rwctm-member-info .depart-thumb{color:#bbb}'.PHP_EOL;
					}
				} else {
					$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info{top:0;-webkit-transform:translateY(0%);-moz-transform:translateY(0%);-ms-transform:translateY(0%);-o-transform:translateY(0%);transform:translateY(0%)}'.PHP_EOL;
				}

				/* Image Hover Effect */
				if(isset($t_combo['enihef']) && $t_combo['enihef'] == 'yes') {
					if(isset($t_combo['lstyle']) && $layout_style == 'lout10') {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover img{-webkit-transform:rotateY(360deg);-moz-transform:rotateY(360deg);transform:rotateY(360deg)}'.PHP_EOL;
					} else {
						/* Zoom In */
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details figure:hover img{opacity:0.6;-webkit-transform:scale(1.3);-moz-transform:scale(1.3);-ms-transform:scale(1.3);-o-transform:scale(1.3);transform:scale(1.3)}'.PHP_EOL;
					}
				}
				/* // Image Hover Effect */

				/* Team Name */
				if((isset($t_combo['tnfont']) && $t_combo['tnfont'] != '48px') || (isset($t_combo['tnmcl']) && $t_combo['tnmcl'] != '#000000')) {
					$custom_css .= '#'.$teamID.' .rwctm_tm_name{'; if(isset($t_combo['tnfont']) && $t_combo['tnfont']) { $custom_css .= 'font-size:'.$t_combo['tnfont'].'px;'; } if(isset($t_combo['tnmcl']) && $t_combo['tnmcl'] != '#000000') { $custom_css .= 'color:'.$t_combo['tnmcl'].';'; } if($tn_margin != '') { $custom_css .= 'margin:'.$tn_margin; } $custom_css .= '}'.PHP_EOL;
					if(isset($t_combo['tnmcl']) && $t_combo['tnmcl'] != '#000000') {
						$custom_css .= '#'.$teamID.' .rwctm_tm_name::after{background:'.$t_combo['tnmcl'].'}'.PHP_EOL;
					}
				}
				if(isset($t_combo['tnflc']) && $t_combo['tnflc'] != '#ffc107') {
					$custom_css .= '#'.$teamID.' .rwctm_tm_name span{color:'.$t_combo['tnflc'].'}'.PHP_EOL;
					$custom_css .= '#'.$teamID.' .rwctm_tm_name::before{background:'.$t_combo['tnflc'].'}'.PHP_EOL;
				}
				/* // Team Name */

				/* Fliter Navigation */
				if((isset($t_combo['flaln']) && $t_combo['flaln'] != 'center') || (isset($t_combo['fmtop']) && $t_combo['fmtop'] != 20) || (isset($t_combo['fmbtm']) && $t_combo['fmbtm'] != 20)) {
					$custom_css .= '#'.$teamID.' .rwctm-team-filter-wrap{';if($t_combo['flaln'] != 'center') { $custom_css .= 'text-align:'.$t_combo['flaln'].';'; }if($t_combo['fmtop'] && $t_combo['fmtop'] != 20) { $custom_css .= 'margin-top:'.$t_combo['fmtop'].'px;'; }if($t_combo['fmbtm'] && $t_combo['fmbtm'] != 20) { $custom_css .= 'margin-bottom:'.$t_combo['fmbtm'].'px'; } $custom_css .= '}'.PHP_EOL;
				}

				if((isset($t_combo['nvbgcl']) && $t_combo['nvbgcl'] != '#e9ecef') || (isset($t_combo['nvfncl']) && $t_combo['nvfncl'] != '#1d233b') || (isset($t_combo['navfsz']) && $t_combo['navfsz'] != '16')) {
					$custom_css .= '#'.$teamID.' .rwctm-team-filter-wrap .rwctm-filter-btn{';if($t_combo['nvbgcl'] && $t_combo['nvbgcl'] != '#e9ecef') { $custom_css .= 'background:'.$t_combo['nvbgcl'].';'; }if($t_combo['nvfncl'] && $t_combo['nvfncl'] != '#1d233b') { $custom_css .= 'color:'.$t_combo['nvfncl'].';'; } if(isset($t_combo['navfsz']) && $t_combo['navfsz'] != '16') { $custom_css .= 'font-size:'.$t_combo['navfsz'].'px;'; } $custom_css .= '}'.PHP_EOL;
				}
				$nav_act_shadow = isset($t_combo['nbaccl']) ? rwctm_adjustBrightness($t_combo['nbaccl'], -50) : 'none';
				if((isset($t_combo['nbaccl']) && $t_combo['nbaccl'] != '#516d9f') || (isset($t_combo['nfaccl']) && $t_combo['nfaccl'] != '#ffffff')) {
					$custom_css .= '#'.$teamID.' .rwctm-team-filter-wrap .rwctm-active-filter{';if($t_combo['nbaccl'] && $t_combo['nbaccl'] != '#516d9f') { $custom_css .= 'background:'.$t_combo['nbaccl'].'!important;box-shadow: inset 0 -3px 0 0 '.$nav_act_shadow.';'; }if($t_combo['nfaccl'] && $t_combo['nfaccl'] != '#1d233b') { $custom_css .= 'color:'.$t_combo['nfaccl'].'!important'; } $custom_css .= '}'.PHP_EOL;
					if($t_combo['nfaccl'] && $t_combo['nfaccl'] != '#ffffff') {
						$custom_css .= '#'.$teamID.' .rwctm-team-filter-wrap .rwctm-filter-btn:hover{background:'.$t_combo['nbaccl'].'!important;box-shadow: inset 0 -3px 0 0 '.$nav_act_shadow.';color:'.$t_combo['nfaccl'].'!important}'.PHP_EOL;
					}
				}
				/* // Fliter Navigation */

				/* Member Alignment */
				if(isset($t_combo['thmaln']) && ($t_combo['thmaln'] && $t_combo['thmaln'] != 'center')) {
					$custom_css .= '#'.$teamID.'.rwctm-container .rwctm-row{text-align:'.$t_combo['thmaln'].'}'.PHP_EOL;
				}
				/* // Member Alignment */

				/* Number of Members per Row */
				if(isset($t_combo['imauto']) && $t_combo['imauto'] != 'yes') {
					$custom_css .= '#'.$teamID.' .rwctm-member{width:'.$dwidth.';padding:'.$padding.'}'.PHP_EOL;
				} else {
					$custom_css .= '#'.$teamID.' .rwctm-member{width:'.$dwidth.'}'.PHP_EOL;
				}
				/* // Number of Members per Row */

				/* Enlarge Column on Hover */
				if(isset($t_combo['encol']) && $t_combo['encol'] == 'yes') {
					$custom_css .= '#'.$teamID.' .rwctm-member:hover{-webkit-transform:scale(1.1);-moz-transform:scale(1.1);-ms-transform:scale(1.1);-o-transform:scale(1.1);transform:scale(1.1);z-index:9}'.PHP_EOL;
				}
				/* // Enlarge Column on Hover */

				/* Thumbnail Caption Details */
				if(isset($t_combo['thtaln']) && $t_combo['thtaln'] != 'center') {
					$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figcaption{text-align:'.$t_combo['thtaln'].'}'.PHP_EOL;
				}
				/* // Thumbnail Caption Details */

				/* // Custom CSS for all Layout */
				/* Member Name */
				if(isset($t_combo['mntfs']) && ($t_combo['mntfs'] && $t_combo['mntfs'] != '18')) {
					$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info h3.name-thumb{font-size:'.$t_combo['mntfs'].'px}'.PHP_EOL;
				}
				/* Department */
				if(isset($t_combo['dptfs']) && ($t_combo['dptfs'] && $t_combo['dptfs'] != '14')) {
					$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .depart-thumb{font-size:'.$t_combo['dptfs'].'px}'.PHP_EOL;
				}
				/* Designation */
				if(isset($t_combo['dgnfs']) && ($t_combo['dgnfs'] && $t_combo['dgnfs'] != '14')) {
					$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .desig-thumb{font-size:'.$t_combo['dgnfs'].'px}'.PHP_EOL;
				}
				/* Short Bio */
				if(isset($t_combo['sbfsz']) && ($t_combo['sbfsz'] && $t_combo['sbfsz'] != '14')) {
					$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .rwctm-short-bio p{font-size:'.$t_combo['sbfsz'].'px}'.PHP_EOL;
				}
				/* Social Media */
				if(isset($t_combo['smifs']) && ($t_combo['smifs'] && $t_combo['smifs'] != '24')) {
					$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .social-thumb>span>a{font-size:'.$t_combo['smifs'].'px}'.PHP_EOL;
				}

				/* common css for popup, slider and grid view */
				if(isset($t_combo['layout'])) :
					/* common css for popup and slider */
					if(($t_combo['layout'] == 'popup' && (isset($t_combo['popdir']) && $t_combo['popdir'] != 'yes')) || $t_combo['layout'] == 'slide') {
						/* Container and font-family */
						if((isset($t_combo['cwidth']) && ($t_combo['cwidth'] && $t_combo['cwidth'] != 100)) || (isset($t_combo['sgfont']) && ($t_combo['sgfont'] && $t_combo['sgfont'] != "'Open Sans', sans-serif")) || ($global_font2 && $global_font2 != "'Open Sans', sans-serif")) {
							$custom_css .= '.'.$teamID.' .rwctm-modal-content .rwctm-content-scrollbar{';if($t_combo['sgfont'] && $t_combo['sgfont'] != "'Open Sans', sans-serif") { $custom_css .= 'font-family:'.$t_combo['sgfont'].';'; } else {if($global_font2 && $global_font2 != "'Open Sans', sans-serif") { $custom_css .= 'font-family:'.$global_font2.';'; }} if($t_combo['cwidth'] && $t_combo['cwidth'] != 100) { $custom_css .= 'width:'.$container_width.'%;'; } $custom_css .= '}'.PHP_EOL;
						}
						if((isset($t_combo['pgfont']) && ($t_combo['pgfont'] && $t_combo['pgfont'] != "'Roboto', sans-serif")) || ($global_font1 && $global_font1 != "'Roboto', sans-serif")) {
							$custom_css .= '.'.$teamID.' .rwctm-modal-content .rwctm-content-scrollbar h1,.'.$teamID.' .rwctm-modal-content .rwctm-content-scrollbar h2,.'.$teamID.' .rwctm-modal-content .rwctm-content-scrollbar h3,.'.$teamID.' .rwctm-modal-content .rwctm-content-scrollbar h4,.'.$teamID.' .rwctm-modal-content .rwctm-content-scrollbar h5,.'.$teamID.' .rwctm-modal-content .rwctm-content-scrollbar h6{';if($t_combo['pgfont'] && $t_combo['pgfont'] != "'Roboto', sans-serif") { $custom_css .= 'font-family:'.$t_combo['pgfont'].';'; } else {if($global_font1 && $global_font1 != "'Roboto', sans-serif") { $custom_css .= 'font-family:'.$global_font1.';'; }} $custom_css .= '}'.PHP_EOL;
						}
						/* Hide Arrow Down Indicator */
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figure figcaption::after{display:none}'.PHP_EOL;
					/* common css for grids */
					} else {
						/* Container and font-family */
						if((isset($t_combo['cwidth']) && ($t_combo['cwidth'] && $t_combo['cwidth'] != 100)) || (isset($t_combo['sgfont']) && ($t_combo['sgfont'] && $t_combo['sgfont'] != "'Open Sans', sans-serif")) || ($global_font2 && $global_font2 != "'Open Sans', sans-serif")) {
							$custom_css .= '#'.$teamID.'.rwctm-container{';if($t_combo['sgfont'] && $t_combo['sgfont'] != "'Open Sans', sans-serif") { $custom_css .= 'font-family:'.$t_combo['sgfont'].';'; } else {if($global_font2 && $global_font2 != "'Open Sans', sans-serif") { $custom_css .= 'font-family:'.$global_font2.';'; }} if($t_combo['cwidth'] && $t_combo['cwidth'] != 100) { $custom_css .= 'width:'.$container_width.'%;'; } $custom_css .= '}'.PHP_EOL;
						}
						if((isset($t_combo['pgfont']) && ($t_combo['pgfont'] && $t_combo['pgfont'] != "'Roboto', sans-serif")) || ($global_font1 && $global_font1 != "'Roboto', sans-serif")) {
							$custom_css .= '#'.$teamID.'.rwctm-container h1,#'.$teamID.'.rwctm-container h2,#'.$teamID.'.rwctm-container h3,#'.$teamID.'.rwctm-container h4,#'.$teamID.'.rwctm-container h5,#'.$teamID.'.rwctm-container h6{';if($t_combo['pgfont'] && $t_combo['pgfont'] != "'Roboto', sans-serif") { $custom_css .= 'font-family:'.$t_combo['pgfont'].';'; } else {if($global_font1 && $global_font1 != "'Roboto', sans-serif") { $custom_css .= 'font-family:'.$global_font1.';'; }} $custom_css .= '}'.PHP_EOL;
						}
						/* Hide Arrow Down Indicator */
						if($layout_style != 'lout15') {
							$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list figure figcaption::after{display:none}'.PHP_EOL;
						}
					}
				endif;

				/* responsice CSS for all */
				if($max_cold > 2) {
					$custom_css .= '@media only screen and (max-width: 1024px) {'.PHP_EOL;
						$custom_css .= '#'.$teamID.' .rwctm-member{width:33.333%}'.PHP_EOL;
					$custom_css .= '}'.PHP_EOL;
				}

				/* Tab */
				$custom_css .= '@media only screen and (max-width: 992px) {'.PHP_EOL;
					/* Team Name */
					$custom_css .= '#'.$teamID.' .rwctm_tm_name{'; if(isset($t_combo['tnftab']) && $t_combo['tnftab']) { $custom_css .= 'font-size:'.$t_combo['tnftab'].'px;'; } $custom_css .= '}'.PHP_EOL;
					/* Filter */
					$custom_css .= '#'.$teamID.' .rwctm-team-filter-wrap .rwctm-filter-btn{';if(isset($t_combo['nvfntb']) && $t_combo['nvfntb']) { $custom_css .= 'font-size:'.$t_combo['nvfntb'].'px;'; } $custom_css .= '}'.PHP_EOL;
					/* Thumb Member Name */
					if(isset($t_combo['mntft']) && $t_combo['mntft']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info h3.name-thumb{font-size:'.$t_combo['mntft'].'px}'.PHP_EOL;
					}
					/* Thumb Department */
					if(isset($t_combo['dptft']) && $t_combo['dptft']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .depart-thumb{font-size:'.$t_combo['dptft'].'px}'.PHP_EOL;
					}
					/* Thumb Designation */
					if(isset($t_combo['dgtft']) && $t_combo['dgtft']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .desig-thumb{font-size:'.$t_combo['dgtft'].'px}'.PHP_EOL;
					}
					/* Thumb Short Bio */
					if(isset($t_combo['sbfst']) && $t_combo['sbfst']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .rwctm-short-bio p{font-size:'.$t_combo['sbfst'].'px}'.PHP_EOL;
					}
					/* Thumb Social Media */
					if(isset($t_combo['smfst']) && $t_combo['smfst']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .social-thumb>span>a{font-size:'.$t_combo['smfst'].'px}'.PHP_EOL;
					}
					/* Member Width */
					$custom_css .= '#'.$teamID.' .rwctm-member {width:'.$twidth.'}'.PHP_EOL;
					/* //Tab */
				$custom_css .= '}'.PHP_EOL;

				$custom_css .= '@media only screen and (max-width:678px) {'.PHP_EOL;
					$custom_css .= '#'.$teamID.' .rwctm-member{width:100%}'.PHP_EOL;
				$custom_css .= '}'.PHP_EOL;

				/* Mobile */
				$custom_css .= '@media only screen and (max-width: 480px) {'.PHP_EOL;
					/* Team Name */
					$custom_css .= '#'.$teamID.' .rwctm_tm_name{'; if(isset($t_combo['tnfmob']) && $t_combo['tnfmob']) { $custom_css .= 'font-size:'.$t_combo['tnfmob'].'px;'; } $custom_css .= '}'.PHP_EOL;
					/* Filter */
					$custom_css .= '#'.$teamID.' .rwctm-team-filter-wrap .rwctm-filter-btn{';if(isset($t_combo['nvfnmb']) && $t_combo['nvfnmb']) { $custom_css .= 'font-size:'.$t_combo['nvfnmb'].'px;'; } $custom_css .= '}'.PHP_EOL;
					/* Thumb Member Name */
					if(isset($t_combo['mntfm']) && $t_combo['mntfm']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info h3.name-thumb{font-size:'.$t_combo['mntfm'].'px}'.PHP_EOL;
					}
					/* Thumb Department */
					if(isset($t_combo['dptfm']) && $t_combo['dptfm']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .depart-thumb{font-size:'.$t_combo['dptfm'].'px}'.PHP_EOL;
					}
					/* Thumb Designation */
					if(isset($t_combo['dgtfm']) && $t_combo['dgtfm']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .desig-thumb{font-size:'.$t_combo['dgtfm'].'px}'.PHP_EOL;
					}
					/* Thumb Short Bio */
					if(isset($t_combo['sbfsm']) && $t_combo['sbfsm']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-member-info .rwctm-short-bio p{font-size:'.$t_combo['sbfsm'].'px}'.PHP_EOL;
					}
					/* Thumb Social Media */
					if(isset($t_combo['smfsm']) && $t_combo['smfsm']) {
						$custom_css .= '#'.$teamID.' .rwctm-member .rwctm-member-list .rwctm-img-details .social-thumb>span>a{font-size:'.$t_combo['smfsm'].'px}'.PHP_EOL;
					}
					/* Member Width */
					$custom_css .= '#'.$teamID.' .rwctm-member {width:'.$mwidth.'}'.PHP_EOL;
					/* //Mobile */
				$custom_css .= '}'.PHP_EOL;

				foreach($m_options as $key => $value) :
					// Retrieve the team member data from the database
					$memberValue = get_option($value);

					// Sanitize the member options
					$memberValue = rwctm_escape_team_member_data($memberValue);

					if(isset($t_combo['layout'])) :
						/* loop css for slide view */
						if(($t_combo['layout'] == 'popup' && (isset($t_combo['popdir']) && $t_combo['popdir'] != 'yes')) || $t_combo['layout'] == 'slide') {
							/* Member Contact Color */
							/* Social Icon */
							if(isset($memberValue['smhvr']) && $memberValue['smhvr']) {
								$custom_css .= '.'.$teamID.' .rwctm-modal-content.member-'.$memberValue['mid'].' .rwctm-contact-info .rwctm-social-icons a:hover{color:'.$memberValue['smhvr'].'!important}'.PHP_EOL;
							}
							/* // Member Contact Color */

						/* loop css for grids */
						} else {}
					endif;

					/* loop css for all */
					/* Column BG */
					if(isset($memberValue['mbgcl']) && $memberValue['mbgcl'] != '#ffffff') {
						if(isset($t_combo['imgopc']) && $t_combo['imgopc'] != '') {
							$img_opacity = rwctm_hex2rgba($memberValue['mbgcl'], $t_combo['imgopc']);
						} else {
							$img_opacity = $memberValue['mbgcl'];
						}
						/* Image Opacity */
						$custom_css .= '#'.$teamID.' .rwctm-member.member-'.$memberValue['mid'].' .rwctm-member-list .rwctm-img-details figure .rwctm-member-thumb::before{content:"";position:absolute;top:0;left:0;width:100%;height:100%;background:'.$img_opacity.';z-index:1}'.PHP_EOL;
					}
					if($layout_style == 'lout6') {
						$custom_css .= '#'.$teamID.' .rwctm-member.member-'.$memberValue['mid'].' .rwctm-member-list .rwctm-img-details:hover figure figcaption{'; if(isset($memberValue['msbio']) && $memberValue['msbio']) { $custom_css .= 'height:47%'; } else { $custom_css .= 'height:110px'; } $custom_css .= '}'.PHP_EOL;
					}
					if($layout_style == 'lout8') {
						if($t_activity) {
							for($i = 1; $i <= $tt_act; $i++) {
								if($t_activity['atype'.$i] == 'social') {
									if(isset($memberValue['aname'.$sm])) {
										$s_media = $memberValue['aname'.$sm];
										$s_color = $memberValue['acolor'.$sm];
									} else {
										$s_media = '';
										$s_color = '';
									}
									if($s_color && $s_color != '#ffffff') {
										$custom_css .= '#'.$teamID.' .rwctm-member.member-'.$memberValue['mid'].' .rwctm-member-list .rwctm-member-info .social-thumb span.'.str_replace([' ','.','&'], '_', $s_media).' > a{color:'.$s_color.'}'.PHP_EOL;
									}
									if(isset($memberValue['smhvr']) && $memberValue['smhvr']) {
										$custom_css .= '#'.$teamID.' .rwctm-member.member-'.$memberValue['mid'].' .rwctm-member-list .rwctm-member-info .social-thumb span.'.str_replace([' ','.','&'], '_', $s_media).' > a:hover{color:'.$memberValue['smhvr'].'}'.PHP_EOL;
									}
								} $sm++;
							} $sm = 1;
						}
					}
					if($layout_style == 'lout9') {
						if($t_activity) {
							for($i = 1; $i <= $tt_act; $i++) {
								if($t_activity['atype'.$i] == 'social') {
									if(isset($memberValue['aname'.$sm])) {
										$s_media = $memberValue['aname'.$sm];
										$s_color = $memberValue['acolor'.$sm];
									} else {
										$s_media = '';
										$s_color = '';
									}
									if(isset($memberValue['smhvr']) && $memberValue['smhvr']) {
										$custom_css .= '#'.$teamID.' .rwctm-member.member-'.$memberValue['mid'].' .rwctm-member-list  .rwctm-member-info .social-thumb span.'.str_replace([' ','.','&'], '_', $s_media).' > a{color:'.$memberValue['smhvr'].'}'.PHP_EOL;
									}
									if($s_color && $s_color != '#ffffff') {
										$custom_css .= '#'.$teamID.' .rwctm-member.member-'.$memberValue['mid'].' .rwctm-member-list .rwctm-member-info .social-thumb span.'.str_replace([' ','.','&'], '_', $s_media).' > a:hover{color:'.$s_color.';border-color:'.$s_color.'}'.PHP_EOL;
									}
								} $sm++;
							} $sm = 1;
						}
					}

					/* Thumbnail Font Color */
					$layout_styles_to_exclude = ['lout1', 'lout3', 'lout4', 'lout5', 'lout8', 'lout9'];
					if(!in_array($layout_style, $layout_styles_to_exclude)) {
						/* Social Icon */
						if($t_activity) {
							for($i = 1; $i <= $tt_act; $i++) {
								if($t_activity['atype'.$i] == 'social') {
									if(isset($memberValue['aname'.$sm])) {
										$s_media = $memberValue['aname'.$sm];
										$s_color = $memberValue['acolor'.$sm];
									} else {
										$s_media = '';
										$s_color = '';
									}
									if($s_color && $s_color != '#ffffff') {
										$custom_css .= '#'.$teamID.' .rwctm-member.member-'.$memberValue['mid'].' .rwctm-member-list .rwctm-img-details .social-thumb span.'.str_replace([' ','.','&'], '_', $s_media).' > a:hover{color:'.$s_color.'}'.PHP_EOL;
									}
								} $sm++;
							} $sm = 1;
						}
					}
					/* // Thumbnail Font Color */

				endforeach;
			endif;
			$post_id = array();
		endforeach;
		if($rwctm_custom != '') { $custom_css .= $rwctm_custom; }
		if($st_pageid == false) {
			return $custom_css;
		} else {
			return $scode_pageid;
		}
	}
}
