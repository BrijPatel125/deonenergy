<?php
/**
 * Projects — URL-parameter filter support.
 *
 * Wire from functions.php with:  require_once DEON_DIR . '/inc/project-filters.php';
 *
 * Adds `?type=<term-slug>` deep-linking to the Project Gallery page. The value
 * is untrusted input: it is run through sanitize_title() and then validated
 * against the real `deon_project_type` terms before it ever reaches a query.
 * Anything unknown or malformed degrades to "all projects".
 *
 * Registers nothing (no CPT, no taxonomy, no rewrite). Read-only helpers used
 * by template-parts/projects/filters.php and template-parts/projects/grid.php.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All published project-type terms, in admin order.
 *
 * @return WP_Term[] Empty array when the taxonomy has no terms yet.
 */
function deon_project_type_terms() {
	static $terms = null;

	if ( null !== $terms ) {
		return $terms;
	}

	$found = get_terms(
		array(
			'taxonomy'   => 'deon_project_type',
			'hide_empty' => false,
		)
	);

	$terms = ( $found && ! is_wp_error( $found ) ) ? $found : array();

	return $terms;
}

/**
 * The project type requested through `?type=`, validated against real terms.
 *
 * Sanitised with sanitize_title() and matched against deon_project_type. An
 * unknown, empty or malformed slug returns '' — callers must treat that as
 * "show everything" rather than as an empty result set.
 *
 * @return string Term slug, or '' when no valid filter is active.
 */
function deon_active_project_type() {
	static $active = null;

	if ( null !== $active ) {
		return $active;
	}

	$active = '';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only public filter.
	$raw = isset( $_GET['type'] ) ? wp_unslash( $_GET['type'] ) : '';
	if ( ! is_string( $raw ) || '' === $raw ) {
		return $active;
	}

	$slug = sanitize_title( $raw );
	if ( '' === $slug ) {
		return $active;
	}

	foreach ( deon_project_type_terms() as $term ) {
		if ( $term->slug === $slug ) {
			$active = $term->slug;
			break;
		}
	}

	return $active;
}

/**
 * Canonical URL of the gallery page, without any query string.
 *
 * @return string
 */
function deon_projects_base_url() {
	$id = get_queried_object_id();

	if ( $id && 'page' === get_post_type( $id ) ) {
		return trailingslashit( get_permalink( $id ) );
	}

	return home_url( '/projects/' );
}

/**
 * Deep link to the gallery filtered by one project type.
 *
 * @param string $slug Term slug, or 'all' / '' for the unfiltered view.
 * @return string Escaped-on-output-by-caller URL.
 */
function deon_project_filter_url( $slug = '' ) {
	$url = deon_projects_base_url();

	if ( $slug && 'all' !== $slug ) {
		$url = add_query_arg( 'type', rawurlencode( $slug ), $url );
	}

	return $url . '#project-filters';
}
