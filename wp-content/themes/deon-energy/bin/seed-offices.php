<?php
/**
 * Seeder for the Offices (deon_office CPT) — footer + Contact page addresses.
 *
 * Populates the three real Deon Energy offices (data from bin/data/company.json)
 * so the footer and Contact page render addresses out of the box. After this,
 * the client edits them under Offices in wp-admin — there is no hardcoded
 * fallback in the templates, so this is the single source of truth.
 *
 * Run:  wp eval-file wp-content/themes/deon-energy/bin/seed-offices.php
 *
 * Idempotent — matches by slug, updates in place on re-run.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Run via WP-CLI: wp eval-file wp-content/themes/deon-energy/bin/seed-offices.php\n" );
	return;
}

$deon_offices = array(
	array(
		'slug'    => 'ahmedabad-office',
		'title'   => 'Ahmedabad Office',
		'address' => "D-604, 605, 606 Westgate, Near YMCA Club, S.G. Highway, Makarba, Ahmedabad-380051, Gujarat, India.",
		'map'     => 'https://maps.app.goo.gl/1hhSsNiHuFgMFwnv7',
		'icon'    => 'pin',
	),
	array(
		'slug'    => 'rajkot-office',
		'title'   => 'Rajkot Office',
		'address' => "401, R K Prime, Near Nana Mava Circle, 150 Feet Ring Road, Rajkot-360 005, Gujarat, India.",
		'map'     => 'https://maps.app.goo.gl/o2xmtotpHkRSdQnP9',
		'icon'    => 'building',
	),
	array(
		'slug'    => 'morbi-office',
		'title'   => 'Morbi Office',
		'address' => "715, 716 & 717 Siromany-142, B/h. Eden Ceramic City, NH-8A, Lalpar-363 642 Morbi, Gujarat, India.",
		'map'     => 'https://maps.app.goo.gl/u1djubd4sfLfpetM9',
		'icon'    => 'globe',
	),
);

$deon_created = 0;
$deon_updated = 0;

foreach ( $deon_offices as $deon_order => $deon_office ) {
	$deon_existing = get_page_by_path( $deon_office['slug'], OBJECT, 'deon_office' );

	$deon_postarr = array(
		'post_type'   => 'deon_office',
		'post_title'  => $deon_office['title'],
		'post_name'   => $deon_office['slug'],
		'post_status' => 'publish',
		'menu_order'  => $deon_order,
	);

	if ( $deon_existing ) {
		$deon_postarr['ID'] = $deon_existing->ID;
		$deon_post_id       = wp_update_post( $deon_postarr, true );
		$deon_updated++;
	} else {
		$deon_post_id = wp_insert_post( $deon_postarr, true );
		$deon_created++;
	}

	if ( is_wp_error( $deon_post_id ) ) {
		WP_CLI::warning( "Failed: {$deon_office['title']} — " . $deon_post_id->get_error_message() );
		continue;
	}

	update_post_meta( $deon_post_id, '_deon_office_address', $deon_office['address'] );
	update_post_meta( $deon_post_id, '_deon_office_map_url', $deon_office['map'] );
	update_post_meta( $deon_post_id, '_deon_office_icon', $deon_office['icon'] );

	WP_CLI::log( sprintf( 'Seeded office: %s', $deon_office['title'] ) );
}

WP_CLI::success( sprintf( 'Offices seeded — %d created, %d updated. Edit them under Offices in wp-admin.', $deon_created, $deon_updated ) );
