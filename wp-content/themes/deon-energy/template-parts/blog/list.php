<?php
/**
 * Knowledge Hub — article grid.
 *
 * Full-width responsive card grid: 1 column on mobile, 2 on tablet, 3 on
 * desktop, 4 on large screens. Each post renders through card-image.php.
 *
 * Replaces the earlier sidebar (Categories nav) + one-full-width-row-per-post
 * layout that mirrored the Technical Papers page. Sidebar intentionally
 * dropped per client request (chat, Sep 2026) in favour of a straight card
 * grid — category archives are still reachable via the main nav / direct
 * links, this page just no longer surfaces an on-page category filter.
 *
 * Runs off the main query, so home.php, archive.php and search.php all reuse it.
 * Args:
 *   'empty_title' / 'empty_text' — copy for the no-posts state.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_a = is_array( $args ) ? $args : array();

$deon_empty_title = isset( $deon_a['empty_title'] ) ? (string) $deon_a['empty_title'] : __( 'Nothing published yet.', 'deon-energy' );
$deon_empty_text  = isset( $deon_a['empty_text'] ) ? (string) $deon_a['empty_text'] : __( 'New insights, analysis, and technical reporting will appear here as they are released.', 'deon-energy' );
?>
<section id="latest-perspectives" class="w-full bg-white">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">

		<?php if ( have_posts() ) : ?>

			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-[32px]" data-anim-stagger>
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/blog/card-image' );
				endwhile;
				?>
			</div>

			<div class="flex justify-center pt-[40px]">
				<?php get_template_part( 'template-parts/blog/pagination' ); ?>
			</div>

		<?php else : ?>

			<div class="border border-deon-divider bg-deon-bg px-[28px] py-[52px] text-center" data-anim>
				<p class="font-display font-bold text-h3 leading-[1.3] text-deon-heading mb-2">
					<?php echo esc_html( $deon_empty_title ); ?>
				</p>
				<p class="font-sans font-normal text-lead leading-[1.6] text-deon-body max-w-[54ch] mx-auto!">
					<?php echo esc_html( $deon_empty_text ); ?>
				</p>
			</div>

		<?php endif; ?>

	</div>
</section>