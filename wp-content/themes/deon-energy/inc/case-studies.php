<?php
/**
 * Deon Energy — Case Study custom post type.
 *
 * Case Studies vs Projects: a Project post is a lightweight portfolio entry
 * (client name, location, capacity, one photo). A Case Study is the richer
 * narrative — problem → solution → results, with quotes, specs, and enough
 * detail to earn a lead's trust. Case studies rank on Google for queries
 * like "solar EPC textile mill Gujarat case study" and get shared by sales.
 *
 * This module provides:
 *   - Registration of the `deon_case_study` CPT
 *   - Meta fields:
 *     * Client name, sector, location
 *     * System type, capacity (kWp), commissioned month/year
 *     * Annual generation (MWh), payback estimate (years), CO2 avoided
 *     * Problem statement, solution summary, results (structured text)
 *     * Client quote + attribution
 *     * Featured metrics for the hero (3 KPIs)
 *   - Admin columns for capacity, sector, commissioned date
 *   - Automatic Article + CaseStudy schema on single case study pages
 *
 * The theme should also add `single-deon_case_study.php` and
 * `archive-deon_case_study.php` templates — placeholders are shipped in
 * this bundle. Both use existing site styling (main.css already defines
 * the utility classes they rely on).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============================================================================
 * 1. CPT registration
 * ========================================================================= */

