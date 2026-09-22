<?php
/**
 * Projects — portfolio statistics helper.
 *
 * Wired by the main thread with:  require_once DEON_DIR . '/inc/projects.php';
 *
 * Registers nothing. It only aggregates the already-published `deon_project`
 * posts into the verified portfolio figures shown by the stat band on the
 * Project Gallery page ( template-parts/projects/stats.php ).
 *
 * Data source, in order:
 *   1. published `deon_project` posts — capacity summed from `_deon_capacity`,
 *      geography parsed from `_deon_location` (both are existing meta fields
 *      registered in deon_project_spec_fields(), functions.php).
 *   2. client-confirmed fallback when nothing is published yet: 154.414 MW /
 *      34 projects / 29 cities & villages / 1 state (Gujarat). These are the
 *      sums computed from the client's own 34 live project listings
 *      ( bin/data/projects.json ). The "50MW" and "300MW" figures quoted
 *      elsewhere on the legacy site are REJECTED — do not reintroduce them.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Client-confirmed fallback figures. Used only when no projects are published.
 *
 * @return array
 */
function deon_project_stats_fallback() {
	return array(
		'total_mw'      => 154.414,
		'project_count' => 34,
		'city_count'    => 29,
		'state_count'   => 1,
		'states'        => array( 'Gujarat' ),
		'is_fallback'   => true,
	);
}

/**
 * Parse a free-text capacity string ( "26 MW", "1.5 MWp", "500 kW" ) to MW.
 *
 * @param string $raw Raw meta value.
 * @return float MW, 0 when unparseable.
 */
function deon_parse_capacity_mw( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return 0.0;
	}
	if ( ! preg_match( '/([0-9]+(?:\.[0-9]+)?)/', $raw, $m ) ) {
		return 0.0;
	}
	$value = (float) $m[1];

	// kW / kWp are stored by some editors — normalise to MW.
	if ( preg_match( '/\bk\s*w/i', $raw ) ) {
		$value = $value / 1000;
	}

	return $value;
}

/**
 * Split a free-text location ( "Soladi, Dhangadhra, Gujarat" or slash form )
 * into its parts. First part is treated as the city/village, last as the state.
 *
 * @param string $raw Raw meta value.
 * @return array{city:string,state:string}
 */
function deon_parse_location_parts( $raw ) {
	$parts = array_values( array_filter( array_map( 'trim', preg_split( '#[,/]#', (string) $raw ) ) ) );
	if ( empty( $parts ) ) {
		return array(
			'city'  => '',
			'state' => '',
		);
	}

	return array(
		'city'  => $parts[0],
		'state' => count( $parts ) > 1 ? end( $parts ) : '',
	);
}

/**
 * Aggregate the published project portfolio.
 *
 * @return array{total_mw:float,project_count:int,city_count:int,state_count:int,states:array,is_fallback:bool}
 */
function deon_project_portfolio_stats() {
	$cached = get_transient( 'deon_project_portfolio_stats' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$ids = get_posts(
		array(
			'post_type'              => 'deon_project',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);

	if ( empty( $ids ) ) {
		$stats = deon_project_stats_fallback();
		set_transient( 'deon_project_portfolio_stats', $stats, HOUR_IN_SECONDS );
		return $stats;
	}

	$total_mw = 0.0;
	$cities   = array();
	$states   = array();

	foreach ( $ids as $id ) {
		$total_mw += deon_parse_capacity_mw( get_post_meta( $id, '_deon_capacity', true ) );

		$place = deon_parse_location_parts( get_post_meta( $id, '_deon_location', true ) );
		if ( '' !== $place['city'] ) {
			$cities[ strtolower( $place['city'] ) ] = $place['city'];
		}
		if ( '' !== $place['state'] ) {
			$states[ strtolower( $place['state'] ) ] = $place['state'];
		}
	}

	$stats = array(
		'total_mw'      => $total_mw,
		'project_count' => count( $ids ),
		'city_count'    => count( $cities ),
		'state_count'   => count( $states ),
		'states'        => array_values( $states ),
		'is_fallback'   => false,
	);

	set_transient( 'deon_project_portfolio_stats', $stats, HOUR_IN_SECONDS );

	return $stats;
}

/**
 * Format MW for display — one decimal place, trailing ".0" dropped.
 *
 * @param float $mw Megawatts.
 * @return string
 */
function deon_format_mw( $mw ) {
	$mw = (float) $mw;
	if ( $mw <= 0 ) {
		return '0';
	}
	$out = number_format_i18n( $mw, 1 );

	return preg_replace( '/\.0$/', '', $out );
}

/**
 * Bust the stats cache whenever a project changes.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function deon_project_stats_flush( $post_id = 0 ) {
	if ( $post_id && 'deon_project' !== get_post_type( $post_id ) ) {
		return;
	}
	delete_transient( 'deon_project_portfolio_stats' );
}
add_action( 'save_post_deon_project', 'deon_project_stats_flush' );
add_action( 'deleted_post', 'deon_project_stats_flush' );
add_action( 'trashed_post', 'deon_project_stats_flush' );
add_action( 'untrashed_post', 'deon_project_stats_flush' );

/**
 * Projects for the homepage "Selected Work" grid.
 *
 * Flagged projects first (`_deon_home_featured`, ordered by menu_order), then
 * the newest published projects to top up a short list. Returns an empty array
 * when no projects exist at all — the partial then keeps its designed
 * placeholders, so a fresh install never renders a gap.
 *
 * @param int $limit How many tiles the grid needs.
 * @return WP_Post[]
 */
function deon_featured_projects( $limit = 4 ) {
	$limit = max( 1, (int) $limit );

	$common = array(
		'post_type'              => 'deon_project',
		'post_status'            => 'publish',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	);

	$featured = get_posts( array_merge( $common, array(
		'posts_per_page' => $limit,
		'meta_key'       => '_deon_home_featured', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value'     => '1',                   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	) ) );

	if ( count( $featured ) >= $limit ) {
		return $featured;
	}

	$fill = get_posts( array_merge( $common, array(
		'posts_per_page' => $limit - count( $featured ),
		'post__not_in'   => wp_list_pluck( $featured, 'ID' ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	) ) );

	return array_merge( $featured, $fill );
}

/**
 * Tile label for a project — title, plus its location when that adds something
 * the title does not already say ( "Manufacturing Plant, Surat" ).
 *
 * @param int $post_id Project ID.
 * @return string
 */
function deon_project_tile_label( $post_id ) {
	$title    = get_the_title( $post_id );
	$location = trim( (string) get_post_meta( $post_id, '_deon_location', true ) );

	if ( ! $location ) {
		return $title;
	}

	// Location meta is often "City, District, State" — the city is enough here.
	$city = trim( (string) strtok( $location, ',' ) );
	if ( ! $city || false !== stripos( $title, $city ) ) {
		return $title;
	}

	/* translators: 1: project title, 2: city. */
	return sprintf( _x( '%1$s, %2$s', 'project tile label', 'deon-energy' ), $title, $city );
}
