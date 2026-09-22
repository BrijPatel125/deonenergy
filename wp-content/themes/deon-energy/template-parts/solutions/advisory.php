<?php
/**
 * Solutions page — Section 06: Advisory & Consultancy.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$cards = array(
	array(
		'accent' => false,
		'title'  => __( 'Feasibility Studies', 'deon-energy' ),
		'body'   => __( 'Technical and commercial assessment of site suitability, generation, and project economics.', 'deon-energy' ),
	),
	array(
		'accent' => true,
		'title'  => __( "Commercial Structuring", 'deon-energy' ),
		'body'   => __( 'Guidance on CAPEX, OPEX/PPA, open-access, and group-captive models based on business priorities.', 'deon-energy' ),
	),
	array(
		'accent' => false,
		'title'  => __( 'Technical Due Diligence', 'deon-energy' ),
		'body'   => __( 'Independent review for lenders, investors, buyers, sellers, and owners of solar assets.', 'deon-energy' ),
	),
	array(
		'accent' => true,
		'title'  => __( 'Decarbonisation Planning', 'deon-energy' ),
		'body'   => __( 'Practical pathways for reducing electricity-related emissions through on-site and off-site renewable energy.', 'deon-energy' ),
	),
);

?>
<section id="advisory" class="sol-advisory">
	<div class="sol-advisory__section-num" aria-hidden="true">06</div>
	<div class="deon-container sol-advisory__inner">
		<div class="sol-advisory__header" data-anim>
			<h2 class="sol-advisory__title"><?php esc_html_e( 'Advisory &amp; Consultancy', 'deon-energy' ); ?></h2>
			<p class="sol-advisory__sub"><?php esc_html_e( 'For customers earlier in their solar journey, we offer standalone advisory work — feasibility, commercial structuring, and technical assessments.', 'deon-energy' ); ?></p>
		</div>
		<div class="sol-advisory__grid" data-anim-stagger>
			<?php foreach ( $cards as $card ) : ?>
			<div class="sol-advisory__card<?php echo $card['accent'] ? ' sol-advisory__card--accent' : ''; ?>">
				<p class="sol-advisory__card-title"><?php echo esc_html( $card['title'] ); ?></p>
				<p class="sol-advisory__card-body"><?php echo esc_html( $card['body'] ); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
