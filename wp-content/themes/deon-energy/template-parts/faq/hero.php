<?php
/**
 * FAQ — hero. Delegates to the shared inner-page hero (redesign wave 1).
 * The live-filter input ([data-faq-search], filtered by main.js) rides in the
 * shared hero's search slot — the id and data attribute are unchanged.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => __( 'Frequently Asked Questions', 'deon-energy' ),
	'title'     => __( 'Answers, not sales pitches.', 'deon-energy' ),
	// The approved copy (PDF §10) gives this page an eyebrow + H1 only.
	'lead'      => '',
	'image'     => get_template_directory_uri() . '/assets/img/project-3.jpg',
	'image_alt' => __( 'Rooftop solar installation spanning an industrial facility', 'deon-energy' ),
	'bg'        => 'offwhite',
	'search'    => true,
) );
