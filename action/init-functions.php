<?php
/**
 * Initial Plugin Setup Functions
 *
 * This file contains functions that play a crucial role in the initial setup
 * of the "RWC Team Members" plugin. These functions handle tasks such as
 * text domain setup for translations, adding action links to the plugin settings,
 * and various other essential tasks needed when the plugin is live at the front-end.
 * It's important to understand the role of each function before making any modifications,
 * as they collectively ensure a smooth and error-free activation process.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Internationalization
 */
if (!function_exists('rwctm_textdomain')) {
	function rwctm_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'rwc-team-members' );
		load_textdomain( 'rwc-team-members', trailingslashit( WP_PLUGIN_DIR ) . 'rwc-team-members/languages/rwc-team-members-' . $locale . '.mo' );
		load_plugin_textdomain( 'rwc-team-members', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
}
add_action( 'init', 'rwctm_textdomain' );

/**
 * Add plugin action links
 */
if (!function_exists('rwctm_plugin_actions')) {
    function rwctm_plugin_actions( $links ) {
        $create_team_url = esc_url(menu_page_url('rwctm-template', false));
        $create_team_url = wp_nonce_url($create_team_url, 'rwctm_create_team_action');

		$setting_team_url = esc_url(menu_page_url('rwctm-settings', false));
        $setting_team_url = wp_nonce_url($setting_team_url, 'rwctm_setting_team_action');

        $support_url = esc_url("https://wordpress.org/support/plugin/rwc-team-members");

        $links[] = '<a href="'. $create_team_url .'">'. esc_html__('Create Team', 'rwc-team-members') .'</a>';
        $links[] = '<a href="'. $setting_team_url .'">'. esc_html__('Settings', 'rwc-team-members') .'</a>';
        $links[] = '<a href="'. $support_url .'" target="_blank">'. esc_html__('Support', 'rwc-team-members') .'</a>';
        return $links;
    }
}

/* Checking rwctm global options */
$rwctm_options = get_option('rwctm_global_options');

/* Setting HTML Editor as the Default Post Editor */
if(isset($rwctm_options['wpedit']) && ($rwctm_options['wpedit'] == 'yes' && $rwctm_options['htmled'] == 'yes')) {
	add_filter( 'wp_default_editor', 'rwctm_WP_default_widget' );
	if (!function_exists('rwctm_WP_default_widget')) {
		function rwctm_WP_default_widget() {
			return "html";
		}
	}
}

/**
* Check date on admin initiation and add to admin notice if it was over 7 days ago.
* @return null
*/
function rwctm_check_installation_date() {
    $spare_me = "";
    $spare_me = get_option('rwctm_spare_me');
 
    if (!$spare_me) {
        $install_date = get_option( 'rwctm_activation_time', 'default_value' );
        $past_date = strtotime( '-7 days' );

        if ($install_date !== 'default_value' && $install_date < $past_date) {
            add_action( 'admin_notices', 'rwctm_display_admin_notice' );
        } else {
            $get_activation_time = strtotime("now");
            add_option('rwctm_activation_time', $get_activation_time );
        }
    }
}
add_action( 'admin_init', 'rwctm_check_installation_date' );

/**
* Display Admin Notice, asking for a review
**/
function rwctm_display_admin_notice() {
    // WordPress global variable 
    global $pagenow;
    if (is_admin() && $pagenow === 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'rwctm-lists') {
        $dont_disturb = esc_url(admin_url('admin.php?page=rwctm-lists&spare_me=1'));
        $plugin_info = get_plugin_data(RWCTM_AUF, true, true);
        $reviewurl = esc_url('https://wordpress.org/support/plugin/' . sanitize_title($plugin_info['TextDomain']) . '/reviews/');

        printf(
            __('<div id="rwctm-review" class="notice notice-success is-dismissible"><p>It\'s been 7 days since your last update or installation of the latest version of <b>%s</b>! We hope you\'ve had a positive experience so far.</p><p>Your feedback is important to us and can help us improve. If you find our team members plugin valuable, could you please take a moment to share your thoughts by leaving a quick review?</p><div class="rwctm-review-btn"><a href="%s" class="button button-primary" target="_blank">Leave a Review</a><a href="%s" class="rwctm-grid-review-done button button-secondary">Already Left a Review</a></div></div>'),
            $plugin_info['Name'],
            $reviewurl,
            $dont_disturb
        );
    }
}

