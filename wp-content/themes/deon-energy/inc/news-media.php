<?php
/**
 * News & Media — registrations + helpers.
 *
 * Wired by the main thread with:  require_once DEON_DIR . '/inc/news-media.php';
 *
 * Deliberately additive: this file registers NO new post type. The News & Media
 * page reuses the existing `deon_press` CPT (media coverage) and the existing
 * `deon_document` CPT + `deon_document_type` taxonomy (downloads). All it adds is
 *
 *   1. three extra `deon_document_type` terms — brand-kit / brand-assets / media-kit
 *   2. one extra meta box on `deon_document` (description, icon, optional external
 *      link + CTA label) so a document can render as a designed Brand Asset card
 *   3. small query/format helpers used by `page-news-media.php`
 *
 * ACF-free, native `add_meta_box` pattern, matching every other CPT in the theme
 * except the Homepage Hero.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* -------------------------------------------------------------------------
 * 1. Document-type terms for the News & Media page.
 * ---------------------------------------------------------------------- */

/**
 * Seed the News & Media document-type terms. Idempotent — safe on every load.
 * Runs at priority 30 so it lands after deon_register_document_cpt() (10) and
 * deon_seed_document_types() (20) in functions.php.
 */
function deon_news_seed_document_types() {
	if ( ! taxonomy_exists( 'deon_document_type' ) ) {
		return;
	}
	// Run the seed loop ONCE, ever. Without this flag, term_exists() only
	// checks "does it exist right now" — so deleting a document type in
	// wp-admin got silently undone on the very next page load (this hook
	// fires on every `init`, not just at setup). The flag makes deletions
	// permanent.
	if ( get_option( 'deon_news_document_types_seeded' ) ) {
		return;
	}

	$terms = array(
		'brand-kit'     => array(
			__( 'Brand Kit', 'deon-energy' ),
			__( 'The single master brand kit archive offered as the primary download on News & Media.', 'deon-energy' ),
		),
		'brand-assets'  => array(
			__( 'Brand Assets', 'deon-energy' ),
			__( 'Logos, colour palettes, guidelines and press imagery. Each document here renders as a Brand Assets card.', 'deon-energy' ),
		),
		'media-kit'     => array(
			__( 'Media Kit', 'deon-energy' ),
			__( 'Fact sheets, company boilerplate and press-ready material for journalists.', 'deon-energy' ),
		),
		'technical-papers' => array(
			__( 'Technical Papers', 'deon-energy' ),
			__( 'Downloadable whitepapers, engineering reports and technical studies shown on the Technical Papers page.', 'deon-energy' ),
		),
	);

	foreach ( $terms as $slug => $data ) {
		if ( ! term_exists( $slug, 'deon_document_type' ) ) {
			wp_insert_term( $data[0], 'deon_document_type', array( 'slug' => $slug, 'description' => $data[1] ) );
		}
	}

	update_option( 'deon_news_document_types_seeded', 1 );
}
add_action( 'init', 'deon_news_seed_document_types', 30 );

/* -------------------------------------------------------------------------
 * 2. Extra meta box on deon_document — presentation fields for asset cards.
 * ---------------------------------------------------------------------- */

/**
 * Icon choices for a Brand Asset card. Keys are stored in meta; the template
 * maps them to inline SVGs (no external icon font / asset request).
 *
 * @return array<string,string>
 */
function deon_news_asset_icons() {
	return array(
		'box'      => __( 'Box / package (identity, logo pack)', 'deon-energy' ),
		'image'    => __( 'Image (photography, press imagery)', 'deon-energy' ),
		'document' => __( 'Document (guidelines, fact sheet)', 'deon-energy' ),
		'palette'  => __( 'Palette (colour, typography)', 'deon-energy' ),
	);
}

