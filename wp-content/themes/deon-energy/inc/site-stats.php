<?php
/**
 * Site Statistics — one editable source for every numeric stat band.
 *
 * Four bands across the site share the same shape (value + label, plus an
 * optional sublabel for ESG). Each tile's default is derived from the published
 * project portfolio ( inc/projects.php ) so the site ships with real figures;
 * the client overrides any tile from Appearance → Customize → Site Statistics.
 *
 * Bands:
 *   home      → template-parts/home/stats.php          (4 tiles)
 *   projects  → template-parts/projects/stats.php       (4 tiles)
 *   investor  → template-parts/investors/financial-highlights.php (4 tiles)
 *   esg       → template-parts/esg/stats.php            (4 tiles, has sublabel)
 *
 * Templates call deon_stat_tiles( $band ) — never get_theme_mod directly.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Band definitions with portfolio-derived defaults.
 *
 * @return array<string,array> keyed by band slug.
 */
function deon_stats_bands() {
	$p = function_exists( 'deon_project_portfolio_stats' )
		? deon_project_portfolio_stats()
		: array(
			'total_mw'      => 154.414,
			'project_count' => 34,
			'city_count'    => 29,
			'state_count'   => 1,
			'states'        => array( 'Gujarat' ),
		);

	$mw       = function_exists( 'deon_format_mw' )
		? deon_format_mw( $p['total_mw'] )
		: number_format_i18n( (float) $p['total_mw'], 1 );
	$projects = number_format_i18n( (int) $p['project_count'] );
	$cities   = number_format_i18n( (int) $p['city_count'] );
	$states   = ! empty( $p['states'] )
		? implode( ', ', $p['states'] )
		: number_format_i18n( (int) $p['state_count'] );

	return array(
		'home' => array(
			'title'   => __( 'Home — Stat Strip', 'deon-energy' ),
			'has_sub' => false,
			'tiles'   => array(
				array( 'value' => $projects,      'label' => __( 'Projects Completed', 'deon-energy' ) ),
				array( 'value' => $mw . ' MW',    'label' => __( 'MW Commissioned', 'deon-energy' ) ),
				array( 'value' => $cities,        'label' => __( 'Cities & Villages', 'deon-energy' ) ),
				array( 'value' => '30',           'label' => __( 'Expert Engineers', 'deon-energy' ) ),
			),
		),
		/*
		 * Projects / Investor / ESG all render through
		 * template-parts/global/stat-band.php, so all three carry the ESG
		 * triple: short category label → value → descriptive sublabel.
		 */
		'projects' => array(
			'title'   => __( 'Projects — Portfolio Band', 'deon-energy' ),
			'has_sub' => true,
			'tiles'   => array(
				array( 'value' => $mw . ' MW', 'label' => __( 'Capacity', 'deon-energy' ), 'sub' => __( 'Commissioned Capacity', 'deon-energy' ) ),
				array( 'value' => $projects,   'label' => __( 'Projects', 'deon-energy' ), 'sub' => __( 'Solar Projects Delivered', 'deon-energy' ) ),
				array( 'value' => $cities,     'label' => __( 'Reach', 'deon-energy' ),    'sub' => __( 'Cities & Villages Covered', 'deon-energy' ) ),
				array( 'value' => $states,     'label' => __( 'Presence', 'deon-energy' ), 'sub' => __( 'Statewide Coverage', 'deon-energy' ) ),
			),
		),
		'investor' => array(
			'title'   => __( 'Investor Relations — Highlights', 'deon-energy' ),
			'has_sub' => true,
			'tiles'   => array(
				array( 'value' => $mw . ' MW', 'label' => __( 'Capacity', 'deon-energy' ), 'sub' => __( 'Commissioned Capacity', 'deon-energy' ) ),
				array( 'value' => $projects,   'label' => __( 'Projects', 'deon-energy' ), 'sub' => __( 'Projects Delivered', 'deon-energy' ) ),
				array( 'value' => $cities,     'label' => __( 'Reach', 'deon-energy' ),    'sub' => __( 'Cities & Villages', 'deon-energy' ) ),
				array( 'value' => $states,     'label' => __( 'Presence', 'deon-energy' ), 'sub' => __( 'Statewide Coverage', 'deon-energy' ) ),
			),
		),
		/*
		 * Impact Snapshot, in the order the approved copy lists it (PDF §6).
		 * Only Capacity ships a default (derived from the published portfolio);
		 * the other three stay EMPTY on purpose — an unsourced ESG figure
		 * carries regulatory exposure, so each tile hides until the client
		 * enters a real number. CO2 Avoided is labelled an estimate, and the
		 * band prints the CEA grid-emission-factor note beneath it.
		 */
		'esg' => array(
			'title'   => __( 'ESG — Impact Stats', 'deon-energy' ),
			'has_sub' => true,
			'tiles'   => array(
				array( 'value' => $mw . ' MW', 'label' => __( 'Capacity', 'deon-energy' ),  'sub' => __( 'Installed Solar Capacity', 'deon-energy' ) ),
				array( 'value' => '',   'label' => __( 'CO2 Avoided', 'deon-energy' ), 'sub' => __( 'Estimated CO2 Avoided', 'deon-energy' ) ),
				array( 'value' => '',   'label' => __( 'Customers', 'deon-energy' ), 'sub' => __( 'Industrial & Commercial Customers Served', 'deon-energy' ) ),
				array( 'value' => '',   'label' => __( 'Team', 'deon-energy' ),      'sub' => __( 'Team Members Employed', 'deon-energy' ) ),
			),
		),
	);
}

