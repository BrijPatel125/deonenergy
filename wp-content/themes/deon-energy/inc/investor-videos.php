<?php
/**
 * Investor Videos — a client-managed video gallery for the Investor Relations
 * page (mirrors the old deonenergy.in /investors "SEBI AV" clips).
 *
 * ACF-free meta-box pattern, same as deon_investor_doc. Each video accepts a
 * YouTube/Vimeo URL *or* an uploaded MP4 (the template picks whichever is set),
 * plus an optional poster image (featured image). YouTube/Vimeo posters are
 * auto-derived when none is set, so a bare URL still renders a real thumbnail.
 *
 * The Videos section renders NOTHING until at least one video is published —
 * graceful hide, no placeholder (same convention as milestones).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the Investor Videos CPT.
 */
function deon_register_investor_video_cpt() {
	register_post_type( 'deon_investor_video', array(
		'labels' => array(
			'name'          => __( 'Investor Videos', 'deon-energy' ),
			'singular_name' => __( 'Investor Video', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Video', 'deon-energy' ),
			'edit_item'     => __( 'Edit Video', 'deon-energy' ),
			'new_item'      => __( 'New Video', 'deon-energy' ),
			'view_item'     => __( 'View Video', 'deon-energy' ),
			'search_items'  => __( 'Search Videos', 'deon-energy' ),
			'not_found'     => __( 'No investor videos yet', 'deon-energy' ),
			'all_items'     => __( 'All Videos', 'deon-energy' ),
			'menu_name'     => __( 'Investor Videos', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_position' => 27,
		'menu_icon'     => 'dashicons-video-alt3',
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
		'has_archive'   => false,
		'rewrite'       => false,
	) );
}
add_action( 'init', 'deon_register_investor_video_cpt' );

/**
 * Meta box: the video source (URL or uploaded file) + a short caption.
 */
function deon_investor_video_meta_box_register() {
	add_meta_box(
		'deon_investor_video_details',
		__( 'Video Details', 'deon-energy' ),
		'deon_investor_video_meta_box_html',
		'deon_investor_video',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'deon_investor_video_meta_box_register' );

function deon_investor_video_meta_box_html( $post ) {
	wp_nonce_field( 'deon_investor_video_save', 'deon_investor_video_nonce' );
	$url     = get_post_meta( $post->ID, '_deon_video_url', true );
	$caption = get_post_meta( $post->ID, '_deon_video_caption', true );
	?>
	<p>
		<label for="deon_video_url"><strong><?php esc_html_e( 'Video URL or file', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_video_url" name="deon_video_url" value="<?php echo esc_attr( $url ); ?>" style="width:100%;margin-top:4px;" placeholder="https://www.youtube.com/watch?v=… · https://vimeo.com/… · …/uploads/clip.mp4">
		<button type="button" class="button deon-media-pick" data-target="deon_video_url" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload File', 'deon-energy' ); ?></button>
		<br><span class="description"><?php esc_html_e( 'Paste a YouTube or Vimeo link, or upload/select an MP4. Whichever you set is used.', 'deon-energy' ); ?></span>
	</p>
	<p>
		<label for="deon_video_caption"><strong><?php esc_html_e( 'Caption (optional)', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_video_caption" name="deon_video_caption" value="<?php echo esc_attr( $caption ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. SEBI Audio-Visual — English', 'deon-energy' ); ?>">
	</p>
	<p class="description">
		<?php esc_html_e( 'Set a Featured Image as the poster/thumbnail. For YouTube & Vimeo a thumbnail is fetched automatically when none is set. Order videos with Page Attributes → Order.', 'deon-energy' ); ?>
	</p>
	<?php
}

function deon_investor_video_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_investor_video_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_investor_video_nonce'] ) ), 'deon_investor_video_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_video_url'] ) ) {
		update_post_meta( $post_id, '_deon_video_url', esc_url_raw( wp_unslash( $_POST['deon_video_url'] ) ) );
	}
	if ( isset( $_POST['deon_video_caption'] ) ) {
		update_post_meta( $post_id, '_deon_video_caption', sanitize_text_field( wp_unslash( $_POST['deon_video_caption'] ) ) );
	}
}
add_action( 'save_post_deon_investor_video', 'deon_investor_video_meta_save' );

/**
 * Parse a video URL into playback data.
 *
 * @return array{type:string,embed:string,thumb:string} type = youtube|vimeo|file|'';
 *         embed = iframe src (YT/Vimeo) or the file URL; thumb = auto poster ('' when unknown).
 */
function deon_video_source( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return array( 'type' => '', 'embed' => '', 'thumb' => '' );
	}

	// YouTube — watch, youtu.be, shorts, embed.
	if ( preg_match( '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m ) ) {
		$id = $m[1];
		return array(
			'type'  => 'youtube',
			'embed' => 'https://www.youtube-nocookie.com/embed/' . $id . '?rel=0&autoplay=1',
			'thumb' => 'https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg',
		);
	}

	// Vimeo — vimeo.com/ID or player.vimeo.com/video/ID.
	if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~', $url, $m ) ) {
		$id    = $m[1];
		$thumb = '';
		$resp  = wp_remote_get( 'https://vimeo.com/api/v2/video/' . $id . '.json', array( 'timeout' => 4 ) );
		if ( ! is_wp_error( $resp ) && 200 === wp_remote_retrieve_response_code( $resp ) ) {
			$data = json_decode( wp_remote_retrieve_body( $resp ), true );
			if ( ! empty( $data[0]['thumbnail_large'] ) ) {
				$thumb = $data[0]['thumbnail_large'];
			}
		}
		return array(
			'type'  => 'vimeo',
			'embed' => 'https://player.vimeo.com/video/' . $id . '?autoplay=1',
			'thumb' => $thumb,
		);
	}

	// Anything else — treat as a direct file (MP4, etc.).
	return array( 'type' => 'file', 'embed' => $url, 'thumb' => '' );
}

/**
 * Published investor videos as render-ready rows. Empty when none exist.
 *
 * @return array<int,array{title:string,caption:string,poster:string,type:string,embed:string}>
 */
function deon_get_investor_videos() {
	$posts = get_posts( array(
		'post_type'      => 'deon_investor_video',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'order'          => 'ASC',
	) );

	$rows = array();
	foreach ( $posts as $p ) {
		$src = deon_video_source( get_post_meta( $p->ID, '_deon_video_url', true ) );
		if ( '' === $src['embed'] ) {
			continue; // no source set — skip.
		}
		$poster = has_post_thumbnail( $p ) ? get_the_post_thumbnail_url( $p, 'large' ) : $src['thumb'];
		$rows[] = array(
			'title'   => get_the_title( $p ),
			'caption' => (string) get_post_meta( $p->ID, '_deon_video_caption', true ),
			'poster'  => (string) $poster,
			'type'    => $src['type'],
			'embed'   => $src['embed'],
		);
	}
	return $rows;
}
