<?php
/**
 * Template Name: Solar Savings Calculator
 *
 * Page template — Solar Savings Calculator (slug: calculator).
 *
 * Interactive on-site estimator. Inputs drive a client-side model
 * (assets/js/calc.js) that updates system size, annual savings, payback and the
 * 10-year projection live. Estimate only — a site visit is required for a
 * binding offer (see the disclaimer in the results panel). Note: this exceeds
 * the SOW §4 "static visual only" scope for the calculator.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg pb-0">
	<?php
	get_template_part( 'template-parts/calculator/hero' );
	get_template_part( 'template-parts/calculator/interface' );
	get_template_part( 'template-parts/calculator/architecture' );
	?>
</main>

<?php get_footer(); ?>
