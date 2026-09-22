<?php
/**
 * Shared dark stat band (client request 2026-08: ESG / Project Gallery /
 * Investor Relations must be identical).
 *
 * ONE shape everywhere, taken from the approved ESG band:
 *   accent label (category) → big value → muted sublabel (description).
 * No icons — the Investor band's icon set was dropped so the three match.
 *
 * Tiles come from `deon_stat_tiles( $band )` ( inc/site-stats.php ), so the
 * client still edits every figure under Appearance → Customize → Site
 * Statistics. Tiles with an empty value are dropped and the row rebalances to
 * however many are left (1–4); an empty band renders nothing.
 *
 * Usage:
 *   get_template_part( 'template-parts/global/stat-band', null, array(
 *       'band' => 'projects',   // 'esg' | 'projects' | 'investor'
 *   ) );
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_band = ( is_array( $args ) && ! empty( $args['band'] ) ) ? (string) $args['band'] : '';
if ( '' === $deon_band || ! function_exists( 'deon_stat_tiles' ) ) {
	return;
}

$deon_tiles = array_values( array_filter( deon_stat_tiles( $deon_band ), static function ( $t ) {
	return '' !== trim( (string) $t['value'] );
} ) );

if ( ! $deon_tiles ) {
	return;
}

$deon_cols = min( 4, max( 1, count( $deon_tiles ) ) );
?>

<section class="w-full bg-deon-dark">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-[clamp(32px,3.2vw,44px)]">
		<div class="grid grid-cols-2 gap-x-6 gap-y-10 md:gap-6 md:grid-cols-[repeat(var(--stat-cols),minmax(0,1fr))]"
		     style="--stat-cols:<?php echo (int) $deon_cols; ?>"
		     data-anim-stagger>
			<?php foreach ( $deon_tiles as $deon_tile ) : ?>
				<div class="border-l border-white/20 pl-[25px] flex flex-col">
					<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-accent">
						<?php echo esc_html( $deon_tile['label'] ); ?>
					</span>
					<span class="font-sans font-extrabold text-[clamp(28px,3vw,38px)] leading-[1.25] tracking-[-1.2px] text-white pt-2" data-count-up>
						<?php echo esc_html( $deon_tile['value'] ); ?>
					</span>
					<?php if ( ! empty( $deon_tile['sub'] ) ) : ?>
					<span class="font-sans font-normal text-[10px] leading-[15px] tracking-[1px] uppercase text-white/60">
						<?php echo esc_html( $deon_tile['sub'] ); ?>
					</span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
