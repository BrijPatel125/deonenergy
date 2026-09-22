<?php
/**
 * Seeder for the About page "Certifications & Compliance" badges
 * (deon_certification CPT).
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-certifications.php
 *     wp eval-file bin/seed-certifications.php remove   # delete badges + artwork
 *
 * Seeds the three ISO management-system badges the previous build hard-coded,
 * pointing each at its bundled artwork in assets/img/ (SVG, which the Media
 * Library rejects by default — hence a filename, not an upload). Idempotent:
 * matches by slug and updates in place.
 *
 * VERIFY BEFORE PUBLISHING. This script only restores the badges that were
 * previously hard-coded into the template — it is not evidence Deon holds the
 * registrations. Confirm each certificate is current, record the issuing body
 * and certificate number on the badge, and unpublish anything that cannot be
 * evidenced. The two non-certifications the old template carried ("Global
 * Compliance — Audit Approved", "ESG Excellence — Leadership Award") are
 * deliberately NOT seeded: neither names an issuing body.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-certifications.php\n" );
	exit( 1 );
}

$remove    = in_array( 'remove', (array) ( $args ?? array() ), true );

$certifications = array(
	array(
		'slug'  => 'iso-9001-2015',
		'code'  => 'ISO 9001:2015',
		'label' => 'Quality Mgmt',
		'icon'  => 'about-cert-iso9001.svg',
	),
	array(
		'slug'  => 'iso-14001',
		'code'  => 'ISO 14001',
		'label' => 'Environment',
		'icon'  => 'about-cert-iso14001.svg',
	),
	array(
		'slug'  => 'iso-45001',
		'code'  => 'ISO 45001',
		'label' => 'Occupational Health',
		'icon'  => 'about-cert-iso45001.svg',
	),
);

if ( $remove ) {
	$existing = get_posts( array(
		'post_type'      => 'deon_certification',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );

	foreach ( $existing as $cert_id ) {
		$thumb_id = get_post_thumbnail_id( $cert_id );
		if ( $thumb_id ) {
			wp_delete_attachment( $thumb_id, true );
		}
		wp_delete_post( $cert_id, true );
	}

	WP_CLI::success( sprintf( 'Removed %d certifications and their artwork.', count( $existing ) ) );
	return;
}

$order = 0;

foreach ( $certifications as $cert ) {
	$order += 10;

	$existing = get_page_by_path( $cert['slug'], OBJECT, 'deon_certification' );

	$postarr = array(
		'post_type'   => 'deon_certification',
		'post_status' => 'publish',
		'post_title'  => $cert['code'],
		'post_name'   => $cert['slug'],
		'menu_order'  => $order,
	);

	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed: {$cert['code']} — " . $post_id->get_error_message() );
		continue;
	}

	update_post_meta( $post_id, '_deon_cert_label', $cert['label'] );
	update_post_meta( $post_id, '_deon_cert_icon', $cert['icon'] );

	WP_CLI::log( "Seeded certification: {$cert['code']} — {$cert['label']}" );
}

WP_CLI::warning( 'Verify each registration is current before this page goes live — add the issuing body and certificate number on each badge, and unpublish anything unevidenced.' );
WP_CLI::success( 'Certifications seeded.' );
