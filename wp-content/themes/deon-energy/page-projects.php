<?php
/**
 * Template Name: Project Gallery
 *
 * Page template — Project Gallery.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">
	<?php
	get_template_part( 'template-parts/projects/hero' );
	// Verified portfolio stat band (change log v1 §5) — the one dark band on this page.
	get_template_part( 'template-parts/projects/stats' );
	get_template_part( 'template-parts/projects/filters' );
	get_template_part( 'template-parts/projects/grid' );
	get_template_part( 'template-parts/projects/featured' );
	get_template_part( 'template-parts/projects/cta' );
	?>
</main>

<?php get_footer(); ?>
