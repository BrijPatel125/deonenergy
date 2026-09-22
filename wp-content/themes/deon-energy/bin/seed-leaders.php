<?php
/**
 * Seeder for the Leadership page (deon_leader CPT).
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-leaders.php
 *
 * Imports every person in bin/data/leadership.json — the client's real team,
 * scraped from https://deonenergy.in/about-us — with name (post title),
 * designation, display group and photograph. Photos are remote URLs on
 * deonenergy.in and are sideloaded into the media library, then set as the
 * featured image (the professional photo shown on the front of the flip card).
 *
 * Idempotent: matches existing leaders by slug and updates them instead of
 * duplicating, and never re-downloads a photo for a leader that already has a
 * featured image. Safe to re-run.
 *
 * Group, designation, quote and bio for the eight people named in the approved
 * copy (PDF §3 Leadership) come from that PDF and ARE re-applied on every run.
 * Everyone else keeps the designation published on deonenergy.in/about-us and
 * has no bio — do not invent one. LinkedIn URLs and the casual photo (flip-card
 * back face) stay empty until the client supplies them.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-leaders.php\n" );
	exit( 1 );
}

// media_sideload_image() and its dependencies are admin-only includes; WP-CLI
// does not load them for us.
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$deon_data_file = get_template_directory() . '/bin/data/leadership.json';

if ( ! file_exists( $deon_data_file ) ) {
	WP_CLI::error( "Data file not found: {$deon_data_file}" );
}

$deon_data = json_decode( file_get_contents( $deon_data_file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions

if ( ! is_array( $deon_data ) || empty( $deon_data['people'] ) ) {
	WP_CLI::error( 'leadership.json has no "people" array.' );
}

/**
 * Client-confirmed name corrections. The live site's SEO tags carry a
 * "Dharmesh Patel" variant; the correct name is "Dharmesh Makadiya".
 */
$deon_name_fixes = array(
	'Dharmesh Patel' => 'Dharmesh Makadiya',
	'jayesh sindhav' => 'Jayesh Sindhav',
);

$deon_order   = 0;
$deon_created = 0;
$deon_updated = 0;
$deon_photos  = 0;

foreach ( $deon_data['people'] as $deon_person ) {
	$deon_order += 10;

	$deon_name = isset( $deon_person['name'] ) ? trim( $deon_person['name'] ) : '';
	if ( '' === $deon_name ) {
		continue;
	}
	if ( isset( $deon_name_fixes[ $deon_name ] ) ) {
		$deon_name = $deon_name_fixes[ $deon_name ];
	}

	$deon_slug = 'leader-' . sanitize_title( $deon_name );
	// `group` carries the section from the approved copy (PDF §3); the old
	// `display_group` layout hint is the fallback for any legacy data file.
	$deon_type = isset( $deon_person['group'] ) ? (string) $deon_person['group'] : '';
	if ( ! in_array( $deon_type, array( 'founder', 'board', 'team' ), true ) ) {
		$deon_type = ( isset( $deon_person['display_group'] ) && 'featured' === $deon_person['display_group'] )
			? 'founder'
			: 'team';
	}

	$deon_existing = get_page_by_path( $deon_slug, OBJECT, 'deon_leader' );

	$deon_postarr = array(
		'post_type'   => 'deon_leader',
		'post_status' => 'publish',
		'post_title'  => $deon_name,
		'post_name'   => $deon_slug,
		'menu_order'  => $deon_order,
	);

	if ( $deon_existing ) {
		$deon_postarr['ID'] = $deon_existing->ID;
		$deon_post_id       = wp_update_post( $deon_postarr, true );
	} else {
		$deon_post_id = wp_insert_post( $deon_postarr, true );
	}

	if ( is_wp_error( $deon_post_id ) ) {
		WP_CLI::warning( "Failed: {$deon_name} — " . $deon_post_id->get_error_message() );
		continue;
	}

	if ( $deon_existing ) {
		++$deon_updated;
	} else {
		++$deon_created;
	}

	update_post_meta( $deon_post_id, '_deon_leader_type', $deon_type );
	update_post_meta( $deon_post_id, '_deon_leader_role', isset( $deon_person['designation'] ) ? (string) $deon_person['designation'] : '' );

	/*
	 * Bio and quote come from the approved copy for the eight people the PDF
	 * names, so they are authoritative and re-seeding restores them. Anyone
	 * else has null in the data file — create the key once so the field exists
	 * in wp-admin, but never overwrite what the client has typed since.
	 */
	foreach ( array( '_deon_leader_bio' => 'bio', '_deon_leader_quote' => 'quote' ) as $deon_key => $deon_field ) {
		$deon_value = isset( $deon_person[ $deon_field ] ) ? (string) $deon_person[ $deon_field ] : '';

		if ( '' !== $deon_value ) {
			update_post_meta( $deon_post_id, $deon_key, $deon_value );
		} elseif ( '' === (string) get_post_meta( $deon_post_id, $deon_key, true ) ) {
			update_post_meta( $deon_post_id, $deon_key, '' );
		}
	}

	// LinkedIn is null for everyone in the source — client fills it in wp-admin.
	if ( '' === (string) get_post_meta( $deon_post_id, '_deon_leader_linkedin', true ) ) {
		update_post_meta( $deon_post_id, '_deon_leader_linkedin', '' );
	}

	// Photo → featured image. Skipped entirely if one is already attached, so
	// re-runs cost no downloads.
	$deon_photo_url = isset( $deon_person['photo_url'] ) ? (string) $deon_person['photo_url'] : '';

	if ( $deon_photo_url && ! has_post_thumbnail( $deon_post_id ) ) {
		$deon_attachment_id = media_sideload_image( $deon_photo_url, $deon_post_id, $deon_name, 'id' );

		if ( is_wp_error( $deon_attachment_id ) ) {
			WP_CLI::warning( "Photo failed for {$deon_name}: " . $deon_attachment_id->get_error_message() );
		} else {
			set_post_thumbnail( $deon_post_id, $deon_attachment_id );
			++$deon_photos;
		}
	}

	WP_CLI::log( sprintf( 'Seeded leader: %s (%s) — %s', $deon_name, $deon_type, isset( $deon_person['designation'] ) ? $deon_person['designation'] : '—' ) );
}

WP_CLI::success( sprintf(
	'Leadership seeded — %d created, %d updated, %d photos sideloaded. Groups, designations, founder quotes and the eight approved bios come from the copy PDF; LinkedIn URLs and casual photos stay empty — add them under Leaders in wp-admin.',
	$deon_created,
	$deon_updated,
	$deon_photos
) );
