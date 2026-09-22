<?php
/**
 * Knowledge Hub — blog listing (WordPress "Posts page").
 *
 * Assign a Page named "Knowledge Hub" (slug knowledge-hub) as the Posts page
 * under Settings → Reading; WordPress then serves this template.
 *
 * Client-approved redesign (2026-08-07): centred hero + the Technical Papers
 * style article list (categories sidebar / newsletter box / stacked rows). The
 * Impact, Resource Archive and In The Press modules are hidden — see the note
 * above get_footer().
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

get_template_part( 'template-parts/blog/hero', null, array(
	'eyebrow'  => __( 'Insights & Analysis', 'deon-energy' ),
	'title'    => __( 'Knowledge Hub', 'deon-energy' ),
	'subtitle' => '',
	'image'    => '',
) );

get_template_part( 'template-parts/blog/list', null, array(
	'empty_title' => __( 'No articles published yet.', 'deon-energy' ),
	'empty_text'  => __( 'New insights, analysis, and technical reporting will appear here as they are released.', 'deon-energy' ),
) );

/*
 * Resource Archive + In The Press are hidden on this page per client request
 * (2026-08-07). The partials and their CPTs stay intact — Documents still power
 * News & Media / Technical Papers, Press still powers the Press Coverage page.
 * Re-enable by uncommenting the two calls below.
 *
 * get_template_part( 'template-parts/blog/resource-archive' );
 * get_template_part( 'template-parts/blog/press' );
 */

get_footer();