function deon_case_study_register_cpt() {
	$labels = array(
		'name'                  => __( 'Case Studies', 'deon-energy' ),
		'singular_name'         => __( 'Case Study', 'deon-energy' ),
		'add_new'               => __( 'Add New', 'deon-energy' ),
		'add_new_item'          => __( 'Add New Case Study', 'deon-energy' ),
		'edit_item'             => __( 'Edit Case Study', 'deon-energy' ),
		'new_item'              => __( 'New Case Study', 'deon-energy' ),
		'view_item'             => __( 'View Case Study', 'deon-energy' ),
		'search_items'          => __( 'Search Case Studies', 'deon-energy' ),
		'not_found'             => __( 'No case studies yet.', 'deon-energy' ),
		'not_found_in_trash'    => __( 'No case studies in Trash.', 'deon-energy' ),
		'menu_name'             => __( 'Case Studies', 'deon-energy' ),
		'featured_image'        => __( 'Hero image (site photo)', 'deon-energy' ),
		'set_featured_image'    => __( 'Set hero image', 'deon-energy' ),
	);

	register_post_type( 'deon_case_study', array(
		'labels'              => $labels,
		'public'              => true,
		'has_archive'         => 'case-studies',
		'rewrite'             => array( 'slug' => 'case-studies', 'with_front' => false ),
		'menu_icon'           => 'dashicons-portfolio',
		'menu_position'       => 21,
		'show_in_rest'        => true,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		'taxonomies'          => array( 'deon_case_study_sector' ),
	) );

	register_taxonomy( 'deon_case_study_sector', 'deon_case_study', array(
		'labels'            => array(
			'name'          => __( 'Sectors', 'deon-energy' ),
			'singular_name' => __( 'Sector', 'deon-energy' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'case-studies/sector' ),
	) );
}
add_action( 'init', 'deon_case_study_register_cpt', 11 );

/* ============================================================================
 * 2. Meta fields — structured details for each case study
 * ========================================================================= */

function deon_case_study_meta_boxes() {
	add_meta_box(
		'deon_case_study_details',
		__( 'Case Study Details', 'deon-energy' ),
		'deon_case_study_details_render',
		'deon_case_study',
		'normal',
		'high'
	);
	add_meta_box(
		'deon_case_study_quote',
		__( 'Client Quote', 'deon-energy' ),
		'deon_case_study_quote_render',
		'deon_case_study',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'deon_case_study_meta_boxes' );

function deon_case_study_details_render( $post ) {
	wp_nonce_field( 'deon_case_study_save', 'deon_case_study_nonce' );

	$fields = array(
		'client_name'         => array( __( 'Client name', 'deon-energy' ), 'text', 'e.g. Datt Polyplast LLP' ),
		'client_sector'       => array( __( 'Sector / Industry', 'deon-energy' ), 'text', 'e.g. Plastics manufacturing' ),
		'location'            => array( __( 'Location (city, state)', 'deon-energy' ), 'text', 'e.g. Saraya, Gujarat' ),
		'capacity_kwp'        => array( __( 'Capacity (kWp)', 'deon-energy' ), 'number', 'e.g. 315' ),
		'system_type'         => array( __( 'System type', 'deon-energy' ), 'select', array( 'Rooftop', 'Ground-mount', 'Hybrid' ) ),
		'commissioned_date'   => array( __( 'Commissioned month/year', 'deon-energy' ), 'text', 'e.g. Aug 2024' ),
		'annual_generation'   => array( __( 'Annual generation (MWh)', 'deon-energy' ), 'number', 'e.g. 480' ),
		'payback_years'       => array( __( 'Payback estimate (years)', 'deon-energy' ), 'text', 'e.g. 4.2' ),
		'co2_avoided'         => array( __( 'CO₂ avoided (tonnes/year)', 'deon-energy' ), 'number', 'e.g. 340' ),
		'problem'             => array( __( 'The challenge (2-3 sentences)', 'deon-energy' ), 'textarea', 'What the client was dealing with before Deon came in.' ),
		'solution'            => array( __( 'Our approach (2-3 sentences)', 'deon-energy' ), 'textarea', 'What Deon built, how it was scoped, what was distinctive.' ),
		'results'             => array( __( 'The outcome (2-3 sentences)', 'deon-energy' ), 'textarea', 'Measurable results — savings, uptime, generation vs target.' ),
	);

	echo '<table class="form-table" role="presentation" style="max-width:900px;">';
	foreach ( $fields as $key => $config ) {
		list( $label, $type, $extra ) = $config;
		$meta_key = '_deon_cs_' . $key;
		$value    = get_post_meta( $post->ID, $meta_key, true );
		echo '<tr>';
		echo '<th scope="row" style="width:220px;"><label for="' . esc_attr( $meta_key ) . '">' . esc_html( $label ) . '</label></th>';
		echo '<td>';
		switch ( $type ) {
			case 'textarea':
				echo '<textarea name="' . esc_attr( $meta_key ) . '" id="' . esc_attr( $meta_key ) . '" rows="3" style="width:100%;max-width:600px;" placeholder="' . esc_attr( $extra ) . '">' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'number':
				echo '<input type="number" step="0.01" name="' . esc_attr( $meta_key ) . '" id="' . esc_attr( $meta_key ) . '" value="' . esc_attr( $value ) . '" style="width:240px;" placeholder="' . esc_attr( $extra ) . '">';
				break;
			case 'select':
				echo '<select name="' . esc_attr( $meta_key ) . '" id="' . esc_attr( $meta_key ) . '" style="width:240px;">';
				echo '<option value="">— ' . esc_html__( 'Select', 'deon-energy' ) . ' —</option>';
				foreach ( $extra as $opt ) {
					echo '<option value="' . esc_attr( $opt ) . '"' . selected( $value, $opt, false ) . '>' . esc_html( $opt ) . '</option>';
				}
				echo '</select>';
				break;
			default:
				echo '<input type="text" name="' . esc_attr( $meta_key ) . '" id="' . esc_attr( $meta_key ) . '" value="' . esc_attr( $value ) . '" style="width:100%;max-width:600px;" placeholder="' . esc_attr( $extra ) . '">';
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

function deon_case_study_quote_render( $post ) {
	$quote  = get_post_meta( $post->ID, '_deon_cs_quote', true );
	$author = get_post_meta( $post->ID, '_deon_cs_quote_author', true );
	$role   = get_post_meta( $post->ID, '_deon_cs_quote_role', true );
	?>
	<p><label style="font-weight:600;"><?php esc_html_e( 'Quote (2-3 sentences from the client — needs written consent to publish)', 'deon-energy' ); ?></label></p>
	<textarea name="_deon_cs_quote" rows="4" style="width:100%;max-width:900px;"><?php echo esc_textarea( $quote ); ?></textarea>
	<p style="margin-top:10px;"><label style="font-weight:600;"><?php esc_html_e( 'Attribution — name', 'deon-energy' ); ?></label></p>
	<input type="text" name="_deon_cs_quote_author" value="<?php echo esc_attr( $author ); ?>" style="width:100%;max-width:400px;" placeholder="<?php esc_attr_e( 'e.g. Rakesh Patel', 'deon-energy' ); ?>">
	<p style="margin-top:10px;"><label style="font-weight:600;"><?php esc_html_e( 'Attribution — role', 'deon-energy' ); ?></label></p>
	<input type="text" name="_deon_cs_quote_role" value="<?php echo esc_attr( $role ); ?>" style="width:100%;max-width:400px;" placeholder="<?php esc_attr_e( 'e.g. Plant Manager, Datt Polyplast', 'deon-energy' ); ?>">
	<?php
}

function deon_case_study_save( $post_id ) {
	if ( ! isset( $_POST['deon_case_study_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['deon_case_study_nonce'] ), 'deon_case_study_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$keys = array(
		'_deon_cs_client_name', '_deon_cs_client_sector', '_deon_cs_location',
		'_deon_cs_capacity_kwp', '_deon_cs_system_type', '_deon_cs_commissioned_date',
		'_deon_cs_annual_generation', '_deon_cs_payback_years', '_deon_cs_co2_avoided',
		'_deon_cs_problem', '_deon_cs_solution', '_deon_cs_results',
		'_deon_cs_quote', '_deon_cs_quote_author', '_deon_cs_quote_role',
	);
	$textarea_keys = array( '_deon_cs_problem', '_deon_cs_solution', '_deon_cs_results', '_deon_cs_quote' );
	foreach ( $keys as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			$raw = wp_unslash( $_POST[ $key ] );
			$value = in_array( $key, $textarea_keys, true ) ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
			if ( '' === $value ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
}
add_action( 'save_post_deon_case_study', 'deon_case_study_save' );

/* ============================================================================
 * 3. Admin columns — quick scannability in the list view
 * ========================================================================= */

function deon_case_study_admin_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['client']     = __( 'Client', 'deon-energy' );
			$new['capacity']   = __( 'Capacity', 'deon-energy' );
			$new['commissioned'] = __( 'Commissioned', 'deon-energy' );
		}
	}
	return $new;
}
add_filter( 'manage_deon_case_study_posts_columns', 'deon_case_study_admin_columns' );

function deon_case_study_admin_column_content( $column, $post_id ) {
	if ( 'client' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_deon_cs_client_name', true ) );
	}
	if ( 'capacity' === $column ) {
		$kwp = get_post_meta( $post_id, '_deon_cs_capacity_kwp', true );
		if ( ! empty( $kwp ) ) {
			echo esc_html( $kwp . ' kWp' );
		}
	}
	if ( 'commissioned' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_deon_cs_commissioned_date', true ) );
	}
}
add_action( 'manage_deon_case_study_posts_custom_column', 'deon_case_study_admin_column_content', 10, 2 );

/* ============================================================================
 * 4. Automatic schema on single case study pages
 * ========================================================================= */

function deon_case_study_schema() {
	if ( ! is_singular( 'deon_case_study' ) ) {
		return;
	}
	$post   = get_post();
	$client = get_post_meta( $post->ID, '_deon_cs_client_name', true );
	$kwp    = get_post_meta( $post->ID, '_deon_cs_capacity_kwp', true );

	$schema = array(
		'@context'     => 'https://schema.org',
		'@type'        => 'Article',
		'@id'          => get_permalink() . '#case-study',
		'headline'     => get_the_title(),
		'description'  => get_the_excerpt(),
		'image'        => has_post_thumbnail() ? get_the_post_thumbnail_url( $post->ID, 'full' ) : '',
		'datePublished' => get_the_date( 'c' ),
		'dateModified'  => get_the_modified_date( 'c' ),
		'author'       => array( '@type' => 'Organization', 'name' => 'Deon Energy Limited' ),
		'publisher'    => array( '@id' => trailingslashit( home_url() ) . '#organization' ),
		'about'        => array(
			'@type' => 'Product',
			'name'  => trim( $kwp ? ( $kwp . ' kWp solar installation' ) : 'Solar installation' ),
			'category' => 'Solar EPC',
		),
	);
	if ( ! empty( $client ) ) {
		$schema['mentions'] = array(
			'@type' => 'Organization',
			'name'  => $client,
		);
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'deon_case_study_schema', 15 );

/* ============================================================================
 * 5. Rewrite-rule flush on first activation
 * ========================================================================= */

function deon_case_study_flush_once() {
	if ( get_option( 'deon_case_study_rewrites_flushed_v1' ) ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'deon_case_study_rewrites_flushed_v1', 1 );
}
add_action( 'init', 'deon_case_study_flush_once', 99 );
