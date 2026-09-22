<?php
/**
 * Deon Energy — Legal disclaimers module.
 *
 * Provides:
 *   - Forward-looking statement disclaimer (auto-injected on investor
 *     & IPO pages; overridable per-page)
 *   - No-solicitation disclaimer (for pages that reference IPO, DRHP,
 *     equity issuance — SEBI-mandated)
 *   - Customizer field for the exact disclaimer text (legal team edits)
 *   - `[deon_disclaimer]` shortcode for placing anywhere manually
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============================================================================
 * 1. Customizer — editable disclaimer text
 * ========================================================================= */

function deon_disclaimers_customizer( $wp_customize ) {
	$wp_customize->add_section( 'deon_disclaimers', array(
		'title'       => __( 'Legal Disclaimers', 'deon-energy' ),
		'priority'    => 220,
		'description' => __( 'Standard SEBI-compliant disclaimers auto-injected on investor and IPO pages. Legal team should review before edits.', 'deon-energy' ),
	) );

	$fields = array(
		'deon_disclaimer_forward' => array(
			'label'    => __( 'Forward-looking statement disclaimer', 'deon-energy' ),
			'default'  => 'This website contains forward-looking statements relating to Deon Energy Limited\'s business, expectations, plans, opportunities, and prospects. These statements involve known and unknown risks and uncertainties. Actual results may differ materially from those anticipated. Deon Energy Limited undertakes no obligation to update these statements to reflect subsequent events or circumstances.',
		),
		'deon_disclaimer_ipo' => array(
			'label'    => __( 'IPO / DRHP disclaimer (no-solicitation)', 'deon-energy' ),
			'default'  => 'This website does not constitute an offer or invitation to subscribe to or purchase any securities of Deon Energy Limited. Any offer of securities will be made only through a Prospectus / Red Herring Prospectus filed with the Securities and Exchange Board of India (SEBI). Prospective investors should not rely on information posted on this website in place of the Prospectus.',
		),
		'deon_disclaimer_general' => array(
			'label'    => __( 'General information disclaimer', 'deon-energy' ),
			'default'  => 'Information on this website is provided for general purposes only and should not be treated as professional, financial, legal, or investment advice. Deon Energy Limited makes no representations as to the completeness, accuracy, or timeliness of the content. Independent professional advice should be sought before acting on any information contained here.',
		),
	);

	foreach ( $fields as $id => $config ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $config['default'],
			'sanitize_callback' => 'sanitize_textarea_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $config['label'],
			'section' => 'deon_disclaimers',
			'type'    => 'textarea',
		) );
	}
}
add_action( 'customize_register', 'deon_disclaimers_customizer' );

/* ============================================================================
 * 2. Renderer + shortcode
 * ========================================================================= */

function deon_render_disclaimer( $type = 'forward' ) {
	$setting_map = array(
		'forward' => array( 'deon_disclaimer_forward', __( 'Forward-Looking Statements', 'deon-energy' ) ),
		'ipo'     => array( 'deon_disclaimer_ipo',     __( 'IPO Information', 'deon-energy' ) ),
		'general' => array( 'deon_disclaimer_general', __( 'General Information', 'deon-energy' ) ),
	);
	if ( ! isset( $setting_map[ $type ] ) ) {
		$type = 'forward';
	}
	list( $setting_id, $heading ) = $setting_map[ $type ];
	$text = get_theme_mod( $setting_id, '' );
	if ( empty( $text ) ) {
		return '';
	}
	ob_start();
	?>
	<aside class="deon-disclaimer" role="note" aria-label="<?php echo esc_attr( $heading ); ?>">
		<style>
		.deon-disclaimer{margin:32px auto;padding:20px 24px;max-width:1120px;background:#fafaf7;border:1px solid #e5e1d8;border-left:3px solid #9ca3af;border-radius:4px;font-family:'Plus Jakarta Sans',sans-serif}
		.deon-disclaimer__heading{font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#6b7280;margin:0 0 10px}
		.deon-disclaimer__text{font-size:12.5px;line-height:1.7;color:#4d5563;margin:0}
		</style>
		<p class="deon-disclaimer__heading"><?php echo esc_html( $heading ); ?></p>
		<p class="deon-disclaimer__text"><?php echo esc_html( $text ); ?></p>
	</aside>
	<?php
	return ob_get_clean();
}

/**
 * Shortcode: [deon_disclaimer type="forward|ipo|general"]
 */
function deon_disclaimer_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'forward' ), $atts, 'deon_disclaimer' );
	return deon_render_disclaimer( $atts['type'] );
}
add_shortcode( 'deon_disclaimer', 'deon_disclaimer_shortcode' );

/* ============================================================================
 * 3. Auto-inject on investor / IPO pages
 * ------------------------------------------------------------------------
 * Investor Relations pages get forward-looking + IPO disclaimers.
 * Regular pages don't get any auto-injection.
 * Any page can override via the meta box (see below).
 * ========================================================================= */

function deon_maybe_autoinject_disclaimer( $content ) {
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	// Per-page override — an editor can turn off auto-injection.
	if ( is_singular() && '1' === get_post_meta( get_the_ID(), '_deon_no_disclaimer', true ) ) {
		return $content;
	}
	// Investor pages get both forward-looking AND IPO disclaimers.
	if ( is_page( array( 'investor-relations', 'investors', 'investor-documents' ) ) ) {
		$content .= deon_render_disclaimer( 'forward' );
		$content .= deon_render_disclaimer( 'ipo' );
		return $content;
	}
	// About / Solutions / Projects pages get the forward-looking one only
	// (they carry business projections and outlook language).
	if ( is_page( array( 'about', 'about-us', 'solutions', 'projects', 'esg' ) ) ) {
		$content .= deon_render_disclaimer( 'forward' );
		return $content;
	}
	return $content;
}
add_filter( 'the_content', 'deon_maybe_autoinject_disclaimer', 25 );

/* ============================================================================
 * 4. Per-page opt-out toggle
 * ========================================================================= */

function deon_disclaimer_meta_box() {
	add_meta_box(
		'deon_disclaimer_meta_box',
		__( 'Legal Disclaimer', 'deon-energy' ),
		'deon_disclaimer_meta_box_render',
		array( 'page', 'post' ),
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'deon_disclaimer_meta_box' );

function deon_disclaimer_meta_box_render( $post ) {
	wp_nonce_field( 'deon_disclaimer_save', 'deon_disclaimer_nonce' );
	$value = get_post_meta( $post->ID, '_deon_no_disclaimer', true );
	?>
	<label>
		<input type="checkbox" name="deon_no_disclaimer" value="1" <?php checked( $value, '1' ); ?>>
		<?php esc_html_e( 'Do not auto-inject legal disclaimer on this page.', 'deon-energy' ); ?>
	</label>
	<p class="description" style="margin-top:6px;">
		<?php esc_html_e( 'By default, investor and business pages receive a legal disclaimer at the bottom. Tick this to opt out for this page.', 'deon-energy' ); ?>
	</p>
	<?php
}

function deon_disclaimer_meta_box_save( $post_id ) {
	if ( ! isset( $_POST['deon_disclaimer_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['deon_disclaimer_nonce'] ), 'deon_disclaimer_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$value = isset( $_POST['deon_no_disclaimer'] ) ? '1' : '';
	if ( '' === $value ) {
		delete_post_meta( $post_id, '_deon_no_disclaimer' );
	} else {
		update_post_meta( $post_id, '_deon_no_disclaimer', $value );
	}
}
add_action( 'save_post', 'deon_disclaimer_meta_box_save' );
