<?php
/**
 * Solutions page — Section 01: Solar EPC.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$epc_cards = array(
	array(
		'icon'  => 'sol-icon-engineering.svg',
		'title' => __( 'Engineering', 'deon-energy' ),
		'body'  => __( 'Advanced shadow analysis and layout optimization using industry-standard simulation software to maximize site efficiency.', 'deon-energy' ),
	),
	array(
		'icon'  => 'sol-icon-procurement.svg',
		'title' => __( 'Procurement', 'deon-energy' ),
		'body'  => __( 'Tier-1 vendor network ensuring high-efficiency monocrystalline PERC modules and grid-resilient inverter systems.', 'deon-energy' ),
	),
	array(
		'icon'  => 'sol-icon-construction.svg',
		'title' => __( 'Construction', 'deon-energy' ),
		'body'  => __( 'Rapid deployment and installation governed by international safety standards and precision mounting structures.', 'deon-energy' ),
	),
);
?>
<section id="solar-epc" class="sol-epc">
	<div class="sol-epc__section-num" aria-hidden="true">01</div>
	<div class="deon-container sol-epc__inner">
		<div class="sol-epc__header" data-anim>
			<h2 class="sol-epc__title"><?php esc_html_e( 'Solar EPC', 'deon-energy' ); ?></h2>
			<p class="sol-epc__sub"><?php esc_html_e( 'Engineering, Procurement & Construction under one contract.', 'deon-energy' ); ?></p>
		</div>
		<div class="sol-epc__grid" data-anim-stagger>
			<?php foreach ( $epc_cards as $card ) : ?>
			<div class="sol-epc__card">
				<div class="sol-epc__card-top">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/' . $card['icon'] ); ?>" alt="" class="sol-epc__card-icon" aria-hidden="true" width="32" height="27">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/sol-icon-arrow.svg' ); ?>" alt="" class="sol-epc__card-arrow" aria-hidden="true" width="14" height="14">
				</div>
				<h3 class="sol-epc__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
				<p class="sol-epc__card-body"><?php echo esc_html( $card['body'] ); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
