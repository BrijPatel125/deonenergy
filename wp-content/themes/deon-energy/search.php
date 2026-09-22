<?php
/**
 * Search results — reuses the Knowledge Hub hero + article list.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

get_template_part( 'template-parts/blog/hero', null, array(
	'eyebrow'  => __( 'Search', 'deon-energy' ),
	/* translators: %s: search query */
	'title'    => sprintf( __( 'Results for “%s”', 'deon-energy' ), get_search_query() ),
	'subtitle' => '',
	'image'    => '',
) );

get_template_part( 'template-parts/blog/list', null, array(
	'empty_title' => __( 'No results found.', 'deon-energy' ),
	'empty_text'  => __( 'Try a different search term.', 'deon-energy' ),
) );

get_footer();
