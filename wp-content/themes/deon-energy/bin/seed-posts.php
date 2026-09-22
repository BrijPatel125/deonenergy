<?php
/**
 * One-off seeder for the Knowledge Hub blog (native WordPress posts).
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-posts.php
 *     wp eval-file bin/seed-posts.php remove   # delete the seeded posts + images
 *
 * Seeds 6 layout-preview articles (3 featured + 3 compact) with categories,
 * excerpts, publish dates and featured images. Idempotent: re-running updates
 * the same posts (matched by slug) instead of duplicating.
 *
 * STAGING ONLY — REMOVE BEFORE LAUNCH. The approved copy is explicit
 * (PDF §7 Knowledge Hub): the example excerpts are tone references and
 * "do not publish placeholder posts". These six exist to prove the listing,
 * card, pagination and single-article templates render; they are not Deon
 * content and must not be live on a public site. Tear them down with the
 * `remove` argument above once real articles exist.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-posts.php\n" );
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// Positional `remove`, not `--remove`: WP-CLI rejects unknown leading-dash
// flags before eval-file ever runs the script.
if ( in_array( 'remove', (array) ( $args ?? array() ), true ) ) {
	$deon_seeded_slugs = array(
		'future-of-utility-scale-solar-western-india',
		'maximizing-yield-bi-facial-module-efficiency',
		'quarterly-energy-market-report-q3',
		'smart-grid-integration-protocols',
		'circular-economy-in-solar-om',
		'hydrogen-the-next-frontier',
	);

	$deon_removed = 0;

	foreach ( $deon_seeded_slugs as $deon_slug ) {
		$deon_post = get_page_by_path( $deon_slug, OBJECT, 'post' );
		if ( ! $deon_post ) {
			continue;
		}
		$deon_thumb = get_post_thumbnail_id( $deon_post->ID );
		if ( $deon_thumb ) {
			wp_delete_attachment( $deon_thumb, true );
		}
		wp_delete_post( $deon_post->ID, true );
		++$deon_removed;
	}

	WP_CLI::success( sprintf( 'Removed %d seeded placeholder posts.', $deon_removed ) );
	return;
}

$theme_uri = get_template_directory_uri();

/**
 * Build a body of designed placeholder content so the single-article template
 * (h2 / paragraphs / list / blockquote) renders fully.
 */
$deon_body = function ( $lede, $h2, $quote ) {
	return "<p>{$lede}</p>\n\n"
		. "<h2>{$h2}</h2>\n\n"
		. "<p>Across Gujarat's industrial corridors, the pace of renewable deployment is accelerating. Deon Energy's engineering teams combine utility-scale execution with rigorous performance modelling to keep projects on schedule and above yield targets.</p>\n\n"
		. "<ul>\n<li>Site-specific irradiance and albedo analysis</li>\n<li>Bankable performance-ratio guarantees</li>\n<li>Predictive O&amp;M with automated cleaning cycles</li>\n</ul>\n\n"
		. "<blockquote><p>{$quote}</p></blockquote>\n\n"
		. "<p>The result is infrastructure that performs reliably across decades — the standard Deon Energy sets on every engagement.</p>";
};

