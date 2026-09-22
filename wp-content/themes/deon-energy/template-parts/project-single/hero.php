<?php
/**
 * Project detail — hero. Delegates to the shared inner-page hero (redesign
 * wave 1): project-type eyebrow, title, location/capacity fact row, and the
 * project's featured image in the shared side panel.
 *
 * The old full-bleed 520px image band below the hero is gone — the featured
 * image now lives inside the hero, which is what kept real content off-screen.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_terms    = get_the_terms( get_the_ID(), 'deon_project_type' );
$deon_term     = ( $deon_terms && ! is_wp_error( $deon_terms ) ) ? $deon_terms[0] : null;
$deon_capacity = get_post_meta( get_the_ID(), '_deon_capacity', true );
$deon_location = get_post_meta( get_the_ID(), '_deon_location', true );

$deon_img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
if ( ! $deon_img ) {
	$deon_img = get_template_directory_uri() . '/assets/img/project-featured-main.jpg';
}

/*
 * Hero background: the project's own featured image, at full size so it holds
 * up full-bleed. Passed as `bg_image`, i.e. only used when the "Hero
 * Background" meta box is empty — an explicitly chosen hero image/video still
 * wins. No featured image => no stock stand-in, the hero stays the plain
 * colour band (the placeholder above only feeds the side panel).
 */
$deon_hero_bg_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );

/*
 * Fact row. Each item carries an icon key so the hero uses the same glyphs as
 * the Project Gallery cards (pin / bolt / calendar) instead of a plain bullet —
 * see deon_meta_icon() in functions.php.
 */
$deon_year = get_post_meta( get_the_ID(), '_deon_commissioned', true );
if ( $deon_year && preg_match( '/\d{4}/', $deon_year, $deon_m ) ) {
	$deon_year = $deon_m[0];
}

$deon_meta = array();
if ( $deon_location ) {
	$deon_meta[] = array( 'icon' => 'location', 'text' => $deon_location );
}
if ( $deon_capacity ) {
	$deon_meta[] = array( 'icon' => 'capacity', 'text' => $deon_capacity );
}
if ( $deon_year ) {
	$deon_meta[] = array( 'icon' => 'date', 'text' => $deon_year );
}
/*
 * Project Status is deliberately NOT in the hero row (client 2026-08-09):
 * three facts read cleanly, four wrapped to a second line on mobile. It is
 * rendered in the spec grid instead — see template-parts/project-single/specs.php.
 */

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => $deon_term ? $deon_term->name : __( 'Project', 'deon-energy' ),
	'title'     => wp_strip_all_tags( get_the_title() ),
	'image'     => $deon_img,
	'image_alt' => sprintf(
		/* translators: %s: project name */
		__( '%s solar project site', 'deon-energy' ),
		wp_strip_all_tags( get_the_title() )
	),
	'bg_image'  => $deon_hero_bg_img,
	// No-media fallback matches every other inner-page hero (off-white).
	'bg'        => 'offwhite',
	'meta'      => $deon_meta,
) );
