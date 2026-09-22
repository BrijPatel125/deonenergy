<?php
/**
 * Per-category hero background (image / video) — term-meta counterpart to the
 * page-level "Hero Background" meta box in functions.php.
 *
 * Category archives are rendered by archive.php → template-parts/blog/hero.php →
 * template-parts/global/page-hero.php, which resolves its background through
 * `deon_get_hero_bg()`. On a term archive that helper hands off to
 * `deon_get_term_hero_bg()` below, so an editor can give a single category its
 * own hero video/image without affecting any other archive.
 *
 * Deliberately reuses the SAME meta keys and the same `.deon-media-pick` admin
 * picker as the page meta box, and runs the values through the shared
 * `deon_hero_bg_normalize()` — the front end needs no extra branch.
 *
 * ACF-free, matching the rest of the theme's admin.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Taxonomies that get the Hero Background field.
 *
 * Categories only, per client scope. Filterable so tags or a custom taxonomy can
 * be opted in later without touching the render/save code.
 *
 * @return string[]
 */
function deon_term_hero_taxonomies() {
	return (array) apply_filters( 'deon_term_hero_taxonomies', array( 'category' ) );
}

/**
 * Meta keys backing the field, and which of them hold URLs.
 *
 * @return array{keys:string[],urls:string[]}
 */
function deon_term_hero_keys() {
	$urls = array( '_deon_hero_bg_image', '_deon_hero_bg_video_file', '_deon_hero_bg_video_embed', '_deon_hero_bg_video_poster' );
	return array(
		'keys' => array_merge( array( '_deon_hero_bg_type', '_deon_hero_bg_video_source' ), $urls ),
		'urls' => $urls,
	);
}

/**
 * Resolve a term's hero background into the same normalized array shape the page
 * hero uses. Empty array => plain colour hero.
 *
 * @param int $term_id Term to read.
 * @return array
 */
function deon_get_term_hero_bg( $term_id ) {
	$term_id = (int) $term_id;
	if ( ! $term_id || ! function_exists( 'deon_hero_bg_normalize' ) ) {
		return array();
	}

	return deon_hero_bg_normalize( array(
		'type'         => get_term_meta( $term_id, '_deon_hero_bg_type', true ),
		'image'        => get_term_meta( $term_id, '_deon_hero_bg_image', true ),
		'video_source' => get_term_meta( $term_id, '_deon_hero_bg_video_source', true ),
		'video_file'   => get_term_meta( $term_id, '_deon_hero_bg_video_file', true ),
		'video_embed'  => get_term_meta( $term_id, '_deon_hero_bg_video_embed', true ),
		'poster'       => get_term_meta( $term_id, '_deon_hero_bg_video_poster', true ),
	) );
}

/**
 * Print the field controls. Shared by the add + edit forms so the two can never
 * drift; only the surrounding markup differs (the edit form is a table).
 *
 * @param int $term_id 0 on the add form.
 */
