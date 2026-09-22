<?php
/**
 * Careers — job application intake + management.
 *
 * The designed "Apply Now" band on a job single used to be presentational (a
 * mailto link); this makes it a real form that stores every submission in
 * wp-admin under **Applications**, mirroring the Contact Us → Inquiries flow
 * (`deon_inquiry` in functions.php).
 *
 * What the client gets:
 *   - Applications menu with an unread bubble + total counts in the list table.
 *   - Job-wise filtering (dropdown on the list screen, plus an "Applications"
 *     count column on the Jobs list that links straight to the filtered view).
 *   - A read-only details panel per application: contact details, cover note,
 *     the job applied for, and the résumé.
 *
 * Résumé privacy: CVs are NOT added to the Media Library. They are written to
 * `uploads/deon-applications/` under an unguessable filename and served only
 * through the gated `deon_app_cv` admin-post endpoint, which requires the
 * `edit_posts` capability. A deny-all `.htaccess` is dropped in that folder for
 * Apache; on nginx add an equivalent `location` deny rule (see docs/CLAUDE.md).
 *
 * ACF-free, matching the rest of the theme's admin.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Résumé upload constraints.
 *
 * @return array{mimes:array<string,string>,max:int} Allowed mimes and byte cap.
 */
function deon_application_upload_rules() {
	return apply_filters( 'deon_application_upload_rules', array(
		'mimes' => array(
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'rtf'  => 'application/rtf',
		),
		// 10 MB — matches the "Limit 10MB" hint in the approved design.
		'max'   => 10 * MB_IN_BYTES,
	) );
}

/**
 * Custom Post Type: Job Application.
 * Not publicly queryable; entries are created by the form handler only.
 */
function deon_register_application_cpt() {
	register_post_type( 'deon_application', array(
		'labels'        => array(
			'name'               => __( 'Applications', 'deon-energy' ),
			'singular_name'      => __( 'Application', 'deon-energy' ),
			'menu_name'          => __( 'Applications', 'deon-energy' ),
			'edit_item'          => __( 'View Application', 'deon-energy' ),
			'search_items'       => __( 'Search Applications', 'deon-energy' ),
			'not_found'          => __( 'No applications yet.', 'deon-energy' ),
			'not_found_in_trash' => __( 'No applications in Trash.', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-id-alt',
		'menu_position' => 24, // directly under Inquiries (23).
		'supports'      => array( 'title' ), // cover note is shown read-only in the details box.
		'capabilities'  => array( 'create_posts' => 'do_not_allow' ), // form handler inserts; no manual "Add New".
		'map_meta_cap'  => true,
	) );
}
add_action( 'init', 'deon_register_application_cpt' );

/**
 * Unread bubble on the Applications menu, same affordance as comment counts.
 */
function deon_application_menu_bubble() {
	global $menu;

	$unread = get_posts( array(
		'post_type'      => 'deon_application',
		'post_status'    => 'publish',
		'posts_per_page' => 100,
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => '_deon_app_read',
				'compare' => 'NOT EXISTS',
			),
		),
	) );
	$count = count( $unread );
	if ( ! $count ) {
		return;
	}

	foreach ( $menu as $key => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=deon_application' === $item[2] ) {
			$menu[ $key ][0] .= sprintf( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- documented way to add a menu bubble.
				' <span class="update-plugins count-%1$d"><span class="update-count">%2$s</span></span>',
				$count,
				number_format_i18n( $count )
			);
			break;
		}
	}
}
add_action( 'admin_menu', 'deon_application_menu_bubble', 999 );

/**
 * Mark an application read once the client opens it.
 */
