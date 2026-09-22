<?php
/**
 * ESG & Sustainability — impact stat band. Delegates to the shared dark band
 * so ESG / Project Gallery / Investor Relations stay identical.
 *
 * Tiles are managed in Appearance → Customize → Site Statistics → ESG
 * ( inc/site-stats.php ). Only the Capacity tile ships a default (the portfolio
 * MW); Carbon Offset / Community / Investment ship EMPTY on purpose — unsourced
 * ESG claims carry regulatory exposure, so a tile stays hidden until the client
 * enters a real figure. The row rebalances to however many are populated.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/stat-band', null, array( 'band' => 'esg' ) );

/*
 * The approved copy attaches this qualifier to the CO2 figure, so it prints
 * only while that tile actually carries a value — no orphan footnote, and no
 * unqualified CO2 claim.
 */
$deon_esg_tiles = function_exists( 'deon_stat_tiles' ) ? deon_stat_tiles( 'esg' ) : array();
$deon_co2       = '';

foreach ( $deon_esg_tiles as $deon_tile ) {
	if ( isset( $deon_tile['sub'] ) && false !== stripos( (string) $deon_tile['sub'], 'CO2 Avoided' ) ) {
		$deon_co2 = trim( (string) $deon_tile['value'] );
	}
}

if ( '' === $deon_co2 ) {
	return;
}
?>
<div class="w-full bg-deon-dark">
	<div class="max-w-[1280px] mx-auto px-4 pb-8 md:px-16">
		<p class="font-sans text-[12px] leading-[19px] italic text-white/60 max-w-[860px]">
			<?php esc_html_e( 'CO2 figures are estimates based on standard grid-emission factors published by the Central Electricity Authority; actual displaced emissions depend on grid mix in the plant\'s state.', 'deon-energy' ); ?>
		</p>
	</div>
</div>
