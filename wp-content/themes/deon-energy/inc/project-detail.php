<?php
/**
 * Project detail — admin + render helpers.
 *
 * Everything the single project page (single-deon_project.php) needs beyond the
 * base CPT (registered in functions.php): a nullable hero image/video, a single
 * story video, an embedded map, a repeatable "Technical Milestones" list, and
 * the "Related Infrastructure" query. All ACF-free — plain post meta, reusing
 * the shared `.deon-media-pick` admin picker already enqueued for deon_project.
 *
 * Meta keys:
 *   _deon_hero_bg_*        Hero background (shared with pages; see functions.php)
 *   _deon_video_url        Story video — MP4 / YouTube / Vimeo
 *   _deon_video_caption    Overlay caption for the story video
 *   _deon_video_poster     Poster image URL for the story video
 *   _deon_map_embed        Google Maps embed/share URL (iframe src is derived)
 *   _deon_milestones       Serialized array of [ 'title' => …, 'desc' => … ]
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* -------------------------------------------------------------------------
 * 1. Hero Background — reuse the page meta box + save on deon_project too.
 *    deon_hero_bg_meta_box_html() / deon_hero_bg_meta_save() live in
 *    functions.php and are post-type agnostic (nonce-guarded).
 * ---------------------------------------------------------------------- */
