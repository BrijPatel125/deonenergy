<?php
/**
 * One-off seeder for the Knowledge Hub modules:
 *   • Impact & Execution  → deon_case_study (3)
 *   • In The Press        → deon_press (4, with outlet logos)
 *   • Resource Archive    → deon_document (4, only when the library is empty)
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-knowledge-hub.php
 *     wp eval-file bin/seed-knowledge-hub.php remove   # delete seeded press + case studies
 *
 * Idempotent: case studies + press are matched by slug and updated in place.
 * Documents are seeded only when no deon_document exists yet, so a client's
 * real Investor Relations library is never touched.
 *
 * STAGING ONLY — REMOVE THE PRESS MENTIONS BEFORE LAUNCH. The seeded press
 * cards put invented quotes under the mastheads of real news organisations
 * (Reuters, Bloomberg, Forbes, The Economic Times) and link to those outlets'
 * home pages, not to any article. The approved copy is explicit (PDF §8 News &
 * Media): "Every card must link to a real, published article." Publishing these
 * is fabricated third-party endorsement — tear them down with the `remove`
 * argument above and let the honest empty state show until real coverage
 * exists.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-knowledge-hub.php\n" );
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$theme_dir = get_template_directory();
$theme_uri = get_template_directory_uri();

// Positional `remove`, not `--remove`: WP-CLI rejects unknown leading-dash
// flags before eval-file ever runs the script. Removes the fabricated press
// mentions and the demo case studies; seeded documents are left alone because
// the client's real library may share the CPT.
if ( in_array( 'remove', (array) ( $args ?? array() ), true ) ) {
	$deon_removed = 0;

	foreach ( array( 'deon_press', 'deon_case_study' ) as $deon_type ) {
		foreach ( get_posts( array(
			'post_type'      => $deon_type,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		) ) as $deon_id ) {
			$deon_thumb = get_post_thumbnail_id( $deon_id );
			if ( $deon_thumb ) {
				wp_delete_attachment( $deon_thumb, true );
			}
			wp_delete_post( $deon_id, true );
			++$deon_removed;
		}
	}

	WP_CLI::success( sprintf( 'Removed %d seeded press mentions / case studies.', $deon_removed ) );
	return;
}

/* ── Case Studies (Impact & Execution) ──────────────────────────────────── */
$case_studies = array(
	array(
		'slug'      => 'mundra-port-project',
		'title'     => 'Mundra Port Project',
		'challenge' => 'High saline environment causing rapid component corrosion and energy loss.',
		'solution'  => 'Deployment of specialized salt-resistant module coatings and automated cleaning.',
		'result'    => '+18% Efficiency',
	),
	array(
		'slug'      => 'rajkot-municipal-grid',
		'title'     => 'Rajkot Municipal Grid',
		'challenge' => 'Fragmented distribution network with frequent power surges and outages.',
		'solution'  => 'IoT-enabled central monitoring and smart-switching load balancers.',
		'result'    => 'Zero Downtime',
	),
	array(
		'slug'      => 'surat-industrial-cluster',
		'title'     => 'Surat Industrial Cluster',
		'challenge' => 'Space constraints for large-scale solar arrays in heavy manufacturing zones.',
		'solution'  => 'Elevated rooftop mounting systems allowing ground-level industrial ops.',
		'result'    => '40MW Peak',
	),
);

$order = 0;
foreach ( $case_studies as $data ) {
	$order++;
	$existing = get_page_by_path( $data['slug'], OBJECT, 'deon_case_study' );
	$postarr  = array(
		'post_type'   => 'deon_case_study',
		'post_status' => 'publish',
		'post_title'  => $data['title'],
		'post_name'   => $data['slug'],
		'menu_order'  => $order,
	);
	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}
	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( $data['slug'] . ': ' . $post_id->get_error_message() );
		continue;
	}
	update_post_meta( $post_id, '_deon_cs_challenge', $data['challenge'] );
	update_post_meta( $post_id, '_deon_cs_solution', $data['solution'] );
	update_post_meta( $post_id, '_deon_cs_result', $data['result'] );
	WP_CLI::log( sprintf( 'Case study: %s (#%d)', $data['title'], $post_id ) );
}

