<?php
/**
 * Investor Relations — hero. Delegates to the shared inner-page hero
 * (redesign wave 1). No badge — the IPO status pill was dropped per client.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => __( 'Shareholder Excellence', 'deon-energy' ),
	'title'     => __( 'Investor Relations', 'deon-energy' ),
	'lead'      => __( 'Disciplined capital allocation, transparent reporting, and long-term value from large-scale solar development.', 'deon-energy' ),
	'image'     => get_template_directory_uri() . '/assets/img/article-3.jpg',
	'image_alt' => __( 'Financial performance dashboard tracking generation and portfolio returns', 'deon-energy' ),
	'bg'        => 'offwhite',
	// IPO status badge removed by client request (2026-08). The
	// `_deon_ir_ipo_status` meta field stays in wp-admin; it is no longer
	// rendered in the hero.
) );