function deon_project_hero_bg_register() {
	if ( ! function_exists( 'deon_hero_bg_meta_box_html' ) ) {
		return;
	}
	add_meta_box(
		'deon_hero_bg',
		__( 'Hero Background (image / video)', 'deon-energy' ),
		'deon_hero_bg_meta_box_html',
		'deon_project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_deon_project', 'deon_project_hero_bg_register' );

add_action( 'save_post_deon_project', 'deon_hero_bg_meta_save' );

/* -------------------------------------------------------------------------
 * 2. "Project Media & Story" meta box — video, map, milestones.
 * ---------------------------------------------------------------------- */
function deon_project_media_meta_box_register() {
	add_meta_box(
		'deon_project_media',
		__( 'Project Media & Story', 'deon-energy' ),
		'deon_project_media_meta_box_html',
		'deon_project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_deon_project', 'deon_project_media_meta_box_register' );

function deon_project_media_meta_box_html( $post ) {
	wp_nonce_field( 'deon_project_media_save', 'deon_project_media_nonce' );

	$video   = get_post_meta( $post->ID, '_deon_video_url', true );
	$caption = get_post_meta( $post->ID, '_deon_video_caption', true );
	$poster  = get_post_meta( $post->ID, '_deon_video_poster', true );
	$map     = get_post_meta( $post->ID, '_deon_map_embed', true );
	$miles   = deon_project_milestones( $post->ID );
	?>
	<style>
		.deon-pm h4{margin:24px 0 8px;font-size:13px;text-transform:uppercase;letter-spacing:.4px;color:#1d2327}
		.deon-pm .deon-pm-hint{color:#646970;margin:0 0 8px}
		.deon-pm input[type=url],.deon-pm input[type=text],.deon-pm textarea{width:100%}
		.deon-pm .deon-pm-row{margin:0 0 12px}
		.deon-ms-list{margin:0;padding:0;list-style:none}
		.deon-ms-item{display:flex;gap:10px;align-items:flex-start;padding:12px;margin:0 0 10px;border:1px solid #dcdcde;background:#f6f7f7;border-radius:4px}
		.deon-ms-item .deon-ms-num{font-weight:700;color:#e8930a;min-width:26px;padding-top:6px}
		.deon-ms-item .deon-ms-fields{flex:1}
		.deon-ms-item .deon-ms-fields input{margin-bottom:6px}
		.deon-ms-remove{border:none;background:#d63638;color:#fff;border-radius:3px;cursor:pointer;padding:4px 9px;height:30px}
	</style>
	<div class="deon-pm">

		<h4><?php esc_html_e( 'Story Video', 'deon-energy' ); ?></h4>
		<p class="deon-pm-hint"><?php esc_html_e( 'One cinematic video for the detail page. Paste a YouTube / Vimeo link, or upload an MP4. Leave blank to hide the video section.', 'deon-energy' ); ?></p>
		<div class="deon-pm-row">
			<input type="url" id="_deon_video_url" name="_deon_video_url" value="<?php echo esc_attr( $video ); ?>" placeholder="https://www.youtube.com/watch?v=…  or  https://…/plant.mp4">
			<button type="button" class="button deon-media-pick" data-target="_deon_video_url" style="margin-top:6px;"><?php esc_html_e( 'Upload MP4', 'deon-energy' ); ?></button>
		</div>
		<div class="deon-pm-row">
			<label for="_deon_video_caption"><strong><?php esc_html_e( 'Caption (optional)', 'deon-energy' ); ?></strong></label>
			<input type="text" id="_deon_video_caption" name="_deon_video_caption" value="<?php echo esc_attr( $caption ); ?>" placeholder="<?php esc_attr_e( 'e.g. The Art of Infrastructure', 'deon-energy' ); ?>">
		</div>
		<div class="deon-pm-row">
			<label for="_deon_video_poster"><strong><?php esc_html_e( 'Poster image (optional — used for MP4)', 'deon-energy' ); ?></strong></label>
			<input type="url" id="_deon_video_poster" name="_deon_video_poster" value="<?php echo esc_attr( $poster ); ?>" placeholder="https://…">
			<button type="button" class="button deon-media-pick" data-target="_deon_video_poster" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload Image', 'deon-energy' ); ?></button>
		</div>

		<h4><?php esc_html_e( 'Location Map', 'deon-energy' ); ?></h4>
		<p class="deon-pm-hint">
			<?php esc_html_e( 'Paste a Google Maps link or embed URL. Open Google Maps → find the site → Share → "Embed a map" → copy the src, or just paste the normal share link. Leave blank to hide the map.', 'deon-energy' ); ?>
		</p>
		<div class="deon-pm-row">
			<input type="url" id="_deon_map_embed" name="_deon_map_embed" value="<?php echo esc_attr( $map ); ?>" placeholder="https://www.google.com/maps/…">
		</div>

		<h4><?php esc_html_e( 'Technical Milestones', 'deon-energy' ); ?></h4>
		<p class="deon-pm-hint"><?php esc_html_e( 'Numbered build/commissioning steps (01, 02, 03…). Add a title and a one-line description for each. Leave empty to hide the section.', 'deon-energy' ); ?></p>
		<ul class="deon-ms-list" data-ms-list>
			<?php
			$deon_i = 0;
			foreach ( $miles as $m ) :
				$deon_i++;
				?>
				<li class="deon-ms-item">
					<span class="deon-ms-num"><?php echo esc_html( str_pad( $deon_i, 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="deon-ms-fields">
						<input type="text" name="deon_ms_title[]" value="<?php echo esc_attr( $m['title'] ); ?>" placeholder="<?php esc_attr_e( 'Milestone title (e.g. Grid Interconnection)', 'deon-energy' ); ?>">
						<input type="text" name="deon_ms_desc[]" value="<?php echo esc_attr( $m['desc'] ); ?>" placeholder="<?php esc_attr_e( 'Short description', 'deon-energy' ); ?>">
					</span>
					<button type="button" class="deon-ms-remove" aria-label="<?php esc_attr_e( 'Remove', 'deon-energy' ); ?>">&times;</button>
				</li>
			<?php endforeach; ?>
		</ul>
		<button type="button" class="button" data-ms-add><?php esc_html_e( '+ Add Milestone', 'deon-energy' ); ?></button>

		<template data-ms-template>
			<li class="deon-ms-item">
				<span class="deon-ms-num">00</span>
				<span class="deon-ms-fields">
					<input type="text" name="deon_ms_title[]" value="" placeholder="<?php esc_attr_e( 'Milestone title (e.g. Grid Interconnection)', 'deon-energy' ); ?>">
					<input type="text" name="deon_ms_desc[]" value="" placeholder="<?php esc_attr_e( 'Short description', 'deon-energy' ); ?>">
				</span>
				<button type="button" class="deon-ms-remove" aria-label="<?php esc_attr_e( 'Remove', 'deon-energy' ); ?>">&times;</button>
			</li>
		</template>
	</div>

	<script>
	( function () {
		var box = document.getElementById( 'deon_project_media' );
		if ( ! box ) { return; }
		var list = box.querySelector( '[data-ms-list]' );
		var tpl  = box.querySelector( '[data-ms-template]' );
		function renumber() {
			list.querySelectorAll( '.deon-ms-num' ).forEach( function ( n, i ) {
				n.textContent = ( '0' + ( i + 1 ) ).slice( -2 );
			} );
		}
		box.querySelector( '[data-ms-add]' ).addEventListener( 'click', function () {
			list.appendChild( tpl.content.cloneNode( true ) );
			renumber();
		} );
		list.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( '.deon-ms-remove' ) ) {
				e.target.closest( '.deon-ms-item' ).remove();
				renumber();
			}
		} );
	} )();
	</script>
	<?php
}

function deon_project_media_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_project_media_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_project_media_nonce'] ) ), 'deon_project_media_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// URL / text singles.
	if ( isset( $_POST['_deon_video_url'] ) ) {
		update_post_meta( $post_id, '_deon_video_url', esc_url_raw( wp_unslash( $_POST['_deon_video_url'] ) ) );
	}
	if ( isset( $_POST['_deon_video_caption'] ) ) {
		update_post_meta( $post_id, '_deon_video_caption', sanitize_text_field( wp_unslash( $_POST['_deon_video_caption'] ) ) );
	}
	if ( isset( $_POST['_deon_video_poster'] ) ) {
		update_post_meta( $post_id, '_deon_video_poster', esc_url_raw( wp_unslash( $_POST['_deon_video_poster'] ) ) );
	}
	if ( isset( $_POST['_deon_map_embed'] ) ) {
		update_post_meta( $post_id, '_deon_map_embed', esc_url_raw( wp_unslash( $_POST['_deon_map_embed'] ) ) );
	}

	// Milestones — zip parallel title/desc arrays, drop rows with no title.
	$titles = isset( $_POST['deon_ms_title'] ) ? (array) wp_unslash( $_POST['deon_ms_title'] ) : array();
	$descs  = isset( $_POST['deon_ms_desc'] ) ? (array) wp_unslash( $_POST['deon_ms_desc'] ) : array();
	$rows   = array();
	foreach ( $titles as $i => $title ) {
		$title = sanitize_text_field( $title );
		$desc  = isset( $descs[ $i ] ) ? sanitize_text_field( $descs[ $i ] ) : '';
		if ( '' === $title && '' === $desc ) {
			continue;
		}
		$rows[] = array( 'title' => $title, 'desc' => $desc );
	}
	if ( $rows ) {
		update_post_meta( $post_id, '_deon_milestones', $rows );
	} else {
		delete_post_meta( $post_id, '_deon_milestones' );
	}
}
add_action( 'save_post_deon_project', 'deon_project_media_meta_save' );

/* -------------------------------------------------------------------------
 * 3. Render helpers.
 * ---------------------------------------------------------------------- */

/**
 * Milestones for a project as an array of [ 'title' => …, 'desc' => … ].
 *
 * @param int $post_id Project ID.
 * @return array<int,array{title:string,desc:string}>
 */
function deon_project_milestones( $post_id ) {
	$rows = get_post_meta( $post_id, '_deon_milestones', true );
	if ( ! is_array( $rows ) ) {
		return array();
	}
	$out = array();
	foreach ( $rows as $r ) {
		$out[] = array(
			'title' => isset( $r['title'] ) ? (string) $r['title'] : '',
			'desc'  => isset( $r['desc'] ) ? (string) $r['desc'] : '',
		);
	}
	return $out;
}

/**
 * The story video URL for a project (empty string when unset).
 *
 * @param int $post_id Project ID.
 * @return string
 */
function deon_project_video_url( $post_id ) {
	return (string) get_post_meta( $post_id, '_deon_video_url', true );
}

/**
 * Normalize a pasted Google Maps URL into an iframe-safe embed src.
 *
 * Accepts an already-embed URL (contains /maps/embed), a full <iframe> paste
 * (extracts the src), or a plain share/place link (wrapped with ?output=embed).
 * Returns '' when there is nothing usable.
 *
 * @param int $post_id Project ID.
 * @return string
 */
function deon_project_map_embed_src( $post_id ) {
	$raw = trim( (string) get_post_meta( $post_id, '_deon_map_embed', true ) );
	if ( '' === $raw ) {
		return '';
	}
	// Full <iframe …> paste — pull the src out.
	if ( preg_match( '/src\s*=\s*["\']([^"\']+)["\']/i', $raw, $m ) ) {
		$raw = $m[1];
	}
	if ( false === strpos( $raw, 'google.' ) && false === strpos( $raw, 'goo.gl' ) ) {
		return '';
	}
	// Already an embed URL — use as-is.
	if ( false !== strpos( $raw, '/maps/embed' ) ) {
		return esc_url_raw( $raw );
	}
	// Plain share/place link — Google renders a basic embed with output=embed.
	$sep = ( false === strpos( $raw, '?' ) ) ? '?' : '&';
	return esc_url_raw( $raw . $sep . 'output=embed' );
}

/**
 * Related projects for the given project: same type first, then most recent
 * others, de-duplicated, excluding the current post.
 *
 * @param int $post_id Current project ID.
 * @param int $limit   Max cards to return.
 * @return int[] Ordered project IDs.
 */
function deon_related_projects( $post_id, $limit = 6 ) {
	$post_id = (int) $post_id;
	$ids     = array();

	$terms = get_the_terms( $post_id, 'deon_project_type' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$same = get_posts( array(
			'post_type'      => 'deon_project',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'post__not_in'   => array( $post_id ),
			'fields'         => 'ids',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'      => array( array(
				'taxonomy' => 'deon_project_type',
				'field'    => 'term_id',
				'terms'    => wp_list_pluck( $terms, 'term_id' ),
			) ),
		) );
		$ids = array_map( 'intval', $same );
	}

	// Top up with other recent projects if the same-type set is thin.
	if ( count( $ids ) < $limit ) {
		$fill = get_posts( array(
			'post_type'      => 'deon_project',
			'post_status'    => 'publish',
			'posts_per_page' => $limit - count( $ids ),
			'post__not_in'   => array_merge( array( $post_id ), $ids ),
			'fields'         => 'ids',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );
		$ids = array_merge( $ids, array_map( 'intval', $fill ) );
	}

	return array_slice( $ids, 0, $limit );
}

/* -------------------------------------------------------------------------
 * 4. Admin list columns — Photo / Type / Capacity / Location.
 * ---------------------------------------------------------------------- */
function deon_project_admin_columns( $cols ) {
	$new = array();
	foreach ( $cols as $key => $label ) {
		if ( 'title' === $key ) {
			$new['deon_thumb'] = __( 'Photo', 'deon-energy' );
		}
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['deon_type']     = __( 'Type', 'deon-energy' );
			$new['deon_capacity'] = __( 'Capacity', 'deon-energy' );
			$new['deon_location'] = __( 'Location', 'deon-energy' );
		}
	}
	return $new;
}
add_filter( 'manage_deon_project_posts_columns', 'deon_project_admin_columns' );

function deon_project_admin_column_content( $col, $post_id ) {
	switch ( $col ) {
		case 'deon_thumb':
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'width:60px;height:60px;object-fit:cover;border-radius:3px;' ) );
			} else {
				echo '<span style="color:#a7aaad;">—</span>';
			}
			break;
		case 'deon_type':
			$terms = get_the_terms( $post_id, 'deon_project_type' );
			echo ( $terms && ! is_wp_error( $terms ) ) ? esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) ) : '<span style="color:#a7aaad;">—</span>';
			break;
		case 'deon_capacity':
			$v = get_post_meta( $post_id, '_deon_capacity', true );
			echo $v ? esc_html( $v ) : '<span style="color:#a7aaad;">—</span>';
			break;
		case 'deon_location':
			$v = get_post_meta( $post_id, '_deon_location', true );
			echo $v ? esc_html( $v ) : '<span style="color:#a7aaad;">—</span>';
			break;
	}
}
add_action( 'manage_deon_project_posts_custom_column', 'deon_project_admin_column_content', 10, 2 );