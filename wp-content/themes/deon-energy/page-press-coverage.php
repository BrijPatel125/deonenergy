<?php
/**
 * Template Name: Press Coverage
 *
 * Paginated archive of every deon_press mention. Create a Page named
 * "Press Coverage" (slug: press-coverage) and assign this template (or the
 * slug auto-resolves it). The Knowledge Hub "In The Press" strip links here
 * via its "View All Coverage" button once more than 4 mentions exist.
 *
 * Pagination uses a ?pg= query arg to avoid the static-page /page/N/ 404.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$deon_per_page = 12;
$deon_pg       = isset( $_GET['pg'] ) ? max( 1, (int) $_GET['pg'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only pagination.

$deon_press = new WP_Query( array(
	'post_type'      => 'deon_press',
	'posts_per_page' => $deon_per_page,
	'paged'          => $deon_pg,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
?>

<main class="bg-deon-bg">

	<?php
	get_template_part( 'template-parts/blog/hero', null, array(
		'eyebrow'  => __( 'Media Coverage', 'deon-energy' ),
		'title'    => __( 'Press Coverage', 'deon-energy' ),
		'subtitle' => __( 'Every feature, interview, and mention of Deon Energy across the press.', 'deon-energy' ),
		'image'     => get_template_directory_uri() . '/assets/img/blog-2.png',
		'image_alt' => __( 'Detail of dark solar module surfaces in raking light', 'deon-energy' ),
	) );
	?>

	<section class="w-full bg-deon-bg">
		<div class="max-w-[1440px] mx-auto px-4 py-[64px] md:px-[80px] md:py-(--section-pad) flex flex-col gap-12">

			<?php if ( $deon_press->have_posts() ) : ?>

				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[24px] items-start" data-anim-stagger>
					<?php while ( $deon_press->have_posts() ) : $deon_press->the_post(); ?>
						<?php get_template_part( 'template-parts/blog/press-card' ); ?>
					<?php endwhile; ?>
				</div>

				<?php
				$deon_base = get_permalink();
				deon_pagination( array(
					'current' => $deon_pg,
					'total'   => (int) $deon_press->max_num_pages,
					'label'   => __( 'Press coverage pagination', 'deon-energy' ),
					'url'     => function ( $page ) use ( $deon_base ) {
						return 1 === $page ? $deon_base : add_query_arg( 'pg', $page, $deon_base );
					},
				) );
				?>

				<?php wp_reset_postdata(); ?>

			<?php else : ?>

				<div class="border border-deon-divider bg-white py-16 px-8 text-center">
					<p class="font-sans font-bold text-h3 leading-snug text-deon-heading mb-2">
						<?php esc_html_e( 'No press coverage yet.', 'deon-energy' ); ?>
					</p>
					<p class="font-sans font-normal leading-relaxed text-deon-body">
						<?php esc_html_e( 'Media mentions will appear here as they are published.', 'deon-energy' ); ?>
					</p>
				</div>

			<?php endif; ?>

		</div>
	</section>

</main>

<?php get_footer(); ?>
