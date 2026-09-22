<?php
/**
 * Project detail — Related Infrastructure. A horizontal scroll-snap carousel of
 * other projects (same type first), with prev/next arrows ( [data-carousel] in
 * assets/js/main.js ). Hidden when there are no other projects.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_related = function_exists( 'deon_related_projects' ) ? deon_related_projects( get_the_ID(), 8 ) : array();
if ( empty( $deon_related ) ) {
	return;
}
?>

<section class="w-full bg-white">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)"
	     data-carousel data-anim>

		<div class="flex items-end justify-between gap-4 mb-8 md:mb-10">
			<h2 class="font-display font-bold text-h2 leading-[1.1] text-deon-heading">
				<?php esc_html_e( 'Related Infrastructure', 'deon-energy' ); ?>
			</h2>
			<div class="flex items-center gap-2 shrink-0">
				<button type="button" data-carousel-prev
				        class="w-[44px] h-[44px] flex items-center justify-center border border-deon-border bg-white text-deon-heading transition-colors hover:border-deon-accent disabled:opacity-40 disabled:cursor-not-allowed"
				        aria-label="<?php esc_attr_e( 'Previous projects', 'deon-energy' ); ?>">
					<span aria-hidden="true">&larr;</span>
				</button>
				<button type="button" data-carousel-next
				        class="w-[44px] h-[44px] flex items-center justify-center border border-deon-border bg-white text-deon-heading transition-colors hover:border-deon-accent disabled:opacity-40 disabled:cursor-not-allowed"
				        aria-label="<?php esc_attr_e( 'Next projects', 'deon-energy' ); ?>">
					<span aria-hidden="true">&rarr;</span>
				</button>
			</div>
		</div>

		<div class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth -mx-1 px-1 pb-2
		            [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
		     data-carousel-track>
			<?php foreach ( $deon_related as $deon_id ) :
				$deon_terms    = get_the_terms( $deon_id, 'deon_project_type' );
				$deon_term     = ( $deon_terms && ! is_wp_error( $deon_terms ) ) ? $deon_terms[0] : null;
				$deon_cat_name = $deon_term ? strtoupper( $deon_term->name ) : '';
				$deon_capacity = get_post_meta( $deon_id, '_deon_capacity', true );
				$deon_location = get_post_meta( $deon_id, '_deon_location', true );
				$deon_img      = get_the_post_thumbnail_url( $deon_id, 'large' );
				?>
				<article class="group relative snap-start shrink-0 w-[280px] md:w-[320px] bg-white border border-deon-border flex flex-col transition-colors hover:border-deon-accent">
					<a href="<?php echo esc_url( get_permalink( $deon_id ) ); ?>" class="absolute inset-0 z-10" aria-label="<?php echo esc_attr( get_the_title( $deon_id ) ); ?>"></a>

					<div class="bg-deon-warm relative overflow-hidden shrink-0 h-[200px]">
						<?php if ( $deon_img ) : ?>
						<img src="<?php echo esc_url( $deon_img ); ?>"
						     alt="<?php echo esc_attr( get_the_title( $deon_id ) ); ?>"
						     class="absolute inset-0 w-full h-full object-cover"
						     loading="lazy" decoding="async">
						<?php endif; ?>
						<?php if ( $deon_cat_name ) : ?>
						<div class="absolute top-4 left-4 bg-white/90 border border-deon-accent px-1">
							<span class="font-sans font-normal text-[10px] leading-[15px] uppercase text-deon-accent">
								<?php echo esc_html( $deon_cat_name ); ?>
							</span>
						</div>
						<?php endif; ?>
					</div>

					<div class="p-[24px] flex flex-col gap-[8px]">
						<h3 class="font-display font-bold text-h3 leading-[1.35] text-deon-heading">
							<?php echo esc_html( get_the_title( $deon_id ) ); ?>
						</h3>
						<div class="flex items-center justify-between gap-3">
							<span class="font-sans font-normal text-[14px] leading-[20px] text-deon-body">
								<?php echo esc_html( $deon_location ); ?>
							</span>
							<?php if ( $deon_capacity ) : ?>
							<span class="font-sans font-bold text-[12px] leading-[18px] text-deon-accent shrink-0">
								<?php echo esc_html( strtoupper( $deon_capacity ) ); ?>
							</span>
							<?php endif; ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>