<?php
/**
 * Single project detail — deon_project CPT.
 *
 * Hero (type eyebrow + title + location/capacity), overview (post editor body),
 * photo gallery with lightbox, and the shared projects CTA. Built to the site
 * design language (Tailwind v4 + spec tokens).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

<article class="bg-white">
	<?php
	get_template_part( 'template-parts/project-single/hero' );
	get_template_part( 'template-parts/project-single/overview' );
	/*
	 * Spec table. Removed 2026-07-22 (client change log v1 §5), restored
	 * 2026-08-09 (client): Grid Voltage / Annual Output / Technology / EPC
	 * Partner / Client-Owner had no other outlet on a project page, so five
	 * fields in the "Project Details" meta box published nowhere. Sits after
	 * the overview so the prose leads; hides itself when nothing is filled.
	 */
	get_template_part( 'template-parts/project-single/specs' );
	get_template_part( 'template-parts/project-single/milestones' );
	get_template_part( 'template-parts/project-single/video' );
	get_template_part( 'template-parts/project-single/gallery' );
	get_template_part( 'template-parts/project-single/map' );
	get_template_part( 'template-parts/project-single/related' );
	get_template_part( 'template-parts/projects/cta' );
	?>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
