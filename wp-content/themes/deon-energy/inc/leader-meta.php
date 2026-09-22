<?php
/**
 * Leadership — "Casual Photo" meta field for the deon_leader CPT.
 *
 * Registers a second, self-contained meta box on the Leader edit screen so the
 * client can attach the informal / candid photo that the 3D flip card reveals
 * on hover (desktop) or tap (touch). ACF-free — native add_meta_box plus the
 * theme's existing `.deon-media-pick` media-library picker, which functions.php
 * already enqueues for the `deon_leader` post type.
 *
 * Lives in its own file (not functions.php) per the redesign spec. The main
 * thread must wire it up:
 *
 *     require_once DEON_DIR . '/inc/leader-meta.php';
 *
 * Until that require exists the templates still work — the flip card simply
 * always falls back to its branded info back-face.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Meta key holding the casual photo URL.
 */
if ( ! defined( 'DEON_LEADER_CASUAL_KEY' ) ) {
	define( 'DEON_LEADER_CASUAL_KEY', '_deon_leader_casual' );
}

/**
 * Register the Casual Photo meta box.
 *
 * NOTE: the Flip / Casual Photo field now lives inside the main "Leader Details"
 * box (functions.php) so the whole record is edited in one place. This separate
 * side box is intentionally NOT registered any more; the render + save helpers
 * below are kept so the field and deon_leader_casual_photo() keep working.
 */
function deon_leader_casual_meta_box_register() {
	add_meta_box(
		'deon_leader_casual',
		__( 'Casual Photo (flip card back)', 'deon-energy' ),
		'deon_leader_casual_meta_box_html',
		'deon_leader',
		'side',
		'low'
	);
}
// add_action( 'add_meta_boxes', 'deon_leader_casual_meta_box_register' ); // Merged into the main box.

/**
 * Render the Casual Photo field.
 *
 * @param WP_Post $post Current leader.
 */
function deon_leader_casual_meta_box_html( $post ) {
	wp_nonce_field( 'deon_leader_casual_save', 'deon_leader_casual_nonce' );
	$url = get_post_meta( $post->ID, DEON_LEADER_CASUAL_KEY, true );
	?>
	<p style="margin-top:0;color:#646970;">
		<?php esc_html_e( 'Optional. The Featured Image is the professional photo shown on the front of the profile card; this candid / informal shot is revealed when the card flips. Leave empty and the card back shows the name, designation, bio and LinkedIn link instead.', 'deon-energy' ); ?>
	</p>

	<?php if ( $url ) : ?>
	<img src="<?php echo esc_url( $url ); ?>" alt=""
	     style="display:block;width:100%;height:auto;margin-bottom:8px;border:1px solid #dcdcde;">
	<?php endif; ?>

	<label for="deon_leader_casual" class="screen-reader-text">
		<?php esc_html_e( 'Casual photo URL', 'deon-energy' ); ?>
	</label>
	<input type="url" id="deon_leader_casual" name="deon_leader_casual"
	       value="<?php echo esc_attr( $url ); ?>" style="width:100%;"
	       placeholder="<?php esc_attr_e( 'Upload or paste an image URL', 'deon-energy' ); ?>">
	<button type="button" class="button deon-media-pick" data-target="deon_leader_casual" style="margin-top:6px;">
		<?php esc_html_e( 'Select / Upload Image', 'deon-energy' ); ?>
	</button>
	<?php
}

/**
 * Persist the Casual Photo field.
 *
 * @param int $post_id Leader ID.
 */
function deon_leader_casual_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_leader_casual_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_leader_casual_nonce'] ) ), 'deon_leader_casual_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['deon_leader_casual'] ) ) {
		update_post_meta( $post_id, DEON_LEADER_CASUAL_KEY, esc_url_raw( wp_unslash( $_POST['deon_leader_casual'] ) ) );
	}
}
add_action( 'save_post_deon_leader', 'deon_leader_casual_meta_save' );

/**
 * Casual photo URL for a leader, or '' when none is set.
 *
 * Templates call this behind function_exists() so they degrade gracefully if
 * this file has not been required yet.
 *
 * @param int $post_id Leader ID.
 * @return string
 */
function deon_leader_casual_photo( $post_id ) {
	$url = get_post_meta( $post_id, DEON_LEADER_CASUAL_KEY, true );

	return is_string( $url ) ? $url : '';
}