/**
 * Register the Site Statistics Customizer panel — one section per band, with a
 * value/label (and sublabel for ESG) control per tile.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function deon_stats_customizer( $wp_customize ) {
	$wp_customize->add_panel( 'deon_stats', array(
		'title'       => __( 'Site Statistics', 'deon-energy' ),
		'description' => __( 'Edit the number tiles shown across the site. Clear a value to fall back to the built-in figure (ESG tiles hide entirely when empty).', 'deon-energy' ),
		'priority'    => 33,
	) );

	foreach ( deon_stats_bands() as $band => $cfg ) {
		$section = "deon_stats_{$band}";
		$wp_customize->add_section( $section, array(
			'title' => $cfg['title'],
			'panel' => 'deon_stats',
		) );

		foreach ( $cfg['tiles'] as $i => $tile ) {
			$n     = $i + 1;
			$parts = array( 'value' => __( 'Value', 'deon-energy' ), 'label' => __( 'Label', 'deon-energy' ) );
			if ( ! empty( $cfg['has_sub'] ) ) {
				$parts['sub'] = __( 'Sublabel', 'deon-energy' );
			}

			foreach ( $parts as $part => $part_label ) {
				$id = "deon_stat_{$band}_{$n}_{$part}";
				$wp_customize->add_setting( $id, array(
					'default'           => isset( $tile[ $part ] ) ? $tile[ $part ] : '',
					'sanitize_callback' => 'sanitize_text_field',
				) );
				$wp_customize->add_control( $id, array(
					/* translators: 1: tile number, 2: field name (Value/Label/Sublabel). */
					'label'   => sprintf( __( 'Tile %1$d — %2$s', 'deon-energy' ), $n, $part_label ),
					'section' => $section,
					'type'    => 'text',
				) );
			}
		}
	}
}
add_action( 'customize_register', 'deon_stats_customizer' );

/**
 * Resolved tiles for a band — Customizer override else portfolio-derived default.
 *
 * @param string $band Band slug (home|projects|investor|esg).
 * @return array<int,array{value:string,label:string,sub?:string}>
 */
function deon_stat_tiles( $band ) {
	$bands = deon_stats_bands();
	if ( ! isset( $bands[ $band ] ) ) {
		return array();
	}

	$cfg = $bands[ $band ];
	$out = array();

	foreach ( $cfg['tiles'] as $i => $tile ) {
		$n   = $i + 1;
		$row = array(
			'value' => get_theme_mod( "deon_stat_{$band}_{$n}_value", $tile['value'] ),
			'label' => get_theme_mod( "deon_stat_{$band}_{$n}_label", $tile['label'] ),
		);
		if ( ! empty( $cfg['has_sub'] ) ) {
			$row['sub'] = get_theme_mod( "deon_stat_{$band}_{$n}_sub", isset( $tile['sub'] ) ? $tile['sub'] : '' );
		}
		$out[] = $row;
	}

	return $out;
}
