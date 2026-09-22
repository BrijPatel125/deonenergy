<?php
/**
 * QA seeder — fully-populated DEMO projects (deon_project CPT).
 *
 * Unlike seed-projects.php (real portfolio: title/capacity/location/type only),
 * this creates a few *fake* projects that fill EVERY field on the detail page,
 * so you can verify each one renders: rich description, all spec rows, hero
 * image/video background, story video, location map, technical milestones, and
 * a full bento gallery. Purely for testing — delete these before launch.
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-demo-projects.php            # create / refresh demos
 *     wp eval-file bin/seed-demo-projects.php remove   # delete demos + their media
 *
 * Idempotent: matched by slug (deon-demo-*). Re-running wipes each demo post and
 * its sideloaded attachments, then recreates them cleanly (no media pile-up).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Run via WP-CLI: wp eval-file bin/seed-demo-projects.php\n" );
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$theme_dir = get_template_directory();
$img       = static function ( $name ) use ( $theme_dir ) {
	return $theme_dir . '/assets/img/' . $name;
};
// Positional `remove`, not `--remove`: WP-CLI rejects unknown leading-dash
// flags before eval-file ever runs the script.
$remove = in_array( 'remove', (array) ( $args ?? array() ), true );

/**
 * Delete a demo post (by slug) and every attachment parented to it. Keeps the
 * media library clean across re-runs.
 */
$wipe_demo = static function ( $slug ) {
	$existing = get_page_by_path( $slug, OBJECT, 'deon_project' );
	if ( ! $existing ) {
		return;
	}
	$kids = get_posts( array(
		'post_type'      => 'attachment',
		'post_parent'    => $existing->ID,
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	) );
	foreach ( $kids as $kid ) {
		wp_delete_attachment( $kid, true );
	}
	wp_delete_post( $existing->ID, true );
	WP_CLI::line( "Wiped existing demo: {$slug}" );
};

/**
 * Import a theme-asset file into the media library, parented to $post_id.
 * Local read (no HTTP round-trip) so it works on locked-down CLI hosts.
 * Returns attachment ID or WP_Error.
 */
$sideload = static function ( $path, $post_id, $title ) {
	if ( ! file_exists( $path ) ) {
		return new WP_Error( 'missing_file', "File not found: {$path}" );
	}
	$upload = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $upload['error'] ) ) {
		return new WP_Error( 'upload_failed', $upload['error'] );
	}
	$filetype = wp_check_filetype( $upload['file'], null );
	$att_id   = wp_insert_attachment( array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => $title,
		'post_status'    => 'inherit',
	), $upload['file'], $post_id );
	if ( is_wp_error( $att_id ) ) {
		return $att_id;
	}
	wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );
	return $att_id;
};

// ── Ensure taxonomy terms ─────────────────────────────────────────────────────
$term_id_for = static function ( $name ) {
	$term = get_term_by( 'name', $name, 'deon_project_type' );
	if ( $term ) {
		return (int) $term->term_id;
	}
	$new = wp_insert_term( $name, 'deon_project_type' );
	return is_wp_error( $new ) ? 0 : (int) $new['term_id'];
};