function deon_term_hero_fields_html( $term_id = 0 ) {
	$term_id = (int) $term_id;
	$get     = static function ( $key ) use ( $term_id ) {
		return $term_id ? (string) get_term_meta( $term_id, $key, true ) : '';
	};

	$type   = $get( '_deon_hero_bg_type' ) ?: 'none';
	$image  = $get( '_deon_hero_bg_image' );
	$vsrc   = $get( '_deon_hero_bg_video_source' ) ?: 'upload';
	$vfile  = $get( '_deon_hero_bg_video_file' );
	$vembed = $get( '_deon_hero_bg_video_embed' );
	$vpost  = $get( '_deon_hero_bg_video_poster' );
	?>
	<div class="deon-term-hero">
		<p style="color:#646970;margin-top:0;">
			<?php esc_html_e( 'Optional background image or video shown behind this category archive\'s hero title. Leave Type = None to keep the plain colour background.', 'deon-energy' ); ?>
		</p>

		<p>
			<label for="deon_term_hero_type"><strong><?php esc_html_e( 'Background type', 'deon-energy' ); ?></strong></label><br>
			<select id="deon_term_hero_type" name="_deon_hero_bg_type" style="margin-top:4px;">
				<option value="none"  <?php selected( $type, 'none' ); ?>><?php esc_html_e( 'None (colour background)', 'deon-energy' ); ?></option>
				<option value="image" <?php selected( $type, 'image' ); ?>><?php esc_html_e( 'Image', 'deon-energy' ); ?></option>
				<option value="video" <?php selected( $type, 'video' ); ?>><?php esc_html_e( 'Video', 'deon-energy' ); ?></option>
			</select>
		</p>

		<div class="deon-hbg-group" data-hbg="image" style="<?php echo 'image' === $type ? '' : 'display:none;'; ?>">
			<p>
				<label for="deon_term_hero_image"><strong><?php esc_html_e( 'Background image', 'deon-energy' ); ?></strong></label><br>
				<input type="url" id="deon_term_hero_image" name="_deon_hero_bg_image" value="<?php echo esc_attr( $image ); ?>" style="width:100%;max-width:420px;margin-top:4px;" placeholder="https://…">
				<button type="button" class="button deon-media-pick" data-target="deon_term_hero_image" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload Image', 'deon-energy' ); ?></button>
			</p>
		</div>

		<div class="deon-hbg-group" data-hbg="video" style="<?php echo 'video' === $type ? '' : 'display:none;'; ?>">
			<p>
				<label for="deon_term_hero_vsrc"><strong><?php esc_html_e( 'Video source', 'deon-energy' ); ?></strong></label><br>
				<select id="deon_term_hero_vsrc" name="_deon_hero_bg_video_source" style="margin-top:4px;">
					<option value="upload" <?php selected( $vsrc, 'upload' ); ?>><?php esc_html_e( 'Upload MP4', 'deon-energy' ); ?></option>
					<option value="embed"  <?php selected( $vsrc, 'embed' ); ?>><?php esc_html_e( 'YouTube / Vimeo URL', 'deon-energy' ); ?></option>
				</select>
			</p>
			<p class="deon-hbg-vsrc" data-vsrc="upload" style="<?php echo 'embed' === $vsrc ? 'display:none;' : ''; ?>">
				<label for="deon_term_hero_vfile"><strong><?php esc_html_e( 'MP4 file', 'deon-energy' ); ?></strong></label><br>
				<input type="url" id="deon_term_hero_vfile" name="_deon_hero_bg_video_file" value="<?php echo esc_attr( $vfile ); ?>" style="width:100%;max-width:420px;margin-top:4px;" placeholder="https://…mp4">
				<button type="button" class="button deon-media-pick" data-target="deon_term_hero_vfile" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload MP4', 'deon-energy' ); ?></button>
			</p>
			<p class="deon-hbg-vsrc" data-vsrc="embed" style="<?php echo 'embed' === $vsrc ? '' : 'display:none;'; ?>">
				<label for="deon_term_hero_vembed"><strong><?php esc_html_e( 'YouTube / Vimeo URL', 'deon-energy' ); ?></strong></label><br>
				<input type="url" id="deon_term_hero_vembed" name="_deon_hero_bg_video_embed" value="<?php echo esc_attr( $vembed ); ?>" style="width:100%;max-width:420px;margin-top:4px;" placeholder="https://www.youtube.com/watch?v=…">
			</p>
			<p>
				<label for="deon_term_hero_poster"><strong><?php esc_html_e( 'Poster image (optional)', 'deon-energy' ); ?></strong></label><br>
				<input type="url" id="deon_term_hero_poster" name="_deon_hero_bg_video_poster" value="<?php echo esc_attr( $vpost ); ?>" style="width:100%;max-width:420px;margin-top:4px;" placeholder="https://…">
				<button type="button" class="button deon-media-pick" data-target="deon_term_hero_poster" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload Image', 'deon-energy' ); ?></button>
			</p>
		</div>

		<script>
		( function () {
			var box = document.currentScript ? document.currentScript.closest( '.deon-term-hero' ) : null;
			if ( ! box ) { return; }
			var typeSel = box.querySelector( '#deon_term_hero_type' );
			var vsrcSel = box.querySelector( '#deon_term_hero_vsrc' );
			function sync() {
				var t = typeSel ? typeSel.value : 'none';
				box.querySelectorAll( '.deon-hbg-group' ).forEach( function ( g ) {
					g.style.display = ( g.getAttribute( 'data-hbg' ) === t ) ? '' : 'none';
				} );
				var vs = vsrcSel ? vsrcSel.value : 'upload';
				box.querySelectorAll( '.deon-hbg-vsrc' ).forEach( function ( p ) {
					p.style.display = ( p.getAttribute( 'data-vsrc' ) === vs ) ? '' : 'none';
				} );
			}
			if ( typeSel ) { typeSel.addEventListener( 'change', sync ); }
			if ( vsrcSel ) { vsrcSel.addEventListener( 'change', sync ); }
			sync();
		} )();
		</script>
	</div>
	<?php
}

