<?php
/**
 * Knowledge Hub — "In The Press" media mentions (deon_press CPT).
 *
 * Figma node 4:1850. Warm section; up to 4 quote cards with outlet logo,
 * pull-quote and a "Read" link. Hidden when none exist. When more than 4
 * press mentions exist, a "View all coverage" link points to the paginated
 * Press Coverage page (a Page using the page-press-coverage.php template).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_press = new WP_Query( array(
	'post_type'      => 'deon_press',
	'posts_per_page' => 4,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'no_found_rows'  => true,
) );

if ( ! $deon_press->have_posts() ) {
	return;
}

// Total published mentions — drives the "View all" link (shown only when > 4).
$deon_press_total = (int) wp_count_posts( 'deon_press' )->publish;
$deon_press_page  = get_page_by_path( 'press-coverage' );
$deon_press_url   = $deon_press_page ? get_permalink( $deon_press_page ) : '';
?>
<section class="w-full bg-deon-warm">
	<div class="max-w-[1280px] mx-auto px-4 py-[64px] md:px-16 md:py-(--section-pad) flex flex-col gap-[48px] md:gap-12">

		<div class="flex flex-col items-center text-center gap-4" data-anim>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-accent">
				<?php esc_html_e( 'Media Coverage', 'deon-energy' ); ?>
			</span>
			<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
				<?php esc_html_e( 'In The Press', 'deon-energy' ); ?>
			</h2>
			<span class="block w-16 h-[2px] bg-deon-accent" aria-hidden="true"></span>
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[24px] items-stretch" data-anim-stagger>
			<?php while ( $deon_press->have_posts() ) : $deon_press->the_post(); ?>
				<?php get_template_part( 'template-parts/blog/press-card' ); ?>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>

		<?php if ( $deon_press_total > 4 && $deon_press_url ) : ?>
		<div class="flex justify-center" data-anim>
			<a href="<?php echo esc_url( $deon_press_url ); ?>"
			   class="group inline-flex items-center gap-2 border border-deon-heading px-[32px] py-[15px]
			          font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-heading
			          transition-colors hover:bg-deon-heading hover:text-white!">
				<?php esc_html_e( 'View All Coverage', 'deon-energy' ); ?>
				<svg class="w-[10px] h-[10px] transition-transform group-hover:translate-x-0.5" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M1 5h8M5 1l4 4-4 4"/></svg>
			</a>
		</div>
		<?php endif; ?>

	</div>
</section>
