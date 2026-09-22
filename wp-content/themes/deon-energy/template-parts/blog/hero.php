<?php
/**
 * Knowledge Hub — hero (listing, archives, search, press coverage).
 *
 * Thin adapter over the shared inner-page hero (redesign wave 1). The original
 * args API is preserved so existing callers keep working:
 *
 *   get_template_part( 'template-parts/blog/hero', null, array(
 *       'eyebrow'   => 'Knowledge Hub',
 *       'title'     => '…',
 *       'subtitle'  => '…',   // maps to the shared hero's `lead`
 *       'image'     => '…',   // optional override
 *       'image_alt' => '…',
 *       'bg'        => 'white' | 'offwhite',
 *   ) );
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_a = is_array( $args ) ? $args : array();

$deon_eyebrow  = isset( $deon_a['eyebrow'] ) ? $deon_a['eyebrow'] : __( 'Knowledge Hub', 'deon-energy' );
$deon_title    = isset( $deon_a['title'] ) ? $deon_a['title'] : __( "Insights from Gujarat's solar experts.", 'deon-energy' );
$deon_subtitle = isset( $deon_a['subtitle'] ) ? $deon_a['subtitle'] : __( 'Analysis, technical resources, and industry reporting on renewable energy in India.', 'deon-energy' );

$deon_image = isset( $deon_a['image'] )
	? $deon_a['image']
	: get_template_directory_uri() . '/assets/img/blog-1.png';
$deon_alt   = isset( $deon_a['image_alt'] )
	? $deon_a['image_alt']
	: __( 'Rows of photovoltaic modules under a clear midday sun', 'deon-energy' );

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => $deon_eyebrow,
	'title'     => $deon_title,
	'lead'      => $deon_subtitle,
	'image'     => $deon_image,
	'image_alt' => $deon_alt,
	'bg'        => isset( $deon_a['bg'] ) ? $deon_a['bg'] : 'offwhite',
) );