/**
* remove the notice for the user if review already done or if the user does not want to
**/
function rwctm_spare_me() {    
    if( isset( $_GET['spare_me'] ) && !empty( $_GET['spare_me'] ) ) {
        $spare_me = $_GET['spare_me'];
        if( $spare_me == 1 ) {
            add_option( 'rwctm_spare_me' , TRUE );
        }
    }
}
add_action( 'admin_init', 'rwctm_spare_me', 5 );

/**
 * Add meta viewport in head section
 * A <meta> viewport element gives the browser instructions on how to control the page's dimensions and scaling.
 */
if (!function_exists('rwctm_add_view_port')) {
	function rwctm_add_view_port() {
		echo '<meta name="viewport" content="' . esc_attr('width=device-width, initial-scale=1, maximum-scale=1') . '">';
	}
}
if(isset($rwctm_options['vwport']) && $rwctm_options['vwport'] == 'yes') {
	add_action('wp_head', 'rwctm_add_view_port');
}

/**
 * The function on admin initalization.
 * Here it check the pages that we are working on are the ones used by the Media Uploader.
 */
if (!function_exists('rwctm_ink_options_setup')) {
	function rwctm_ink_options_setup() {
		global $pagenow;
		if ('media-upload.php' == $pagenow || 'async-upload.php' == $pagenow) {
			add_filter('gettext', 'rwctm_replace_window_text', 1, 2);
		}
	}
}
add_action('admin_init', 'rwctm_ink_options_setup');

/**
 * Referer parameter in our script file is for to know from which page we are launching
 * the Media Uploader as we want to change the text "Insert into Post".
 */
if (!function_exists('rwctm_replace_window_text')) {
	function rwctm_replace_window_text($translated_text, $text) {
		if ('Insert into Post' == $text) {
			$referer = strpos(wp_get_referer(), 'media_page');
			if ($referer != '') {
				return esc_html__('Upload Image', 'rwc-team-members');
			}
		}
		return $translated_text;
	}
}

/**
 * Return an ID of an attachment by searching the database with the file URL.
 *
 * First checks to see if the $url is pointing to a file that exists in
 * the wp-content directory. If so, then we search the database for a
 * partial match consisting of the remaining path AFTER the wp-content
 * directory. Finally, if a match is found the attachment ID will be
 * returned.
 *
 * @param string $url The URL of the image (ex: http://mysite.com/wp-content/uploads/2013/05/test-image.jpg)
 * 
 * @return int|null $attachment Returns an attachment ID, or null if no attachment is found
 */
if (!function_exists('rwctm_get_attachment_id_by_url')) {
	function rwctm_get_attachment_id_by_url( $url ) {
		// Split the $url into two parts with the wp-content directory as the separator
		$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

		// Get the host of the current site and the host of the $url, ignoring www
		$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

		// Return nothing if there aren't any $url parts or if the current host and $url host do not match
		if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
			return null;
		}

		// Now we're going to quickly search the DB for any attachment GUID with a partial path match
		// Example: /uploads/2013/05/test-image.jpg
		$parsed_url[1] = preg_replace( '/-[0-9]{1,4}x[0-9]{1,4}\.(jpg|jpeg|png|gif|bmp)$/i', '.$1', $parsed_url[1] );
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $wpdb->esc_like( $parsed_url[1] ) ) );

		// Returns null if no attachment is found
		return isset( $attachment[0] ) ? intval( $attachment[0] ) : null;
	}
}

/**
 * Convert hex color to rgba format with optional opacity.
 *
 * @param string $color   Hex color code.
 * @param int    $opacity Opacity value (0 to 100).
 *
 * @return string RGBA color string.
 */
if (!function_exists('rwctm_hex2rgba')) {
	function rwctm_hex2rgba($color, $opacity = false) {
		$default = 'rgb(0, 0, 0)';

		//Return default if no color provided
		if(empty($color)) {
			return $default;
		}

		//Sanitize $color if "#" is provided
		if ($color[0] === '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) === 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) === 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if($opacity !== false){
			$opacity = abs($opacity)/100;
			
			$opacity = max(0, min(1, $opacity)); // Ensure opacity is within valid range

			/* if(abs($opacity) > 1)
				$opacity = 1.0; */

			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		} else {
			$output = 'rgba('.implode(",",$rgb).', 0)';
		}

		//Return rgb(a) color string
		return $output;
	}
}