function deon_application_mark_read() {
	$screen = get_current_screen();
	if ( ! $screen || 'deon_application' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only flag on an admin view.
	if ( $post_id && current_user_can( 'edit_post', $post_id ) ) {
		update_post_meta( $post_id, '_deon_app_read', 1 );
	}
}
add_action( 'current_screen', 'deon_application_mark_read' );

/**
 * Admin list columns: applicant, job, email, phone, résumé, received.
 */
function deon_application_columns( $columns ) {
	$date = isset( $columns['date'] ) ? $columns['date'] : '';
	unset( $columns['date'] );

	$columns['deon_app_job']    = __( 'Job', 'deon-energy' );
	$columns['deon_app_email']  = __( 'Email', 'deon-energy' );
	$columns['deon_app_phone']  = __( 'Phone', 'deon-energy' );
	$columns['deon_app_cv']     = __( 'Résumé', 'deon-energy' );
	$columns['date']            = $date ? $date : __( 'Received', 'deon-energy' );

	return $columns;
}
add_filter( 'manage_deon_application_posts_columns', 'deon_application_columns' );

function deon_application_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'deon_app_job':
			$job_id = (int) get_post_meta( $post_id, '_deon_app_job_id', true );
			$title  = (string) get_post_meta( $post_id, '_deon_app_job_title', true );
			if ( $job_id && get_post( $job_id ) ) {
				printf(
					'<a href="%1$s">%2$s</a>',
					esc_url( add_query_arg( array( 'post_type' => 'deon_application', 'deon_job_id' => $job_id ), admin_url( 'edit.php' ) ) ),
					esc_html( $title ? $title : get_the_title( $job_id ) )
				);
			} else {
				echo esc_html( $title ? $title : '—' );
			}
			break;

		case 'deon_app_email':
			$email = (string) get_post_meta( $post_id, '_deon_app_email', true );
			if ( $email ) {
				printf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $email ), esc_html( $email ) );
			} else {
				echo '—';
			}
			break;

		case 'deon_app_phone':
			$phone = (string) get_post_meta( $post_id, '_deon_app_phone', true );
			echo $phone ? esc_html( $phone ) : '—';
			break;

		case 'deon_app_cv':
			$link = deon_application_cv_link( $post_id );
			if ( $link ) {
				printf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $link ),
					esc_html__( 'Download', 'deon-energy' )
				);
			} else {
				echo '—';
			}
			break;
	}
}
add_action( 'manage_deon_application_posts_custom_column', 'deon_application_column_content', 10, 2 );

/**
 * Unread rows read as bold, like unreplied comments.
 */
function deon_application_row_class( $classes, $class, $post_id ) {
	if ( 'deon_application' === get_post_type( $post_id ) && ! get_post_meta( $post_id, '_deon_app_read', true ) ) {
		$classes[] = 'deon-app-unread';
	}
	return $classes;
}
add_filter( 'post_class', 'deon_application_row_class', 10, 3 );

function deon_application_admin_css( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || 'deon_application' !== $screen->post_type ) {
		return;
	}
	wp_add_inline_style( 'wp-admin', '.deon-app-unread td, .deon-app-unread th { font-weight: 600; }'
		. '.deon-app-detail th { width: 170px; text-align: left; vertical-align: top; padding: 8px 12px 8px 0; }'
		. '.deon-app-detail td { padding: 8px 0; }'
		. '.deon-app-note { white-space: pre-wrap; background: #f6f7f7; padding: 12px; border-left: 3px solid #e8930a; }' );
}
add_action( 'admin_enqueue_scripts', 'deon_application_admin_css' );

/**
 * "Filter by job" dropdown above the list table. Only jobs that actually have
 * applications are listed, so the control stays short.
 */
function deon_application_job_filter( $post_type ) {
	if ( 'deon_application' !== $post_type ) {
		return;
	}

	global $wpdb;
	$job_ids = $wpdb->get_col( $wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one grouped meta read, no core API for it.
		"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
		 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE pm.meta_key = %s AND p.post_type = %s AND p.post_status != %s",
		'_deon_app_job_id',
		'deon_application',
		'trash'
	) );

	if ( ! $job_ids ) {
		return;
	}

	$current = isset( $_GET['deon_job_id'] ) ? (int) $_GET['deon_job_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
	?>
	<select name="deon_job_id">
		<option value="0"><?php esc_html_e( 'All jobs', 'deon-energy' ); ?></option>
		<?php
		foreach ( $job_ids as $job_id ) {
			$job_id = (int) $job_id;
			$title  = get_the_title( $job_id );
			if ( '' === $title ) {
				continue;
			}
			printf(
				'<option value="%1$d" %2$s>%3$s (%4$s)</option>',
				$job_id,
				selected( $current, $job_id, false ),
				esc_html( $title ),
				esc_html( number_format_i18n( deon_application_count_for_job( $job_id ) ) )
			);
		}
		?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'deon_application_job_filter' );