/**
 * Add-term form (Posts → Categories, left column).
 */
function deon_term_hero_add_form( $taxonomy ) {
	if ( ! in_array( $taxonomy, deon_term_hero_taxonomies(), true ) ) {
		return;
	}
	wp_nonce_field( 'deon_term_hero_save', 'deon_term_hero_nonce' );
	?>
	<div class="form-field term-deon-hero-wrap">
		<label><strong><?php esc_html_e( 'Hero Background', 'deon-energy' ); ?></strong></label>
		<?php deon_term_hero_fields_html( 0 ); ?>
	</div>
	<?php
}

/**
 * Edit-term form (Posts → Categories → Edit). Table row, unlike the add form.
 */
function deon_term_hero_edit_form( $term, $taxonomy ) {
	if ( ! in_array( $taxonomy, deon_term_hero_taxonomies(), true ) ) {
		return;
	}
	wp_nonce_field( 'deon_term_hero_save', 'deon_term_hero_nonce' );
	?>
	<tr class="form-field term-deon-hero-wrap">
		<th scope="row"><label><?php esc_html_e( 'Hero Background', 'deon-energy' ); ?></label></th>
		<td><?php deon_term_hero_fields_html( $term->term_id ); ?></td>
	</tr>
	<?php
}

/**
 * Persist the fields. Empty values are deleted so clearing a field in wp-admin
 * actually returns the archive to the plain colour hero.
 *
 * @param int $term_id Term being saved.
 */
function deon_term_hero_save( $term_id ) {
	if ( ! isset( $_POST['deon_term_hero_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_term_hero_nonce'] ) ), 'deon_term_hero_save' ) ) {
		return;
	}
	$term = get_term( $term_id );
	if ( ! $term || is_wp_error( $term ) || ! in_array( $term->taxonomy, deon_term_hero_taxonomies(), true ) ) {
		return;
	}
	$tax = get_taxonomy( $term->taxonomy );
	if ( ! $tax || ! current_user_can( $tax->cap->edit_terms ) ) {
		return;
	}

	$map = deon_term_hero_keys();

	// Type "None" wipes the whole group — leaving the video source / embed rows
	// behind would silently pre-fill an old URL if the editor switches back.
	$type = isset( $_POST['_deon_hero_bg_type'] ) ? sanitize_text_field( wp_unslash( $_POST['_deon_hero_bg_type'] ) ) : '';
	if ( 'image' !== $type && 'video' !== $type ) {
		foreach ( $map['keys'] as $key ) {
			delete_term_meta( $term_id, $key );
		}
		return;
	}

	foreach ( $map['keys'] as $key ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw   = wp_unslash( $_POST[ $key ] );
		$value = in_array( $key, $map['urls'], true ) ? esc_url_raw( $raw ) : sanitize_text_field( $raw );

		if ( '' === $value ) {
			delete_term_meta( $term_id, $key );
			continue;
		}
		update_term_meta( $term_id, $key, $value );
	}
}

foreach ( deon_term_hero_taxonomies() as $deon_term_hero_tax ) {
	add_action( "{$deon_term_hero_tax}_add_form_fields", 'deon_term_hero_add_form' );
	add_action( "{$deon_term_hero_tax}_edit_form_fields", 'deon_term_hero_edit_form', 10, 2 );
	add_action( "created_{$deon_term_hero_tax}", 'deon_term_hero_save' );
	add_action( "edited_{$deon_term_hero_tax}", 'deon_term_hero_save' );
}
unset( $deon_term_hero_tax );
