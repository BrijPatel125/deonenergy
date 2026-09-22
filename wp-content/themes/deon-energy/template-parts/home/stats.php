<?php
/**
 * Homepage — Stats strip.
 *
 * @package Deon_Energy
 */

/**
 * The four tiles are managed in Appearance → Customize → Site Statistics → Home
 * ( inc/site-stats.php ). Each ships a portfolio-derived default; clearing a
 * value in the Customizer falls back to that default. Tiles with no value are
 * dropped so the row never renders an empty cell.
 */
$deon_stats = function_exists( 'deon_stat_tiles' ) ? deon_stat_tiles( 'home' ) : array();
$deon_stats = array_values( array_filter( $deon_stats, static function ( $t ) {
	return '' !== trim( (string) $t['value'] );
} ) );

if ( ! $deon_stats ) {
	return;
}
?>
<section class="stats" aria-label="<?php esc_attr_e( 'Key figures', 'deon-energy' ); ?>">
	<div class="stats__inner deon-container" style="--stats-cols:<?php echo esc_attr( count( $deon_stats ) ); ?>">
		<?php foreach ( $deon_stats as $stat ) : ?>
			<div class="stats__item">
				<span class="stats__value" data-count-up><?php echo esc_html( $stat['value'] ); ?></span>
				<span class="stats__label"><?php echo esc_html( $stat['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
