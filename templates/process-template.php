<?php
/**
 * Instant Team Creation Template
 *
 * This template page allows administrators to create a team instantly with a single click
 * for the "RWC Team Members" plugin. Administrators can swiftly generate a new team
 * by clicking a button. The team can later be edited from the team settings page.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="add_team_template" class="wrap">
	<div class="postbox-container" style="width:100%">
		<h2 class="main-header"><?php esc_html_e('RWC Team Member Templates', 'rwc-team-members'); ?></h2>
		<hr>
		<div class="template-container"><?php
			for($tp = 10; $tp >= 1; $tp--) { ?>
				<div class="template-items">
					<div class="template-img">
						<img src="<?php printf(esc_url(plugins_url('../assets/images/templates/template-%s.png', __FILE__ ), 'rwc-team-members' ), esc_html( $tp ));
						?>" alt="<?php esc_html_e('Template Preview', 'rwc-team-members'); ?>"><?php
						printf(
							'<a class="demo_link" href="%1$s" target="_blank"><span class="dashicons dashicons-format-image"></span>%2$s</a>',
							esc_url( "https://www.realwebcare.com/images/our-team/template_demo_" . esc_attr( $tp ) . ".png" ),
							esc_html__( 'DEMO ', 'rwc-team-members' )
						); ?>
					</div>
					<h2 class="template-name"><?php
					printf(
						esc_html__( 'Template %s', 'rwc-team-members' ),
						esc_html( $tp )
					);
					?></h2>
					<div class="template-actions">
						<span class="button button-secondary activate_team" data-count="<?php echo esc_attr($tp); ?>"><?php esc_html_e('Create Team', 'rwc-team-members'); ?></span>
					</div>
				</div><?php
			} ?>
		</div>
	</div>
</div>