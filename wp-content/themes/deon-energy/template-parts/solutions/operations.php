<?php
/**
 * Solutions page — Section 05: Operations & Maintenance.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$features = array(
	array( __( 'Preventive Maintenance', 'deon-energy' ) ),
array( __( 'Corrective Maintenance', 'deon-energy' ) ),
array( __( 'Thermal & Visual Inspection', 'deon-energy' ) ),
array( __( 'Remote Monitoring', 'deon-energy' ) ),
);



$cards = array(
	array(
		'label' => __( 'Yield Optimisation', 'deon-energy' ),
		'body'  => __( 'Cleaning cycles and operational parameters adjusted to actual site conditions and seasonal irradiance patterns.', 'deon-energy' ),
	),
	array(
		'label' => __( 'Response Times', 'deon-energy' ),
		'body'  => __( 'Regional service coverage with defined response-time commitments in the O&M contract, matched to plant criticality.', 'deon-energy' ),
	),
	array(
		'label' => __( 'Module Health Checks', 'deon-energy' ),
		'body'  => __( 'Periodic visual inspection and, where required, thermal imaging to identify degradation, hot-spots or damaged connections early.', 'deon-energy' ),
	),
	array(
		'label' => __( 'Monitoring & Reporting', 'deon-energy' ),
		'body'  => __( "Monthly generation reports, uptime tracking and clear escalation for anything that needs the customer's attention.", 'deon-energy' ),
	),
);

?>
<section id="operations" class="sol-om">
	<div class="sol-om__section-num" aria-hidden="true">05</div>
	<div class="deon-container sol-om__inner">
		<div class="sol-om__left" data-anim>
			<h2 class="sol-om__title"><?php esc_html_e( 'Operations & Maintenance', 'deon-energy' ); ?></h2>
			<p class="sol-om__body"><?php esc_html_e( 'A solar plant either keeps performing or quietly loses generation. Our O&M combines structured preventive and corrective maintenance with remote monitoring.', 'deon-energy' ); ?></p>
			<ul class="sol-om__features">
				<?php foreach ( $features as $feat ) : ?>
				<li class="sol-om__feature-item">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/sol-icon-check.svg' ); ?>" alt="" class="sol-om__feature-icon" aria-hidden="true" width="20" height="20">
					<span>
						<span class="sol-om__feature-label"><?php echo esc_html( $feat[0] ); ?></span>
						<span class="sol-om__feature-desc"><?php echo esc_html( $feat[1] ); ?></span>
					</span>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="sol-om__cards" data-anim-stagger>
			<?php foreach ( $cards as $card ) : ?>
			<div class="sol-om__card">
				<p class="sol-om__card-label"><?php echo esc_html( $card['label'] ); ?></p>
				<p class="sol-om__card-body"><?php echo esc_html( $card['body'] ); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
