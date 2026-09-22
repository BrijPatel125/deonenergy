<?php
/**
 * Template Name: Investor Relations
 *
 * Page template — Investor Relations (slug: investor-relations).
 * Public IR page + downloadable-documents list (deon_document CPT).
 * Investor portal / login / live feeds are out of scope (SOW §4).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">
	<?php
	get_template_part( 'template-parts/investors/hero' );
	// Dark stat band sits directly under the hero, matching Projects + ESG.
	get_template_part( 'template-parts/investors/financial-highlights' );
	get_template_part( 'template-parts/investors/ipo-overview' );
	get_template_part( 'template-parts/investors/videos' );
	get_template_part( 'template-parts/investors/resources' );
	get_template_part( 'template-parts/investors/cta' );
	?>
</main>

<?php get_footer(); ?>
