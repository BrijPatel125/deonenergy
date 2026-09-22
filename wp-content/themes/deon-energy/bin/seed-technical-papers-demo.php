<?php
/**
 * OPTIONAL DEMO seeder for the Technical Papers page — structure only, no fake
 * files.
 *
 *     wp eval-file bin/seed-technical-papers-demo.php
 *
 * What it does:
 *   • ensures the `technical-papers` deon_document_type term exists (normally
 *     created by inc/news-media.php on init);
 *   • creates two deon_document stubs (matching the reference design's featured
 *     papers) filed under `technical-papers`, each with a category eyebrow and
 *     abstract, but NO file attached — the client uploads the real PDF into the
 *     "File URL" field, and optionally a cover image + summary URL.
 *
 * What it deliberately does NOT do:
 *   • it attaches NO PDF and NO cover image. Until the client uploads a real
 *     file, each paper shows its title/abstract but no "Download Whitepaper"
 *     link and no size badge — nothing fake ships.
 *
 * Idempotent — entries are matched by slug and updated in place. Dev convenience,
 * not part of the theme runtime; safe to delete after seeding.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-technical-papers-demo.php\n" );
	exit( 1 );
}

if ( ! post_type_exists( 'deon_document' ) || ! taxonomy_exists( 'deon_document_type' ) ) {
	WP_CLI::error( 'deon_document / deon_document_type are not registered. Is the Deon Energy theme active?' );
}

/* ── Document-type term (mirrors inc/news-media.php, in case it is not yet required) ── */
if ( ! term_exists( 'technical-papers', 'deon_document_type' ) ) {
	wp_insert_term(
		'Technical Papers',
		'deon_document_type',
		array(
			'slug'        => 'technical-papers',
			'description' => 'Downloadable whitepapers, engineering reports and technical studies shown on the Technical Papers page.',
		)
	);
	WP_CLI::log( 'Created document type: Technical Papers' );
}

/* ── Technical paper stubs (no file attached) ───────────────────────────── */
$papers = array(
	array(
		'slug'     => 'grid-stability-solutions-high-penetration-solar',
		'title'    => 'Grid Stability Solutions in High-Penetration Solar Environments',
		'category' => 'Technical Guide',
		'abstract' => 'An empirical study on the impact of utility-scale solar PV on regional grid frequency stability. This paper outlines advanced inverter control algorithms and battery storage synchronization strategies required for the next generation of resilient energy networks.',
		'year'     => '2024',
		'order'    => 10,
	),
	array(
		'slug'     => 'high-efficiency-pv-modules-multi-busbar-advantage',
		'title'    => 'High-Efficiency PV Modules: Analyzing the Multi-Busbar Advantage',
		'category' => 'Engineering Report',
		'abstract' => 'Comparative analysis of electron transport efficiency in standard vs. multi-busbar PV cells. We present laboratory results demonstrating an average 3.2% increase in energy harvest under varied thermal conditions and shading scenarios.',
		'year'     => '2024',
		'order'    => 20,
	),
);

foreach ( $papers as $paper ) {
	$existing = get_page_by_path( $paper['slug'], OBJECT, 'deon_document' );

	$postarr = array(
		'post_type'   => 'deon_document',
		'post_status' => 'publish',
		'post_title'  => $paper['title'],
		'post_name'   => $paper['slug'],
		'menu_order'  => $paper['order'],
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
		WP_CLI::warning( "Failed on {$paper['title']}: " . $post_id->get_error_message() );
		continue;
	}

	wp_set_object_terms( $post_id, 'technical-papers', 'deon_document_type', false );

	update_post_meta( $post_id, '_deon_tp_category', $paper['category'] );
	update_post_meta( $post_id, '_deon_tp_abstract', $paper['abstract'] );
	update_post_meta( $post_id, '_deon_document_year', $paper['year'] );

	WP_CLI::log( "{$verb} technical paper: {$paper['title']} (upload the real PDF under 'File URL')" );
}

WP_CLI::success( 'Technical Papers scaffolding seeded. No PDFs were attached — upload real files under each paper\'s "File URL".' );