/**
 * Apply the job filter to the list query.
 */
function deon_application_filter_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( 'deon_application' !== $query->get( 'post_type' ) ) {
		return;
	}
	$job_id = isset( $_GET['deon_job_id'] ) ? (int) $_GET['deon_job_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
	if ( $job_id ) {
		$query->set( 'meta_key', '_deon_app_job_id' );
		$query->set( 'meta_value', $job_id );
	}
}
add_action( 'pre_get_posts', 'deon_application_filter_query' );

/**
 * How many applications a given job has received (excluding trash).
 */
function deon_application_count_for_job( $job_id ) {
	$job_id = (int) $job_id;
	if ( ! $job_id ) {
		return 0;
	}
	$ids = get_posts( array(
		'post_type'      => 'deon_application',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_key'       => '_deon_app_job_id',
		'meta_value'     => $job_id,
	) );
	return count( $ids );
}

/**
 * "Applications" count column on the Jobs list, linking to the filtered view —
 * the fastest route to "how is this opening doing?".
 */
function deon_job_application_column( $columns ) {
	$columns['deon_job_apps'] = __( 'Applications', 'deon-energy' );
	return $columns;
}
add_filter( 'manage_deon_job_posts_columns', 'deon_job_application_column' );

function deon_job_application_column_content( $column, $post_id ) {
	if ( 'deon_job_apps' !== $column ) {
		return;
	}
	$count = deon_application_count_for_job( $post_id );
	if ( ! $count ) {
		echo '—';
		return;
	}
	printf(
		'<a href="%1$s">%2$s</a>',
		esc_url( add_query_arg( array( 'post_type' => 'deon_application', 'deon_job_id' => $post_id ), admin_url( 'edit.php' ) ) ),
		esc_html( number_format_i18n( $count ) )
	);
}
add_action( 'manage_deon_job_posts_custom_column', 'deon_job_application_column_content', 10, 2 );

/**
 * Read-only details panel on a single application.
 */
