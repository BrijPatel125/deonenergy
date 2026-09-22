<?php
/**
 * Template Name: Investor Documents
 *
 * Downloadable-documents listing for the Investor Relations page. Reached from
 * an Investor Resources card via ?type=<document-type-slug>; with no/invalid
 * type it lists every type grouped. Slug: investor-documents.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">
	<?php get_template_part( 'template-parts/investors/documents-list' ); ?>
</main>

<?php get_footer(); ?>