function deon_news_asset_meta_box_register() {
	add_meta_box(
		'deon_news_asset_card',
		__( 'News & Media card display', 'deon-energy' ),
		'deon_news_asset_meta_box_html',
		'deon_document',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'deon_news_asset_meta_box_register' );

function deon_news_asset_meta_box_html( $post ) {
	wp_nonce_field( 'deon_news_asset_save', 'deon_news_asset_nonce' );

	$desc  = get_post_meta( $post->ID, '_deon_news_asset_desc', true );
	$icon  = get_post_meta( $post->ID, '_deon_news_asset_icon', true );
	$label = get_post_meta( $post->ID, '_deon_news_asset_cta', true );
	$link  = get_post_meta( $post->ID, '_deon_news_asset_link', true );
	?>
	<p style="margin-top:0;color:#666;">
		<?php esc_html_e( 'Only used when this document is filed under the "Brand Assets" document type — it becomes a card on the News & Media page.', 'deon-energy' ); ?>
	</p>

	<p>
		<label for="deon_news_asset_desc"><strong><?php esc_html_e( 'Short description', 'deon-energy' ); ?></strong></label><br>
		<textarea id="deon_news_asset_desc" name="deon_news_asset_desc" rows="2" style="width:100%;margin-top:4px;"
		          placeholder="<?php esc_attr_e( 'e.g. Master logo files in CMYK and RGB versions for all background types.', 'deon-energy' ); ?>"><?php echo esc_textarea( $desc ); ?></textarea>
	</p>

	<p>
		<label for="deon_news_asset_icon"><strong><?php esc_html_e( 'Card icon', 'deon-energy' ); ?></strong></label><br>
		<select id="deon_news_asset_icon" name="deon_news_asset_icon" style="margin-top:4px;">
			<?php foreach ( deon_news_asset_icons() as $key => $label_text ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>><?php echo esc_html( $label_text ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<p>
		<label for="deon_news_asset_cta"><strong><?php esc_html_e( 'Card link label', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_news_asset_cta" name="deon_news_asset_cta" value="<?php echo esc_attr( $label ); ?>"
		       style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. View Assets — defaults to "Download"', 'deon-energy' ); ?>">
	</p>

	<p>
		<label for="deon_news_asset_link"><strong><?php esc_html_e( 'Card link URL (optional)', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_news_asset_link" name="deon_news_asset_link" value="<?php echo esc_attr( $link ); ?>"
		       style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'Leave empty to link the uploaded File URL above', 'deon-energy' ); ?>">
	</p>
	<?php
}

function deon_news_asset_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_news_asset_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_news_asset_nonce'] ) ), 'deon_news_asset_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['deon_news_asset_desc'] ) ) {
		update_post_meta( $post_id, '_deon_news_asset_desc', sanitize_textarea_field( wp_unslash( $_POST['deon_news_asset_desc'] ) ) );
	}
	if ( isset( $_POST['deon_news_asset_icon'] ) ) {
		$icon = sanitize_key( wp_unslash( $_POST['deon_news_asset_icon'] ) );
		update_post_meta( $post_id, '_deon_news_asset_icon', array_key_exists( $icon, deon_news_asset_icons() ) ? $icon : 'document' );
	}
	if ( isset( $_POST['deon_news_asset_cta'] ) ) {
		update_post_meta( $post_id, '_deon_news_asset_cta', sanitize_text_field( wp_unslash( $_POST['deon_news_asset_cta'] ) ) );
	}
	if ( isset( $_POST['deon_news_asset_link'] ) ) {
		update_post_meta( $post_id, '_deon_news_asset_link', esc_url_raw( wp_unslash( $_POST['deon_news_asset_link'] ) ) );
	}
}
add_action( 'save_post_deon_document', 'deon_news_asset_meta_save' );

/* -------------------------------------------------------------------------
 * 3. Helpers used by page-news-media.php.
 * ---------------------------------------------------------------------- */

/**
 * Documents filed under a given deon_document_type term.
 *
 * @param string $term_slug deon_document_type slug.
 * @param int    $count     Max documents (-1 for all).
 * @return WP_Query|null    Null when the CPT/taxonomy is not registered.
 */
function deon_news_documents( $term_slug, $count = -1 ) {
	if ( ! post_type_exists( 'deon_document' ) || ! taxonomy_exists( 'deon_document_type' ) ) {
		return null;
	}

	return new WP_Query( array(
		'post_type'      => 'deon_document',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $count,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'deon_document_type',
				'field'    => 'slug',
				'terms'    => $term_slug,
			),
		),
	) );
}

/**
 * The single master brand-kit download URL, or '' when nothing is uploaded.
 *
 * @return string
 */
function deon_news_brand_kit_url() {
	if ( function_exists( 'deon_document_latest_url' ) ) {
		return (string) deon_document_latest_url( 'brand-kit' );
	}
	return '';
}

/**
 * Press-office email address.
 *
 * Defaults to the address shown on the approved News & Media design
 * (media@deonenergy.in). Filterable so the client can swap in a different press
 * inbox without touching a template.
 *
 * @return string
 */
function deon_news_press_email() {
	return (string) apply_filters( 'deon_news_press_email', 'media@deonenergy.in' );
}

/**
 * Whether a published Press Coverage archive page exists to link "View all" to.
 *
 * @return string Permalink, or '' when the page has not been created.
 */
function deon_news_press_archive_url() {
	$page = get_page_by_path( 'press-coverage' );
	if ( $page && 'publish' === $page->post_status ) {
		return (string) get_permalink( $page );
	}
	return '';
}

/* -------------------------------------------------------------------------
 * 4. Technical Papers — extra presentation fields on deon_document.
 *
 * The Technical Papers page (page-technical-papers.php, slug `technical-papers`)
 * reuses the deon_document CPT filed under the `technical-papers` document type
 * (seeded above). deon_document only supports title + page-attributes, so the
 * abstract, thumbnail and category eyebrow that the design needs are stored as
 * post meta here rather than as core editor content / featured image. File URL +
 * year come from the existing "Document Details" meta box; PDF size auto-derives
 * via deon_document_file_meta().
 * ---------------------------------------------------------------------- */

