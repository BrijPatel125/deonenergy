<?php
/**
 * One-off seeder for the About page Growth Timeline (deon_milestone CPT).
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-milestones.php
 *
 * Seeds the 5 milestones from the approved copy (PDF §2) with year, description, future flag, order
 * and featured image. Idempotent: re-running updates the same milestones
 * (matched by slug) instead of duplicating. Delete this file after seeding
 * staging — it is a dev utility, not part of the theme runtime.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-milestones.php\n" );
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$theme_dir = get_template_directory();

/**
 * Import a file that already lives on disk (theme assets) into the media
 * library. Avoids media_sideload_image's HTTP round-trip, which fails on hosts
 * that cannot reach their own front end from the CLI.
 */
$sideload_local = function ( $path, $post_id, $title ) {
	if ( ! file_exists( $path ) ) {
		return new WP_Error( 'missing_file', "File not found: {$path}" );
	}

	$upload = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $upload['error'] ) ) {
		return new WP_Error( 'upload_failed', $upload['error'] );
	}

	$filetype      = wp_check_filetype( $upload['file'], null );
	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => $title,
		'post_status'    => 'inherit',
	), $upload['file'], $post_id );

	if ( is_wp_error( $attachment_id ) ) {
		return $attachment_id;
	}

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );

	return $attachment_id;
};

$milestones = array(
	array(
		'slug'   => 'milestone-incorporation',
		'phase'  => 'Company incorporated',
		'year'   => '2020',
		'desc'   => 'Deon Energy Limited established, headquartered in Ahmedabad.',
		'future' => false,
		'image'  => $theme_dir . '/assets/img/project-1.jpg',
	),
	/*
	 * The approved copy leaves the next four years as "[Year]" for the client
	 * to confirm, so they seed with an empty year — the timeline prints the
	 * year line only when one is set. Fill them in under Milestones in
	 * wp-admin.
	 */
	array(
		'slug'   => 'milestone-first-rooftop',
		'phase'  => 'First industrial rooftop project commissioned',
		'year'   => '',
		'desc'   => 'First industrial rooftop plant handed over to a commercial customer.',
		'future' => false,
		'image'  => $theme_dir . '/assets/img/project-2.jpg',
	),
	array(
		'slug'   => 'milestone-second-office',
		'phase'  => 'Second regional office opened',
		'year'   => '',
		'desc'   => 'Regional presence extended beyond Ahmedabad into Rajkot and Morbi.',
		'future' => false,
		'image'  => $theme_dir . '/assets/img/project-3.jpg',
	),
	array(
		'slug'   => 'milestone-ground-mount',
		'phase'  => 'First ground-mount / open-access project undertaken',
		'year'   => '',
		'desc'   => 'Portfolio widened from rooftop into ground-mount and open-access solar.',
		'future' => false,
		'image'  => $theme_dir . '/assets/img/project-4.jpg',
	),
	array(
		'slug'   => 'milestone-in-progress',
		'phase'  => 'Current milestone in progress',
		'year'   => '',
		'desc'   => 'Ongoing expansion of the delivery and O&M footprint across Gujarat.',
		'future' => true,
		'image'  => $theme_dir . '/assets/img/about-hero.jpg',
	),
);


/*
 * Milestones seeded by the pre-copy version of this script. They carry invented
 * years/phases that the approved copy replaced, so retire them by slug — only
 * these five, never anything the client added by hand.
 */
$legacy_slugs = array(
	'milestone-2014-inception',
	'milestone-2017-expansion',
	'milestone-2020-certification',
	'milestone-2023-maturity',
	'milestone-2025-vision',
);

foreach ( $legacy_slugs as $legacy_slug ) {
	$legacy = get_page_by_path( $legacy_slug, OBJECT, 'deon_milestone' );
	if ( ! $legacy ) {
		continue;
	}
	$legacy_thumb = get_post_thumbnail_id( $legacy->ID );
	if ( $legacy_thumb ) {
		wp_delete_attachment( $legacy_thumb, true );
	}
	wp_delete_post( $legacy->ID, true );
	WP_CLI::log( "Removed superseded milestone: {$legacy_slug}" );
}

$order = 0;

foreach ( $milestones as $m ) {
	$order += 10;

	$existing = get_page_by_path( $m['slug'], OBJECT, 'deon_milestone' );

	$postarr = array(
		'post_type'   => 'deon_milestone',
		'post_status' => 'publish',
		'post_title'  => $m['phase'],
		'post_name'   => $m['slug'],
		'menu_order'  => $order,
	);

	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed: {$m['slug']} — " . $post_id->get_error_message() );
		continue;
	}

	update_post_meta( $post_id, '_deon_milestone_year', $m['year'] );
	update_post_meta( $post_id, '_deon_milestone_desc', $m['desc'] );
	update_post_meta( $post_id, '_deon_milestone_future', $m['future'] ? '1' : '' );

	if ( ! has_post_thumbnail( $post_id ) ) {
		$attachment_id = $sideload_local( $m['image'], $post_id, $m['phase'] );
		if ( ! is_wp_error( $attachment_id ) ) {
			set_post_thumbnail( $post_id, $attachment_id );
		} else {
			WP_CLI::warning( "Image failed for {$m['slug']}: " . $attachment_id->get_error_message() );
		}
	}

	WP_CLI::log( "Seeded milestone: {$m['year']} — {$m['phase']}" );
}

WP_CLI::success( 'Growth Timeline milestones seeded.' );