/* ── Press Mentions (In The Press) ──────────────────────────────────────── */
$press = array(
	array(
		'slug'  => 'economic-times-benchmark',
		'title' => 'The Economic Times',
		'quote' => 'Deon Energy Limited sets a new benchmark for corporate transparency in the Gujarat solar sector.',
		'url'   => 'https://economictimes.indiatimes.com/',
		'logo'  => $theme_uri . '/assets/img/press-economic-times.jpg',
	),
	array(
		'slug'  => 'bloomberg-scaling',
		'title' => 'Bloomberg',
		'quote' => 'Analyzing the rapid scaling of renewable infrastructure: The Deon Energy model.',
		'url'   => 'https://www.bloomberg.com/',
		'logo'  => $theme_uri . '/assets/img/press-bloomberg.jpg',
	),
	array(
		'slug'  => 'forbes-emerging-leaders',
		'title' => 'Forbes',
		'quote' => "Top 10 Emerging Leaders in Asia's Renewable Energy Market 2024.",
		'url'   => 'https://www.forbes.com/',
		'logo'  => $theme_uri . '/assets/img/press-forbes.jpg',
	),
	array(
		'slug'  => 'reuters-record-investment',
		'title' => 'Reuters',
		'quote' => 'Deon Energy secures record investment for western grid expansion projects.',
		'url'   => 'https://www.reuters.com/',
		'logo'  => $theme_uri . '/assets/img/press-reuters.jpg',
	),
);

$order = 0;
foreach ( $press as $data ) {
	$order++;
	$existing = get_page_by_path( $data['slug'], OBJECT, 'deon_press' );
	$postarr  = array(
		'post_type'   => 'deon_press',
		'post_status' => 'publish',
		'post_title'  => $data['title'],
		'post_name'   => $data['slug'],
		'menu_order'  => $order,
	);
	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}
	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( $data['slug'] . ': ' . $post_id->get_error_message() );
		continue;
	}
	update_post_meta( $post_id, '_deon_press_quote', $data['quote'] );
	update_post_meta( $post_id, '_deon_press_url', $data['url'] );
	if ( ! has_post_thumbnail( $post_id ) ) {
		$att_id = media_sideload_image( $data['logo'], $post_id, $data['title'], 'id' );
		if ( is_wp_error( $att_id ) ) {
			WP_CLI::warning( $data['slug'] . ' logo: ' . $att_id->get_error_message() );
		} else {
			set_post_thumbnail( $post_id, $att_id );
		}
	}
	WP_CLI::log( sprintf( 'Press: %s (#%d)', $data['title'], $post_id ) );
}

/* ── Resource Archive (deon_document) — only if the library is empty ─────── */
$has_docs = get_posts( array( 'post_type' => 'deon_document', 'posts_per_page' => 1, 'post_status' => 'any', 'fields' => 'ids' ) );

if ( $has_docs ) {
	WP_CLI::log( 'Documents already exist — skipping Resource Archive seed (client library preserved).' );
} else {
	$sample_pdf = $theme_dir . '/deo_sow.pdf'; // Bundled sample so file size/format resolve.
	$documents  = array(
		array( 'slug' => 'annual-sustainability-report-2023', 'title' => 'Annual Sustainability Report 2023', 'year' => '2023' ),
		array( 'slug' => 'technical-specification-deon-series-x', 'title' => 'Technical Specification: DEON Series-X', 'year' => '2024' ),
		array( 'slug' => 'gujarat-solar-policy-overview-2024', 'title' => 'Gujarat Solar Policy Overview 2024', 'year' => '2024' ),
		array( 'slug' => 'investor-presentation-green-bonds-series-a', 'title' => 'Investor Presentation: Green Bonds Series A', 'year' => '2024' ),
	);

	$order = 0;
	foreach ( $documents as $data ) {
		$order++;
		$post_id = wp_insert_post( array(
			'post_type'   => 'deon_document',
			'post_status' => 'publish',
			'post_title'  => $data['title'],
			'post_name'   => $data['slug'],
			'menu_order'  => $order,
		), true );
		if ( is_wp_error( $post_id ) ) {
			WP_CLI::warning( $data['slug'] . ': ' . $post_id->get_error_message() );
			continue;
		}
		update_post_meta( $post_id, '_deon_document_year', $data['year'] );

		// Sideload the bundled sample PDF so DOWNLOAD + "PDF • size" resolve.
		if ( file_exists( $sample_pdf ) ) {
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
		WP_CLI::log( sprintf( 'Document: %s (#%d)', $data['title'], $post_id ) );
	}
}

WP_CLI::success( 'Knowledge Hub modules seeded.' );
