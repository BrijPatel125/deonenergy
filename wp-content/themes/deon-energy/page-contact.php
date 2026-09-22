<?php
/**
 * Template Name: Contact Us
 *
 * Page template — Contact Us.
 *
 * Slug-based: applies to the WP page with slug "contact".
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">
	<?php
	get_template_part( 'template-parts/contact/hero' );
	get_template_part( 'template-parts/contact/main' );
	get_template_part( 'template-parts/contact/channels' );
	?>
</main>

<?php get_footer(); ?>
