<?php
/**
 * Homepage — Client logo ticker (auto-scrolling).
 *
 * Driven by the `deon_client` CPT (title = name, featured image = logo,
 * menu_order = sequence). The whole section is skipped while no clients are
 * published, so the homepage never ships placeholder client names.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_clients = function_exists( 'deon_client_logos' ) ? deon_client_logos( 12 ) : array();

if ( ! $deon_clients ) {
	return;
}
?>
<section class="logos" aria-label="<?php esc_attr_e( 'Trusted by', 'deon-energy' ); ?>">
	<div class="logos__viewport">
		<div class="logos__track">
			<?php
			// Rendered twice so the marquee loops seamlessly; the copy is hidden from AT.
			for ( $deon_copy = 0; $deon_copy < 2; $deon_copy++ ) :
				foreach ( $deon_clients as $deon_client ) :
					$deon_name = get_the_title( $deon_client );
					$deon_url  = (string) get_post_meta( $deon_client->ID, '_deon_client_url', true );
					?>
					<span class="logos__item"<?php echo $deon_copy ? ' aria-hidden="true"' : ''; ?>>
						<?php if ( $deon_url && ! $deon_copy ) : ?>
						<a class="logos__link" href="<?php echo esc_url( $deon_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php endif; ?>

						<?php
						if ( has_post_thumbnail( $deon_client ) ) {
							echo get_the_post_thumbnail(
								$deon_client,
								'medium',
								array(
									'class'   => 'logos__logo',
									'alt'     => esc_attr( $deon_name ),
									'loading' => 'lazy',
								)
							);
						} else {
							echo esc_html( $deon_name );
						}
						?>

						<?php if ( $deon_url && ! $deon_copy ) : ?>
						</a>
						<?php endif; ?>
					</span>
					<?php
				endforeach;
			endfor;
			?>
		</div>
	</div>
</section>