function deon_tp_meta_box_register() {
	add_meta_box(
		'deon_tp_paper',
		__( 'Technical Paper display', 'deon-energy' ),
		'deon_tp_meta_box_html',
		'deon_document',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'deon_tp_meta_box_register' );

function deon_tp_meta_box_html( $post ) {
	wp_nonce_field( 'deon_tp_save', 'deon_tp_nonce' );

	$abstract = get_post_meta( $post->ID, '_deon_tp_abstract', true );
	$category = get_post_meta( $post->ID, '_deon_tp_category', true );
	$image    = get_post_meta( $post->ID, '_deon_tp_image', true );
	$summary  = get_post_meta( $post->ID, '_deon_tp_summary_url', true );
	?>
	<p style="margin-top:0;color:#666;">
		<?php esc_html_e( 'Only used when this document is filed under the "Technical Papers" document type — it becomes a paper on the Technical Papers page.', 'deon-energy' ); ?>
	</p>

	<p>
		<label for="deon_tp_category"><strong><?php esc_html_e( 'Category / paper type', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_tp_category" name="deon_tp_category" value="<?php echo esc_attr( $category ); ?>"
		       style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. Technical Guide, Engineering Report — also drives the sidebar filter', 'deon-energy' ); ?>">
	</p>

	<p>
		<label for="deon_tp_abstract"><strong><?php esc_html_e( 'Abstract', 'deon-energy' ); ?></strong></label><br>
		<textarea id="deon_tp_abstract" name="deon_tp_abstract" rows="4" style="width:100%;margin-top:4px;"
		          placeholder="<?php esc_attr_e( 'Short summary shown under the paper title.', 'deon-energy' ); ?>"><?php echo esc_textarea( $abstract ); ?></textarea>
	</p>

	<p>
		<label for="deon_tp_image"><strong><?php esc_html_e( 'Cover image URL (optional)', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_tp_image" name="deon_tp_image" value="<?php echo esc_attr( $image ); ?>"
		       style="width:100%;margin-top:4px;" placeholder="https://…/wp-content/uploads/paper-cover.jpg">
		<button type="button" class="button deon-media-pick" data-target="deon_tp_image" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload Image', 'deon-energy' ); ?></button>
	</p>

	<p>
		<label for="deon_tp_summary_url"><strong><?php esc_html_e( 'Read-summary URL (optional)', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_tp_summary_url" name="deon_tp_summary_url" value="<?php echo esc_attr( $summary ); ?>"
		       style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'Leave empty to hide the "Read Summary" link', 'deon-energy' ); ?>">
	</p>
	<?php
}

function deon_tp_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_tp_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_tp_nonce'] ) ), 'deon_tp_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['deon_tp_abstract'] ) ) {
		update_post_meta( $post_id, '_deon_tp_abstract', sanitize_textarea_field( wp_unslash( $_POST['deon_tp_abstract'] ) ) );
	}
	if ( isset( $_POST['deon_tp_category'] ) ) {
		update_post_meta( $post_id, '_deon_tp_category', sanitize_text_field( wp_unslash( $_POST['deon_tp_category'] ) ) );
	}
	if ( isset( $_POST['deon_tp_image'] ) ) {
		update_post_meta( $post_id, '_deon_tp_image', esc_url_raw( wp_unslash( $_POST['deon_tp_image'] ) ) );
	}
	if ( isset( $_POST['deon_tp_summary_url'] ) ) {
		update_post_meta( $post_id, '_deon_tp_summary_url', esc_url_raw( wp_unslash( $_POST['deon_tp_summary_url'] ) ) );
	}
}
add_action( 'save_post_deon_document', 'deon_tp_meta_save' );

/**
 * Published technical papers (deon_document filed under `technical-papers`).
 *
 * @param int $count Max papers (-1 for all).
 * @return WP_Query|null Null when the CPT/taxonomy is not registered.
 */
function deon_tp_papers( $count = -1 ) {
	if ( function_exists( 'deon_news_documents' ) ) {
		return deon_news_documents( 'technical-papers', $count );
	}
	return null;
}

/**
 * Distinct, non-empty paper categories across all published technical papers, in
 * first-seen (menu_order) order. Powers the sidebar filter — an empty array means
 * no categories are set, so the template hides the filter.
 *
 * @return string[]
 */
function deon_tp_category_list() {
	$out = array();
	$q   = deon_tp_papers( -1 );
	if ( ! ( $q instanceof WP_Query ) ) {
		return $out;
	}
	foreach ( $q->posts as $paper ) {
		$cat = trim( (string) get_post_meta( $paper->ID, '_deon_tp_category', true ) );
		if ( '' !== $cat && ! in_array( $cat, $out, true ) ) {
			$out[] = $cat;
		}
	}
	return $out;
}