/**
 * adjust brightness of a colour
 * not the best way to do it but works well enough here
 *
 * @param type $hex
 * @param type $steps
 * @return type
 */
if (!function_exists('rwctm_adjustBrightness')) {
	function rwctm_adjustBrightness($hex, $steps) {
		// Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max(-255, min(255, $steps));

		// Normalize into a six character long hex string
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) === 3) {
			$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
		}

		// Split into three parts: R, G and B
		$color_parts = str_split($hex, 2);
		$return = '#';

		foreach ($color_parts as $color) {
			$color   = hexdec($color); // Convert to decimal
			$color   = max(0,min(255,$color + $steps)); // Adjust color
			$return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
		}
		return $return;
	}
}

/* Display WP editor content at front-end */
if (!function_exists('rwctm_display_wp_editor_content')) {
	function rwctm_display_wp_editor_content($content = '') {
        // Ensure $content is a string
        if (is_null($content) || !is_string($content)) {
            $content = '';
        }

		// Decode HTML entities, strip unnecessary <br> tags, and trim the content
		$content = trim(html_entity_decode(wp_kses_post($content)));

        // Safeguard preg_replace calls
		$content = preg_replace('/<br[^>]*>/', '', $content);

		// Define the array of allowed HTML tags and attributes
		$allowedTags = wp_kses_allowed_html('post');

		// Filter the content to keep only the allowed tags
		$content = wp_kses($content, $allowedTags);

		// Apply wpautop() to format paragraphs
		$content = wpautop($content);

		return $content;
	}
}

/**
 * Extract the domain name from a given URL.
 *
 * @param string $url URL to extract domain from.
 *
 * @return string Extracted domain name.
 */
if (!function_exists('rwctm_separate_domain_name')) {
	function rwctm_separate_domain_name($url='') {
		if (empty($url)) {
			$url = 'Unknown Website';
		}

		// Parse the URL to get the host
		$host = wp_parse_url($url, PHP_URL_HOST);

		// Split the host into parts
		$hostParts = explode('.', $host);
		
		$domain = '';
		$partsCount = count($hostParts);
		
		// Check if the last part is a 2-letter country code top-level domain
		if ($partsCount > 2 && strlen($hostParts[$partsCount - 1]) === 2) {
			$domain = $hostParts[$partsCount - 3] . '.' . $hostParts[$partsCount - 2] . '.' . $hostParts[$partsCount - 1];
		} else {
			$domain = $hostParts[$partsCount - 2] . '.' . $hostParts[$partsCount - 1];
		}

		return ucfirst($domain);  // Output: Example.com
	}
}

/**
 * Sanitize and escape team options to ensure safe usage in the plugin.
 *
 * @param array $options The array of team options to be sanitized.
 * @return array Sanitized and escaped team options.
 */
if (!function_exists('rwctm_sanitize_team_options')) {
	function rwctm_sanitize_team_options($options) {
		// Check if $options is an array, if not, return an empty array or handle it appropriately
		if (!is_array($options)) {
			// Handle the case where $options is not an array, such as returning an empty array
			return array();
		}
		// Iterate through each key-value pair in the $options array to escape and sanitize the data
		foreach ($options as $key => $value) {
			// Use a switch statement to apply the appropriate escaping function based on the type of data
			switch ($key) {
				// Add cases for each key as needed to handle specific data types
				case 'spurl':
					// If the data represents a URL, use esc_url() for proper escaping
					$textarea_urls = isset($value) ? $value : '';
					$lines = explode("\n", $textarea_urls);
	
					// Sanitize each line in the textarea separately
					$cleaned_urls = array_map(
						function ($line) {
							return esc_url_raw(sanitize_text_field(trim($line)));
						},
						$lines
					);
	
					$options[$key] = implode("\n", $cleaned_urls);
					break;
				case 'tmname':
					// If the data represents general HTML content, use esc_html() for proper escaping
					$options[$key] = esc_html($value);
					break;
				// Add more cases for other keys as needed, with appropriate escaping functions
				default:
					// For other data types, use esc_attr() as a fallback to ensure safe output
					$options[$key] = esc_attr($value);
					break;
			}
		}
		// Return the sanitized and escaped team options
		return $options;
	}
}

