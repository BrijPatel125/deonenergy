<?php
/**
 * Project detail — location map. Embedded Google Map from _deon_map_embed
 * (normalized to an iframe-safe src by deon_project_map_embed_src()). Hidden
 * when no usable map URL is set.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_map = function_exists( 'deon_project_map_embed_src' ) ? deon_project_map_embed_src( get_the_ID() ) : '';
if ( '' === $deon_map ) {
	return;
}

$deon_location = get_post_meta( get_the_ID(), '_deon_location', true );
?>

<section class="w-full bg-white">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">
		<div class="flex items-center gap-4 mb-8 md:mb-10" data-anim>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-accent shrink-0">
				<?php esc_html_e( 'Location', 'deon-energy' ); ?>
			</span>
			<div class="bg-deon-border h-px flex-1 min-w-0"></div>
			<?php if ( $deon_location ) : ?>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-body shrink-0">
				<?php echo esc_html( $deon_location ); ?>
			</span>
			<?php endif; ?>
		</div>

		<div class="w-full overflow-hidden border border-deon-border bg-deon-warm" data-anim>
			<iframe
				src="<?php echo esc_url( $deon_map ); ?>"
				class="w-full h-[clamp(320px,45vw,540px)] block"
				style="border:0;"
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"
				allowfullscreen
				title="<?php echo esc_attr( sprintf( /* translators: %s: project name */ __( '%s location map', 'deon-energy' ), get_the_title() ) ); ?>"></iframe>
		</div>
	</div>
</section>
