<?php
/**
 * Solar Calculator — hero. Delegates to the shared inner-page hero (redesign wave 1).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => __( 'Estimation Tool', 'deon-energy' ),
	'title'     => __( 'Solar Savings Calculator', 'deon-energy' ),
	// The approved copy (PDF §11) gives this page an eyebrow + H1 only.
	'lead'      => '',
	'image'     => get_template_directory_uri() . '/assets/img/calc-solar-panel.png',
	'image_alt' => __( 'Close-up of a monocrystalline solar panel surface at golden hour', 'deon-energy' ),
	// Section rhythm: inner-page heroes are uniformly off-white across the site.
	'bg'        => 'offwhite',
) );
