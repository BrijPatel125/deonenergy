<?php
/**
 * Solutions page — Section 03: Commercial Rooftop Solar.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$features = array(
	array(
		'num'   => '01',
		'title' => __( 'Grid-Cost Reduction', 'deon-energy' ),
		'body'  => __( 'On-site generation reduces the share of your load billed at commercial or HT industrial tariffs, which tend to rise over time.', 'deon-energy' ),
	),
	array(
		'num'   => '02',
		'title' => __( 'Emissions Reduction', 'deon-energy' ),
		'body'  => __( 'Every kWh generated on-site displaces a kWh from the grid, reducing your Scope 2 emissions footprint measurably.', 'deon-energy' ),
	),
	array(
		'num'   => '03',
		'title' => __( 'Depreciation Benefit', 'deon-energy' ),
		'body'  => __( 'Eligible rooftop solar assets may qualify for accelerated depreciation, improving first-year tax economics for businesses.', 'deon-energy' ),
		// The approved copy carries this qualifier with the claim — keep them together.
		'note'  => __( 'Eligibility and rates depend on prevailing tax provisions and the buyer’s tax status. Please consult your tax advisor.', 'deon-energy' ),
	),
	array(
		'num'   => '04',
		'title' => __( 'Modular & Scalable', 'deon-energy' ),
		'body'  => __( 'Designs that let you start with the roof area you have today and extend later as your facility grows.', 'deon-energy' ),
	),
	array(
		'num'   => '05',
		'title' => __( 'Long Design Life', 'deon-energy' ),
		'body'  => __( 'Modules and structures selected and sized for a 25-year design life with manufacturer performance warranties.', 'deon-energy' ),
	),
	array(
		'num'   => '06',
		'title' => __( 'Remote Monitoring', 'deon-energy' ),
		'body'  => __( 'Cloud-based generation monitoring so you see production, downtime and yield in one place, not spread across spreadsheets.', 'deon-energy' ),
	),
);

?>
<section id="rooftop-solar" class="sol-rooftop">
	<div class="sol-rooftop__section-num" aria-hidden="true">03</div>
	<div class="deon-container sol-rooftop__inner">
		<div class="sol-rooftop__header" data-anim>
			<h2 class="sol-rooftop__title"><?php esc_html_e( 'Commercial & Industrial Rooftop Solar', 'deon-energy' ); ?></h2>
			<p class="sol-rooftop__sub"><?php esc_html_e( 'Turning underused rooftop area into a productive energy asset.', 'deon-energy' ); ?></p>
		</div>
		<div class="sol-rooftop__grid" data-anim-stagger>
			<?php foreach ( $features as $feat ) : ?>
			<div class="sol-rooftop__item">
				<span class="sol-rooftop__num" aria-hidden="true"><?php echo esc_html( $feat['num'] ); ?></span>
				<h3 class="sol-rooftop__feat-title"><?php echo esc_html( $feat['title'] ); ?></h3>
				<p class="sol-rooftop__feat-body"><?php echo esc_html( $feat['body'] ); ?></p>
				<?php if ( ! empty( $feat['note'] ) ) : ?>
				<p class="sol-rooftop__feat-note"><?php echo esc_html( $feat['note'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
