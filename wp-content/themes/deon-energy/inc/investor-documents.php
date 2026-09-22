<?php
/**
 * Investor Documents — a DEDICATED content type for the Investor Relations page.
 *
 * Why separate from deon_document: the deon_document CPT + its deon_document_type
 * taxonomy are SHARED (News & Media brand assets, Technical Papers, the Knowledge
 * Hub Resource Archive). Mixing investor disclosures into that shared pool leaked
 * foreign categories into the IR "Categories" sidebar and required a fragile
 * allowlist. This CPT gives the client ONE unambiguous place — wp-admin →
 * "Investor Documents" — to manage every IR document and its categories, fully
 * dynamic with no cross-module bleed.
 *
 * Meta keys reuse `_deon_document_file` / `_deon_document_year` so the existing
 * deon_document_file_meta() formatter works unchanged. Featured flag marks the
 * single document shown in the big "Featured Document" card.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the Investor Documents CPT + its own Category taxonomy.
 */
function deon_register_investor_doc_cpt() {
	register_post_type( 'deon_investor_doc', array(
		'labels' => array(
			'name'               => __( 'Investor Documents', 'deon-energy' ),
			'singular_name'      => __( 'Investor Document', 'deon-energy' ),
			'add_new_item'       => __( 'Add New Investor Document', 'deon-energy' ),
			'edit_item'          => __( 'Edit Investor Document', 'deon-energy' ),
			'new_item'           => __( 'New Investor Document', 'deon-energy' ),
			'view_item'          => __( 'View Investor Document', 'deon-energy' ),
			'search_items'       => __( 'Search Investor Documents', 'deon-energy' ),
			'not_found'          => __( 'No investor documents yet', 'deon-energy' ),
			'all_items'          => __( 'All Documents', 'deon-energy' ),
			'menu_name'          => __( 'Investor Documents', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_position' => 26,
		'menu_icon'     => 'dashicons-analytics',
		'supports'      => array( 'title', 'editor', 'page-attributes' ),
		'has_archive'   => false,
		'rewrite'       => false,
	) );

	register_taxonomy( 'deon_investor_category', 'deon_investor_doc', array(
		'labels' => array(
			'name'          => __( 'Categories', 'deon-energy' ),
			'singular_name' => __( 'Category', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Category', 'deon-energy' ),
			'menu_name'     => __( 'Categories', 'deon-energy' ),
		),
		'hierarchical'      => true,
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'rewrite'           => false,
	) );
}
add_action( 'init', 'deon_register_investor_doc_cpt' );

/**
 * Seed the default investor categories so the sidebar is populated out of the
 * box. Idempotent — the client freely renames / adds / deletes them afterwards
 * under Investor Documents → Categories, and the sidebar reflects it live.
 */
/**
 * One-time cleanup of duplicate / leftover investor categories.
 *
 * We inherited two sets of terms in the DB:
 *   1. Old-seeder leftovers ("Financial Statements", "DRHP & Filings",
 *      "AGM & Notices", etc.) from the very first version of this file.
 *   2. Manual duplicates the client added via wp-admin before the
 *      code-seeded 17-category set (with slugs like `01-…`) landed.
 *
 * Sorting by slug pushes numeric-prefixed slugs to the top and the plain
 * letter-only duplicates below them, so BOTH sets render in the sidebar.
 * This function deletes the letter-only duplicates in one pass, then sets
 * a flag so it never runs again. Terms are matched by slug (unique in WP),
 * so nothing that has the numeric-prefixed slug is ever touched.
 */
function deon_cleanup_duplicate_investor_categories() {
	if ( ! taxonomy_exists( 'deon_investor_category' ) ) {
		return;
	}
	if ( get_option( 'deon_investor_dupes_cleaned_v1' ) ) {
		return;
	}

	$slugs_to_remove = array(
		// Duplicates of the client's intended 17 (auto-slugged from the names
		// when the same categories were also added manually in wp-admin).
		'financial-results',
		'ipo',
		'annual-return',
		'annual-report',
		'corporate-announcements',
		'shareholding-pattern',
		'corporate-governance-policy',
		'investor-grievance',
		'board-of-directors',
		'committee-composition',
		'disclosures-under-regulation-46-of-sebi-lodr-regulations-2015',
		'material-documents',
		'material-contracts',
		'material-creditors-material-litigations',
		'advertisements',
		'av-link',
		'company-policies',
		'stock-exchange-compliance',

		// Leftovers from the very first seeder (different names entirely —
		// safe to remove because the current list supersedes them).
		'financial-statements',
		'drhp-filings',
		'corporate-governance',
		'annual-reports',
		'agm-notices',
		'investor-presentations',
		'sebi-disclosures',
		'regulatory-updates',
	);

	foreach ( $slugs_to_remove as $slug ) {
		$term = get_term_by( 'slug', $slug, 'deon_investor_category' );
		if ( $term && ! is_wp_error( $term ) ) {
			wp_delete_term( (int) $term->term_id, 'deon_investor_category' );
		}
	}

	update_option( 'deon_investor_dupes_cleaned_v1', 1 );
}
add_action( 'init', 'deon_cleanup_duplicate_investor_categories', 15 );

function deon_seed_investor_categories() {
	if ( ! taxonomy_exists( 'deon_investor_category' ) ) {
		return;
	}
	// Run the seed loop ONCE, ever. Without this flag, term_exists() only
	// checks "does it exist right now" — so deleting a category in wp-admin
	// got silently undone on the very next page load (this hook fires on
	// every `init`, not just at setup). The flag makes deletions permanent.
	//
	// Note: the flag is versioned (`_v2`). Bumping the suffix is how we ask
	// WordPress to run the seeder one more time when the intended list of
	// categories has changed — the old flag stays set so the old seeder body
	// (a different set of terms) never runs again.
	if ( get_option( 'deon_investor_categories_seeded_v2' ) ) {
		return;
	}

	// Slugs are numeric-prefixed so the sidebar (ordered by slug, see
	// template-parts/investors/resources.php) renders them in this exact
	// sequence rather than alphabetically by name.
	$terms = array(
		'01-financial-results'              => __( 'Financial Results', 'deon-energy' ),
		'02-ipo'                            => __( 'IPO', 'deon-energy' ),
		'03-annual-return'                  => __( 'Annual Return', 'deon-energy' ),
		'04-annual-report'                  => __( 'Annual Report', 'deon-energy' ),
		'05-corporate-announcements'        => __( 'Corporate Announcements', 'deon-energy' ),
		'06-shareholding-pattern'           => __( 'Shareholding Pattern', 'deon-energy' ),
		'07-corporate-governance-policy'    => __( 'Corporate Governance Policy', 'deon-energy' ),
		'08-investor-grievance'             => __( 'Investor Grievance', 'deon-energy' ),
		'09-board-of-directors'             => __( 'Board of Directors', 'deon-energy' ),
		'10-disclosures-reg-46-sebi-lodr'   => __( 'Disclosures under Regulation 46 of SEBI (LODR) Regulations, 2015', 'deon-energy' ),
		'11-material-documents'             => __( 'Material Documents', 'deon-energy' ),
		'12-material-contracts'             => __( 'Material Contracts', 'deon-energy' ),
		'13-material-creditors-litigations' => __( 'Material Creditors & Material Litigations', 'deon-energy' ),
		'14-advertisements'                 => __( 'Advertisements', 'deon-energy' ),
		'15-av-link'                        => __( 'AV Link', 'deon-energy' ),
		'16-company-policies'               => __( 'Company Policies', 'deon-energy' ),
		'17-stock-exchange-compliance'      => __( 'Stock Exchange Compliance', 'deon-energy' ),
	);
	foreach ( $terms as $slug => $name ) {
		if ( ! term_exists( $slug, 'deon_investor_category' ) ) {
			wp_insert_term( $name, 'deon_investor_category', array( 'slug' => $slug ) );
		}
	}

	// Sub-category: "Committee Composition" nested under "Board of Directors".
	// Slug 09a-… sits between 09-board-of-directors and 10-… when sorted, so
	// the child renders right under its parent in the sidebar.
	$parent = get_term_by( 'slug', '09-board-of-directors', 'deon_investor_category' );
	if ( $parent && ! is_wp_error( $parent ) && ! term_exists( '09a-committee-composition', 'deon_investor_category' ) ) {
		wp_insert_term(
			__( 'Committee Composition', 'deon-energy' ),
			'deon_investor_category',
			array(
				'slug'   => '09a-committee-composition',
				'parent' => (int) $parent->term_id,
			)
		);
	}

	update_option( 'deon_investor_categories_seeded_v2', 1 );
}
add_action( 'init', 'deon_seed_investor_categories', 20 );

/**
 * Meta box: file URL, year, and the "Featured Document" flag.
 */
function deon_investor_doc_meta_box_register() {
	add_meta_box(
		'deon_investor_doc_details',
		__( 'Document Details', 'deon-energy' ),
		'deon_investor_doc_meta_box_html',
		'deon_investor_doc',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'deon_investor_doc_meta_box_register' );

function deon_investor_doc_meta_box_html( $post ) {
	wp_nonce_field( 'deon_investor_doc_save', 'deon_investor_doc_nonce' );
	$file     = get_post_meta( $post->ID, '_deon_document_file', true );
	$year     = get_post_meta( $post->ID, '_deon_document_year', true );
	$featured = get_post_meta( $post->ID, '_deon_invdoc_featured', true );
	?>
	<p>
		<label for="deon_document_file"><strong><?php esc_html_e( 'File URL (PDF)', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_document_file" name="deon_document_file" value="<?php echo esc_attr( $file ); ?>" style="width:100%;margin-top:4px;" placeholder="https://…/wp-content/uploads/report.pdf">
		<button type="button" class="button deon-media-pick" data-target="deon_document_file" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload File', 'deon-energy' ); ?></button>
	</p>
	<p>
		<label for="deon_document_year"><strong><?php esc_html_e( 'Year', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_document_year" name="deon_document_year" value="<?php echo esc_attr( $year ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. 2026', 'deon-energy' ); ?>">
	</p>
	<p>
		<label>
			<input type="checkbox" name="deon_invdoc_featured" value="1" <?php checked( $featured, '1' ); ?>>
			<strong><?php esc_html_e( 'Show as the Featured Document', 'deon-energy' ); ?></strong>
		</label><br>
		<span class="description"><?php esc_html_e( 'The large highlighted card at the top of the disclosures list. If several are flagged, the most recent wins; if none, the latest DRHP & Filings document is used.', 'deon-energy' ); ?></span>
	</p>
	<?php
}

function deon_investor_doc_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_investor_doc_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_investor_doc_nonce'] ) ), 'deon_investor_doc_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_document_file'] ) ) {
		update_post_meta( $post_id, '_deon_document_file', esc_url_raw( wp_unslash( $_POST['deon_document_file'] ) ) );
	}
	if ( isset( $_POST['deon_document_year'] ) ) {
		update_post_meta( $post_id, '_deon_document_year', sanitize_text_field( wp_unslash( $_POST['deon_document_year'] ) ) );
	}
	update_post_meta( $post_id, '_deon_invdoc_featured', isset( $_POST['deon_invdoc_featured'] ) ? '1' : '' );
}
add_action( 'save_post_deon_investor_doc', 'deon_investor_doc_meta_save' );

/**
 * Latest investor document's file URL for a given category slug (or any category
 * when $cat_slug is ''). Empty string when none. Used by the IPO Overview card.
 */
function deon_investor_doc_latest_url( $cat_slug = '' ) {
	$args = array(
		'post_type'      => 'deon_investor_doc',
		'posts_per_page' => 1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'fields'         => 'ids',
		'no_found_rows'  => true,
	);
	if ( $cat_slug ) {
		$args['tax_query'] = array(
			array( 'taxonomy' => 'deon_investor_category', 'field' => 'slug', 'terms' => $cat_slug ),
		);
	}
	$ids = get_posts( $args );
	return $ids ? (string) get_post_meta( $ids[0], '_deon_document_file', true ) : '';
}

/**
 * The Featured Document post: newest flagged, else newest in DRHP & Filings,
 * else newest overall. Returns a WP_Post or null.
 */
function deon_investor_doc_featured() {
	$flagged = get_posts( array(
		'post_type'      => 'deon_investor_doc',
		'posts_per_page' => 1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'meta_key'       => '_deon_invdoc_featured',
		'meta_value'     => '1',
		'no_found_rows'  => true,
	) );
	if ( $flagged ) {
		return $flagged[0];
	}

	$drhp = get_posts( array(
		'post_type'      => 'deon_investor_doc',
		'posts_per_page' => 1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'tax_query'      => array(
			array( 'taxonomy' => 'deon_investor_category', 'field' => 'slug', 'terms' => 'drhp-filings' ),
		),
		'no_found_rows'  => true,
	) );
	if ( $drhp ) {
		return $drhp[0];
	}

	$latest = get_posts( array(
		'post_type'      => 'deon_investor_doc',
		'posts_per_page' => 1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	) );
	return $latest ? $latest[0] : null;
}