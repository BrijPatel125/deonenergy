<?php
/**
 * Template Name: ESG & Sustainability
 *
 * Page template — ESG & Sustainability.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">
	<?php
	get_template_part( 'template-parts/esg/hero' );
	get_template_part( 'template-parts/esg/stats' );
	get_template_part( 'template-parts/esg/pillars' );
	get_template_part( 'template-parts/esg/community' );
	get_template_part( 'template-parts/esg/sdg' );
	get_template_part( 'template-parts/esg/cta' );
	?>
	<div class="w-full bg-white py-8" aria-hidden="true"></div>
</main>

<?php get_footer(); ?>
