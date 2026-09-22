<?php
/**
 * Template Name: About Us
 *
 * About Deon Energy page template.
 * Auto-resolves for any page with slug "about".
 * Also selectable via Page Attributes → Template.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$deon_about_sections = array(
	'hero',
	'overview',
	'values',
	'vision-mission',
	'how-we-work',
	'timeline',
	'certifications',
);

foreach ( $deon_about_sections as $deon_section ) {
	get_template_part( 'template-parts/about/' . $deon_section );
}

get_footer();
