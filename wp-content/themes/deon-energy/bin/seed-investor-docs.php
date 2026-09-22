<?php
/**
 * One-off seeder for the Investor Relations page — deon_investor_doc CPT.
 *
 * Populates the "Latest Financial Disclosures" list + the Featured Document card
 * with demo data so staging looks real for client review. Each document gets the
 * bundled sample PDF sideloaded, so DOWNLOAD links and the "PDF • size" meta line
 * resolve. Categories (deon_investor_category) are seeded by the theme on init;
 * this script only assigns documents to them.
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-investor-docs.php
 *
 * Idempotent: documents are matched by slug and updated in place. Safe to re-run.
 * Delete this file after seeding staging — it is a dev utility, not theme runtime.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-investor-docs.php\n" );
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$theme_dir  = get_template_directory();
$sample_pdf = $theme_dir . '/deo_sow.pdf'; // Bundled sample so file size/format resolve.

/* slug => [ title, content, year, category-slug, featured ] */
$documents = array(
	array(
		'slug'     => 'draft-red-herring-prospectus',
		'title'    => 'Draft Red Herring Prospectus (DRHP)',
		'content'  => 'Detailed disclosure document filed with the Securities and Exchange Board of India (SEBI) regarding the proposed Initial Public Offering (IPO). This document outlines our financial health, market position, and growth roadmap for the next decade.',
		'year'     => '2026',
		'category' => 'drhp-filings',
		'featured' => true,
	),
	array(
		'slug'     => 'audited-financial-results-fy-2025-26',
		'title'    => 'Audited Standalone & Consolidated Financial Results — FY 2025-26',
		'content'  => 'Year Ended March 31, 2026.',
		'year'     => '2026',
		'category' => 'financial-statements',
		'featured' => false,
	),
	array(
		'slug'     => 'annual-report-2024-25',
		'title'    => 'Annual Report — 2024-25',
		'content'  => 'Institutional Excellence Report.',
		'year'     => '2025',
		'category' => 'annual-reports',
		'featured' => false,
	),
	array(
		'slug'     => 'corporate-governance-report-q3-2025',
		'title'    => 'Corporate Governance Report — Q3 2025',
		'content'  => 'Listing Obligations (LODR) Regulation 27.',
		'year'     => '2025',
		'category' => 'corporate-governance',
		'featured' => false,
	),
	array(
		'slug'     => 'shareholding-pattern-q4-2025',
		'title'    => 'Shareholding Pattern — Q4 2025',
		'content'  => 'Detailed equity structure and distribution as filed with the stock exchanges.',
		'year'     => '2025',
		'category' => 'shareholding-pattern',
		'featured' => false,
	),
	array(
		'slug'     => 'agm-notice-2025',
		'title'    => 'Notice of 12th Annual General Meeting',
		'content'  => 'Notice, agenda and explanatory statement for the Annual General Meeting.',
		'year'     => '2025',
		'category' => 'agm-notices',
		'featured' => false,
	),
	array(
		'slug'     => 'investor-presentation-h1-2025',
		'title'    => 'Investor Presentation — H1 2025',
		'content'  => 'Strategy deck and analyst briefing covering operational and financial performance.',
		'year'     => '2025',
		'category' => 'investor-presentations',
		'featured' => false,
	),
	array(
		'slug'     => 'sebi-regulatory-update-2026',
		'title'    => 'SEBI Regulatory Update — Q1 2026',
		'content'  => 'Latest SEBI-approved regulatory notification relevant to the public listing process.',
		'year'     => '2026',
		'category' => 'regulatory-updates',
		'featured' => false,
	),
);

$order = 0;
foreach ( $documents as $data ) {
	$order++;

	$existing = get_posts( array(
		'post_type'      => 'deon_investor_doc',
		'name'           => $data['slug'],
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );

	$post_id = wp_insert_post( array(
		'ID'           => $existing ? $existing[0] : 0,
		'post_type'    => 'deon_investor_doc',
		'post_status'  => 'publish',
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_content' => $data['content'],
		'menu_order'   => $order,
	), true );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( $data['slug'] . ': ' . $post_id->get_error_message() );
		continue;
	}

	update_post_meta( $post_id, '_deon_document_year', $data['year'] );
	update_post_meta( $post_id, '_deon_invdoc_featured', $data['featured'] ? '1' : '' );

	// Assign the category (term seeded by the theme on init).
	if ( term_exists( $data['category'], 'deon_investor_category' ) ) {
		wp_set_object_terms( $post_id, $data['category'], 'deon_investor_category', false );
	}

	// Sideload the bundled sample PDF once, so DOWNLOAD + "PDF • size" resolve.
	$has_file = get_post_meta( $post_id, '_deon_document_file', true );
	if ( ! $has_file && file_exists( $sample_pdf ) ) {
		$tmp = wp_tempnam( basename( $sample_pdf ) );
		copy( $sample_pdf, $tmp );
		$att_id = media_handle_sideload(
			array( 'name' => $data['slug'] . '.pdf', 'tmp_name' => $tmp ),
			$post_id,
			$data['title']
		);
		if ( is_wp_error( $att_id ) ) {
			@unlink( $tmp );
			WP_CLI::warning( $data['slug'] . ' file: ' . $att_id->get_error_message() );
		} else {
			update_post_meta( $post_id, '_deon_document_file', wp_get_attachment_url( $att_id ) );
		}
	}

	WP_CLI::log( sprintf( 'Investor Document: %s (#%d)', $data['title'], $post_id ) );
}

WP_CLI::success( 'Investor Documents seeded.' );
