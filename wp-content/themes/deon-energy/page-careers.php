<?php
/**
 * Template Name: Careers
 *
 * Page template — Careers.
 *
 * Slug: careers. Phase 2 (Tailwind v4). Open Positions pull from the deon_job
 * CPT; hero counter and roles-available label reflect the live published count.
 * Everything else (Life at Deon, Hiring Process, dark CTA) is static.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">
	<?php
	get_template_part( 'template-parts/careers/hero' );
	get_template_part( 'template-parts/careers/life' );
	get_template_part( 'template-parts/careers/hiring' );
	get_template_part( 'template-parts/careers/positions' );
	get_template_part( 'template-parts/careers/cta' );
	?>
</main>

<?php get_footer(); ?>
