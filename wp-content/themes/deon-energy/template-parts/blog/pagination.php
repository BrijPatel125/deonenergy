<?php
/**
 * Knowledge Hub / archives / search — pagination.
 *
 * Was a standalone prev/next arrow pair (Figma node 4:1651); replaced by the
 * shared numbered `.deon-pager` control so every paged listing on the site
 * matches (client request 2026-08). See inc/pagination.php.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'deon_the_posts_pagination' ) ) {
	deon_the_posts_pagination( __( 'Posts pagination', 'deon-energy' ) );
}
