<?php
/**
 * Careers — hero. Delegates to the shared inner-page hero (redesign wave 1).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Plain warm hero — no hero photo, no stat row.
get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow' => __( 'Careers', 'deon-energy' ),
	'title'   => __( 'Careers at Deon', 'deon-energy' ),
	'lead'    => __( 'Real projects, real sites, real impact. Join a team that builds infrastructure meant to last.', 'deon-energy' ),
	'bg'      => 'offwhite',
) );