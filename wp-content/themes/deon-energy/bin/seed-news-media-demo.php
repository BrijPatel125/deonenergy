<?php
/**
 * OPTIONAL DEMO seeder for the News & Media page — structure only, no fake data.
 *
 *     wp eval-file bin/seed-news-media-demo.php
 *
 * What it does:
 *   • ensures the `brand-kit` / `brand-assets` / `media-kit` deon_document_type
 *     terms exist (normally created by inc/news-media.php on init);
 *   • creates the three Brand Asset card stubs named in the approved copy
 *     (PDF §8: Logo Files, Project Photography, Brand Guidelines) with those
 *     descriptions and icons, but NO file attached — the client uploads the
 *     real files into these existing entries.
 *
 * What it deliberately does NOT do:
 *   • it seeds NO `deon_press` mentions. The client has not supplied the media
 *     coverage links; fabricated coverage must never ship. The Media Library
 *     section renders its "coming soon" state until real mentions are entered.
 *
 * Idempotent — entries are matched by slug and updated in place. This is a dev
 * convenience, not part of the theme runtime; safe to delete after seeding.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-news-media-demo.php\n" );
	exit( 1 );
}

if ( ! post_type_exists( 'deon_document' ) || ! taxonomy_exists( 'deon_document_type' ) ) {
	WP_CLI::error( 'deon_document / deon_document_type are not registered. Is the Deon Energy theme active?' );
}

/* ── Document-type terms (mirrors inc/news-media.php, in case it is not yet required) ── */
$terms = array(
	'brand-kit'    => array( 'Brand Kit', 'The single master brand kit archive offered as the primary download on News & Media.' ),
	'brand-assets' => array( 'Brand Assets', 'Logos, colour palettes, guidelines and press imagery. Each document here renders as a Brand Assets card.' ),
	'media-kit'    => array( 'Media Kit', 'Fact sheets, company boilerplate and press-ready material for journalists.' ),
);

foreach ( $terms as $slug => $data ) {
	if ( ! term_exists( $slug, 'deon_document_type' ) ) {
		wp_insert_term( $data[0], 'deon_document_type', array( 'slug' => $slug, 'description' => $data[1] ) );
		WP_CLI::log( "Created document type: {$data[0]}" );
	}
}

/* ── Brand Asset card stubs ─────────────────────────────────────────────── */
$assets = array(
	array(
		'slug'  => 'identity-and-logo',
		'title' => 'Logo Files',
		'desc'  => 'Master logo files in CMYK and RGB, in both light and dark variants.',
		'icon'  => 'box',
		'cta'   => 'View Assets',
		'order' => 10,
	),
	array(
		'slug'  => 'press-imagery',
		'title' => 'Project Photography',
		'desc'  => 'High-resolution photography of Deon Energy installations, cleared for editorial use.',
		'icon'  => 'image',
		'cta'   => 'Access Gallery',
		'order' => 20,
	),
	array(
		'slug'  => 'brand-guidelines',
		'title' => 'Brand Guidelines',
		'desc'  => 'Colour palette, typography, clear-space rules and correct logo usage.',
		'icon'  => 'palette',
		'cta'   => 'View Guidelines',
		'order' => 30,
	),
);

foreach ( $assets as $asset ) {
	$existing = get_page_by_path( $asset['slug'], OBJECT, 'deon_document' );

	$postarr = array(
		'post_type'   => 'deon_document',
		'post_status' => 'publish',
		'post_title'  => $asset['title'],
		'post_name'   => $asset['slug'],
		'menu_order'  => $asset['order'],
	);

	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( $postarr, true );
		$verb          = 'Updated';
	} else {
		$post_id = wp_insert_post( $postarr, true );
		$verb    = 'Created';
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed on {$asset['title']}: " . $post_id->get_error_message() );
		continue;
	}

	wp_set_object_terms( $post_id, 'brand-assets', 'deon_document_type', false );

	update_post_meta( $post_id, '_deon_news_asset_desc', $asset['desc'] );
	update_post_meta( $post_id, '_deon_news_asset_icon', $asset['icon'] );
	update_post_meta( $post_id, '_deon_news_asset_cta', $asset['cta'] );

	WP_CLI::log( "{$verb} brand asset: {$asset['title']} (upload the real file under 'File URL')" );
}

WP_CLI::success( 'News & Media scaffolding seeded. No press coverage was created — enter real mentions under Press.' );
