<?php
/**
 * Knowledge Hub — "Impact & Execution" case studies (deon_case_study CPT).
 *
 * Figma node 4:1741. Black section; 3 columns divided by faint left borders,
 * each with Challenge / Solution / Result. Hidden when no case studies exist.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_cs = new WP_Query( array(
	'post_type'      => 'deon_case_study',
	'posts_per_page' => 6,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'no_found_rows'  => true,
) );

if ( ! $deon_cs->have_posts() ) {
	return;
}
?>
<section class="w-full bg-deon-dark">
	<div class="max-w-[1280px] mx-auto px-4 py-[64px] md:px-16 md:py-(--section-pad) flex flex-col gap-[48px] md:gap-12">
		<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.32px] text-white!" data-anim>
			<?php esc_html_e( 'Impact & Execution', 'deon-energy' ); ?>
		</h2>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-[32px] md:gap-[24px]" data-anim-stagger>
			<?php while ( $deon_cs->have_posts() ) : $deon_cs->the_post();
				$deon_challenge = get_post_meta( get_the_ID(), '_deon_cs_challenge', true );
				$deon_solution  = get_post_meta( get_the_ID(), '_deon_cs_solution', true );
				$deon_result    = get_post_meta( get_the_ID(), '_deon_cs_result', true );
			?>
			<article class="border-l border-white/20 pl-[33px] py-[16px] flex flex-col gap-[32px]">
				<h3 class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] text-deon-accent!">
					<?php the_title(); ?>
				</h3>
				<div class="flex flex-col gap-[32px]">
					<?php if ( $deon_challenge ) : ?>
					<div class="flex flex-col gap-[8px]">
						<span class="font-sans font-normal text-[10px] leading-[15px] uppercase text-white/50"><?php esc_html_e( 'Challenge', 'deon-energy' ); ?></span>
						<p class="font-sans font-normal leading-relaxed text-white"><?php echo esc_html( $deon_challenge ); ?></p>
					</div>
					<?php endif; ?>
					<?php if ( $deon_solution ) : ?>
					<div class="flex flex-col gap-[8px]">
						<span class="font-sans font-normal text-[10px] leading-[15px] uppercase text-white/50"><?php esc_html_e( 'Solution', 'deon-energy' ); ?></span>
						<p class="font-sans font-normal leading-relaxed text-white"><?php echo esc_html( $deon_solution ); ?></p>
					</div>
					<?php endif; ?>
					<?php if ( $deon_result ) : ?>
					<div class="flex flex-col gap-[8px]">
						<span class="font-sans font-normal text-[10px] leading-[15px] uppercase text-white/50"><?php esc_html_e( 'Result', 'deon-energy' ); ?></span>
						<p class="font-sans font-bold text-h3 leading-snug text-deon-accent"><?php echo esc_html( $deon_result ); ?></p>
					</div>
					<?php endif; ?>
				</div>
			</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
