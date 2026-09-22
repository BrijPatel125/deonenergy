<?php
/**
 * Knowledge Hub — "Resource Archive" downloads table (reuses deon_document CPT).
 *
 * Figma node 4:1803. Left blurb + right table of the latest documents. Format +
 * size are derived from the attached file. Hidden when no documents exist.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_docs = new WP_Query( array(
	'post_type'      => 'deon_document',
	'posts_per_page' => 4,
	'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	'no_found_rows'  => true,
) );

if ( ! $deon_docs->have_posts() ) {
	return;
}
?>
<section class="w-full bg-deon-bg">
	<div class="max-w-[1280px] mx-auto px-4 py-[64px] md:px-16 md:py-(--section-pad)
	            grid grid-cols-1 md:grid-cols-12 gap-[32px] md:gap-[24px]">

		<div class="md:col-span-4 flex flex-col gap-[16px]" data-anim>
			<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
				<?php esc_html_e( 'Resource Archive', 'deon-energy' ); ?>
			</h2>
			<p class="font-sans font-normal leading-relaxed text-deon-body max-w-[357px]">
				<?php esc_html_e( 'Technical specifications, corporate filings, and annual sustainability reports available for public download.', 'deon-energy' ); ?>
			</p>
		</div>

		<div class="md:col-span-8 border-t border-deon-divider" data-anim-stagger>
			<?php while ( $deon_docs->have_posts() ) : $deon_docs->the_post();
				$deon_file = (string) get_post_meta( get_the_ID(), '_deon_document_file', true );
				$deon_meta = deon_document_file_meta( get_the_ID() );
			?>
			<div class="border-b border-deon-divider flex items-center justify-between gap-4 py-[24px] px-[16px]">
				<div class="flex flex-col">
					<h4 class="font-sans font-bold text-deon-heading"><?php the_title(); ?></h4>
					<?php if ( $deon_meta ) : ?>
					<span class="font-sans font-normal text-[12px] leading-[16px] text-deon-body"><?php echo esc_html( $deon_meta ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( $deon_file ) : ?>
				<a href="<?php echo esc_url( $deon_file ); ?>" class="flex items-center gap-[8px] text-deon-accent hover:opacity-80 shrink-0" download>
					<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase"><?php esc_html_e( 'Download', 'deon-energy' ); ?></span>
					<svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M8 12L3 7L4.4 5.55L7 8.15V0H9V8.15L11.6 5.55L13 7L8 12ZM2 16C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V11H2V14H14V11H16V14C16 14.55 15.8042 15.0208 15.4125 15.4125C15.0208 15.8042 14.55 16 14 16H2Z" fill="currentColor"/></svg>
				</a>
				<?php endif; ?>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