$posts = array(
	array(
		'slug'     => 'future-of-utility-scale-solar-western-india',
		'title'    => 'The Future of Utility-Scale Solar in Western India',
		'category' => 'Industry Analysis',
		'excerpt'  => 'Analyzing the policy shifts and technological advancements driving the next decade of solar infrastructure.',
		'date'     => '2023-10-24 09:00:00',
		'image'    => $theme_uri . '/assets/img/blog-1.png',
		'lede'     => 'Western India is on the cusp of a solar decade. Policy tailwinds, falling module prices and maturing grid infrastructure are converging to make utility-scale generation the default choice for industrial power.',
		'h2'       => 'A decade of structural change',
		'quote'    => 'The next ten years will be defined not by capacity added, but by capacity that actually performs.',
	),
	array(
		'slug'     => 'maximizing-yield-bi-facial-module-efficiency',
		'title'    => 'Maximizing Yield: Bi-Facial Module Efficiency',
		'category' => 'Technical Guide',
		'excerpt'  => 'Exploring the technical parameters that dictate bi-facial performance in high-albedo, sandy environments.',
		'date'     => '2023-10-12 09:00:00',
		'image'    => $theme_uri . '/assets/img/blog-2.png',
		'lede'     => 'Bi-facial modules promise double-digit yield gains — but only when ground albedo, mounting height and row spacing are tuned to the site. Here is how we model that trade-off.',
		'h2'       => 'Reading the ground, not just the sky',
		'quote'    => 'In high-albedo terrain, the reflection off the ground can matter as much as the sun overhead.',
	),
	array(
		'slug'     => 'quarterly-energy-market-report-q3',
		'title'    => 'Quarterly Energy Market Report: Q3 Update',
		'category' => 'Market Watch',
		'excerpt'  => 'A comprehensive review of energy pricing, grid stability, and investment flows in the renewable sector.',
		'date'     => '2023-09-28 09:00:00',
		'image'    => $theme_uri . '/assets/img/blog-3.png',
		'lede'     => 'Q3 saw record capital flow into renewable infrastructure alongside firming tariffs. This report unpacks the pricing, grid-stability and investment signals that matter for the quarters ahead.',
		'h2'       => 'Where the capital is going',
		'quote'    => 'Stable grids attract patient capital — and patient capital builds durable energy systems.',
	),
	array(
		'slug'     => 'smart-grid-integration-protocols',
		'title'    => 'Smart Grid Integration Protocols',
		'category' => 'White Paper',
		'excerpt'  => 'Standardizing data exchange between private solar clusters and regional load centers.',
		'date'     => '2023-09-14 09:00:00',
		'image'    => $theme_uri . '/assets/img/blog-1.png',
		'lede'     => 'As private solar clusters proliferate, the interface between generation and regional load centres becomes the critical path. This white paper proposes a standard data-exchange protocol.',
		'h2'       => 'A common language for the grid',
		'quote'    => 'Interoperability is the quiet infrastructure that makes distributed energy dependable.',
	),
	array(
		'slug'     => 'circular-economy-in-solar-om',
		'title'    => 'Circular Economy in Solar O&M',
		'category' => 'Sustainability',
		'excerpt'  => 'Managing the lifecycle of photovoltaic assets through responsible decommissioning and recycling.',
		'date'     => '2023-08-30 09:00:00',
		'image'    => $theme_uri . '/assets/img/blog-2.png',
		'lede'     => 'A solar asset is only as sustainable as its end-of-life plan. We examine how responsible decommissioning and module recycling close the loop on photovoltaic infrastructure.',
		'h2'       => 'Designing for the second life',
		'quote'    => 'True sustainability is measured at the end of an asset\'s life, not the beginning.',
	),
	array(
		'slug'     => 'hydrogen-the-next-frontier',
		'title'    => 'Hydrogen: The Next Frontier',
		'category' => 'Innovation',
		'excerpt'  => 'Assessing the feasibility of solar-to-hydrogen conversion in coastal Gujarat industrial zones.',
		'date'     => '2023-08-16 09:00:00',
		'image'    => $theme_uri . '/assets/img/blog-3.png',
		'lede'     => 'Green hydrogen could turn coastal Gujarat into an export hub. We assess the feasibility of co-locating solar-to-hydrogen conversion with existing industrial demand.',
		'h2'       => 'From electrons to molecules',
		'quote'    => 'Hydrogen lets us store sunshine as fuel — and ship it anywhere.',
	),
);

foreach ( $posts as $data ) {

	$existing = get_page_by_path( $data['slug'], OBJECT, 'post' );

	$postarr = array(
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_excerpt' => $data['excerpt'],
		'post_content' => $deon_body( $data['lede'], $data['h2'], $data['quote'] ),
		'post_date'    => $data['date'],
	);

	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id = wp_update_post( $postarr, true );
		$verb = 'Updated';
	} else {
		$post_id = wp_insert_post( $postarr, true );
		$verb = 'Created';
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( $data['slug'] . ': ' . $post_id->get_error_message() );
		continue;
	}

	// Category (created if missing).
	wp_set_object_terms( $post_id, $data['category'], 'category', false );

	// Featured image — sideload only if one isn't already set.
	if ( ! has_post_thumbnail( $post_id ) ) {
		$att_id = media_sideload_image( $data['image'], $post_id, $data['title'], 'id' );
		if ( is_wp_error( $att_id ) ) {
			WP_CLI::warning( $data['slug'] . ' image: ' . $att_id->get_error_message() );
		} else {
			set_post_thumbnail( $post_id, $att_id );
		}
	}

	WP_CLI::log( sprintf( '%s: %s (#%d)', $verb, $data['title'], $post_id ) );
}

WP_CLI::success( 'Knowledge Hub posts seeded.' );
