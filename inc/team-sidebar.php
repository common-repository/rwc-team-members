<?php
/**
 * Admin Sidebar Page
 *
 * This file contains the admin sidebar page for the "RWC Team Members" plugin.
 * The sidebar page provides quick access to information about how to use the plugin's
 * shortcode in post/page editors and theme files. It also displays various information
 * and links related to the plugin, offering valuable resources for administrators.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('rwctm_sidebar')) {
    function rwctm_sidebar($class='') {
        if($class != '') {
            $class = ' '.$class;
        }
        $content = '';
        $content .= '
        <div id="rwctm-sidebar" class="postbox-container code'.esc_attr($class).'">
            <div id="rwctmusage-note" class="rwctmusage-sidebar">
                <h3>'.esc_html__('Code Usage Instruction', 'rwc-team-members').'</h3>
                <div class="rwctm">
                    <p class="rwctm-first">'.sprintf(__('To display a team shortcode in a WordPress theme file, you can use the %s function:', 'rwc-team-members'), '<strong>do_shortcode</strong>').'</p>'.
                    sprintf(__('<p>%s</p>', 'rwc-team-members'), '<pre><code><span class="rwctm-meta">&lt;?php</span> <span class="rwctm-keyword">echo</span> <span class="rwctm-function">do_shortcode</span>(<span class="rwctm-string">\'[rwc-team-members id=&quot;SHORTCODE_ID&quot;]\'</span>); <span class="rwctm-meta">?&gt;</span></code></pre>').'
                    <p>'.sprintf(__('Replace %s with the actual id of the team that you want to display.', 'rwc-team-members'), '<strong>SHORTCODE_ID</strong>').'</p>
                    <p>'.esc_html__('You can place this code anywhere in your theme file where you want the team to appear. For example, you could place it in the template file for a specific page of your site.', 'rwc-team-members').'</p>
                    <p>'.esc_html__('Keep in mind that if you are making changes directly to your theme files, those changes will be overwritten if you update the theme. To avoid this, you can create a child theme and make your changes there instead.', 'rwc-team-members').'</p>
                </div>
            </div>
            <div id="rwctmusage-info" class="rwctmusage-sidebar">'.
                sprintf(__('
                    <h3>Plugin Info</h3>
                    <ul class="rwctmusage-list">
                        <li>RWC Team Members</li>
                        <li>Version: 0.5</li>
                        <li>Scripts: PHP + CSS + JS</li>
                        <li>Requires: Wordpress 5.0</li>
                        <li>First Released: 14 March, 2024</li>
                        <li>Last Updated: 25 July, 2024</li>
                        <li>By: <a href="%1s" target="_blank">Realwebcare</a></li>
                        <li>Facebook Page: <a href="%2s" target="_blank">Realwebcare</a></li>
                        <li>Need Help? <a href="%3s" target="_blank">Support</a></li>
                        <li>Like it? Please leave us a <a target="_blank" href="%4s">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. We highly appreciate your support!</li>
                    </ul>', 'rwc-team-members'),
                    esc_url("https://www.realwebcare.com/"),
                    esc_url("https://www.facebook.com/realwebcare"),
                    esc_url("https://wordpress.org/support/plugin/rwc-team-members/"),
                    esc_url("https://wordpress.org/support/plugin/rwc-team-members/reviews/?filter=5/#new-post")
                ).'
            </div>
        </div>';
        return wp_kses_post($content);
    }
}