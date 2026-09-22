<?php
/**
 * QA FIXTURES — fake `deon_job` postings for checking the Careers listing and
 * the job detail page (hero → About the Role → Core Responsibilities →
 * Professional Requirements → The Deon Workspace → Role Analytics → Apply Now).
 *
 *     wp eval-file bin/seed-demo-jobs.php            # create / update demos
 *     wp eval-file bin/seed-demo-jobs.php remove     # delete demos
 *
 * Three fixtures on purpose:
 *   • deon-demo-job-full      — every field populated (sidebar shows 3 rows)
 *   • deon-demo-job-lean      — no seniority/travel/requirements (thin sidebar,
 *                               Professional Requirements section auto-hides)
 *   • deon-demo-job-external  — Apply URL set, so Submit Candidacy leaves the
 *                               mailto fallback and points at an ATS link
 *
 * Idempotent — matched by slug and updated in place. NOT part of the theme
 * runtime; delete the demos before launch.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-demo-jobs.php\n" );
	exit( 1 );
}

if ( ! post_type_exists( 'deon_job' ) ) {
	WP_CLI::error( 'deon_job is not registered. Is the Deon Energy theme active?' );
}

// Positional `remove`, not `--remove`: WP-CLI rejects unknown leading-dash
// flags before eval-file ever runs the script.
$remove = in_array( 'remove', (array) ( $args ?? array() ), true );

$demos = array(
	array(
		'slug'       => 'deon-demo-job-full',
		'title'      => 'Senior Solar Project Engineer',
		'department' => 'Renewable Infrastructure',
		'location'   => 'Ahmedabad, India',
		'type'       => 'Full-time',
		'seniority'  => 'Lead/Senior',
		'travel'     => '20% EMEA',
		'apply'      => '',
		'order'      => 10,
		'desc'       => 'Technical lead for large-scale utility solar installations across the EMEA region.',
		'body'       => '<p>As a Senior Solar Project Engineer at DEON ENERGY LIMITED, you will be the technical lead for our large-scale utility solar installations across the EMEA region. We are looking for a visionary strategist who combines deep technical engineering expertise with the ability to navigate complex regulatory and institutional landscapes.</p><p>This is not just an engineering role; it is an intellectual leadership position within a firm that is redefining global renewable infrastructure.</p>',
		'respons'    => "Lead the end-to-end technical design and engineering of 50MW+ utility-scale PV systems.\nCoordinate with global consultancy partners to ensure grid interconnection stability and compliance.\nOversee rigorous Quality Assurance protocols across multi-national construction sites.\nDevelop and refine DEON's proprietary engineering standards for high-efficiency energy harvesting.",
		'requires'   => "Educational Foundation | Master's degree in Electrical Engineering, Renewable Energy, or a related technical discipline from a top-tier institution.\nTechnical Proficiency | 8+ years of experience in solar PV engineering with mastery of PVSyst, AutoCAD, and electrical modeling software.\nLinguistic Capabilities | Fluency in English is mandatory; professional proficiency in German or French is highly desirable for cross-border collaboration.",
	),
	array(
		'slug'       => 'deon-demo-job-lean',
		'title'      => 'O&M Field Technician',
		'department' => 'Operations & Maintenance',
		'location'   => 'Surat, India',
		'type'       => 'Full-time',
		'seniority'  => '',
		'travel'     => '',
		'apply'      => '',
		'order'      => 20,
		'desc'       => 'Preventive and corrective maintenance across our operating rooftop and ground-mount portfolio.',
		'body'       => '',
		'respons'    => "Run scheduled preventive maintenance on inverters, combiner boxes and module strings.\nRespond to plant alarms and close corrective tickets within contracted SLA windows.\nMaintain accurate service logs and spare-part inventory for the assigned cluster.",
		'requires'   => '',
	),
	array(
		'slug'       => 'deon-demo-job-external',
		'title'      => 'Head of Structured Finance',
		'department' => 'Capital Markets',
		'location'   => 'Mumbai, India',
		'type'       => 'Contract',
		'seniority'  => 'Director',
		'travel'     => '40% Domestic',
		'apply'      => 'https://example.com/careers/head-of-structured-finance',
		'order'      => 30,
		'desc'       => 'Own the debt structuring and institutional capital stack for the utility-scale pipeline.',
		'body'       => '<p>You will own the capital structure behind DEON\'s utility-scale pipeline — from term-sheet negotiation through to financial close — working directly with the founding team and our institutional lenders.</p>',
		'respons'    => "Structure project debt and mezzanine facilities for a multi-GW development pipeline.\nLead diligence workstreams with lenders, rating agencies and independent engineers.\nBuild and defend project financial models through investment-committee review.",
		'requires'   => "Educational Foundation | CA, MBA (Finance) or equivalent from a top-tier institution.\nSector Experience | 10+ years in infrastructure or renewable project finance, with closed transactions above INR 500 Cr.",
	),
);

/* ── Removal ────────────────────────────────────────────────────────────── */
if ( $remove ) {
	foreach ( $demos as $demo ) {
		$existing = get_page_by_path( $demo['slug'], OBJECT, 'deon_job' );
		if ( $existing ) {
			wp_delete_post( $existing->ID, true );
			WP_CLI::log( "Deleted: {$demo['title']}" );
		}
	}
	WP_CLI::success( 'Demo jobs removed.' );
	return;
}

/* ── Seed ───────────────────────────────────────────────────────────────── */
foreach ( $demos as $demo ) {
	$existing = get_page_by_path( $demo['slug'], OBJECT, 'deon_job' );

	$postarr = array(
		'post_type'    => 'deon_job',
		'post_status'  => 'publish',
		'post_title'   => $demo['title'],
		'post_name'    => $demo['slug'],
		'post_content' => $demo['body'],
		'menu_order'   => $demo['order'],
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
		WP_CLI::warning( "{$demo['title']}: " . $post_id->get_error_message() );
		continue;
	}

	$meta = array(
		'_deon_job_department'     => $demo['department'],
		'_deon_job_location'       => $demo['location'],
		'_deon_job_type'           => $demo['type'],
		'_deon_job_seniority'      => $demo['seniority'],
		'_deon_job_travel'         => $demo['travel'],
		'_deon_job_desc'           => $demo['desc'],
		'_deon_job_responsibilities' => $demo['respons'],
		'_deon_job_requirements'   => $demo['requires'],
		'_deon_job_apply'          => $demo['apply'],
	);

	foreach ( $meta as $key => $value ) {
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	WP_CLI::log( "{$verb}: {$demo['title']} — " . get_permalink( $post_id ) );
}

WP_CLI::success( 'Demo jobs seeded — ' . count( $demos ) . ' total. Remove later with: wp eval-file bin/seed-demo-jobs.php --remove' );
