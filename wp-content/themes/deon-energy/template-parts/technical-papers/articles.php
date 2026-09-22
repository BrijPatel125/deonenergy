<?php
/**
 * Technical Papers — "From the Knowledge Hub" article grid.
 *
 * Per the client design, the Technical Papers page ends with a grid of latest
 * blog articles (2 rows × 3 on desktop). Fully dynamic: pulls the 6 most recent
 * native posts via deon_recent_posts_query() and reuses the shared Knowledge Hub
 * image card (template-parts/blog/card-image.php) so styling stays in one place.
 *
 * The whole section is hidden when there are no published posts — no placeholder
 * cards, matching the empty-state behaviour of the paper list and Knowledge Hub
 * modules. A "View All Articles" button links to the Knowledge Hub listing.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_articles = function_exists( 'deon_recent_posts_query' ) ? deon_recent_posts_query( 6 ) : null;

if ( ! ( $deon_articles instanceof WP_Query ) || ! $deon_articles->have_posts() ) {
	return;
}

// Posts page (Knowledge Hub) URL for the "View All" button; falls back to home.
$deon_blog_url = ( (int) get_option( 'page_for_posts' ) ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );
?>
<section class="w-full bg-deon-bg border-t border-deon-divider">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">

		<div class="flex flex-col gap-[10px] mb-[36px]" data-anim>
			<p class="font-sans font-bold text-eyebrow tracking-[2px] uppercase text-deon-accent">
				<?php esc_html_e( 'From the Knowledge Hub', 'deon-energy' ); ?>
			</p>
			<h2 class="font-display font-extrabold text-h2 leading-[1.2] tracking-[-0.01em] text-deon-heading">
				<?php esc_html_e( 'Latest Insights', 'deon-energy' ); ?>
			</h2>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px] lg:gap-[32px]" data-anim-stagger>
			<?php
			while ( $deon_articles->have_posts() ) :
				$deon_articles->the_post();
				get_template_part( 'template-parts/blog/card-image' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<div class="flex justify-center mt-[40px]" data-anim>
			<a href="<?php echo esc_url( $deon_blog_url ); ?>"
			   class="inline-flex items-center gap-2 border border-deon-heading px-[28px] py-[14px] font-sans font-bold text-[12px] tracking-[1.2px] uppercase text-deon-heading transition-colors hover:bg-deon-heading hover:text-white">
				<?php esc_html_e( 'View All Articles', 'deon-energy' ); ?>
				<svg class="w-[10px] h-[10px]" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M1 5h8M5 1l4 4-4 4"/></svg>
			</a>
		</div>

	</div>
</section>
