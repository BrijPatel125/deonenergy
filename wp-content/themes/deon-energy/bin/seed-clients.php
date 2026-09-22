<?php
/**
 * Seeder for the homepage client logo ticker (deon_client CPT).
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-clients.php
 *     wp eval-file bin/seed-clients.php remove   # delete clients + their logos
 *
 * (Positional `remove`, not `--remove`: WP-CLI rejects unknown leading-dash
 * flags before the script ever runs.)
 *
 * Seeds the client roster carried over from the previous deonenergy.in site.
 * Logos ship with the theme in assets/img/clients/ and are sideloaded off disk
 * (no HTTP round-trip, so it works on hosts that cannot reach their own front
 * end from the CLI). Idempotent: re-running matches by slug and updates in
 * place instead of duplicating, and never re-imports a logo a client already
 * has.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-clients.php\n" );
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$theme_dir = get_template_directory();
$logo_dir  = $theme_dir . '/assets/img/clients/';
$remove    = in_array( 'remove', (array) ( $args ?? array() ), true );

/**
 * Import a file already on disk into the media library.
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

/*
 * name => logo filename in assets/img/clients/.
 * Names were transcribed from the logo artwork itself; the previous site
 * carried no alt text. Order here is the ticker order (menu_order, step 10).
 */
$clients = array(
	'Shubh Darshan Polypack'            => 'cl-1.jpg',
	'Radha Laxmi Spintex Pvt. Ltd.'     => 'cl-4.jpg',
	'Lemzon Granito'                    => 'cl-5.jpg',
	'Italica'                           => 'cl-7.jpg',
	'Loggerhead'                        => 'cl-8.jpg',
	'Leaspin Textile LLP'               => 'cl-9.jpg',
	'Leamak'                            => 'cl-10.jpg',
	'Benani Steel'                      => 'cl-11.jpg',
	'Safar Polyfibre Pvt. Ltd.'         => 'cl-12.jpg',
	'Dwarkadhish Cotspin Pvt. Ltd.'     => 'cl-13.jpg',
	'Fiotex'                            => 'cl-14.jpg',
	'Ocean Packaging'                   => 'cl-15.jpg',
	'Makson'                            => 'cl-16.jpg',
	'GRV Spintex Pvt. Ltd.'             => 'cl-17.jpg',
	'Brasstech Pipe Fittings'           => 'cl-18.jpg',
	'Skajen'                            => 'cl-19.jpg',
	'Sanariya Ceramic'                  => 'cl-20.jpg',
	'Marquina Ceramic'                  => 'cl-21.jpg',
	'Itacon Granito'                    => 'cl-22.jpg',
	'Solizo Vitrified Pvt. Ltd.'        => 'cl-23.jpg',
	'Natural Tex Yarn Pvt. Ltd.'        => 'cl-24.jpg',
	'Lavish'                            => 'cl-25.jpg',
	'Omax Cotspin Pvt. Ltd.'            => 'cl-26.jpg',
	'Sperita'                           => 'cl-27.jpg',
	'Eden Polypack'                     => 'cl-28.jpg',
	'Shaldip Coating'                   => 'cl-29.jpg',
	'Lux Gres'                          => 'cl-30.jpg',
	'Metro Arte Ceramica'               => 'cl-31.jpg',
	'Skill Precision Balls Pvt. Ltd.'   => 'cl-32.jpg',
	'Fezzan'                            => 'cl-33.jpg',
	'Lakme'                             => 'cl-34.jpg',
	'Iris Polypack LLP'                 => 'cl-35.jpg',
	'SKP'                               => 'cl-36.jpg',
	'Conor Granito'                     => 'cl-37.jpg',
	'Sparten Marble'                    => 'cl-38.jpg',
	'Samarpan Polyfab LLP'              => 'cl-39.jpg',
	'Velloza'                           => 'cl-40.jpg',
	'Crizal Tiles'                      => 'cl-41.jpg',
	'Leadsun Vitrified Tiles'           => 'cl-42.jpg',
	'JD Merchandise Pvt. Ltd.'          => 'cl-43.jpg',
	'Cyan Granito'                      => 'cl-44.jpg',
	'Hi-Mac Castings Pvt. Ltd.'         => 'cl-45.jpg',
	'True Colors'                       => 'cl-46.jpg',
	'Aarko Granito'                     => 'cl-47.jpg',
	'Parcos Tiles LLP'                  => 'cl-48.jpg',
	'Phenix Spinning Pvt. Ltd.'         => 'cl-49.jpg',
	'Anjani Spintex Pvt. Ltd.'          => 'cl-50.jpg',
	'Coarser Spinning Pvt. Ltd.'        => 'cl-51.jpg',
	'Colortile'                         => 'cl-52.jpg',
	'Mega Tile'                         => 'cl-53.jpg',
	'Motto'                             => 'cl-54.jpg',
	'Patson Papers'                     => 'cl-55.jpg',
	'Nioni'                             => 'cl-56.jpg',
	'Flais Granito'                     => 'cl-57.jpg',
	'Holo Sanitaryware'                 => 'cl-58.jpg',
	'Safar'                             => 'cl-59.jpg',
	'Intile'                            => 'cl-60.jpg',
	'Sega Tiles'                        => 'cl-61.jpg',
	'Mega Tiles'                        => 'cl-62.jpg',
	'Velzone Granito'                   => 'cl-63.jpg',
	'Apollo Papers LLP'                 => 'cl-64.jpg',
	'Sonic'                             => 'cl-65.jpg',
	'Spencera'                          => 'cl-66.jpeg',
	'Uday Granito'                      => 'cl-67.jpeg',
	'Skype Tile LLP'                    => 'cl-71.jpeg',
	'Radheshyam Spinning Mill Pvt. Ltd.' => 'cl-69.jpeg',
	'Tulshi Mineral'                    => 'cl-70.jpg',
	'Shree Industries'                  => 'cl-72.jpg',
	'Patel Cotton Industries'           => 'cln-2.jpg',
	'Bhavani Cotspin LLP'               => 'cln-3.jpg',
	// cl-6 is a wordless monogram — seeded so the logo is not lost, but the
	// client should rename it under Clients in wp-admin.
	'Unidentified Client'               => 'cl-6.jpg',
);

