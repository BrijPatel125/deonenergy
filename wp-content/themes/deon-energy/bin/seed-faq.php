<?php
/**
 * One-off seeder for the FAQ page (deon_faq CPT + deon_faq_category taxonomy).
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-faq.php
 *
 * Content is the approved copy (PDF §10 FAQ): four categories and eight
 * questions, verbatim. Idempotent: re-running updates the same categories +
 * questions (both matched by slug) instead of creating duplicates.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-faq.php\n" );
	exit( 1 );
}

$categories = array(
	array(
		'name' => 'Solar Technology', 'slug' => 'solar-technology', 'icon' => 'solar',
		'faqs' => array(
			array(
				'slug' => 'faq-long-term-performance',
				'q' => 'How does Deon Energy protect long-term plant performance?',
				'a' => 'Through three things — component selection (Tier-1 modules and inverters with strong warranty backing), site-specific engineering (spacing, tilt and cable design matched to actual site conditions), and a structured O&M contract with monitoring and scheduled preventive work. Long-term generation is engineered, not assumed.',
			),
			array(
				'slug' => 'faq-industrial-vs-commercial',
				'q' => 'What is the difference between industrial and commercial grade solar systems?',
				'a' => 'The core equipment is often similar, but the design approach differs. Industrial systems tend to be larger, on stronger structures, integrated with higher-voltage evacuation, and built to withstand harsher site conditions (heat, dust, humidity, chemical exposure). Commercial systems are smaller, typically LT-connected, and optimised for the tariff and consumption pattern of a commercial building. We engineer to the load and the environment, not to a template.',
			),
		),
	),
	array(
		'name' => 'Commercial Projects', 'slug' => 'commercial-projects', 'icon' => 'commercial',
		'faqs' => array(
			array(
				'slug' => 'faq-payback-period',
				'q' => 'What kind of payback period should we expect for a commercial solar system?',
				'a' => 'Payback varies significantly by tariff, load profile, roof or land availability, financing structure and applicable subsidies. As a broad range for eligible commercial and industrial customers in Gujarat under a CAPEX model, payback typically falls within 3–6 years, but a proper site-specific assessment is the only way to give you a defensible number.\\n\\n<p class="deon-faq__note"><em>Illustrative range only, not a commitment. Actual payback depends on your tariff, load profile, site conditions, and financing.</em></p>',
			),
			array(
				'slug' => 'faq-operating-regions',
				'q' => 'Which regions in India does Deon Energy operate in?',
				'a' => 'Our head office is in Ahmedabad, with regional offices in Rajkot and Morbi. Gujarat is our primary operating market, and we selectively take on projects in other states where we can deliver to the same technical and safety standard.',
			),
		),
	),
	array(
		'name' => 'Investment & Commercials', 'slug' => 'investment-commercials', 'icon' => 'investment',
		'faqs' => array(
			array(
				'slug' => 'faq-proposal-contents',
				'q' => 'What does a proposal from Deon typically include?',
				'a' => 'A written proposal includes plant sizing based on your load and available area, the recommended equipment configuration, estimated annual generation and revenue/savings modelling (with assumptions stated), CAPEX vs OPEX options where relevant, delivery timeline, and applicable regulatory and safety compliance. Every commercial number carries the assumptions behind it.',
			),
			array(
				'slug' => 'faq-financial-reports',
				'q' => 'Are Deon Energy’s financial or investment reports publicly available?',
				'a' => 'For queries relating to the company’s financial position, filings or investor information, please write to our investor relations contact. We will share what is publicly available and direct you to the right disclosures.',
			),
		),
	),
	array(
		'name' => 'Maintenance & Support', 'slug' => 'maintenance-support', 'icon' => 'maintenance',
		'faqs' => array(
			array(
				'slug' => 'faq-om-contract-scope',
				'q' => 'What is included in a Deon O&M contract?',
				'a' => 'A typical O&M contract covers scheduled preventive maintenance (module cleaning cycles, inverter servicing, structural checks), corrective maintenance within defined response times, 24/7 remote monitoring, monthly generation and uptime reporting, and coordination with the DISCOM for any grid-side issues. Contract scope is confirmed in writing project by project.',
			),
			array(
				'slug' => 'faq-response-time',
				'q' => 'How quickly does your team respond to an issue on-site?',
				'a' => 'Response times are defined contractually and depend on the plant location and criticality tier. For plants under active O&M, we commit to defined SLA windows and track performance against them month by month.',
			),
		),
	),
);

$order      = 0;
$cat_order  = 10;
foreach ( $categories as $cat ) {
	$term = term_exists( $cat['slug'], 'deon_faq_category' );
	if ( ! $term ) {
		$term = wp_insert_term( $cat['name'], 'deon_faq_category', array( 'slug' => $cat['slug'] ) );
	}
	if ( is_wp_error( $term ) ) {
		WP_CLI::warning( "Category '{$cat['name']}': " . $term->get_error_message() );
		continue;
	}
	$term_id = (int) $term['term_id'];
	update_term_meta( $term_id, '_deon_faq_icon', $cat['icon'] );
	// Display order = the order the approved copy lists the categories.
	update_term_meta( $term_id, '_deon_faq_order', $cat_order );
	$cat_order += 10;
	WP_CLI::log( "Category ready: {$cat['name']} (#{$term_id})" );

	foreach ( $cat['faqs'] as $faq ) {
		$existing = get_page_by_path( $faq['slug'], OBJECT, 'deon_faq' );
		$postarr  = array(
			'post_title'   => $faq['q'],
			'post_name'    => $faq['slug'],
			'post_content' => $faq['a'],
			'post_type'    => 'deon_faq',
			'post_status'  => 'publish',
			'menu_order'   => $order++,
		);
		if ( $existing ) {
			$postarr['ID'] = $existing->ID;
			$post_id = wp_update_post( $postarr );
		} else {
			$post_id = wp_insert_post( $postarr );
		}
		if ( is_wp_error( $post_id ) ) {
			WP_CLI::warning( "FAQ '{$faq['slug']}': " . $post_id->get_error_message() );
			continue;
		}
		wp_set_object_terms( $post_id, $term_id, 'deon_faq_category' );
		WP_CLI::log( "  FAQ ready: {$faq['q']}" );
	}
}

WP_CLI::success( 'FAQ content seeded.' );
