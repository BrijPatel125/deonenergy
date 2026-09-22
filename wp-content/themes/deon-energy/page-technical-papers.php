<?php
/**
 * Template Name: Technical Papers
 *
 * Library of downloadable technical papers / whitepapers. Linked from the footer
 * Resources column ("Technical Papers" → /technical-papers/).
 *
 * Create a Page named "Technical Papers" with slug `technical-papers`; WordPress
 * resolves this template from the slug automatically (page-{slug}.php), or it can
 * be picked explicitly under Page Attributes → Template — same convention as
 * page-news-media.php / page-press-coverage.php.
 *
 * Data source (no new CPT): the existing `deon_document` CPT filed under the
 * `technical-papers` deon_document_type term (seeded by inc/news-media.php). The
 * page still renders without that file required — it just shows the empty state.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">

	<?php
	get_template_part( 'template-parts/technical-papers/hero' );
	?>

	<?php
	// Optional editor content, rendered between the hero and the paper list.
	while ( have_posts() ) :
		the_post();
		$deon_body = trim( get_the_content() );
		if ( '' === $deon_body ) {
			continue;
		}
		?>
		<section class="w-full bg-white">
			<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] pt-(--section-pad)">
				<div class="deon-prose max-w-[72ch]"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	endwhile;
	?>

	<?php get_template_part( 'template-parts/technical-papers/papers' ); ?>

	<?php get_template_part( 'template-parts/technical-papers/articles' ); ?>

</main>

<?php get_footer(); ?>