// ── Demo definitions ─────────────────────────────────────────────────────────
// Each exercises a different hero background type: image, video, none.
$demos = array(
	array(
		'slug'    => 'deon-demo-sonoran-sun-array',
		'title'   => 'DEMO — Sonoran Sun Array',
		'type'    => 'Ground Mount',
		'order'   => 900,
		'content' => "<h2>Architecting the future of desert energy.</h2>\n<p>The Sonoran Sun Array represents a pinnacle of solar engineering, specifically optimized for the high-irradiance, extreme-temperature environment of the Arizona desert. Commissioned in 2022, this project utilizes bifacial module technology and adaptive tracking algorithms to maximize yield across the scorched landscape of Phoenix.</p>\n<ul>\n<li>Adaptive single-axis tracking</li>\n<li>Bifacial mono-PERC modules</li>\n<li>Grid-forming inverters for regional stability</li>\n</ul>\n<p>This is placeholder copy to verify the <strong>rich-text overview</strong> renders headings, paragraphs and lists correctly.</p>",
		'specs'   => array(
			'_deon_capacity'      => '45.0 MW',
			'_deon_location'      => 'Phoenix, AZ',
			'_deon_status'        => 'Operational',
			'_deon_commissioned'  => 'Q4 2022',
			'_deon_grid_voltage'  => '230 kV',
			'_deon_annual_output' => '112 GWh',
			'_deon_modules'       => 'Bifacial mono-PERC',
			'_deon_epc_partner'   => 'Deon Engineering',
			'_deon_client'        => 'Deon Energy Ltd.',
		),
		'hero'    => array( 'type' => 'image', 'image' => 'about-hero.jpg' ),
		'video'   => array( 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'caption' => 'The Art of Infrastructure', 'poster' => 'project-featured-main.jpg' ),
		'map'     => 'https://www.google.com/maps?q=Phoenix,Arizona',
		'gallery' => array( 'project-gallery-1.jpg', 'project-gallery-2.jpg', 'project-gallery-3.jpg', 'project-gallery-4.jpg', 'project-gallery-5.jpg', 'project-gallery-6.jpg' ),
		'feature' => 'project-featured-main.jpg',
		'miles'   => array(
			array( 'title' => 'Site Remediation & Prep', 'desc' => 'Completed Q1 2022 — advanced soil stabilization for high-wind durability.' ),
			array( 'title' => 'Grid Interconnection', 'desc' => 'Completed Q3 2022 — 230kV substation integration for regional stability.' ),
			array( 'title' => 'Full Commissioning', 'desc' => 'Completed Q4 2022 — peak efficiency reached 15% above initial modeling.' ),
		),
	),
	array(
		'slug'    => 'deon-demo-coastal-grid-hybrid',
		'title'   => 'DEMO — Coastal Grid Hybrid',
		'type'    => 'C&I Captive',
		'order'   => 901,
		'content' => "<h2>Storage-firmed generation for a 24/7 industrial load.</h2>\n<p>A captive solar-plus-storage installation feeding a coastal manufacturing cluster. Placeholder description text confirms the overview section wraps, spaces and styles correctly across breakpoints.</p>\n<p>Second paragraph to check vertical rhythm inside <em>.deon-prose</em>.</p>",
		'specs'   => array(
			'_deon_capacity'      => '25.0 MW',
			'_deon_location'      => 'Kutch, Gujarat',
			'_deon_status'        => 'Under Construction',
			'_deon_commissioned'  => 'Est. Q2 2025',
			'_deon_grid_voltage'  => '66 kV',
			'_deon_annual_output' => '58 GWh',
			'_deon_modules'       => 'TOPCon N-type',
			'_deon_epc_partner'   => 'Deon Engineering',
			'_deon_client'        => 'Coastal Textiles Pvt. Ltd.',
		),
		'hero'    => array( 'type' => 'video', 'embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'poster' => 'esg-hero.png' ),
		'video'   => array( 'url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 'caption' => 'Building on the Coast', 'poster' => 'project-featured-detail-1.jpg' ),
		'map'     => 'https://www.google.com/maps?q=Kutch,Gujarat',
		'gallery' => array( 'project-1.jpg', 'project-2.jpg', 'project-3.jpg', 'project-4.jpg', 'project-featured-detail-1.jpg' ),
		'feature' => 'project-featured-detail-1.jpg',
		'miles'   => array(
			array( 'title' => 'Financial Close', 'desc' => 'Placeholder milestone one.' ),
			array( 'title' => 'Module Delivery', 'desc' => 'Placeholder milestone two.' ),
		),
	),
	array(
		'slug'    => 'deon-demo-highland-plains',
		'title'   => 'DEMO — Highland Plains (minimal hero)',
		'type'    => 'Ground Mount',
		'order'   => 902,
		'content' => "<h2>Testing the plain hero + partial fields.</h2>\n<p>This demo intentionally leaves the hero background on <strong>None</strong> and omits the map, to confirm the plain colour hero still renders and that the map section auto-hides when empty.</p>",
		'specs'   => array(
			'_deon_capacity' => '10.0 MW',
			'_deon_location' => 'Nashik, Maharashtra',
			'_deon_status'   => 'Operational',
			'_deon_modules'  => 'Mono-PERC',
		),
		'hero'    => array( 'type' => 'none' ),
		'video'   => array( 'url' => 'https://vimeo.com/76979871', 'caption' => 'Plains in Motion', 'poster' => '' ),
		'map'     => '', // omitted — map section should not appear.
		'gallery' => array( 'project-gallery-1.jpg', 'project-gallery-3.jpg', 'project-gallery-5.jpg' ),
		'feature' => 'project-2.jpg',
		'miles'   => array(),
	),
);

// ── Remove mode ───────────────────────────────────────────────────────────────
if ( $remove ) {
	foreach ( $demos as $d ) {
		$wipe_demo( $d['slug'] );
	}
	WP_CLI::success( 'Demo projects removed.' );
	return;
}

// ── Create / refresh ──────────────────────────────────────────────────────────
foreach ( $demos as $d ) {
	$wipe_demo( $d['slug'] );

	$post_id = wp_insert_post( array(
		'post_title'   => $d['title'],
		'post_name'    => $d['slug'],
		'post_content' => $d['content'],
		'post_type'    => 'deon_project',
		'post_status'  => 'publish',
		'menu_order'   => $d['order'],
	), true );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed to insert {$d['title']}: " . $post_id->get_error_message() );
		continue;
	}

	// Type term.
	$tid = $term_id_for( $d['type'] );
	if ( $tid ) {
		wp_set_object_terms( $post_id, array( $tid ), 'deon_project_type' );
	}

	// Spec meta.
	foreach ( $d['specs'] as $key => $val ) {
		update_post_meta( $post_id, $key, $val );
	}

	// Featured image.
	$feat = $sideload( $img( $d['feature'] ), $post_id, $d['title'] . ' — featured' );
	if ( ! is_wp_error( $feat ) ) {
		set_post_thumbnail( $post_id, $feat );
	}

	// Gallery.
	$gallery_ids = array();
	foreach ( $d['gallery'] as $g ) {
		$gid = $sideload( $img( $g ), $post_id, $d['title'] . ' — gallery' );
		if ( ! is_wp_error( $gid ) ) {
			$gallery_ids[] = $gid;
		}
	}
	if ( $gallery_ids ) {
		update_post_meta( $post_id, '_deon_gallery', implode( ',', $gallery_ids ) );
	}

	// Hero background.
	$hero = $d['hero'];
	update_post_meta( $post_id, '_deon_hero_bg_type', $hero['type'] );
	if ( 'image' === $hero['type'] ) {
		$hb = $sideload( $img( $hero['image'] ), $post_id, $d['title'] . ' — hero bg' );
		if ( ! is_wp_error( $hb ) ) {
			update_post_meta( $post_id, '_deon_hero_bg_image', wp_get_attachment_url( $hb ) );
		}
	} elseif ( 'video' === $hero['type'] ) {
		update_post_meta( $post_id, '_deon_hero_bg_video_source', 'embed' );
		update_post_meta( $post_id, '_deon_hero_bg_video_embed', $hero['embed'] );
		if ( ! empty( $hero['poster'] ) ) {
			$hp = $sideload( $img( $hero['poster'] ), $post_id, $d['title'] . ' — hero poster' );
			if ( ! is_wp_error( $hp ) ) {
				update_post_meta( $post_id, '_deon_hero_bg_video_poster', wp_get_attachment_url( $hp ) );
			}
		}
	}

	// Story video.
	update_post_meta( $post_id, '_deon_video_url', $d['video']['url'] );
	update_post_meta( $post_id, '_deon_video_caption', $d['video']['caption'] );
	if ( ! empty( $d['video']['poster'] ) ) {
		$vp = $sideload( $img( $d['video']['poster'] ), $post_id, $d['title'] . ' — video poster' );
		if ( ! is_wp_error( $vp ) ) {
			update_post_meta( $post_id, '_deon_video_poster', wp_get_attachment_url( $vp ) );
		}
	}

	// Map.
	if ( '' !== $d['map'] ) {
		update_post_meta( $post_id, '_deon_map_embed', $d['map'] );
	}

	// Milestones.
	if ( ! empty( $d['miles'] ) ) {
		update_post_meta( $post_id, '_deon_milestones', $d['miles'] );
	}

	WP_CLI::success( "#{$post_id} — {$d['title']} (hero: {$hero['type']}, gallery: " . count( $gallery_ids ) . ", milestones: " . count( $d['miles'] ) . ')' );
}

WP_CLI::success( 'Demo projects seeded — ' . count( $demos ) . ' total. Remove later with: wp eval-file bin/seed-demo-projects.php remove' );