function deon_application_meta_box() {
	add_meta_box(
		'deon_application_details',
		__( 'Application Details', 'deon-energy' ),
		'deon_application_details_box',
		'deon_application',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_deon_application', 'deon_application_meta_box' );

function deon_application_details_box( $post ) {
	$get = static function ( $key ) use ( $post ) {
		return (string) get_post_meta( $post->ID, $key, true );
	};

	$email     = $get( '_deon_app_email' );
	$job_id    = (int) $get( '_deon_app_job_id' );
	$job_title = $get( '_deon_app_job_title' );
	$cv_link   = deon_application_cv_link( $post->ID );
	$cv_name   = $get( '_deon_app_cv_name' );
	?>
	<table class="deon-app-detail">
		<tr>
			<th><?php esc_html_e( 'Applicant', 'deon-energy' ); ?></th>
			<td><?php echo esc_html( trim( $get( '_deon_app_first' ) . ' ' . $get( '_deon_app_last' ) ) ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Email', 'deon-energy' ); ?></th>
			<td>
				<?php if ( $email ) : ?>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				<?php else : ?>
					—
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Phone', 'deon-energy' ); ?></th>
			<td><?php echo $get( '_deon_app_phone' ) ? esc_html( $get( '_deon_app_phone' ) ) : '—'; ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Applied for', 'deon-energy' ); ?></th>
			<td>
				<?php if ( $job_id && get_post( $job_id ) ) : ?>
					<a href="<?php echo esc_url( get_edit_post_link( $job_id ) ); ?>"><?php echo esc_html( $job_title ? $job_title : get_the_title( $job_id ) ); ?></a>
				<?php else : ?>
					<?php echo $job_title ? esc_html( $job_title ) : '—'; ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Résumé', 'deon-energy' ); ?></th>
			<td>
				<?php if ( $cv_link ) : ?>
					<a class="button" href="<?php echo esc_url( $cv_link ); ?>"><?php esc_html_e( 'Download résumé', 'deon-energy' ); ?></a>
					<?php if ( $cv_name ) : ?>
						<span style="margin-left:8px;color:#646970;"><?php echo esc_html( $cv_name ); ?></span>
					<?php endif; ?>
				<?php else : ?>
					<em><?php esc_html_e( 'No file attached.', 'deon-energy' ); ?></em>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Received', 'deon-energy' ); ?></th>
			<td><?php echo esc_html( get_the_date( '', $post ) . ' ' . get_the_time( '', $post ) ); ?></td>
		</tr>
	</table>

	<?php if ( '' !== trim( (string) $post->post_content ) ) : ?>
		<h4 style="margin-bottom:6px;"><?php esc_html_e( 'Cover note', 'deon-energy' ); ?></h4>
		<div class="deon-app-note"><?php echo esc_html( $post->post_content ); ?></div>
	<?php endif; ?>

	<?php if ( $email ) : ?>
		<p style="margin-top:16px;">
			<a class="button button-primary" href="mailto:<?php echo esc_attr( $email ); ?>?subject=<?php echo rawurlencode( sprintf( /* translators: %s: job title. */ __( 'Your application: %s', 'deon-energy' ), $job_title ) ); ?>">
				<?php esc_html_e( 'Reply to applicant', 'deon-energy' ); ?>
			</a>
		</p>
	<?php endif; ?>
	<?php
}

/**
 * Directory that holds résumés. Kept out of the Media Library and out of the
 * year/month tree so a deny rule can target one folder.
 *
 * @return array{path:string,url:string}
 */
function deon_application_upload_dir() {
	$uploads = wp_get_upload_dir();
	$path    = trailingslashit( $uploads['basedir'] ) . 'deon-applications';
	$url     = trailingslashit( $uploads['baseurl'] ) . 'deon-applications';

	if ( ! file_exists( $path ) ) {
		wp_mkdir_p( $path );
	}
	// Apache: block direct hits. nginx needs an equivalent server-level rule.
	if ( ! file_exists( $path . '/.htaccess' ) ) {
		file_put_contents( $path . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}
	if ( ! file_exists( $path . '/index.html' ) ) {
		file_put_contents( $path . '/index.html', '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	return array( 'path' => $path, 'url' => $url );
}

/**
 * Admin-only download URL for an application's résumé, or '' when there is none.
 */
function deon_application_cv_link( $post_id ) {
	$file = (string) get_post_meta( $post_id, '_deon_app_cv_file', true );
	if ( '' === $file ) {
		return '';
	}
	return wp_nonce_url(
		add_query_arg(
			array( 'action' => 'deon_app_cv', 'app' => (int) $post_id ),
			admin_url( 'admin-post.php' )
		),
		'deon_app_cv_' . (int) $post_id
	);
}

/**
 * Serve a résumé to logged-in staff only. Files never leave this endpoint, so
 * the upload folder can stay locked down.
 */
function deon_application_serve_cv() {
	$post_id = isset( $_GET['app'] ) ? (int) $_GET['app'] : 0;
	if ( ! $post_id || ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You are not allowed to download this file.', 'deon-energy' ), 403 );
	}
	check_admin_referer( 'deon_app_cv_' . $post_id );

	if ( 'deon_application' !== get_post_type( $post_id ) ) {
		wp_die( esc_html__( 'Not an application.', 'deon-energy' ), 400 );
	}

	$dir  = deon_application_upload_dir();
	$file = (string) get_post_meta( $post_id, '_deon_app_cv_file', true );
	$path = trailingslashit( $dir['path'] ) . basename( $file ); // basename() pins it inside the folder.

	if ( '' === $file || ! file_exists( $path ) ) {
		wp_die( esc_html__( 'Résumé file not found.', 'deon-energy' ), 404 );
	}

	$name = (string) get_post_meta( $post_id, '_deon_app_cv_name', true );
	$name = $name ? $name : basename( $path );
	$type = wp_check_filetype( $path );

	nocache_headers();
	header( 'Content-Type: ' . ( $type['type'] ? $type['type'] : 'application/octet-stream' ) );
	header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $name ) . '"' );
	header( 'Content-Length: ' . filesize( $path ) );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- streaming a gated download.
	exit;
}
add_action( 'admin_post_deon_app_cv', 'deon_application_serve_cv' );

/**
 * Delete the résumé from disk when its application is deleted for good.
 */
function deon_application_delete_file( $post_id ) {
	if ( 'deon_application' !== get_post_type( $post_id ) ) {
		return;
	}
	$file = (string) get_post_meta( $post_id, '_deon_app_cv_file', true );
	if ( '' === $file ) {
		return;
	}
	$dir  = deon_application_upload_dir();
	$path = trailingslashit( $dir['path'] ) . basename( $file );
	if ( file_exists( $path ) ) {
		wp_delete_file( $path );
	}
}
add_action( 'before_delete_post', 'deon_application_delete_file' );

/**
 * Where application notifications are sent. Falls back to the contact-form
 * recipient, then the site admin email.
 */
function deon_application_recipient() {
	$recipient = get_theme_mod( 'deon_apply_recipient', '' );
	if ( is_email( $recipient ) ) {
		return $recipient;
	}
	return function_exists( 'deon_notification_recipient' ) ? deon_notification_recipient() : get_option( 'admin_email' );
}

/**
 * Handle an application submission: validate, store the file, insert the
 * application, notify HR. Posts to admin-post.php so logged-out visitors work
 * via the nopriv hook — same shape as deon_handle_contact_submit().
 */
function deon_handle_application_submit() {
	$job_id   = isset( $_POST['deon_job_id'] ) ? (int) $_POST['deon_job_id'] : 0;
	$fallback = $job_id ? get_permalink( $job_id ) : home_url( '/careers/' );
	$redirect = wp_get_referer() ? wp_get_referer() : $fallback;

	$fail = static function ( $reason ) use ( $redirect ) {
		wp_safe_redirect( add_query_arg( array( 'applied' => 'error', 'reason' => $reason ), $redirect ) . '#apply' );
		exit;
	};

	if ( ! isset( $_POST['deon_apply_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_apply_nonce'] ) ), 'deon_apply_submit' ) ) {
		$fail( 'nonce' );
	}

	// Honeypot — bots fill this hidden field; humans never see it.
	if ( ! empty( $_POST['deon_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'applied', 'success', $redirect ) . '#apply' ); // silent drop.
		exit;
	}

	$first = isset( $_POST['deon_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['deon_first_name'] ) ) : '';
	$last  = isset( $_POST['deon_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['deon_last_name'] ) ) : '';
	$email = isset( $_POST['deon_email'] ) ? sanitize_email( wp_unslash( $_POST['deon_email'] ) ) : '';
	$phone = isset( $_POST['deon_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['deon_phone'] ) ) : '';
	$note  = isset( $_POST['deon_cover_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['deon_cover_note'] ) ) : '';

	if ( '' === $first || '' === $last || ! is_email( $email ) ) {
		$fail( 'fields' );
	}

	$job       = $job_id ? get_post( $job_id ) : null;
	$job_title = ( $job && 'deon_job' === $job->post_type ) ? $job->post_title : '';

	// Résumé (required — an application without one is not reviewable).
	$rules  = deon_application_upload_rules();
	$upload = isset( $_FILES['deon_cv'] ) ? $_FILES['deon_cv'] : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- validated below by wp_handle_upload().

	if ( ! $upload || ! isset( $upload['name'] ) || '' === $upload['name'] || UPLOAD_ERR_NO_FILE === (int) $upload['error'] ) {
		$fail( 'nocv' );
	}
	if ( UPLOAD_ERR_OK !== (int) $upload['error'] ) {
		$fail( UPLOAD_ERR_INI_SIZE === (int) $upload['error'] || UPLOAD_ERR_FORM_SIZE === (int) $upload['error'] ? 'toobig' : 'upload' );
	}
	if ( (int) $upload['size'] > $rules['max'] ) {
		$fail( 'toobig' );
	}

	$check = wp_check_filetype( $upload['name'], $rules['mimes'] );
	if ( ! $check['ext'] || ! $check['type'] ) {
		$fail( 'filetype' );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$dir      = deon_application_upload_dir();
	$original = sanitize_file_name( $upload['name'] );

	// Unguessable stored name; the human-readable original is kept in meta and
	// restored in the Content-Disposition header on download.
	$stored = wp_unique_filename( $dir['path'], 'cv-' . wp_generate_password( 20, false, false ) . '.' . $check['ext'] );

	$overrides = array(
		'test_form' => false,
		'mimes'     => $rules['mimes'],
		'unique_filename_callback' => static function () use ( $stored ) {
			return $stored;
		},
	);

	$to_dir = static function ( $dirs ) use ( $dir ) {
		$dirs['path']   = $dir['path'];
		$dirs['url']    = $dir['url'];
		$dirs['subdir'] = '';
		return $dirs;
	};
	add_filter( 'upload_dir', $to_dir );
	$moved = wp_handle_upload( $upload, $overrides );
	remove_filter( 'upload_dir', $to_dir );

	if ( ! is_array( $moved ) || isset( $moved['error'] ) || empty( $moved['file'] ) ) {
		$fail( 'upload' );
	}

	// Store the application.
	$app_id = wp_insert_post( array(
		'post_type'    => 'deon_application',
		'post_status'  => 'publish',
		/* translators: 1: applicant name, 2: job title. */
		'post_title'   => $job_title
			? sprintf( __( '%1$s — %2$s', 'deon-energy' ), trim( $first . ' ' . $last ), $job_title )
			: trim( $first . ' ' . $last ),
		'post_content' => $note,
	) );

	if ( is_wp_error( $app_id ) || ! $app_id ) {
		wp_delete_file( $moved['file'] );
		$fail( 'store' );
	}

	update_post_meta( $app_id, '_deon_app_first', $first );
	update_post_meta( $app_id, '_deon_app_last', $last );
	update_post_meta( $app_id, '_deon_app_email', $email );
	update_post_meta( $app_id, '_deon_app_phone', $phone );
	update_post_meta( $app_id, '_deon_app_job_id', $job_id );
	update_post_meta( $app_id, '_deon_app_job_title', $job_title );
	update_post_meta( $app_id, '_deon_app_cv_file', basename( $moved['file'] ) );
	update_post_meta( $app_id, '_deon_app_cv_name', $original );

	// Notify HR, with the résumé attached so they can triage from the inbox.
	$site    = get_bloginfo( 'name' );
	$body    = sprintf( "%s: %s\n", __( 'Applicant', 'deon-energy' ), trim( $first . ' ' . $last ) );
	$body   .= sprintf( "%s: %s\n", __( 'Email', 'deon-energy' ), $email );
	$body   .= sprintf( "%s: %s\n", __( 'Phone', 'deon-energy' ), $phone );
	$body   .= sprintf( "%s: %s\n\n", __( 'Applied for', 'deon-energy' ), $job_title ? $job_title : __( 'General application', 'deon-energy' ) );
	if ( '' !== $note ) {
		$body .= sprintf( "%s:\n%s\n\n", __( 'Cover note', 'deon-energy' ), $note );
	}
	$body   .= sprintf( "%s: %s\n", __( 'Manage in wp-admin', 'deon-energy' ), get_edit_post_link( $app_id, 'raw' ) );

	wp_mail(
		deon_application_recipient(),
		/* translators: 1: site name, 2: job title. */
		sprintf( __( '[%1$s] New application: %2$s', 'deon-energy' ), $site, $job_title ? $job_title : __( 'General', 'deon-energy' ) ),
		$body,
		array( 'Reply-To: ' . trim( $first . ' ' . $last ) . ' <' . $email . '>' ),
		array( $moved['file'] )
	);

	wp_safe_redirect( add_query_arg( 'applied', 'success', $redirect ) . '#apply' );
	exit;
}
add_action( 'admin_post_deon_apply_submit', 'deon_handle_application_submit' );
add_action( 'admin_post_nopriv_deon_apply_submit', 'deon_handle_application_submit' );