/**
 * Sanitize the activity options stored in the database.
 *
 * This function ensures that activity options are properly sanitized before use,
 * preventing potential security vulnerabilities and ensuring safe output.
 *
 * @param array $activities The array of activity options to be sanitized.
 * @return array The sanitized array of activity options.
 */
if (!function_exists('rwctm_sanitize_activity_options')) {
    function rwctm_sanitize_activity_options($activities) {
        // Check if the input is an array and not empty
        if (is_array($activities) && !empty($activities)) {
            // Loop through each key-value pair in the activity options
            foreach ($activities as $key => $value) {
                // Check if the key starts with 'alink', indicating a URL
                if (strpos($key, 'alink') === 0) {
                    // If URL, sanitize using esc_url_raw
                    $activities[$key] = esc_url_raw($value);
                } else {
                    // If not a URL, sanitize as general HTML content using esc_html
                    $activities[$key] = esc_html($value);
                }
            }
        }

        // Return the sanitized array of activity options
        return $activities;
    }
}

/**
 * Sanitize team member data retrieved from the database.
 *
 * This function ensures that team member data is properly sanitized before use,
 * preventing potential security vulnerabilities and ensuring safe output.
 *
 * @param array $member_options The array of team member data to be sanitized.
 * @return array The sanitized array of team member data.
 */
if (!function_exists('rwctm_escape_team_member_data')) {
    function rwctm_escape_team_member_data($member_options) {
        // Check if the input is an array and not empty
        if (is_array($member_options) && !empty($member_options)) {
            // Loop through each key-value pair in the team member data
            foreach ($member_options as $key => $value) {
                // Check if the key corresponds to specific fields that require HTML escaping
                if (
                    $key == 'mdisp' || $key == 'mname' || $key == 'mdept' ||
                    $key == 'mdsgn' || $key == 'mexpn' || $key == 'email' ||
                    $key == 'mlocn' || $key == 'rtext' || (strpos($key, 'aname') === 0)
                ) {
                    // Use esc_html() for general HTML content
                    $member_options[$key] = esc_html($value);
                }
                // Check if the key corresponds to URL fields
                elseif ($key == 'mwurl' || $key == 'mbimg') {
                    // Use esc_url() for URLs
                    $member_options[$key] = esc_url($value);
                }
                // Check if the key corresponds to textarea fields
                elseif ($key == 'mdesc' || $key == 'msbio' || $key == 'tgline') {
                    // Use esc_textarea() for textarea content
                    $member_options[$key] = esc_textarea($value);
                }
                // Fallback: Use esc_attr() for other types of data
                else {
                    $member_options[$key] = esc_attr($value);
                }
            }
        }

        // Return the sanitized array of team member data
        return $member_options;
    }
}

/**
 * Sanitize and escape global options data.
 *
 * This function takes an array of global options data, escapes and sanitizes each piece
 * of data dynamically based on its type, and returns the sanitized data.
 *
 * @param array $options The global options data to be sanitized.
 * @return array Sanitized and escaped global options data.
 */
if (!function_exists('rwctm_sanitize_global_options')) {
	function rwctm_sanitize_global_options($options) {
		// Check if the options data is not the default value
		if ($options !== 'default_value') {
			// Escape and sanitize each piece of data dynamically using a foreach loop
			foreach ($options as $key => $value) {
				// Use an appropriate escaping function based on the type of data
				switch ($key) {
					case 'gcfont':
						// For URL data, use esc_url() for proper escaping
						$options[$key] = esc_url($value);
						break;
					case 'prfont':
					case 'scfont':
						// If the data represents general HTML content, use wp_kses_post() for proper escaping
						$options[$key] = wp_kses_post($value);
						break;
					default:
						// For other data types, use esc_attr() as a fallback to ensure safe output
						$options[$key] = esc_attr($value);
						break;
				}
			}
		}

		// Return the sanitized options data
		return $options;
	}
}
