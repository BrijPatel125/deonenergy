<?php
/**
 * About page — hero. Delegates to the shared inner-page hero (redesign wave 1).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => __( 'Who We Are', 'deon-energy' ),
	'title'     => __( 'About Deon Energy', 'deon-energy' ),
	// No hero lead in the approved copy — the intro body opens the Overview section.
	'image'     => get_template_directory_uri() . '/assets/img/about-hero.jpg',
	'image_alt' => __( 'Deon Energy technician inspecting panel rows on a utility-scale solar site', 'deon-energy' ),
	// Section rhythm: About Overview below is white, so the hero takes off-white.
	'bg'        => 'offwhite',
) );
