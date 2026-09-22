<?php
/**
 * Contact — hero. Delegates to the shared inner-page hero (redesign wave 1).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => __( 'Contact', 'deon-energy' ),
	'title'     => __( 'Contact Us', 'deon-energy' ),
	// The approved copy (PDF §12) supplies direct channels, not hero prose.
	'lead'      => '',
	'image'     => get_template_directory_uri() . '/assets/img/contact-newsletter-bg.png',
	'image_alt' => __( 'Solar array at dusk against a mountain horizon', 'deon-energy' ),
	'bg'        => 'offwhite',
) );
