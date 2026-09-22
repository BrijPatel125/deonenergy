<?php
/**
 * Deon Energy — Project seeder.
 *
 * Populates the deon_project CPT with the real project portfolio scraped from
 * the live site (https://deonenergy.in/project-gallery — 34 projects). Sets
 * title, capacity, location and project type. Photos/gallery are NOT seeded —
 * the client adds real plant images per project via the admin gallery box.
 *
 * Usage (WP-CLI from WordPress root):
 *   wp eval-file wp-content/themes/deon-energy/seed-projects.php
 *
 * Safe to re-run — skips if projects already exist.
 * To wipe fake/old data and re-seed with the real portfolio:
 *   wp eval-file wp-content/themes/deon-energy/seed-projects.php --force
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( "Run via WP-CLI:\n  wp eval-file wp-content/themes/deon-energy/seed-projects.php\n" );
}

$force = in_array( '--force', $GLOBALS['argv'] ?? [], true );

// ── Idempotency check ────────────────────────────────────────────────────────
$existing = get_posts( array(
	'post_type'      => 'deon_project',
	'posts_per_page' => 1,
	'post_status'    => 'any',
) );

if ( $existing && ! $force ) {
	WP_CLI::line( 'Projects already seeded. Pass --force to wipe and re-seed.' );
	return;
}

if ( $existing && $force ) {
	foreach ( get_posts( array( 'post_type' => 'deon_project', 'posts_per_page' => -1, 'post_status' => 'any' ) ) as $p ) {
		wp_delete_post( $p->ID, true );
	}
	WP_CLI::line( 'Wiped existing projects.' );
}

// ── Real project data (from live project gallery) ─────────────────────────────
// type: 'Ground Mount' = standalone plant sites · 'C&I Captive' = company clients.
$projects = array(
	array( 'title' => 'Soladi Plant',                     'type' => 'Ground Mount', 'location' => 'Soladi, Dhangadhra, Gujarat',        'capacity' => '26 MW' ),
	array( 'title' => 'Ramgadh',                          'type' => 'Ground Mount', 'location' => 'Ramgadh, Dhangadhra, Gujarat',       'capacity' => '13 MW' ),
	array( 'title' => 'Bhechada',                         'type' => 'Ground Mount', 'location' => 'Bhechada, Surendranagar, Gujarat',   'capacity' => '12.6 MW' ),
	array( 'title' => 'Raygadh',                          'type' => 'Ground Mount', 'location' => 'Raygadh, Dhangadhra, Gujarat',       'capacity' => '12 MW' ),
	array( 'title' => 'Methan',                           'type' => 'Ground Mount', 'location' => 'Methan, Dhangadhra, Gujarat',        'capacity' => '9.5 MW' ),
	array( 'title' => 'Vavdi',                            'type' => 'Ground Mount', 'location' => 'Vavdi, Surendranagar, Gujarat',      'capacity' => '8.1 MW' ),
	array( 'title' => 'Makson Pharmaceuticals Pvt. Ltd.', 'type' => 'C&I Captive',  'location' => 'Khodu, Surendranagar, Gujarat',      'capacity' => '8 MW' ),
	array( 'title' => 'Rajpar',                           'type' => 'Ground Mount', 'location' => 'Dhrangadhra, Surendranagar, Gujarat', 'capacity' => '7.6 MW' ),
	array( 'title' => 'Lavish Granito Pvt. Ltd.',         'type' => 'C&I Captive',  'location' => 'Khodu, Surendranagar, Gujarat',      'capacity' => '7.3 MW' ),
	array( 'title' => 'Anida',                            'type' => 'Ground Mount', 'location' => 'Anida, Amreli, Gujarat',             'capacity' => '7.3 MW' ),
	array( 'title' => 'GRV Spintex Pvt. Ltd.',            'type' => 'C&I Captive',  'location' => 'Sethvadala, Jamnagar, Gujarat',      'capacity' => '5 MW' ),
	array( 'title' => 'Shaldip Coating & Vinyl',          'type' => 'C&I Captive',  'location' => 'Sitapur, Dhangadhra, Gujarat',       'capacity' => '4 MW' ),
	array( 'title' => 'Megacity Vitrified LLP',           'type' => 'C&I Captive',  'location' => 'Karyani, Botad, Gujarat',            'capacity' => '4 MW' ),
	array( 'title' => 'Italica Granito Pvt. Ltd.',        'type' => 'C&I Captive',  'location' => 'Haripar, Dhrol, Gujarat',            'capacity' => '4 MW' ),
	array( 'title' => 'Lemzone Granito LLP',              'type' => 'C&I Captive',  'location' => 'Bhensdad, Dhrol, Gujarat',           'capacity' => '4 MW' ),
	array( 'title' => 'Natural Tex Yarn Pvt. Ltd.',       'type' => 'C&I Captive',  'location' => 'Jaliya, Amreli, Gujarat',            'capacity' => '3.5 MW' ),
	array( 'title' => 'Safar Polyfibre Pvt. Ltd.',        'type' => 'C&I Captive',  'location' => 'Valasan, Jamnagar, Gujarat',         'capacity' => '3.4 MW' ),
	array( 'title' => 'Agritex Enterprise LLP',           'type' => 'C&I Captive',  'location' => 'Rajula, Gujarat',                    'capacity' => '1.5 MW' ),
	array( 'title' => 'Leaspin Textile LLP',              'type' => 'C&I Captive',  'location' => 'Aniyari, Gujarat',                   'capacity' => '1.5 MW' ),
	array( 'title' => 'OMAX Cotspin Pvt. Ltd.',           'type' => 'C&I Captive',  'location' => 'Sitapur, Dhangadhra, Gujarat',       'capacity' => '1.3 MW' ),
	array( 'title' => 'Fiotex Cotspin Pvt. Ltd.',         'type' => 'C&I Captive',  'location' => 'Padadhari, Gujarat',                 'capacity' => '1.3 MW' ),
	array( 'title' => 'Leamak Healthcare Pvt. Ltd.',      'type' => 'C&I Captive',  'location' => 'Bavla, Ahmedabad, Gujarat',          'capacity' => '1.2 MW' ),
	array( 'title' => 'Benani Steel',                     'type' => 'C&I Captive',  'location' => 'Gondal, Rajkot, Gujarat',            'capacity' => '1 MW' ),
	array( 'title' => 'Radhalaxmi Spintex Pvt. Ltd.',     'type' => 'C&I Captive',  'location' => 'Lakhadhirgadh, Tankara, Gujarat',    'capacity' => '999 kW' ),
	array( 'title' => 'Bhavani Cotspin LLP',              'type' => 'C&I Captive',  'location' => 'Haripar, Dhangdhra, Gujarat',        'capacity' => '900 kW' ),
	array( 'title' => 'Divine Polyfeb LLP',               'type' => 'C&I Captive',  'location' => 'Thorala, Morbi, Gujarat',            'capacity' => '700 kW' ),
	array( 'title' => 'Holo Sanitaryware Pvt. Ltd.',      'type' => 'C&I Captive',  'location' => 'Ghutu, Morbi, Gujarat',              'capacity' => '700 kW' ),
	array( 'title' => 'Oryn Polypack LLP',                'type' => 'C&I Captive',  'location' => 'Tankara, Morbi, Gujarat',            'capacity' => '650 kW' ),
	array( 'title' => 'Samarpan Polyfab LLP',             'type' => 'C&I Captive',  'location' => 'Tankara, Morbi, Gujarat',            'capacity' => '650 kW' ),
	array( 'title' => 'Milan Cottex',                     'type' => 'C&I Captive',  'location' => 'Chital, Amreli, Gujarat',            'capacity' => '650 kW' ),
	array( 'title' => 'Patel Cotton Industries',          'type' => 'C&I Captive',  'location' => 'Dhangadhra, Gujarat',                'capacity' => '650 kW' ),
	array( 'title' => 'Iris Polypack LLP',                'type' => 'C&I Captive',  'location' => 'Tankara, Morbi, Gujarat',            'capacity' => '600 kW' ),
	array( 'title' => 'Datt Polyplast LLP',               'type' => 'C&I Captive',  'location' => 'Saraya, Tankara, Gujarat',           'capacity' => '515 kW' ),
	array( 'title' => 'Shubh Darshan Polypack',           'type' => 'C&I Captive',  'location' => 'Savdi, Tankara, Gujarat',            'capacity' => '300 kW' ),
);

// ── Create / fetch taxonomy terms ─────────────────────────────────────────────
$type_names = array( 'Ground Mount', 'C&I Captive' );
$term_ids   = array();

foreach ( $type_names as $name ) {
	$existing_term = get_term_by( 'name', $name, 'deon_project_type' );
	if ( $existing_term ) {
		$term_ids[ $name ] = $existing_term->term_id;
	} else {
		$inserted = wp_insert_term( $name, 'deon_project_type' );
		if ( is_wp_error( $inserted ) ) {
			WP_CLI::warning( "Could not create term '{$name}': " . $inserted->get_error_message() );
			continue;
		}
		$term_ids[ $name ] = $inserted['term_id'];
	}
}

WP_CLI::line( 'Terms ready: ' . implode( ', ', array_keys( $term_ids ) ) );

// ── Create posts ──────────────────────────────────────────────────────────────
$order = 1;
foreach ( $projects as $project ) {

	$post_id = wp_insert_post( array(
		'post_title'  => $project['title'],
		'post_type'   => 'deon_project',
		'post_status' => 'publish',
		'menu_order'  => $order,
	), true );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed to insert '{$project['title']}': " . $post_id->get_error_message() );
		continue;
	}

	update_post_meta( $post_id, '_deon_location', $project['location'] );
	update_post_meta( $post_id, '_deon_capacity', $project['capacity'] );

	if ( isset( $term_ids[ $project['type'] ] ) ) {
		wp_set_object_terms( $post_id, array( (int) $term_ids[ $project['type'] ] ), 'deon_project_type' );
	}

	WP_CLI::success( "#{$post_id} — {$project['title']} ({$project['capacity']}) [{$project['type']}]" );
	$order++;
}

WP_CLI::success( 'Seeding complete — ' . count( $projects ) . ' projects.' );
