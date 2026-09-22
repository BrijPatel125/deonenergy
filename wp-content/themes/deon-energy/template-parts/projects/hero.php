<?php
/**
 * Project Gallery — hero. Delegates to the shared inner-page hero so its shape
 * matches About / Solutions (eyebrow → centered title, no lead, no stats).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/page-hero', null, array(
	// The approved copy (PDF §5) gives this page an H1 only — no eyebrow.
	'title'   => __( 'Our Projects', 'deon-energy' ),
	'bg'      => 'offwhite',
) );
