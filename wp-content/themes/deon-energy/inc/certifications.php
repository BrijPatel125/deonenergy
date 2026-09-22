<?php
/**
 * Certifications — client-managed badges for the About page
 * "Certifications & Compliance" section.
 *
 * ACF-free meta-box pattern, same as deon_client / deon_investor_video. Each
 * badge is one post: title = the registration code shown in bold ("ISO
 * 9001:2015"), meta = the short line under it ("Quality Mgmt"), featured image
 * = an optional raster override for the bundled badge artwork, menu_order =
 * display order.
 *
 * The section renders NOTHING until at least one certification is published —
 * graceful hide, no placeholder (same convention as milestones). That is
 * deliberate: the approved copy lists this section by heading only, and a
 * registration Deon does not hold is not a placeholder we can ship.
 *
 * Code can still inject badges without touching wp-admin via the
 * `deon_about_certifications` filter; published posts and filtered rows share
 * one shape (icon_url | icon, code, label).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the Certifications CPT.
 */
function deon_register_certification_cpt() {
	register_post_type( 'deon_certification', array(
		'labels' => array(
			'name'          => __( 'Certifications', 'deon-energy' ),
			'singular_name' => __( 'Certification', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Certification', 'deon-energy' ),
			'edit_item'     => __( 'Edit Certification', 'deon-energy' ),
			'new_item'      => __( 'New Certification', 'deon-energy' ),
			'view_item'     => __( 'View Certification', 'deon-energy' ),
			'search_items'  => __( 'Search Certifications', 'deon-energy' ),
			'not_found'     => __( 'No certifications yet', 'deon-energy' ),
			'all_items'     => __( 'All Certifications', 'deon-energy' ),
			'menu_name'     => __( 'Certifications', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_position' => 27,
		'menu_icon'     => 'dashicons-awards',
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
		'has_archive'   => false,
		'rewrite'       => false,
	) );
}
add_action( 'init', 'deon_register_certification_cpt' );

/**
 * Badge artwork bundled with the theme.
 *
 * These ship as SVG, which WordPress refuses in the Media Library by default —
 * so a badge points at one of these by filename instead of being uploaded. A
 * Featured Image, when set, overrides the choice (use that for a raster badge
 * supplied by the certifying body).
 *
 * @return array<string,string> filename => human label.
 */
function deon_certification_bundled_icons() {
	return array(
		'about-cert-iso9001.svg'    => __( 'ISO 9001 mark', 'deon-energy' ),
		'about-cert-iso14001.svg'   => __( 'ISO 14001 mark', 'deon-energy' ),
		'about-cert-iso45001.svg'   => __( 'ISO 45001 mark', 'deon-energy' ),
		'about-cert-compliance.svg' => __( 'Compliance mark', 'deon-energy' ),
		'about-cert-esg.svg'        => __( 'ESG mark', 'deon-energy' ),
	);
}

/**
 * Meta box: the label line under the code, plus the issuing body / certificate
 * number kept on record.
 */
function deon_certification_meta_box_register() {
	add_meta_box(
		'deon_certification_details',
		__( 'Certification Details', 'deon-energy' ),
		'deon_certification_meta_box_html',
		'deon_certification',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'deon_certification_meta_box_register' );

/**
 * Meta box markup.
 *
 * @param WP_Post $post Current certification.
 */
function deon_certification_meta_box_html( $post ) {
	wp_nonce_field( 'deon_certification_save', 'deon_certification_nonce' );

	$icon   = get_post_meta( $post->ID, '_deon_cert_icon', true );
	$label  = get_post_meta( $post->ID, '_deon_cert_label', true );
	$issuer = get_post_meta( $post->ID, '_deon_cert_issuer', true );
	$number = get_post_meta( $post->ID, '_deon_cert_number', true );
	?>
	<p style="color:#646970;margin-top:0;">
		<?php esc_html_e( 'Put the registration code in the title (e.g. "ISO 9001:2015"), then pick the badge artwork below. Use Page Attributes → Order to sequence the row.', 'deon-energy' ); ?>
	</p>
	<p>
		<label for="deon_cert_label"><strong><?php esc_html_e( 'Label', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_cert_label" name="deon_cert_label" value="<?php echo esc_attr( $label ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. Quality Mgmt', 'deon-energy' ); ?>">
		<br><span style="color:#646970;"><?php esc_html_e( 'The short line printed under the code on the badge.', 'deon-energy' ); ?></span>
	</p>
	<p>
		<label for="deon_cert_icon"><strong><?php esc_html_e( 'Badge artwork', 'deon-energy' ); ?></strong></label><br>
		<select id="deon_cert_icon" name="deon_cert_icon" style="margin-top:4px;">
			<option value=""><?php esc_html_e( '— None —', 'deon-energy' ); ?></option>
			<?php foreach ( deon_certification_bundled_icons() as $deon_icon_file => $deon_icon_label ) : ?>
			<option value="<?php echo esc_attr( $deon_icon_file ); ?>" <?php selected( $icon, $deon_icon_file ); ?>><?php echo esc_html( $deon_icon_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<br><span style="color:#646970;"><?php esc_html_e( 'Artwork bundled with the theme. Setting a Featured Image overrides this — use that for a raster badge issued to Deon.', 'deon-energy' ); ?></span>
	</p>
	<p>
		<label for="deon_cert_issuer"><strong><?php esc_html_e( 'Issuing body', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_cert_issuer" name="deon_cert_issuer" value="<?php echo esc_attr( $issuer ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. TÜV SÜD South Asia', 'deon-energy' ); ?>">
	</p>
	<p>
		<label for="deon_cert_number"><strong><?php esc_html_e( 'Certificate number', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_cert_number" name="deon_cert_number" value="<?php echo esc_attr( $number ); ?>" style="width:100%;margin-top:4px;">
		<br><span style="color:#646970;"><?php esc_html_e( 'Issuing body and certificate number are for your own records — they are not printed on the page. Publish a badge only for a registration Deon currently holds and can evidence.', 'deon-energy' ); ?></span>
	</p>
	<?php
}

/**
 * Persist the meta box.
 *
 * @param int $post_id Certification ID.
 */
function deon_certification_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_certification_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_certification_nonce'] ) ), 'deon_certification_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$fields = array(
		'deon_cert_label'  => '_deon_cert_label',
		'deon_cert_issuer' => '_deon_cert_issuer',
		'deon_cert_number' => '_deon_cert_number',
	);

	foreach ( $fields as $field => $meta_key ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}

	// Only ever store a filename the theme actually ships — this becomes a src.
	if ( isset( $_POST['deon_cert_icon'] ) ) {
		$icon = sanitize_text_field( wp_unslash( $_POST['deon_cert_icon'] ) );
		update_post_meta( $post_id, '_deon_cert_icon', isset( deon_certification_bundled_icons()[ $icon ] ) ? $icon : '' );
	}
}
add_action( 'save_post_deon_certification', 'deon_certification_meta_save' );

/**
 * Certifications list screen: badge thumbnail + label columns.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function deon_certification_admin_columns( $columns ) {
	$out = array(
		'cb'         => $columns['cb'],
		'deon_badge' => __( 'Badge', 'deon-energy' ),
		'title'      => __( 'Code', 'deon-energy' ),
		'deon_label' => __( 'Label', 'deon-energy' ),
	);

	return $out + $columns;
}
add_filter( 'manage_deon_certification_posts_columns', 'deon_certification_admin_columns' );

/**
 * Render the custom list columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Certification ID.
 */
function deon_certification_admin_column_html( $column, $post_id ) {
	if ( 'deon_badge' === $column ) {
		$bundled = deon_certification_bundled_icons();
		$icon    = (string) get_post_meta( $post_id, '_deon_cert_icon', true );

		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'width:60px;height:60px;object-fit:contain;' ) );
		} elseif ( $icon && isset( $bundled[ $icon ] ) ) {
			printf(
				'<img src="%s" alt="" style="width:60px;height:60px;object-fit:contain;">',
				esc_url( get_template_directory_uri() . '/assets/img/' . $icon )
			);
		} else {
			echo '<span style="color:#d63638;">' . esc_html__( 'No badge', 'deon-energy' ) . '</span>';
		}
	}

	if ( 'deon_label' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, '_deon_cert_label', true ) );
	}
}
add_action( 'manage_deon_certification_posts_custom_column', 'deon_certification_admin_column_html', 10, 2 );

/**
 * Default the list screen to display order.
 *
 * @param WP_Query $query Current admin query.
 */
function deon_certification_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'deon_certification' !== $query->get( 'post_type' ) ) {
		return;
	}
	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'deon_certification_admin_order' );

/**
 * Published certification badges for the About page, in menu_order.
 *
 * @param int $limit Maximum badges to return.
 * @return array<int,array{icon_url:string,code:string,label:string}> Empty when
 *                                                                   none exist.
 */
function deon_get_certifications( $limit = 12 ) {
	$posts = get_posts( array(
		'post_type'      => 'deon_certification',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $limit,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	) );

	$badges = array();

	$bundled = deon_certification_bundled_icons();

	foreach ( $posts as $post ) {
		$icon_url = (string) get_the_post_thumbnail_url( $post, 'medium' );

		if ( ! $icon_url ) {
			$icon = (string) get_post_meta( $post->ID, '_deon_cert_icon', true );
			if ( $icon && isset( $bundled[ $icon ] ) ) {
				$icon_url = get_template_directory_uri() . '/assets/img/' . $icon;
			}
		}

		$badges[] = array(
			'icon_url' => $icon_url,
			'code'     => get_the_title( $post ),
			'label'    => (string) get_post_meta( $post->ID, '_deon_cert_label', true ),
		);
	}

	return $badges;
}
