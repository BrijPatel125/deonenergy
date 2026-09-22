<?php
/**
 * Solutions page — Section 02: System Design & Compliance.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$compliance = array(
	array(
		'name' => __( 'State Renewable Energy Agencies', 'deon-energy' ),
		'desc' => __( 'Applicable renewable-energy approvals and registrations.', 'deon-energy' ),
	),
	array(
		'name' => __( 'State DISCOMs / Utilities', 'deon-energy' ),
		'desc' => __( 'Grid connectivity, power evacuation, net metering, and related approvals.', 'deon-energy' ),
	),
	array(
		'name' => __( 'MNRE', 'deon-energy' ),
		'desc' => __( 'Applicable central renewable-energy guidelines and schemes.', 'deon-energy' ),
	),
	array(
		'name' => __( 'CEIG / Electrical Inspectorate', 'deon-energy' ),
		'desc' => __( 'Statutory electrical safety inspections and approvals, wherever applicable.', 'deon-energy' ),
	),
	array(
		'name' => __( 'Other Local & Statutory Authorities', 'deon-energy' ),
		'desc' => __( 'Project-specific permissions, clearances, and compliance requirements based on location and project scope.', 'deon-energy' ),
	),
);

?>
<style>
#system-design .sol-sysdesign__inner {
	align-items: stretch;
}
#system-design .sol-sysdesign__left {
	display: flex;
	flex-direction: column;
}
#system-design .sol-sysdesign__img-wrap {
	flex: 1;
	min-height: 320px;
}
#system-design .sol-sysdesign__img {
	width: 100%;
	height: 100%;
	object-fit: cover;
}
</style>
<section id="system-design" class="sol-sysdesign">
	<div class="sol-sysdesign__section-num" aria-hidden="true">02</div>
	<div class="deon-container sol-sysdesign__inner">
		<div class="sol-sysdesign__left" data-anim>
			<h2 class="sol-sysdesign__title"><?php esc_html_e( 'System Design & Compliance', 'deon-energy' ); ?></h2>
			<p class="sol-sysdesign__body"><?php esc_html_e( 'Every installation is engineered for safety, performance, and reliable grid integration, and is reviewed before execution.', 'deon-energy' ); ?></p>
			<div class="sol-sysdesign__img-wrap">
				<img src="<?php echo esc_url( deon_site_image_url( 'deon_img_solutions_grid', 'sol-system-design.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Solar panel installation', 'deon-energy' ); ?>" class="sol-sysdesign__img">
			</div>
		</div>
		<div class="sol-sysdesign__right" data-anim>
			<p class="sol-sysdesign__compliance-label"><?php esc_html_e( 'Approvals We Coordinate', 'deon-energy' ); ?></p>
			<ul class="sol-sysdesign__compliance-list">
				<?php foreach ( $compliance as $item ) : ?>
				<li class="sol-sysdesign__compliance-row">
					<span class="sol-sysdesign__compliance-code"><?php echo esc_html( $item['name'] ); ?></span>
					<span class="sol-sysdesign__compliance-name"><?php echo esc_html( $item['desc'] ); ?></span>
				</li>
				<?php endforeach; ?>
			</ul>
			<p class="sol-sysdesign__compliance-note">
				<strong><?php esc_html_e( 'Important:', 'deon-energy' ); ?></strong>
				<?php esc_html_e( 'Approvals and regulatory requirements vary by state, DISCOM, project type, capacity, and applicable regulations. Deon Energy coordinates the required processes accordingly.', 'deon-energy' ); ?>
			</p>
		</div>
	</div>
</section>