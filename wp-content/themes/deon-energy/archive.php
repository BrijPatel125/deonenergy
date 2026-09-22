<?php
/**
 * Archive — category / tag / author / date listings for Knowledge Hub posts.
 *
 * Shares the hero + list partial with home.php so a category archive is simply
 * the Knowledge Hub listing scoped to one term (the sidebar highlights it).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

if ( is_category() ) {
	$deon_eyebrow = __( 'Category', 'deon-energy' );
	$deon_title   = single_cat_title( '', false );
} elseif ( is_tag() ) {
	$deon_eyebrow = __( 'Tag', 'deon-energy' );
	$deon_title   = single_tag_title( '', false );
} elseif ( is_author() ) {
	$deon_eyebrow = __( 'Author', 'deon-energy' );
	$deon_title   = get_the_author();
} else {
	$deon_eyebrow = __( 'Knowledge Hub', 'deon-energy' );
	$deon_title   = wp_strip_all_tags( get_the_archive_title() );
}
$deon_desc = wp_strip_all_tags( get_the_archive_description() );

get_template_part( 'template-parts/blog/hero', null, array(
	'eyebrow'  => $deon_eyebrow,
	'title'    => $deon_title,
	'subtitle' => $deon_desc,
	'image'    => '',
) );

get_template_part( 'template-parts/blog/list', null, array(
	'empty_title' => __( 'Nothing here yet.', 'deon-energy' ),
	'empty_text'  => __( 'No articles match this archive.', 'deon-energy' ),
) );

get_footer();