if ( $remove ) {
	$existing = get_posts( array(
		'post_type'      => 'deon_client',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );

	foreach ( $existing as $client_id ) {
		$thumb_id = get_post_thumbnail_id( $client_id );
		if ( $thumb_id ) {
			wp_delete_attachment( $thumb_id, true );
		}
		wp_delete_post( $client_id, true );
	}

	WP_CLI::success( sprintf( 'Removed %d clients and their logos.', count( $existing ) ) );
	return;
}

$order   = 0;
$seeded  = 0;
$skipped = 0;

foreach ( $clients as $name => $file ) {
	$order += 10;
	$slug   = sanitize_title( $name );

	$existing = get_page_by_path( $slug, OBJECT, 'deon_client' );

	$postarr = array(
		'post_type'   => 'deon_client',
		'post_status' => 'publish',
		'post_title'  => $name,
		'post_name'   => $slug,
		'menu_order'  => $order,
	);

	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed: {$name} — " . $post_id->get_error_message() );
		$skipped++;
		continue;
	}

	if ( ! has_post_thumbnail( $post_id ) ) {
		$attachment_id = $sideload_local( $logo_dir . $file, $post_id, $name );
		if ( is_wp_error( $attachment_id ) ) {
			WP_CLI::warning( "Logo failed for {$name}: " . $attachment_id->get_error_message() );
		} else {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	$seeded++;
}

WP_CLI::success( sprintf( 'Seeded %d clients (%d skipped).', $seeded, $skipped ) );
