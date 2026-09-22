<?php
/**
 * Generic page template.
 *
 * The fallback for any Page without a dedicated `page-{slug}.php` — Privacy
 * Policy, Terms of Use, Disclaimer, Thank You, and whatever else the client
 * adds later. Before this file existed those pages fell through to index.php,
 * which renders a bare title + content with no container and no CSS at all
 * (`.content-fallback` is unstyled), so a newly created page looked broken.
 *
 * Shape matches every other inner page: shared hero (eyebrow-less, breadcrumb
 * included) then the editor content in `.deon-prose`. The "Hero Background"
 * meta box works here too — page-hero reads it via deon_get_hero_bg().
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">

	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/global/page-hero', null, array(
			'title' => wp_strip_all_tags( get_the_title() ),
			'bg'    => 'offwhite',
		) );
		?>

		<section class="w-full bg-deon-bg">
			<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">
				<div class="deon-prose max-w-[72ch]">
					<?php the_content(); ?>
					<?php
					// Multi-page posts (<!--nextpage-->) — legal documents are the
					// most likely place in this theme for one.
					wp_link_pages( array(
						'before' => '<div class="deon-pager">',
						'after'  => '</div>',
					) );
					?>
				</div>
			</div>
		</section>

		<?php
	endwhile;
	?>

</main>

<?php get_footer(); ?>
