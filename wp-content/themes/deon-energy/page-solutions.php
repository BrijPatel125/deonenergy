<?php
/**
 * Template Name: Solutions
 *
 * Core Capabilities / Solutions page.
 * Auto-resolves for any page with slug "solutions".
 * Also selectable via Page Attributes → Template.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$deon_solutions_sections = array(
	'hero',
	'epc',
	'system-design',
	'rooftop',
	'financial-models',
	'operations',
	'advisory',
	'cta',
);

foreach ( $deon_solutions_sections as $deon_section ) {
	get_template_part( 'template-parts/solutions/' . $deon_section );
}

get_footer();
