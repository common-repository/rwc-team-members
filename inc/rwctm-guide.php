<?php
/**
 * Help Page
 *
 * This file contains the help page for the "RWC Team Members" plugin
 * in the admin panel. The help page provides documentation, instructions, and
 * assistance to administrators on how to use various features of the plugin.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="wrap">
	<div id="rwctm-narration" class="rwctm_guide postbox-container">
		<h2 class="main-header"><?php esc_html_e('RWC Team Members - Help', 'rwc-team-members'); ?><hr></h2>
		<div id="rwctmusage-note" class="rwctmusage-maincontent">
			<div class="rwctm">
				<h3><?php esc_html_e('Watch the Video', 'rwc-team-members'); ?></h3>
				<p><?php esc_html_e('Check out the 10-minutes "Getting Started" video for an overview of the team member plugin. Next, go to the template page to make your first team. Then, explore various options to showcase your team professionally.', 'rwc-team-members'); ?></p>
				<div class="getting-started_video">
					<!-- <img src="<?php //echo esc_url(plugins_url( '../assets/images/rwc-team-members.png', __FILE__ )); ?>" alt="Thumbnail"> -->
					<iframe width="620" height="350" src="https://www.youtube-nocookie.com/embed/N7kWGArhA4U" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>

			<div class="rwctm">
				<h3><?php esc_html_e('Documentation', 'rwc-team-members'); ?></h3>
				<p><?php
					printf(__('
						To see the full documentation, please click on the following link: <strong><a href="%s" target="_blank">Documentation</a></strong>.', 'rwc-team-members'),
						esc_url("https://www.realwebcare.com/plugin/rwc-team-members/")
					); ?></p>
			</div>
			<hr>
			<div class="rwctm">
				<p class="prepend-top append-1"><?php
				printf(__('
					Thank you for selecting our plugin! We deeply appreciate your feedback and are committed to offering you top-notch support. Need more help beyond this guide? Our team is ready to assist you through the <a href="%1s" target="_blank">WordPress Support Threads</a>. Your satisfaction is paramount, and we\'re here to exceed your expectations. If you find our plugin valuable, kindly consider leaving a stellar <a target="_blank" href="%2s">★★★★★</a> review. Your support truly matters to us!', 'rwc-team-members'),
					esc_url("https://wordpress.org/support/plugin/rwc-team-members"),
					esc_url("https://wordpress.org/support/plugin/rwc-team-members/reviews/?filter=5/#new-post")
				); ?></p>
			</div>
		</div><!-- End rwctmusage-maincontent -->
	</div><!-- End postbox-container -->
	<?php echo wp_kses_post(rwctm_sidebar()); ?>
</div><!-- End wrap -->