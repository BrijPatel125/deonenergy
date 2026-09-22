<?php
/**
 * Solutions page — hero. Delegates to the shared inner-page hero (redesign wave 1).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => __( 'What We Do', 'deon-energy' ),
	'title'     => __( 'Our Solar Solutions', 'deon-energy' ),
	// No hero lead in the approved copy — section 01 opens the page.
	'image'     => get_template_directory_uri() . '/assets/img/sol-system-design.jpg',
	'image_alt' => __( 'Engineering workstation showing a photovoltaic array layout and yield model', 'deon-energy' ),
	'bg'        => 'offwhite',
) );
