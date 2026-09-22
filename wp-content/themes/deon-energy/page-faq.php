<?php
/**
 * Template Name: FAQ
 *
 * Page template — Frequently Asked Questions (Knowledge Base).
 *
 * Slug: faq. Phase 2 (Tailwind v4). Categories + questions pull from the
 * deon_faq CPT grouped by the deon_faq_category taxonomy (see deon_faq_groups()).
 * Hero, CTA band and the two bottom cards are static.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">
	<?php
	get_template_part( 'template-parts/faq/hero' );
	get_template_part( 'template-parts/faq/main' );
	get_template_part( 'template-parts/faq/cta' );
	?>
</main>

<?php get_footer(); ?>
