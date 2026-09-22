<?php
/**
 * ESG & Sustainability — hero. Delegates to the shared inner-page hero
 * (redesign wave 1). Copy and image stay ACF-overridable.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_acf     = function_exists( 'get_field' );
$deon_eyebrow = $deon_acf ? get_field( 'esg_hero_eyebrow' ) : '';
$deon_heading = $deon_acf ? get_field( 'esg_hero_heading' ) : '';
$deon_intro   = $deon_acf ? get_field( 'esg_hero_intro' ) : '';
$deon_image   = $deon_acf ? get_field( 'esg_hero_image' ) : '';

$deon_img_url = ( is_array( $deon_image ) && ! empty( $deon_image['url'] ) )
	? $deon_image['url']
	: get_template_directory_uri() . '/assets/img/esg-hero.png';
$deon_img_alt = ( is_array( $deon_image ) && ! empty( $deon_image['alt'] ) )
	? $deon_image['alt']
	: __( 'Utility-scale solar array at sunrise', 'deon-energy' );

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => $deon_eyebrow ? $deon_eyebrow : __( 'Environmental, Social & Governance', 'deon-energy' ),
	'title'     => $deon_heading ? $deon_heading : __( 'ESG & Sustainability', 'deon-energy' ),
	// The approved copy (PDF §6) gives this page an eyebrow + H1 only; a lead
	// still renders if the client types one into the ACF field.
	'lead'      => $deon_intro ? $deon_intro : '',
	'image'     => $deon_img_url,
	'image_alt' => $deon_img_alt,
	// Match About / Projects: inner-page heroes all sit on off-white.
	'bg'        => 'offwhite',
) );
