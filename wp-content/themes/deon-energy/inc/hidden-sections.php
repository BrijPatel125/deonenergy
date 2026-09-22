<?php
/**
 * Admin notices for content types whose front-end section is currently hidden.
 *
 * The Knowledge Hub page no longer renders the Impact & Execution, Resource
 * Archive, or In The Press modules (see home.php). The CPTs stay registered so
 * nothing is lost, but an editor adding entries would otherwise have no way to
 * know the section is switched off. Each list/edit screen therefore explains
 * where the content does — and no longer does — appear.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Notice copy per post type.
 *
 * 'section'   — the front-end module that is hidden.
 * 'still'     — where the same content is still published (empty = nowhere).
 *
 * @return array<string, array<string, string>>
 */
function deon_hidden_section_notices() {
	return array(
		'deon_case_study' => array(
			'section' => __( '“Impact & Execution” on the Knowledge Hub page', 'deon-energy' ),
			'still'   => '',
		),
		'deon_document'   => array(
			'section' => __( '“Resource Archive” on the Knowledge Hub page', 'deon-energy' ),
			'still'   => __( 'Documents are still published on the News &amp; Media and Technical Papers pages.', 'deon-energy' ),
		),
		'deon_press'      => array(
			'section' => __( '“In The Press” on the Knowledge Hub page', 'deon-energy' ),
			'still'   => __( 'Press mentions are still published on the News &amp; Media page.', 'deon-energy' ),
		),
	);
}

/**
 * Render the notice on the list table and the post editor for those types.
 */
function deon_render_hidden_section_notice() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || ! in_array( $screen->base, array( 'edit', 'post' ), true ) ) {
		return;
	}

	$notices = deon_hidden_section_notices();

	if ( ! isset( $notices[ $screen->post_type ] ) ) {
		return;
	}

	$notice = $notices[ $screen->post_type ];
	?>
	<div class="notice notice-warning">
		<p>
			<strong><?php esc_html_e( 'Hidden on the website', 'deon-energy' ); ?></strong> —
			<?php
			printf(
				/* translators: %s: name of the hidden front-end section */
				esc_html__( 'the %s section is currently switched off, so entries saved here are not shown to visitors.', 'deon-energy' ),
				esc_html( $notice['section'] )
			);
			?>
			<?php echo wp_kses_post( $notice['still'] ); ?>
			<?php esc_html_e( 'Your content is safe and stays editable — ask your developer to re-enable the section when you want it back.', 'deon-energy' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'deon_render_hidden_section_notice' );

/**
 * Flag the hidden types in the admin menu so the state is visible before you
 * even open the screen: "Case Studies (hidden)".
 */
function deon_label_hidden_section_menus() {
	global $menu;

	if ( ! is_array( $menu ) ) {
		return;
	}

	$suffix = ' <span style="color:#996800;font-weight:400;">' . esc_html__( '(hidden)', 'deon-energy' ) . '</span>';

	foreach ( array_keys( deon_hidden_section_notices() ) as $post_type ) {
		$slug = 'edit.php?post_type=' . $post_type;

		foreach ( $menu as $index => $item ) {
			if ( isset( $item[2] ) && $slug === $item[2] ) {
				$menu[ $index ][0] .= $suffix;
			}
		}
	}
}
add_action( 'admin_menu', 'deon_label_hidden_section_menus', 999 );
