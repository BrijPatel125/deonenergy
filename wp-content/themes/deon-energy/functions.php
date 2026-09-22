<?php
/**
 * Deon Energy theme functions.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'DEON_VERSION', '0.9.0' );

/**
 * Cache-busting version for a theme asset. Returns the file's modification
 * time so every upload auto-invalidates browser + CDN cache. Falls back to
 * DEON_VERSION when the file can't be found.
 *
 * @param string $rel_path Path relative to theme root, e.g. '/assets/css/main.css'.
 * @return string
 */
function deon_asset_version( $rel_path ) {
	$full = DEON_DIR . $rel_path;
	if ( file_exists( $full ) ) {
		return (string) filemtime( $full );
	}
	return DEON_VERSION;
}
define( 'DEON_DIR', get_template_directory() );
define( 'DEON_URI', get_template_directory_uri() );

/**
 * Feature modules (redesign v1). Each is self-contained; templates guard with
 * function_exists() so a missing module degrades rather than fatals.
 */
require_once DEON_DIR . '/inc/projects.php';     // Portfolio stat aggregation.
require_once DEON_DIR . '/inc/leader-meta.php';  // Leader "Casual Photo" meta (flip card back).
require_once DEON_DIR . '/inc/news-media.php';   // News & Media doc terms + card meta.
require_once DEON_DIR . '/inc/project-filters.php'; // ?type= gallery filtering.
require_once DEON_DIR . '/inc/investor-documents.php'; // Dedicated Investor Documents CPT + categories.
require_once DEON_DIR . '/inc/investor-videos.php';    // Investor Videos CPT (URL or MP4) + gallery helpers.
require_once DEON_DIR . '/inc/site-stats.php';       // Editable stat bands (Customizer → Site Statistics).
require_once DEON_DIR . '/inc/project-detail.php';   // Project detail: hero media, video, map, milestones, related.
require_once DEON_DIR . '/inc/hidden-sections.php';  // Admin notices for CPTs whose front-end section is switched off.
require_once DEON_DIR . '/inc/pagination.php';       // Shared .deon-pager control used by every paged listing.
require_once DEON_DIR . '/inc/term-hero.php';        // Per-category hero background (term meta).
require_once DEON_DIR . '/inc/applications.php';     // Careers: job application intake + wp-admin management.
require_once DEON_DIR . '/inc/certifications.php';   // About page: client-managed Certifications & Compliance badges.
require_once DEON_DIR . '/inc/seo.php';              // Phase 1 SEO: meta description, OG, Twitter, canonical, JSON-LD schema.
require_once DEON_DIR . '/inc/seo-ai.php';           // Phase 3 SEO: llms.txt, robots.txt, speakable, HowTo, knowsAbout — AI/LLM ranking (GEO/AEO).
require_once DEON_DIR . '/inc/analytics.php';        // GA4, GTM, Meta Pixel, LinkedIn Insight Tag, Microsoft Clarity — Customizer-driven.
require_once DEON_DIR . '/inc/cookie-consent.php';   // IT Act / GDPR consent banner — gates analytics tags.
require_once DEON_DIR . '/inc/legal-disclaimers.php'; // Forward-looking + IPO + general disclaimers, auto-injected on relevant pages.
require_once DEON_DIR . '/inc/case-studies.php';     // Case Study CPT for named client work with structured meta fields.

/**
 * Theme setup: supports, menus, image sizes.
 */
function deon_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'deon-energy' ),
			'footer'  => __( 'Footer Menu', 'deon-energy' ),
		)
	);
}
add_action( 'after_setup_theme', 'deon_setup' );

/**
 * Sanitize a phone number down to digits only (drops +, spaces, dashes).
 */
function deon_sanitize_phone_digits( $value ) {
	return preg_replace( '/\D+/', '', (string) $value );
}

/**
 * Theme Customizer — Social Links panel.
 */
function deon_customizer( $wp_customize ) {
	$wp_customize->add_section( 'deon_social', array(
		'title'    => __( 'Social Links', 'deon-energy' ),
		'priority' => 30,
	) );

	// key => [ control label, default URL ]. Defaults are the live channels, so
	// the fields show the current links and the footer renders them out of the
	// box; clearing a field hides that icon (footer.php).
	$platforms = array(
		'facebook'  => array( __( 'Facebook URL', 'deon-energy' ), 'https://www.facebook.com/deonenergylimited' ),
		'instagram' => array( __( 'Instagram URL', 'deon-energy' ), 'https://www.instagram.com/deon_energy.in/' ),
		'linkedin'  => array( __( 'LinkedIn URL', 'deon-energy' ), 'https://www.linkedin.com/company/deon-energy/' ),
		'youtube'   => array( __( 'YouTube URL', 'deon-energy' ), 'https://www.youtube.com/@deonenergy/videos' ),
	);

	foreach ( $platforms as $key => $meta ) {
		$wp_customize->add_setting( "deon_social_{$key}", array(
			'default'           => $meta[1],
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( "deon_social_{$key}", array(
			'label'   => $meta[0],
			'section' => 'deon_social',
			'type'    => 'url',
		) );
	}

	// WhatsApp number — powers the floating WhatsApp button (footer.php) and the
	// "WhatsApp us" footer link. Enter in international format, digits only, no
	// "+" or spaces (e.g. 918008905933). Clearing it hides the floating button.
	$wp_customize->add_setting( 'deon_whatsapp_number', array(
		'default'           => '18008905933',
		'sanitize_callback' => 'deon_sanitize_phone_digits',
	) );
	$wp_customize->add_control( 'deon_whatsapp_number', array(
		'label'       => __( 'WhatsApp number', 'deon-energy' ),
		'description' => __( 'Digits only, international format, no "+" or spaces (e.g. 918008905933). Powers the floating WhatsApp button. Leave blank to hide it.', 'deon-energy' ),
		'section'     => 'deon_social',
		'type'        => 'text',
	) );

	// Careers — application intake. By default every job detail page renders the
	// theme's own form (template-parts/job/apply-form.php), which files each
	// submission under wp-admin → Applications and emails the recipient below.
	// A form-plugin shortcode here overrides it, for clients running intake
	// through their own tooling instead.
	$wp_customize->add_section( 'deon_careers', array(
		'title'    => __( 'Careers', 'deon-energy' ),
		'priority' => 31,
	) );
	$wp_customize->add_setting( 'deon_apply_recipient', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'deon_apply_recipient', array(
		'label'       => __( 'Application notification email', 'deon-energy' ),
		'description' => __( 'Where new job applications are emailed (résumé attached). Blank falls back to the contact-form recipient, then the site admin email. Every application is stored under Applications in wp-admin regardless.', 'deon-energy' ),
		'section'     => 'deon_careers',
		'type'        => 'email',
	) );
	$wp_customize->add_setting( 'deon_apply_form_shortcode', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'deon_apply_form_shortcode', array(
		'label'       => __( 'Application form shortcode (optional)', 'deon-energy' ),
		'description' => __( 'e.g. [contact-form-7 id="123" title="Job Application"]. Only set this to replace the built-in form with a plugin — submissions then live in that plugin, not under Applications.', 'deon-energy' ),
		'section'     => 'deon_careers',
		'type'        => 'textarea',
	) );

	// Homepage Hero — the orange capacity badge floating in the hero.
	$wp_customize->add_section( 'deon_hero', array(
		'title'    => __( 'Homepage Hero', 'deon-energy' ),
		'priority' => 32,
	) );
	$wp_customize->add_setting( 'deon_hero_stat_value', array(
		'default'           => '450MW+',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'deon_hero_stat_value', array(
		'label'   => __( 'Capacity badge — value', 'deon-energy' ),
		'section' => 'deon_hero',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'deon_hero_stat_label', array(
		'default'           => __( 'Installed Capacity', 'deon-energy' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'deon_hero_stat_label', array(
		'label'   => __( 'Capacity badge — label', 'deon-energy' ),
		'section' => 'deon_hero',
		'type'    => 'text',
	) );

	// -------------------------------------------------------------------------
	// Site Images — every editable page-body image the client can swap without
	// touching files. The template still hardcodes a default filename in
	// /assets/img/ that ships with the theme, so if a setting is empty (fresh
	// install or the client cleared it) the page still renders the shipped
	// image. Setting an image in the Customizer overrides that default.
	// -------------------------------------------------------------------------
	$wp_customize->add_section( 'deon_site_images', array(
		'title'       => __( 'Site Images', 'deon-energy' ),
		'priority'    => 33,
		'description' => __( 'Change page images without editing files. Each field replaces one image on the site — leave blank to use the shipped default.', 'deon-energy' ),
	) );

	$deon_image_fields = array(
		'deon_img_about_hero'      => __( 'About page — main image (worker on solar panels)', 'deon-energy' ),
		'deon_img_solutions_grid'  => __( 'Solutions page — grid integration image (monitor screens)', 'deon-energy' ),
		'deon_img_esg_pillar_1'    => __( 'ESG page — pillar 1 image (left of the three)', 'deon-energy' ),
		'deon_img_esg_pillar_2'    => __( 'ESG page — pillar 2 image (middle of the three)', 'deon-energy' ),
		'deon_img_esg_pillar_3'    => __( 'ESG page — pillar 3 image (right of the three)', 'deon-energy' ),
		'deon_img_esg_community_1' => __( 'ESG Community & People — left image (workers)', 'deon-energy' ),
		'deon_img_esg_community_2' => __( 'ESG Community & People — right image (office)', 'deon-energy' ),
	);
	foreach ( $deon_image_fields as $deon_setting_id => $deon_label ) {
		$wp_customize->add_setting( $deon_setting_id, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$deon_setting_id,
				array(
					'label'   => $deon_label,
					'section' => 'deon_site_images',
				)
			)
		);
	}
}
add_action( 'customize_register', 'deon_customizer' );

/**
 * Return a Customizer-overridable image URL.
 *
 * Look up the Customizer setting first; if the client has uploaded a
 * replacement image in Appearance → Customize → Site Images, use that.
 * Otherwise fall back to the shipped file under /assets/img/. Keeps every
 * template one-liner and means changing an image never touches PHP.
 *
 * @param string $setting_id      Customizer setting slug (e.g. 'deon_img_about_hero').
 * @param string $default_filename Fallback filename under /assets/img/.
 * @return string Absolute URL to the image to render.
 */
function deon_site_image_url( $setting_id, $default_filename ) {
	$custom = get_theme_mod( $setting_id, '' );
	if ( is_string( $custom ) && '' !== $custom ) {
		return esc_url( $custom );
	}
	return esc_url( get_template_directory_uri() . '/assets/img/' . $default_filename );
}

/**
 * Enqueue styles and scripts.
 */
function deon_assets() {
	// Phase 2 SEO: serve minified CSS/JS in production. WP_DEBUG on = raw files
	// (easier to inspect); WP_DEBUG off = .min files (smaller = faster).
	// If a .min file is missing (e.g. dev environment), fall back to the raw one.
	$deon_min = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
	if ( '.min' === $deon_min && ! file_exists( DEON_DIR . '/assets/css/main.min.css' ) ) {
		$deon_min = '';
	}

	wp_enqueue_style(
		'deon-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'deon-main', DEON_URI . '/assets/css/main' . $deon_min . '.css', array( 'deon-fonts' ), deon_asset_version( '/assets/css/main' . $deon_min . '.css' ) );

	// Phase 2 pages use Tailwind v4. main.css stays loaded for shared header/footer styles.
	$is_legacy_page = is_front_page() || is_page( array( 'about', 'solutions' ) );
	if ( ! $is_legacy_page && file_exists( DEON_DIR . '/assets/css/tailwind.dist.css' ) ) {
		wp_enqueue_style( 'deon-tailwind', DEON_URI . '/assets/css/tailwind.dist.css', array( 'deon-main' ), deon_asset_version( '/assets/css/tailwind.dist.css' ) );
	}

	wp_enqueue_script( 'deon-main', DEON_URI . '/assets/js/main' . $deon_min . '.js', array(), deon_asset_version( '/assets/js/main' . $deon_min . '.js' ), true );

	// Newsletter subscribe: give main.js the admin-ajax endpoint + a nonce, but
	// only on the two templates that render a form. The server response is the
	// source of truth for success/exists/error copy; JS only needs the pre-request
	// validation string plus a generic network-failure fallback.
	if ( is_page_template( 'page-contact.php' ) || is_page_template( 'page-technical-papers.php' ) ) {
		wp_localize_script( 'deon-main', 'deonNewsletter', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'deon_newsletter' ),
			'i18n'    => array(
				'invalid' => __( 'Please enter a valid email address.', 'deon-energy' ),
				'error'   => __( 'Something went wrong. Please try again.', 'deon-energy' ),
			),
		) );
	}

	// Solar calculator: interactive estimation model on the calculator template.
	if ( is_page_template( 'page-calculator.php' ) || is_page( 'calculator' ) ) {
		wp_enqueue_script( 'deon-calc', DEON_URI . '/assets/js/calc' . $deon_min . '.js', array(), deon_asset_version( '/assets/js/calc' . $deon_min . '.js' ), true );
	}

	// Project detail: gallery lightbox on single project pages.
	if ( is_singular( 'deon_project' ) ) {
		wp_enqueue_script( 'deon-project-gallery', DEON_URI . '/assets/js/project-gallery' . $deon_min . '.js', array(), deon_asset_version( '/assets/js/project-gallery' . $deon_min . '.js' ), true );
	}

	// Swiper: load on front page whenever ACF is active (hero type may be slider).
	if ( is_front_page() && function_exists( 'get_field' ) ) {
		wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );
		wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );
		wp_enqueue_script( 'deon-hero-slider', DEON_URI . '/assets/js/hero-slider.js', array( 'swiper' ), deon_asset_version( '/assets/js/hero-slider.js' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'deon_assets' );

/**
 * Flag <html> as JS-capable before first paint.
 *
 * The preloader curtain and nav progress bar (header.php) are display:none by
 * default and only unhide under `.deon-js`. Without JS nothing renders them,
 * and without this flag firing *before* the body paints the curtain would flash
 * in after the fact. Kept out of the templates per the no-inline-script rule —
 * it lives here and prints through wp_head at priority 1.
 */
function deon_js_flag() {
	wp_print_inline_script_tag( "document.documentElement.className+=' deon-js';" );
}
add_action( 'wp_head', 'deon_js_flag', 1 );

/**
 * Site favicons / app icons — output into <head> via wp_head().
 *
 * Package generated by realfavicongenerator; files bundled in the theme
 * (assets/favicon/) rather than the web root, so paths are theme-relative.
 * The apple-mobile-web-app-title stays static ("Deon Energy") to match the
 * shipped site.webmanifest.
 */
function deon_favicons() {
	$base = DEON_URI . '/assets/favicon';
	printf( '<link rel="icon" type="image/png" href="%s/favicon-96x96.png" sizes="96x96">' . "\n", esc_url( $base ) );
	printf( '<link rel="icon" type="image/svg+xml" href="%s/favicon.svg">' . "\n", esc_url( $base ) );
	printf( '<link rel="shortcut icon" href="%s/favicon.ico">' . "\n", esc_url( $base ) );
	printf( '<link rel="apple-touch-icon" sizes="180x180" href="%s/apple-touch-icon.png">' . "\n", esc_url( $base ) );
	echo '<meta name="apple-mobile-web-app-title" content="Deon Energy">' . "\n";
	printf( '<link rel="manifest" href="%s/site.webmanifest">' . "\n", esc_url( $base ) );
}
add_action( 'wp_head', 'deon_favicons' );

/**
 * Preconnect to Google Fonts hosts for faster font loading.
 */
function deon_resource_hints( $hints, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$hints[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'deon_resource_hints', 10, 2 );

/**
 * Custom Post Type: Project + Taxonomy: Project Type.
 */
function deon_register_project_cpt() {
	register_post_type( 'deon_project', array(
		'labels'        => array(
			'name'          => __( 'Projects', 'deon-energy' ),
			'singular_name' => __( 'Project', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Project', 'deon-energy' ),
			'edit_item'     => __( 'Edit Project', 'deon-energy' ),
		),
		'public'        => true,
		'has_archive'   => false,
		'menu_icon'     => 'dashicons-portfolio',
		'supports'      => array( 'title', 'thumbnail', 'excerpt', 'editor', 'page-attributes' ),
		'show_in_rest'  => true,
		'rewrite'       => array( 'slug' => 'project' ),
	) );

	register_taxonomy( 'deon_project_type', 'deon_project', array(
		'labels'        => array(
			'name'          => __( 'Project Types', 'deon-energy' ),
			'singular_name' => __( 'Project Type', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Type', 'deon-energy' ),
		),
		'hierarchical'  => false,
		'public'        => true,
		'show_in_rest'  => true,
		'rewrite'       => array( 'slug' => 'project-type' ),
	) );
}
add_action( 'init', 'deon_register_project_cpt' );

/**
 * Spec fields for a project. Key => [ label, placeholder ]. Rendered in the
 * "Project Details" meta box and, when filled, as the spec list on the single
 * project detail page ( single-deon_project.php ). ACF-free — plain post meta.
 */
function deon_project_spec_fields() {
	return array(
		'_deon_capacity'      => array( __( 'Capacity', 'deon-energy' ),        'e.g. 26 MW' ),
		'_deon_location'      => array( __( 'Location', 'deon-energy' ),        'e.g. Soladi, Dhangadhra, Gujarat' ),
		'_deon_status'        => array( __( 'Project Status', 'deon-energy' ),  'e.g. Operational' ),
		'_deon_commissioned'  => array( __( 'Commissioned', 'deon-energy' ),    'e.g. Q3 2023' ),
		'_deon_grid_voltage'  => array( __( 'Grid Voltage', 'deon-energy' ),    'e.g. 132 kV' ),
		'_deon_annual_output' => array( __( 'Annual Output', 'deon-energy' ),   'e.g. 42 GWh' ),
		'_deon_modules'       => array( __( 'Technology / Modules', 'deon-energy' ), 'e.g. Bifacial mono-PERC' ),
		'_deon_epc_partner'   => array( __( 'EPC Partner', 'deon-energy' ),     'e.g. Deon Engineering' ),
		'_deon_client'        => array( __( 'Client / Owner', 'deon-energy' ),  'e.g. Deon Energy Ltd.' ),
	);
}

/**
 * Meta boxes: Project Details (specs) + Project Gallery for deon_project.
 */
function deon_project_meta_box_register() {
	add_meta_box(
		'deon_project_details',
		__( 'Project Details', 'deon-energy' ),
		'deon_project_meta_box_html',
		'deon_project',
		'side'
	);
	add_meta_box(
		'deon_project_gallery',
		__( 'Project Gallery', 'deon-energy' ),
		'deon_project_gallery_meta_box_html',
		'deon_project',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'deon_project_meta_box_register' );

function deon_project_meta_box_html( $post ) {
	wp_nonce_field( 'deon_project_save', 'deon_project_nonce' );
	?>
	<p style="margin:0 0 12px;padding:0 0 12px;border-bottom:1px solid #dcdcde;">
		<label for="_deon_home_featured">
			<input type="checkbox" id="_deon_home_featured" name="_deon_home_featured" value="1" <?php checked( get_post_meta( $post->ID, '_deon_home_featured', true ), '1' ); ?>>
			<strong><?php esc_html_e( 'Show on homepage', 'deon-energy' ); ?></strong>
		</label><br>
		<span style="color:#646970;"><?php esc_html_e( 'Adds this project to the "Our Work Speaks for Itself" grid (4 tiles). Order them with the Order field under Page Attributes.', 'deon-energy' ); ?></span>
	</p>
	<?php
	echo '<p style="color:#646970;margin-top:0;">' . esc_html__( 'Only filled rows appear on the detail page. Write the project description in the main editor above.', 'deon-energy' ) . '</p>';
	foreach ( deon_project_spec_fields() as $key => $field ) {
		$val = get_post_meta( $post->ID, $key, true );
		printf(
			'<p style="margin:0 0 12px;"><label for="%1$s"><strong>%2$s</strong></label><br><input type="text" id="%1$s" name="%1$s" value="%3$s" placeholder="%4$s" style="width:100%%;margin-top:4px;"></p>',
			esc_attr( $key ),
			esc_html( $field[0] ),
			esc_attr( $val ),
			esc_attr( $field[1] )
		);
	}
}

/**
 * Gallery meta box — stores selected attachment IDs as a CSV in _deon_gallery.
 * The picker JS ( deon_admin_media_picker_enqueue ) wires the buttons below.
 */
function deon_project_gallery_meta_box_html( $post ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post->ID, '_deon_gallery', true ) ) ) );
	?>
	<p style="color:#646970;margin-top:0;"><?php esc_html_e( 'Add photos of the plant. Drag thumbnails to reorder. These appear in the gallery grid with a click-to-zoom lightbox.', 'deon-energy' ); ?></p>
	<div class="deon-gallery-field">
		<ul class="deon-gallery-list" data-target="deon_project_gallery_ids">
			<?php foreach ( $ids as $id ) :
				$thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
				if ( ! $thumb ) continue; ?>
				<li data-id="<?php echo esc_attr( $id ); ?>">
					<img src="<?php echo esc_url( $thumb ); ?>" alt="">
					<button type="button" class="deon-gallery-remove" aria-label="<?php esc_attr_e( 'Remove', 'deon-energy' ); ?>">&times;</button>
				</li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" id="deon_project_gallery_ids" name="deon_project_gallery" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
		<button type="button" class="button button-primary deon-gallery-add" data-target="deon_project_gallery_ids"><?php esc_html_e( 'Add / Edit Images', 'deon-energy' ); ?></button>
	</div>
	<?php
}

function deon_project_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_project_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_project_nonce'] ) ), 'deon_project_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	foreach ( array_keys( deon_project_spec_fields() ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
	if ( isset( $_POST['deon_project_gallery'] ) ) {
		$ids = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['deon_project_gallery'] ) ) ) ) );
		update_post_meta( $post_id, '_deon_gallery', implode( ',', $ids ) );
	}

	// Homepage "Selected Work" flag — unchecked boxes post nothing, so delete.
	if ( isset( $_POST['_deon_home_featured'] ) ) {
		update_post_meta( $post_id, '_deon_home_featured', '1' );
	} else {
		delete_post_meta( $post_id, '_deon_home_featured' );
	}
}
add_action( 'save_post_deon_project', 'deon_project_meta_save' );

/**
 * Return the ordered gallery attachment IDs for a project.
 */
function deon_project_gallery_ids( $post_id ) {
	return array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post_id, '_deon_gallery', true ) ) ) );
}

/**
 * Custom Post Type: Leader (Leadership page — founders + core team).
 * ACF-free (options pages / repeaters are ACF PRO only), matching the
 * deon_project / deon_office pattern. A "Display Type" select routes each
 * leader to either the large Founder Feature layout or the Core Team grid.
 */
function deon_register_leader_cpt() {
	register_post_type( 'deon_leader', array(
		'labels'        => array(
			'name'          => __( 'Leaders', 'deon-energy' ),
			'singular_name' => __( 'Leader', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Leader', 'deon-energy' ),
			'edit_item'     => __( 'Edit Leader', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 21,
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ), // title = name; thumbnail = photo; page-attributes = menu_order for drag-sort.
		'hierarchical'  => false,
	) );
}
add_action( 'init', 'deon_register_leader_cpt' );

/**
 * Display-type options for a leader.
 */
function deon_leader_types() {
	return array(
		'founder' => __( 'Founder — large feature block', 'deon-energy' ),
		'board'   => __( 'Board of Directors — flip card', 'deon-energy' ),
		'team'    => __( 'Core Team — flip card', 'deon-energy' ),
	);
}

/**
 * Meta box: role, quote, bio, links for deon_leader.
 */
function deon_leader_meta_box_register() {
	add_meta_box(
		'deon_leader_details',
		__( 'Leader Details', 'deon-energy' ),
		'deon_leader_meta_box_html',
		'deon_leader',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'deon_leader_meta_box_register' );

function deon_leader_meta_box_html( $post ) {
	wp_nonce_field( 'deon_leader_save', 'deon_leader_nonce' );
	$type     = get_post_meta( $post->ID, '_deon_leader_type', true );
	$type     = $type ? $type : 'team';
	$role     = get_post_meta( $post->ID, '_deon_leader_role', true );
	$quote    = get_post_meta( $post->ID, '_deon_leader_quote', true );
	$bio      = get_post_meta( $post->ID, '_deon_leader_bio', true );
	$lnk_lbl  = get_post_meta( $post->ID, '_deon_leader_link_label', true );
	$lnk_url  = get_post_meta( $post->ID, '_deon_leader_link_url', true );
	$linkedin = get_post_meta( $post->ID, '_deon_leader_linkedin', true );
	$x_url    = get_post_meta( $post->ID, '_deon_leader_x', true );
	$profile  = get_post_meta( $post->ID, '_deon_leader_profile', true );
	$casual   = get_post_meta( $post->ID, '_deon_leader_casual', true );
	$types    = deon_leader_types();

	// Field group visibility keys, toggled live by the Display Type select below.
	$grp = 'style="margin:0 0 4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#8c8f94;padding-top:14px;border-top:1px solid #e0e0e0;"';
	?>
	<style>
		.deon-leader-fields p { margin: 12px 0; }
		.deon-leader-fields label strong { font-size: 13px; }
		.deon-leader-fields .desc { display:block; color:#646970; margin-top:3px; font-size:12px; }
		.deon-leader-fields input[type=text], .deon-leader-fields input[type=url], .deon-leader-fields textarea { width:100%; margin-top:4px; }
		.deon-leader-fields [data-show] { display:none; }
		.deon-leader-thumb { display:block; max-width:120px; height:auto; margin:6px 0; border:1px solid #dcdcde; }
	</style>

	<div class="deon-leader-fields">

		<!-- 1) Category — decides which fields below apply -->
		<p>
			<label for="deon_leader_type"><strong><?php esc_html_e( '1. Category — where does this person appear?', 'deon-energy' ); ?></strong></label><br>
			<select id="deon_leader_type" name="deon_leader_type" data-leader-type style="margin-top:4px;max-width:100%;">
				<?php foreach ( $types as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $type, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="desc"><?php esc_html_e( 'Founder = big feature block at the top. Board / Core Team = a flip card in that grid. The fields below change to match your choice.', 'deon-energy' ); ?></span>
		</p>

		<!-- 2) Basics — every category -->
		<p <?php echo $grp; // phpcs:ignore ?>><?php esc_html_e( 'Basics', 'deon-energy' ); ?></p>
		<p>
			<label><strong><?php esc_html_e( 'Photo', 'deon-energy' ); ?></strong></label>
			<span class="desc"><?php esc_html_e( 'Use the Featured Image box (top-right) to set this person’s photo. Displayed in black & white.', 'deon-energy' ); ?></span>
		</p>
		<p>
			<label for="deon_leader_role"><strong><?php esc_html_e( 'Role', 'deon-energy' ); ?></strong></label><br>
			<input type="text" id="deon_leader_role" name="deon_leader_role" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'e.g. Co-Founder & CEO / Independent Director', 'deon-energy' ); ?>">
		</p>
		<p>
			<label for="deon_leader_linkedin"><strong><?php esc_html_e( 'LinkedIn URL', 'deon-energy' ); ?></strong></label><br>
			<input type="url" id="deon_leader_linkedin" name="deon_leader_linkedin" value="<?php echo esc_attr( $linkedin ); ?>" placeholder="https://www.linkedin.com/in/name/">
			<span class="desc"><?php esc_html_e( 'Founder: adds a “Connect on LinkedIn” link. Board / Core Team: shows a small LinkedIn badge on the card. Leave blank for none.', 'deon-energy' ); ?></span>
		</p>

		<!-- 3) Founder-only -->
		<div data-show="founder">
			<p <?php echo $grp; // phpcs:ignore ?>><?php esc_html_e( 'Founder feature — extra fields', 'deon-energy' ); ?></p>
			<p>
				<label for="deon_leader_bio"><strong><?php esc_html_e( 'Bio', 'deon-energy' ); ?></strong></label><br>
				<textarea id="deon_leader_bio" name="deon_leader_bio" rows="3" placeholder="<?php esc_attr_e( 'The supporting paragraph shown in the founder feature block.', 'deon-energy' ); ?>"><?php echo esc_textarea( $bio ); ?></textarea>
			</p>
			<p>
				<label for="deon_leader_quote"><strong><?php esc_html_e( 'Quote', 'deon-energy' ); ?></strong></label><br>
				<textarea id="deon_leader_quote" name="deon_leader_quote" rows="3" placeholder="<?php esc_attr_e( 'The large headline quote in the feature block.', 'deon-energy' ); ?>"><?php echo esc_textarea( $quote ); ?></textarea>
			</p>
			<p>
				<label for="deon_leader_link_label"><strong><?php esc_html_e( 'Custom button — label', 'deon-energy' ); ?></strong></label><br>
				<input type="text" id="deon_leader_link_label" name="deon_leader_link_label" value="<?php echo esc_attr( $lnk_lbl ); ?>" placeholder="<?php esc_attr_e( 'e.g. Read Philosophy', 'deon-energy' ); ?>">
			</p>
			<p>
				<label for="deon_leader_link_url"><strong><?php esc_html_e( 'Custom button — URL', 'deon-energy' ); ?></strong></label><br>
				<input type="url" id="deon_leader_link_url" name="deon_leader_link_url" value="<?php echo esc_attr( $lnk_url ); ?>" placeholder="https://…">
				<span class="desc"><?php esc_html_e( 'The button only appears when a URL is set. Point it at a page, blog post, or an uploaded PDF (copy its URL from the Media Library).', 'deon-energy' ); ?></span>
			</p>
			<p>
				<label for="deon_leader_x"><strong><?php esc_html_e( 'X / Twitter URL', 'deon-energy' ); ?></strong></label><br>
				<input type="url" id="deon_leader_x" name="deon_leader_x" value="<?php echo esc_attr( $x_url ); ?>" placeholder="https://x.com/name/">
			</p>
			<p>
				<label for="deon_leader_profile"><strong><?php esc_html_e( 'Download Profile (PDF)', 'deon-energy' ); ?></strong></label><br>
				<input type="url" id="deon_leader_profile" name="deon_leader_profile" value="<?php echo esc_attr( $profile ); ?>" style="width:calc(100% - 130px);" placeholder="<?php esc_attr_e( 'Upload or paste a PDF URL', 'deon-energy' ); ?>">
				<button type="button" class="button deon-media-pick" data-target="deon_leader_profile" style="margin-top:4px;"><?php esc_html_e( 'Select file', 'deon-energy' ); ?></button>
				<span class="desc"><?php esc_html_e( 'Adds a “Download Profile” link. Leave empty to hide it.', 'deon-energy' ); ?></span>
			</p>
		</div>

		<!-- 4) Board / Core Team-only: second (reserved) photo -->
		<div data-show="board team">
			<p <?php echo $grp; // phpcs:ignore ?>><?php esc_html_e( 'Second photo (reserved)', 'deon-energy' ); ?></p>
			<p>
				<label for="deon_leader_casual"><strong><?php esc_html_e( 'Second Photo (optional, reserved)', 'deon-energy' ); ?></strong></label><br>
				<?php if ( $casual ) : ?>
					<img src="<?php echo esc_url( $casual ); ?>" alt="" class="deon-leader-thumb">
				<?php endif; ?>
				<input type="url" id="deon_leader_casual" name="deon_leader_casual" value="<?php echo esc_attr( $casual ); ?>" style="width:calc(100% - 130px);" placeholder="<?php esc_attr_e( 'Upload or paste an image URL', 'deon-energy' ); ?>">
				<button type="button" class="button deon-media-pick" data-target="deon_leader_casual" style="margin-top:4px;"><?php esc_html_e( 'Select image', 'deon-energy' ); ?></button>
				<span class="desc"><strong><?php esc_html_e( 'Note:', 'deon-energy' ); ?></strong> <?php esc_html_e( 'The card flip effect has been removed — this photo is not shown on the site right now. The field is kept so a second image can be saved for a possible future treatment. Safe to leave blank.', 'deon-energy' ); ?></span>
			</p>
		</div>

		<p class="desc" style="border-top:1px solid #e0e0e0;padding-top:12px;">
			<?php esc_html_e( 'Tip: set the order people appear with the Order field in the Page Attributes box (lower number = first).', 'deon-energy' ); ?>
		</p>
	</div>

	<script>
	( function () {
		var box = document.querySelector( '.deon-leader-fields' );
		if ( ! box ) { return; }
		var sel = box.querySelector( '[data-leader-type]' );
		function sync() {
			var val = sel.value;
			box.querySelectorAll( '[data-show]' ).forEach( function ( el ) {
				var types = el.getAttribute( 'data-show' ).split( ' ' );
				// Explicit 'block' (not '') — else the [data-show]{display:none} CSS rule wins.
				el.style.display = types.indexOf( val ) !== -1 ? 'block' : 'none';
			} );
		}
		sel.addEventListener( 'change', sync );
		sync();
	} )();
	</script>
	<?php
}

function deon_leader_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_leader_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_leader_nonce'] ) ), 'deon_leader_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_leader_type'] ) ) {
		$type = sanitize_key( wp_unslash( $_POST['deon_leader_type'] ) );
		update_post_meta( $post_id, '_deon_leader_type', array_key_exists( $type, deon_leader_types() ) ? $type : 'team' );
	}
	if ( isset( $_POST['deon_leader_role'] ) ) {
		update_post_meta( $post_id, '_deon_leader_role', sanitize_text_field( wp_unslash( $_POST['deon_leader_role'] ) ) );
	}
	if ( isset( $_POST['deon_leader_quote'] ) ) {
		update_post_meta( $post_id, '_deon_leader_quote', sanitize_textarea_field( wp_unslash( $_POST['deon_leader_quote'] ) ) );
	}
	if ( isset( $_POST['deon_leader_bio'] ) ) {
		update_post_meta( $post_id, '_deon_leader_bio', sanitize_textarea_field( wp_unslash( $_POST['deon_leader_bio'] ) ) );
	}
	if ( isset( $_POST['deon_leader_link_label'] ) ) {
		update_post_meta( $post_id, '_deon_leader_link_label', sanitize_text_field( wp_unslash( $_POST['deon_leader_link_label'] ) ) );
	}
	if ( isset( $_POST['deon_leader_link_url'] ) ) {
		update_post_meta( $post_id, '_deon_leader_link_url', esc_url_raw( wp_unslash( $_POST['deon_leader_link_url'] ) ) );
	}
	if ( isset( $_POST['deon_leader_linkedin'] ) ) {
		update_post_meta( $post_id, '_deon_leader_linkedin', esc_url_raw( wp_unslash( $_POST['deon_leader_linkedin'] ) ) );
	}
	if ( isset( $_POST['deon_leader_heading'] ) ) {
		update_post_meta( $post_id, '_deon_leader_heading', sanitize_text_field( wp_unslash( $_POST['deon_leader_heading'] ) ) );
	}
	if ( isset( $_POST['deon_leader_x'] ) ) {
		update_post_meta( $post_id, '_deon_leader_x', esc_url_raw( wp_unslash( $_POST['deon_leader_x'] ) ) );
	}
	if ( isset( $_POST['deon_leader_profile'] ) ) {
		update_post_meta( $post_id, '_deon_leader_profile', esc_url_raw( wp_unslash( $_POST['deon_leader_profile'] ) ) );
	}
	// Flip / casual photo — folded into this box (was a separate side meta box).
	if ( isset( $_POST['deon_leader_casual'] ) ) {
		update_post_meta( $post_id, '_deon_leader_casual', esc_url_raw( wp_unslash( $_POST['deon_leader_casual'] ) ) );
	}
}
add_action( 'save_post_deon_leader', 'deon_leader_meta_save' );

/**
 * Leaders list screen — show Photo, Category and Role columns so the client can
 * manage the whole team at a glance instead of opening each person.
 *
 * @param array $cols Existing columns.
 * @return array
 */
function deon_leader_admin_columns( $cols ) {
	$new = array();
	foreach ( $cols as $key => $label ) {
		if ( 'title' === $key ) {
			$new['deon_leader_thumb'] = __( 'Photo', 'deon-energy' );
		}
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['deon_leader_cat']  = __( 'Category', 'deon-energy' );
			$new['deon_leader_role'] = __( 'Role', 'deon-energy' );
		}
	}
	return $new;
}
add_filter( 'manage_deon_leader_posts_columns', 'deon_leader_admin_columns' );

/**
 * Render the custom Leaders columns.
 *
 * @param string $col     Column key.
 * @param int    $post_id Leader ID.
 */
function deon_leader_admin_column_content( $col, $post_id ) {
	if ( 'deon_leader_thumb' === $col ) {
		$thumb = get_the_post_thumbnail( $post_id, array( 48, 48 ), array( 'style' => 'width:48px;height:48px;object-fit:cover;border-radius:4px;filter:grayscale(1);' ) );
		echo $thumb ? $thumb : '<span style="color:#a7aaad;">—</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail is escaped.
	} elseif ( 'deon_leader_cat' === $col ) {
		$types = deon_leader_types();
		$type  = get_post_meta( $post_id, '_deon_leader_type', true );
		echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : ( $type ? $type : '—' ) );
	} elseif ( 'deon_leader_role' === $col ) {
		$role = get_post_meta( $post_id, '_deon_leader_role', true );
		echo $role ? esc_html( $role ) : '<span style="color:#a7aaad;">—</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'manage_deon_leader_posts_custom_column', 'deon_leader_admin_column_content', 10, 2 );

/**
 * Category filter dropdown above the Leaders list.
 *
 * @param string $post_type Current screen post type.
 */
function deon_leader_admin_filter( $post_type ) {
	if ( 'deon_leader' !== $post_type ) {
		return;
	}
	$current = isset( $_GET['deon_leader_type'] ) ? sanitize_key( wp_unslash( $_GET['deon_leader_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	echo '<select name="deon_leader_type">';
	echo '<option value="">' . esc_html__( 'All categories', 'deon-energy' ) . '</option>';
	foreach ( deon_leader_types() as $key => $label ) {
		printf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $key ),
			selected( $current, $key, false ),
			esc_html( $label )
		);
	}
	echo '</select>';
}
add_action( 'restrict_manage_posts', 'deon_leader_admin_filter' );

/**
 * Apply the category filter to the Leaders list query.
 *
 * @param WP_Query $query Current admin query.
 */
function deon_leader_admin_filter_query( $query ) {
	global $pagenow;
	if ( ! is_admin() || 'edit.php' !== $pagenow || ! $query->is_main_query() ) {
		return;
	}
	if ( ( $query->get( 'post_type' ) === 'deon_leader' ) && ! empty( $_GET['deon_leader_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query->set( 'meta_query', array(
			array(
				'key'   => '_deon_leader_type',
				'value' => sanitize_key( wp_unslash( $_GET['deon_leader_type'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			),
		) );
	}
}
add_action( 'pre_get_posts', 'deon_leader_admin_filter_query' );

/**
 * Custom Post Type: FAQ (Knowledge Base — Frequently Asked Questions).
 * ACF-free, matching the deon_project / deon_leader pattern. Each FAQ is a
 * question (post title) + answer (post editor), grouped by the hierarchical
 * deon_faq_category taxonomy so the FAQ page can render one accordion block
 * per category. Sort within a category via menu_order (page-attributes).
 */
function deon_register_faq_cpt() {
	register_post_type( 'deon_faq', array(
		'labels'        => array(
			'name'          => __( 'FAQs', 'deon-energy' ),
			'singular_name' => __( 'FAQ', 'deon-energy' ),
			'add_new_item'  => __( 'Add New FAQ', 'deon-energy' ),
			'edit_item'     => __( 'Edit FAQ', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-editor-help',
		'menu_position' => 22,
		'supports'      => array( 'title', 'editor', 'page-attributes' ), // title = question; editor = answer; menu_order = sort.
		'hierarchical'  => false,
	) );

	register_taxonomy( 'deon_faq_category', 'deon_faq', array(
		'labels'        => array(
			'name'          => __( 'FAQ Categories', 'deon-energy' ),
			'singular_name' => __( 'FAQ Category', 'deon-energy' ),
			'add_new_item'  => __( 'Add New FAQ Category', 'deon-energy' ),
		),
		'hierarchical'  => true,
		'public'        => false,
		'show_ui'       => true,
		'show_admin_column' => true,
		'rewrite'       => false,
	) );
}
add_action( 'init', 'deon_register_faq_cpt' );

/**
 * Icon options for a FAQ category. Key => admin label. The key resolves to an
 * inline SVG via deon_faq_icon_svg(); the accordion header shows it beside the
 * category name. Matches the four designed categories, plus a generic fallback.
 */
function deon_faq_icons() {
	return array(
		'solar'       => __( 'Solar / Sun', 'deon-energy' ),
		'commercial'  => __( 'Commercial / Building', 'deon-energy' ),
		'investment'  => __( 'Investment / Coin', 'deon-energy' ),
		'maintenance' => __( 'Maintenance / Gear', 'deon-energy' ),
		'general'     => __( 'General / Help', 'deon-energy' ),
	);
}

/**
 * Inline SVG for a FAQ category icon key. 20x20, currentColor stroke so the
 * accent colour is inherited from the header. Returns the generic help mark
 * when the key is empty or unknown.
 */
function deon_faq_icon_svg( $key ) {
	$icons = array(
		'solar'       => '<circle cx="10" cy="10" r="3.5"/><path d="M10 1.5v2M10 16.5v2M1.5 10h2M16.5 10h2M4 4l1.4 1.4M14.6 14.6L16 16M16 4l-1.4 1.4M5.4 14.6L4 16"/>',
		'commercial'  => '<rect x="3" y="2.5" width="9" height="15"/><path d="M12 7h5v10.5H12M5.5 6h1.5M9 6h.5M5.5 9h1.5M9 9h.5M5.5 12h1.5M9 12h.5M14 10h1M14 13h1"/>',
		'investment'  => '<circle cx="10" cy="10" r="7.5"/><path d="M10 5.5v9M12.2 7.4c0-1-1-1.7-2.2-1.7S7.8 6.4 7.8 7.5s1 1.6 2.2 1.9 2.2.8 2.2 1.9-1 1.8-2.2 1.8-2.2-.7-2.2-1.7"/>',
		'maintenance' => '<circle cx="10" cy="10" r="2.5"/><path d="M10 1.8l1 2.2 2.3-.6.3 2.4 2.4.3-.6 2.3 2.2 1-2.2 1 .6 2.3-2.4.3-.3 2.4-2.3-.6-1 2.2-1-2.2-2.3.6-.3-2.4-2.4-.3.6-2.3-2.2-1 2.2-1-.6-2.3 2.4-.3.3-2.4 2.3.6z"/>',
		'general'     => '<circle cx="10" cy="10" r="7.5"/><path d="M7.8 7.6c0-1.2 1-2.1 2.2-2.1s2.2.8 2.2 2-.8 1.6-1.6 2.1c-.6.4-.8.8-.8 1.5M10 14.2v.3"/>',
	);
	$key = array_key_exists( $key, $icons ) ? $key : 'general';
	return '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icons[ $key ] . '</svg>';
}

/**
 * Term-meta: icon picker on the FAQ Category add/edit screens.
 */
function deon_faq_cat_add_field() {
	?>
	<div class="form-field">
		<label for="deon_faq_icon"><?php esc_html_e( 'Icon', 'deon-energy' ); ?></label>
		<select name="deon_faq_icon" id="deon_faq_icon">
			<?php foreach ( deon_faq_icons() as $k => $label ) : ?>
			<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<p><?php esc_html_e( 'Shown beside the category name on the FAQ page.', 'deon-energy' ); ?></p>
	</div>
	<?php
}
add_action( 'deon_faq_category_add_form_fields', 'deon_faq_cat_add_field' );

function deon_faq_cat_edit_field( $term ) {
	$icon = get_term_meta( $term->term_id, '_deon_faq_icon', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="deon_faq_icon"><?php esc_html_e( 'Icon', 'deon-energy' ); ?></label></th>
		<td>
			<select name="deon_faq_icon" id="deon_faq_icon">
				<?php foreach ( deon_faq_icons() as $k => $label ) : ?>
				<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $icon, $k ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Shown beside the category name on the FAQ page.', 'deon-energy' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'deon_faq_category_edit_form_fields', 'deon_faq_cat_edit_field' );

function deon_faq_cat_save_field( $term_id ) {
	if ( isset( $_POST['deon_faq_icon'] ) ) {
		$icon = sanitize_key( wp_unslash( $_POST['deon_faq_icon'] ) );
		update_term_meta( $term_id, '_deon_faq_icon', array_key_exists( $icon, deon_faq_icons() ) ? $icon : 'general' );
	}
}
add_action( 'created_deon_faq_category', 'deon_faq_cat_save_field' );
add_action( 'edited_deon_faq_category', 'deon_faq_cat_save_field' );

/**
 * Build FAQ page data: an ordered list of categories, each with its published
 * FAQ items (question + rendered answer). Categories with no items are skipped.
 * Returns an empty array when nothing is published so the template can fall
 * back to the designed placeholder content.
 *
 * @return array<int,array{name:string,slug:string,icon:string,items:array}>
 */
function deon_faq_groups() {
	$terms = get_terms( array(
		'taxonomy'   => 'deon_faq_category',
		'hide_empty' => true,
		'orderby'    => 'name',
	) );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	/*
	 * Categories render in the order the approved copy lists them (PDF §10),
	 * carried as the `_deon_faq_order` term meta that bin/seed-faq.php writes.
	 * Sorting in PHP rather than through get_terms' meta_key keeps categories
	 * the client adds by hand — which have no such meta — in the list; they
	 * fall to the end, alphabetically.
	 */
	usort( $terms, static function ( $a, $b ) {
		$a_order = get_term_meta( $a->term_id, '_deon_faq_order', true );
		$b_order = get_term_meta( $b->term_id, '_deon_faq_order', true );
		$a_order = ( '' === $a_order ) ? PHP_INT_MAX : (int) $a_order;
		$b_order = ( '' === $b_order ) ? PHP_INT_MAX : (int) $b_order;

		return ( $a_order === $b_order )
			? strcasecmp( $a->name, $b->name )
			: $a_order <=> $b_order;
	} );

	$groups = array();
	foreach ( $terms as $term ) {
		$q = new WP_Query( array(
			'post_type'      => 'deon_faq',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
			'tax_query'      => array(
				array(
					'taxonomy' => 'deon_faq_category',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		) );

		if ( ! $q->have_posts() ) {
			wp_reset_postdata();
			continue;
		}

		$items = array();
		while ( $q->have_posts() ) {
			$q->the_post();
			$items[] = array(
				'question' => get_the_title(),
				'answer'   => apply_filters( 'the_content', get_the_content() ),
			);
		}
		wp_reset_postdata();

		$groups[] = array(
			'name'  => $term->name,
			'slug'  => $term->slug,
			'icon'  => get_term_meta( $term->term_id, '_deon_faq_icon', true ),
			'items' => $items,
		);
	}

	return $groups;
}

/**
 * Custom Post Type: Document (Investor Relations — downloadable filings).
 * ACF-free, matching the deon_project / deon_leader pattern. Each document is a
 * downloadable PDF, categorised by the deon_document_type taxonomy so the
 * Investor Resources cards + IPO buttons can resolve the latest file per type.
 * File is stored as the Media Library URL (paste-in, matching the leader link
 * convention); year + menu_order are for admin organisation.
 */
function deon_register_document_cpt() {
	register_post_type( 'deon_document', array(
		'labels'        => array(
			'name'          => __( 'Documents', 'deon-energy' ),
			'singular_name' => __( 'Document', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Document', 'deon-energy' ),
			'edit_item'     => __( 'Edit Document', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-media-document',
		'menu_position' => 23,
		'supports'      => array( 'title', 'page-attributes' ), // title = document name; page-attributes = menu_order for drag-sort.
		'hierarchical'  => false,
	) );

	register_taxonomy( 'deon_document_type', 'deon_document', array(
		'labels'        => array(
			'name'          => __( 'Document Types', 'deon-energy' ),
			'singular_name' => __( 'Document Type', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Type', 'deon-energy' ),
		),
		'hierarchical'  => false,
		'public'        => false,
		'show_ui'       => true,
		'show_admin_column' => true,
		'rewrite'       => false,
	) );
}
add_action( 'init', 'deon_register_document_cpt' );

/**
 * Pre-seed the document-type terms so the client has ready-made categories that
 * line up with the designed Investor Resources cards + IPO buttons. Idempotent.
 */
function deon_seed_document_types() {
	if ( ! taxonomy_exists( 'deon_document_type' ) ) {
		return;
	}
	// Run the seed loop ONCE, ever. Without this flag, term_exists() only
	// checks "does it exist right now" — so deleting a document type in
	// wp-admin got silently undone on the very next page load (this hook
	// fires on every `init`, not just at setup). The flag makes deletions
	// permanent.
	if ( get_option( 'deon_document_types_seeded' ) ) {
		return;
	}
	// name + description seed the Investor Resources cards (card copy = term
	// name/description; editable at Documents → Document Types). 'regulatory-updates'
	// backs the IPO "View Regulatory Updates" button and is not shown as a card.
	$terms = array(
		'financial-statements'   => array( __( 'Financial Statements', 'deon-energy' ),   __( 'Annual and quarterly performance reports.', 'deon-energy' ) ),
		'drhp-filings'           => array( __( 'DRHP & Filings', 'deon-energy' ),          __( 'Registration documents and legal filings.', 'deon-energy' ) ),
		'shareholding-pattern'   => array( __( 'Shareholding Pattern', 'deon-energy' ),    __( 'Detailed equity structure and distribution.', 'deon-energy' ) ),
		'corporate-governance'   => array( __( 'Corporate Governance', 'deon-energy' ),    __( 'Policies and ethical framework documents.', 'deon-energy' ) ),
		'agm-notices'            => array( __( 'AGM & Notices', 'deon-energy' ),           __( 'Upcoming meetings and official notices.', 'deon-energy' ) ),
		'press-releases'         => array( __( 'Press Releases', 'deon-energy' ),          __( 'Media center and official announcements.', 'deon-energy' ) ),
		'investor-presentations' => array( __( 'Investor Presentations', 'deon-energy' ),  __( 'Strategy decks and analyst briefings.', 'deon-energy' ) ),
		'registrar'              => array( __( 'Contact Registrar', 'deon-energy' ),       __( 'Shareholder grievance and registrar info.', 'deon-energy' ) ),
		'regulatory-updates'     => array( __( 'Regulatory Updates', 'deon-energy' ),      __( 'Latest SEBI-approved regulatory notifications.', 'deon-energy' ) ),
	);
	foreach ( $terms as $slug => $data ) {
		if ( ! term_exists( $slug, 'deon_document_type' ) ) {
			wp_insert_term( $data[0], 'deon_document_type', array( 'slug' => $slug, 'description' => $data[1] ) );
		}
	}

	update_option( 'deon_document_types_seeded', 1 );
}
add_action( 'init', 'deon_seed_document_types', 20 );

/**
 * Meta box: file URL + year for deon_document.
 */
function deon_document_meta_box_register() {
	add_meta_box(
		'deon_document_details',
		__( 'Document Details', 'deon-energy' ),
		'deon_document_meta_box_html',
		'deon_document',
		'side'
	);
}
add_action( 'add_meta_boxes', 'deon_document_meta_box_register' );

function deon_document_meta_box_html( $post ) {
	wp_nonce_field( 'deon_document_save', 'deon_document_nonce' );
	$file = get_post_meta( $post->ID, '_deon_document_file', true );
	$year = get_post_meta( $post->ID, '_deon_document_year', true );
	?>
	<p>
		<label for="deon_document_file"><strong><?php esc_html_e( 'File URL (PDF)', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_document_file" name="deon_document_file" value="<?php echo esc_attr( $file ); ?>" style="width:100%;margin-top:4px;" placeholder="https://…/wp-content/uploads/report.pdf">
		<button type="button" class="button deon-media-pick" data-target="deon_document_file" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload File', 'deon-energy' ); ?></button>
		<br><span style="color:#646970;"><?php esc_html_e( 'Click Select / Upload File to pick a PDF from the Media Library (or upload a new one). Assign a Document Type on the right so it appears under the matching Investor Resources card.', 'deon-energy' ); ?></span>
	</p>
	<p>
		<label for="deon_document_year"><strong><?php esc_html_e( 'Year', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_document_year" name="deon_document_year" value="<?php echo esc_attr( $year ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. 2026', 'deon-energy' ); ?>">
	</p>
	<?php
}

function deon_document_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_document_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_document_nonce'] ) ), 'deon_document_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_document_file'] ) ) {
		update_post_meta( $post_id, '_deon_document_file', esc_url_raw( wp_unslash( $_POST['deon_document_file'] ) ) );
	}
	if ( isset( $_POST['deon_document_year'] ) ) {
		update_post_meta( $post_id, '_deon_document_year', sanitize_text_field( wp_unslash( $_POST['deon_document_year'] ) ) );
	}
}
add_action( 'save_post_deon_document', 'deon_document_meta_save' );

/**
 * Resolve the newest document file URL for a given document-type slug.
 * Returns '' when no document of that type has been published yet, so callers
 * can fall back to a placeholder link.
 */
function deon_document_latest_url( $slug ) {
	$q = new WP_Query( array(
		'post_type'              => 'deon_document',
		'posts_per_page'         => 1,
		'no_found_rows'          => true,
		'orderby'                => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'tax_query'              => array(
			array(
				'taxonomy' => 'deon_document_type',
				'field'    => 'slug',
				'terms'    => $slug,
			),
		),
	) );
	if ( ! $q->have_posts() ) {
		return '';
	}
	return (string) get_post_meta( $q->posts[0]->ID, '_deon_document_file', true );
}

/**
 * Designed Investor Resources cards. slug matches a deon_document_type term
 * (except 'registrar', which links to the Contact page). Each card's ACCESS
 * link resolves to the latest document of that type at render time.
 */
function deon_investor_resource_cards() {
	return array(
		array( 'slug' => 'financial-statements',   'icon' => 'investor-doc-financial.svg',     'title' => __( 'Financial Statements', 'deon-energy' ),   'desc' => __( 'Annual and quarterly performance reports.', 'deon-energy' ) ),
		array( 'slug' => 'drhp-filings',            'icon' => 'investor-doc-drhp.svg',          'title' => __( 'DRHP & Filings', 'deon-energy' ),        'desc' => __( 'Registration documents and legal filings.', 'deon-energy' ) ),
		array( 'slug' => 'shareholding-pattern',    'icon' => 'investor-doc-shareholding.svg',  'title' => __( 'Shareholding Pattern', 'deon-energy' ),   'desc' => __( 'Detailed equity structure and distribution.', 'deon-energy' ) ),
		array( 'slug' => 'corporate-governance',    'icon' => 'investor-doc-governance.svg',     'title' => __( 'Corporate Governance', 'deon-energy' ),   'desc' => __( 'Policies and ethical framework documents.', 'deon-energy' ) ),
		array( 'slug' => 'agm-notices',             'icon' => 'investor-doc-agm.svg',           'title' => __( 'AGM & Notices', 'deon-energy' ),         'desc' => __( 'Upcoming meetings and official notices.', 'deon-energy' ) ),
		array( 'slug' => 'press-releases',          'icon' => 'investor-doc-press.svg',         'title' => __( 'Press Releases', 'deon-energy' ),         'desc' => __( 'Media center and official announcements.', 'deon-energy' ) ),
		array( 'slug' => 'investor-presentations',  'icon' => 'investor-doc-presentations.svg', 'title' => __( 'Investor Presentations', 'deon-energy' ), 'desc' => __( 'Strategy decks and analyst briefings.', 'deon-energy' ) ),
		array( 'slug' => 'registrar',               'icon' => 'investor-doc-registrar.svg',     'title' => __( 'Contact Registrar', 'deon-energy' ),      'desc' => __( 'Shareholder grievance and registrar info.', 'deon-energy' ) ),
	);
}

/**
 * Term meta: a custom "card link URL" for each document type. When set, the
 * Investor Resources card links here; otherwise it falls back to the latest
 * document of that type, then to '#'.
 */
function deon_document_type_link_field_add() {
	?>
	<div class="form-field">
		<label for="deon_dtype_link"><?php esc_html_e( 'Card Link URL', 'deon-energy' ); ?></label>
		<input type="url" name="deon_dtype_link" id="deon_dtype_link" value="" placeholder="https://…">
		<p><?php esc_html_e( 'Optional. Where the Investor Resources card for this type links. Leave empty to auto-link the latest uploaded document of this type.', 'deon-energy' ); ?></p>
	</div>
	<?php
}
add_action( 'deon_document_type_add_form_fields', 'deon_document_type_link_field_add' );

function deon_document_type_link_field_edit( $term ) {
	$link = get_term_meta( $term->term_id, '_deon_dtype_link', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="deon_dtype_link"><?php esc_html_e( 'Card Link URL', 'deon-energy' ); ?></label></th>
		<td>
			<input type="url" name="deon_dtype_link" id="deon_dtype_link" value="<?php echo esc_attr( $link ); ?>" placeholder="https://…" style="width:95%;">
			<p class="description"><?php esc_html_e( 'Optional. Where the Investor Resources card for this type links. Leave empty to auto-link the latest uploaded document of this type.', 'deon-energy' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'deon_document_type_edit_form_fields', 'deon_document_type_link_field_edit' );

function deon_document_type_link_save( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['deon_dtype_link'] ) ) {
		update_term_meta( $term_id, '_deon_dtype_link', esc_url_raw( wp_unslash( $_POST['deon_dtype_link'] ) ) );
	}
}
add_action( 'created_deon_document_type', 'deon_document_type_link_save' );
add_action( 'edited_deon_document_type', 'deon_document_type_link_save' );

/**
 * Number of published documents of a given type slug.
 */
function deon_document_type_count( $slug ) {
	$q = new WP_Query( array(
		'post_type'      => 'deon_document',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'tax_query'      => array(
			array( 'taxonomy' => 'deon_document_type', 'field' => 'slug', 'terms' => $slug ),
		),
	) );
	return (int) $q->found_posts;
}

/**
 * Resolve the ACCESS link for an Investor Resources card by type slug, in order:
 *   1. custom term "Card Link URL" (manual override)
 *   2. the Investor Documents listing filtered to this type (when files exist)
 *   3. the newest document file (fallback if the listing page is missing)
 *   4. '#'  — registrar links to the Contact page.
 */
function deon_document_type_card_link( $slug ) {
	$term = get_term_by( 'slug', $slug, 'deon_document_type' );
	if ( $term ) {
		$custom = get_term_meta( $term->term_id, '_deon_dtype_link', true );
		if ( $custom ) {
			return $custom;
		}
	}
	if ( 'registrar' === $slug ) {
		return home_url( '/contact/' );
	}
	if ( deon_document_type_count( $slug ) > 0 ) {
		$page = get_page_by_path( 'investor-documents' );
		if ( $page ) {
			return add_query_arg( 'type', rawurlencode( $slug ), get_permalink( $page ) );
		}
		$file = deon_document_latest_url( $slug );
		if ( $file ) {
			return $file;
		}
	}
	return '#';
}

/**
 * Admin media picker: wires any <button class="deon-media-pick" data-target="input-id">
 * to the WP media library, writing the chosen file URL into the target input.
 * Loaded on the page + document edit screens where IR file fields appear, and on
 * the category term screens (per-category hero background).
 */
function deon_admin_media_picker_enqueue( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$is_post_screen = in_array( $hook, array( 'post.php', 'post-new.php' ), true )
		&& in_array( $screen->post_type, array( 'page', 'deon_document', 'deon_investor_doc', 'deon_investor_video', 'deon_leader', 'deon_project', 'deon_milestone' ), true );

	// Category add/edit screens carry the term-level "Hero Background" fields.
	$is_term_screen = in_array( $hook, array( 'edit-tags.php', 'term.php' ), true )
		&& function_exists( 'deon_term_hero_taxonomies' )
		&& in_array( $screen->taxonomy, deon_term_hero_taxonomies(), true );

	if ( ! $is_post_screen && ! $is_term_screen ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_add_inline_script(
		'jquery-core',
		"jQuery(function($){
			$(document).on('click','.deon-media-pick',function(e){
				e.preventDefault();
				var btn=$(this), input=$('#'+btn.data('target'));
				var frame=wp.media({title:'" . esc_js( __( 'Select or upload a file', 'deon-energy' ) ) . "',button:{text:'" . esc_js( __( 'Use this file', 'deon-energy' ) ) . "'},multiple:false});
				frame.on('select',function(){ input.val(frame.state().get('selection').first().toJSON().url).trigger('change'); });
				frame.open();
			});

			// Gallery picker: multi-select + drag-reorder + remove. Stores CSV of IDs.
			function deonSyncGallery(field){
				var ids=field.find('.deon-gallery-list li').map(function(){return $(this).data('id');}).get();
				field.find('input[type=hidden]').val(ids.join(','));
			}
			$('.deon-gallery-list').sortable({update:function(){deonSyncGallery($(this).closest('.deon-gallery-field'));}});
			$(document).on('click','.deon-gallery-add',function(e){
				e.preventDefault();
				var field=$(this).closest('.deon-gallery-field'), list=field.find('.deon-gallery-list');
				var frame=wp.media({title:'" . esc_js( __( 'Select project images', 'deon-energy' ) ) . "',button:{text:'" . esc_js( __( 'Add to gallery', 'deon-energy' ) ) . "'},library:{type:'image'},multiple:'add'});
				frame.on('select',function(){
					frame.state().get('selection').each(function(att){
						var a=att.toJSON();
						if(list.find('li[data-id=\"'+a.id+'\"]').length) return;
						var thumb=(a.sizes&&a.sizes.thumbnail?a.sizes.thumbnail.url:a.url);
						list.append('<li data-id=\"'+a.id+'\"><img src=\"'+thumb+'\" alt=\"\"><button type=\"button\" class=\"deon-gallery-remove\" aria-label=\"remove\">&times;</button></li>');
					});
					deonSyncGallery(field);
				});
				frame.open();
			});
			$(document).on('click','.deon-gallery-remove',function(e){
				e.preventDefault();
				var field=$(this).closest('.deon-gallery-field');
				$(this).closest('li').remove();
				deonSyncGallery(field);
			});
		});"
	);
	wp_add_inline_style(
		'wp-admin',
		'.deon-gallery-list{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 12px;padding:0;list-style:none}'
		. '.deon-gallery-list li{position:relative;width:90px;height:90px;cursor:move;border:1px solid #dcdcde;background:#f0f0f1}'
		. '.deon-gallery-list img{width:100%;height:100%;object-fit:cover;display:block}'
		. '.deon-gallery-remove{position:absolute;top:-8px;right:-8px;width:20px;height:20px;line-height:18px;border-radius:50%;border:none;background:#d63638;color:#fff;cursor:pointer;font-size:14px;padding:0}'
	);
}
add_action( 'admin_enqueue_scripts', 'deon_admin_media_picker_enqueue' );

/**
 * Page meta box: Investor Relations singleton fields (hero pill, IPO buttons,
 * support contact). Shown only on the page using the Investor Relations
 * template / slug. No options page (per ACF-free convention) — the values live
 * on the page itself.
 */
function deon_ir_is_target_page( $post ) {
	if ( ! $post || 'page' !== $post->post_type ) {
		return false;
	}
	return 'page-investor-relations.php' === get_page_template_slug( $post->ID ) || 'investor-relations' === $post->post_name;
}

function deon_ir_meta_box_register( $post ) {
	if ( ! deon_ir_is_target_page( $post ) ) {
		return;
	}
	add_meta_box(
		'deon_ir_details',
		__( 'Investor Relations Content', 'deon-energy' ),
		'deon_ir_meta_box_html',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'deon_ir_meta_box_register' );

function deon_ir_meta_box_html( $post ) {
	wp_nonce_field( 'deon_ir_save', 'deon_ir_nonce' );
	$fields = array(
		'_deon_ir_ipo_status'  => array( __( 'Hero — IPO status pill', 'deon-energy' ),      'text', 'IPO Status: Pre-Filing Phase' ),
		'_deon_ir_btn1_label'  => array( __( 'IPO card — button 1 label', 'deon-energy' ),   'text', 'Download DRHP Filing' ),
		'_deon_ir_btn1_url'    => array( __( 'IPO card — button 1 URL', 'deon-energy' ),      'url',  '' ),
		'_deon_ir_btn2_label'  => array( __( 'IPO card — button 2 label', 'deon-energy' ),   'text', 'View Regulatory Updates' ),
		'_deon_ir_btn2_url'    => array( __( 'IPO card — button 2 URL', 'deon-energy' ),      'url',  '' ),
		'_deon_ir_email'       => array( __( 'Support — email', 'deon-energy' ),              'text', 'investors@deonenergy.com' ),
		'_deon_ir_phone'       => array( __( 'Support — phone', 'deon-energy' ),              'text', '1800 890 5933' ),
	);
	echo '<p style="color:#646970;margin-top:0;">' . esc_html__( 'Leave a field empty to use the designed default. Button URLs override the auto-linked latest document — paste a link or upload a file.', 'deon-energy' ) . '</p>';
	foreach ( $fields as $key => $f ) {
		$val = get_post_meta( $post->ID, $key, true );
		printf(
			'<p><label for="%1$s"><strong>%2$s</strong></label><br><input type="%3$s" id="%1$s" name="%1$s" value="%4$s" style="width:100%%;margin-top:4px;" placeholder="%5$s">%6$s</p>',
			esc_attr( $key ),
			esc_html( $f[0] ),
			esc_attr( $f[1] ),
			esc_attr( $val ),
			esc_attr( $f[2] ),
			'url' === $f[1]
				? sprintf(
					'<button type="button" class="button deon-media-pick" data-target="%1$s" style="margin-top:6px;">%2$s</button>',
					esc_attr( $key ),
					esc_html__( 'Select / Upload File', 'deon-energy' )
				)
				: ''
		);
	}
}

function deon_ir_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_ir_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_ir_nonce'] ) ), 'deon_ir_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$url_keys = array( '_deon_ir_btn1_url', '_deon_ir_btn2_url' );
	$keys     = array( '_deon_ir_ipo_status', '_deon_ir_btn1_label', '_deon_ir_btn1_url', '_deon_ir_btn2_label', '_deon_ir_btn2_url', '_deon_ir_email', '_deon_ir_phone' );
	foreach ( $keys as $key ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] );
		update_post_meta( $post_id, $key, in_array( $key, $url_keys, true ) ? esc_url_raw( $raw ) : sanitize_text_field( $raw ) );
	}
}
add_action( 'save_post_page', 'deon_ir_meta_save' );

/**
 * Read an Investor Relations page field with a designed fallback. Reads meta
 * from the currently queried page, so it is safe to call inside partials.
 */
function deon_ir_field( $key, $fallback = '' ) {
	$val = get_post_meta( get_queried_object_id(), $key, true );
	return ( '' !== $val && null !== $val ) ? $val : $fallback;
}

/**
 * Per-page hero background (image / video). ACF-free native meta box on the
 * `page` type — the inner-page counterpart to the Homepage ACF hero. Mirrors
 * the Investor Relations box above and reuses the existing `.deon-media-pick`
 * admin picker (already enqueued on `page`) and `deon_hero_embed_url()`.
 */

/**
 * Resolve the current (or given) page's hero background into a normalized array.
 * Returns an empty array when there is no usable media — callers then fall back
 * to the plain colour hero. Safe to call inside hero partials.
 */
function deon_get_hero_bg( $post_id = null ) {
	if ( null === $post_id ) {
		if ( is_home() ) {
			// The blog listing (is_home) is served by the assigned Posts page.
			$post_id = (int) get_option( 'page_for_posts' );
		} elseif ( is_singular() ) {
			$post_id = get_queried_object_id();
		} elseif ( is_category() && function_exists( 'deon_get_term_hero_bg' ) ) {
			// Category archives carry their own hero media as term meta.
			return deon_get_term_hero_bg( get_queried_object_id() );
		} else {
			/*
			 * Tag / author / date / search / 404: get_queried_object_id() is NOT
			 * a post ID here (on a term archive it is the term ID), so reading
			 * post meta with it silently borrows an unrelated page's hero
			 * whenever the two IDs happen to collide. Keep the colour hero.
			 */
			return array();
		}
	}
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return array();
	}

	return deon_hero_bg_normalize( array(
		'type'         => get_post_meta( $post_id, '_deon_hero_bg_type', true ),
		'image'        => get_post_meta( $post_id, '_deon_hero_bg_image', true ),
		'video_source' => get_post_meta( $post_id, '_deon_hero_bg_video_source', true ),
		'video_file'   => get_post_meta( $post_id, '_deon_hero_bg_video_file', true ),
		'video_embed'  => get_post_meta( $post_id, '_deon_hero_bg_video_embed', true ),
		'poster'       => get_post_meta( $post_id, '_deon_hero_bg_video_poster', true ),
	) );
}

/**
 * Shared normalizer for raw hero-background values, whichever store they came
 * from (post meta for pages/projects, term meta for category archives). Returns
 * an empty array whenever the media is unusable so callers fall back to the
 * plain colour hero.
 *
 * @param array $raw type / image / video_source / video_file / video_embed / poster.
 * @return array Normalized background, or array() when there is nothing to show.
 */
function deon_hero_bg_normalize( array $raw ) {
	$type = isset( $raw['type'] ) ? (string) $raw['type'] : '';
	if ( 'image' !== $type && 'video' !== $type ) {
		return array();
	}

	$bg = array(
		'type'         => $type,
		'image'        => isset( $raw['image'] ) ? (string) $raw['image'] : '',
		'video_source' => ! empty( $raw['video_source'] ) ? (string) $raw['video_source'] : 'upload',
		'video_file'   => isset( $raw['video_file'] ) ? (string) $raw['video_file'] : '',
		'video_embed'  => isset( $raw['video_embed'] ) ? (string) $raw['video_embed'] : '',
		'poster'       => isset( $raw['poster'] ) ? (string) $raw['poster'] : '',
	);

	// Fall back to the colour hero when the chosen media field is empty.
	if ( 'image' === $type && '' === $bg['image'] ) {
		return array();
	}
	if ( 'video' === $type ) {
		$has_video = ( 'embed' === $bg['video_source'] )
			? ( '' !== $bg['video_embed'] && '' !== deon_hero_embed_url( $bg['video_embed'] ) )
			: ( '' !== $bg['video_file'] );
		if ( ! $has_video ) {
			return array();
		}
	}

	return $bg;
}

function deon_hero_bg_meta_box_register( $post ) {
	// The homepage has its own (ACF) hero controls — don't add a second one.
	if ( (int) get_option( 'page_on_front' ) === (int) $post->ID ) {
		return;
	}
	add_meta_box(
		'deon_hero_bg',
		__( 'Hero Background', 'deon-energy' ),
		'deon_hero_bg_meta_box_html',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'deon_hero_bg_meta_box_register' );

function deon_hero_bg_meta_box_html( $post ) {
	wp_nonce_field( 'deon_hero_bg_save', 'deon_hero_bg_nonce' );

	$type   = get_post_meta( $post->ID, '_deon_hero_bg_type', true ) ?: 'none';
	$image  = get_post_meta( $post->ID, '_deon_hero_bg_image', true );
	$vsrc   = get_post_meta( $post->ID, '_deon_hero_bg_video_source', true ) ?: 'upload';
	$vfile  = get_post_meta( $post->ID, '_deon_hero_bg_video_file', true );
	$vembed = get_post_meta( $post->ID, '_deon_hero_bg_video_embed', true );
	$vpost  = get_post_meta( $post->ID, '_deon_hero_bg_video_poster', true );
	?>
	<p style="color:#646970;margin-top:0;">
		<?php esc_html_e( 'Add a background image or video shown behind this page\'s hero title (same treatment as the Homepage hero). Leave Type = None to keep the plain colour background.', 'deon-energy' ); ?>
	</p>

	<p>
		<label for="deon_hero_bg_type"><strong><?php esc_html_e( 'Background type', 'deon-energy' ); ?></strong></label><br>
		<select id="deon_hero_bg_type" name="_deon_hero_bg_type" style="margin-top:4px;">
			<option value="none"  <?php selected( $type, 'none' ); ?>><?php esc_html_e( 'None (colour background)', 'deon-energy' ); ?></option>
			<option value="image" <?php selected( $type, 'image' ); ?>><?php esc_html_e( 'Image', 'deon-energy' ); ?></option>
			<option value="video" <?php selected( $type, 'video' ); ?>><?php esc_html_e( 'Video', 'deon-energy' ); ?></option>
		</select>
	</p>

	<div class="deon-hbg-group" data-hbg="image" style="<?php echo 'image' === $type ? '' : 'display:none;'; ?>">
		<p>
			<label for="_deon_hero_bg_image"><strong><?php esc_html_e( 'Background image', 'deon-energy' ); ?></strong></label><br>
			<input type="url" id="_deon_hero_bg_image" name="_deon_hero_bg_image" value="<?php echo esc_attr( $image ); ?>" style="width:100%;margin-top:4px;" placeholder="https://…">
			<button type="button" class="button deon-media-pick" data-target="_deon_hero_bg_image" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload Image', 'deon-energy' ); ?></button>
		</p>
	</div>

	<div class="deon-hbg-group" data-hbg="video" style="<?php echo 'video' === $type ? '' : 'display:none;'; ?>">
		<p>
			<label for="deon_hero_bg_video_source"><strong><?php esc_html_e( 'Video source', 'deon-energy' ); ?></strong></label><br>
			<select id="deon_hero_bg_video_source" name="_deon_hero_bg_video_source" style="margin-top:4px;">
				<option value="upload" <?php selected( $vsrc, 'upload' ); ?>><?php esc_html_e( 'Upload MP4', 'deon-energy' ); ?></option>
				<option value="embed"  <?php selected( $vsrc, 'embed' ); ?>><?php esc_html_e( 'YouTube / Vimeo URL', 'deon-energy' ); ?></option>
			</select>
		</p>
		<p class="deon-hbg-vsrc" data-vsrc="upload" style="<?php echo 'embed' === $vsrc ? 'display:none;' : ''; ?>">
			<label for="_deon_hero_bg_video_file"><strong><?php esc_html_e( 'MP4 file', 'deon-energy' ); ?></strong></label><br>
			<input type="url" id="_deon_hero_bg_video_file" name="_deon_hero_bg_video_file" value="<?php echo esc_attr( $vfile ); ?>" style="width:100%;margin-top:4px;" placeholder="https://…mp4">
			<button type="button" class="button deon-media-pick" data-target="_deon_hero_bg_video_file" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload MP4', 'deon-energy' ); ?></button>
		</p>
		<p class="deon-hbg-vsrc" data-vsrc="embed" style="<?php echo 'embed' === $vsrc ? '' : 'display:none;'; ?>">
			<label for="_deon_hero_bg_video_embed"><strong><?php esc_html_e( 'YouTube / Vimeo URL', 'deon-energy' ); ?></strong></label><br>
			<input type="url" id="_deon_hero_bg_video_embed" name="_deon_hero_bg_video_embed" value="<?php echo esc_attr( $vembed ); ?>" style="width:100%;margin-top:4px;" placeholder="https://www.youtube.com/watch?v=…">
		</p>
		<p>
			<label for="_deon_hero_bg_video_poster"><strong><?php esc_html_e( 'Poster image (optional)', 'deon-energy' ); ?></strong></label><br>
			<input type="url" id="_deon_hero_bg_video_poster" name="_deon_hero_bg_video_poster" value="<?php echo esc_attr( $vpost ); ?>" style="width:100%;margin-top:4px;" placeholder="https://…">
			<button type="button" class="button deon-media-pick" data-target="_deon_hero_bg_video_poster" style="margin-top:6px;"><?php esc_html_e( 'Select / Upload Image', 'deon-energy' ); ?></button>
		</p>
	</div>

	<script>
	( function () {
		var box = document.getElementById( 'deon_hero_bg' );
		if ( ! box ) { return; }
		var typeSel = box.querySelector( '#deon_hero_bg_type' );
		var vsrcSel = box.querySelector( '#deon_hero_bg_video_source' );
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
	<?php
}

function deon_hero_bg_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_hero_bg_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_hero_bg_nonce'] ) ), 'deon_hero_bg_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$url_keys = array( '_deon_hero_bg_image', '_deon_hero_bg_video_file', '_deon_hero_bg_video_embed', '_deon_hero_bg_video_poster' );
	$keys     = array_merge( array( '_deon_hero_bg_type', '_deon_hero_bg_video_source' ), $url_keys );
	foreach ( $keys as $key ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] );
		update_post_meta( $post_id, $key, in_array( $key, $url_keys, true ) ? esc_url_raw( $raw ) : sanitize_text_field( $raw ) );
	}
}
add_action( 'save_post_page', 'deon_hero_bg_meta_save' );

/**
 * Custom Post Type: Milestone (About page — Growth Timeline carousel).
 * ACF-free, matching the deon_project / deon_job pattern. Each milestone is one
 * slide: year label on the rail, then a card with photo, phase and description.
 * menu_order drives the left-to-right order. Featured image = card photo.
 */
function deon_register_milestone_cpt() {
	register_post_type( 'deon_milestone', array(
		'labels'        => array(
			'name'          => __( 'Milestones', 'deon-energy' ),
			'singular_name' => __( 'Milestone', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Milestone', 'deon-energy' ),
			'edit_item'     => __( 'Edit Milestone', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-chart-line',
		'menu_position' => 23,
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ), // title = phase label; thumbnail = card photo; page-attributes = menu_order.
		'hierarchical'  => false,
	) );
}
add_action( 'init', 'deon_register_milestone_cpt' );

/**
 * Meta box: year, description and future flag for deon_milestone.
 */
function deon_milestone_meta_box_register() {
	add_meta_box(
		'deon_milestone_details',
		__( 'Milestone Details', 'deon-energy' ),
		'deon_milestone_meta_box_html',
		'deon_milestone',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'deon_milestone_meta_box_register' );

function deon_milestone_meta_box_html( $post ) {
	wp_nonce_field( 'deon_milestone_save', 'deon_milestone_nonce' );
	$year   = get_post_meta( $post->ID, '_deon_milestone_year', true );
	$desc   = get_post_meta( $post->ID, '_deon_milestone_desc', true );
	$future = get_post_meta( $post->ID, '_deon_milestone_future', true );
	$icon   = get_post_meta( $post->ID, '_deon_milestone_icon', true );
	?>
	<p>
		<label for="deon_milestone_year"><strong><?php esc_html_e( 'Year (rail label)', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_milestone_year" name="deon_milestone_year" value="<?php echo esc_attr( $year ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. 2014 or 1998-99', 'deon-energy' ); ?>">
	</p>
	<p>
		<label for="deon_milestone_icon"><strong><?php esc_html_e( 'Icon (timeline circle)', 'deon-energy' ); ?></strong></label><br>
		<?php if ( $icon ) : ?>
			<img src="<?php echo esc_url( $icon ); ?>" alt="" class="deon-leader-thumb" style="width:44px;height:44px;object-fit:contain;background:#eaf6fc;padding:6px;border-radius:50%;">
		<?php endif; ?>
		<input type="url" id="deon_milestone_icon" name="deon_milestone_icon" value="<?php echo esc_attr( $icon ); ?>" style="width:calc(100% - 130px);" placeholder="<?php esc_attr_e( 'Upload or paste an icon URL (SVG / PNG)', 'deon-energy' ); ?>">
		<button type="button" class="button deon-media-pick" data-target="deon_milestone_icon" style="margin-top:4px;"><?php esc_html_e( 'Select icon', 'deon-energy' ); ?></button>
		<br><em><?php esc_html_e( 'Shown in the small circle beside the photo. Best: a simple line/solid icon on transparent background. Leave blank to use a rotating default set.', 'deon-energy' ); ?></em>
	</p>
	<p>
		<label for="deon_milestone_desc"><strong><?php esc_html_e( 'Description', 'deon-energy' ); ?></strong></label><br>
		<textarea id="deon_milestone_desc" name="deon_milestone_desc" rows="3" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'One sentence about what happened. e.g. First industrial solar contract signed.', 'deon-energy' ); ?>"><?php echo esc_textarea( $desc ); ?></textarea>
	</p>
	<p>
		<label>
			<input type="checkbox" name="deon_milestone_future" value="1" <?php checked( $future, '1' ); ?>>
			<strong><?php esc_html_e( 'Future milestone', 'deon-energy' ); ?></strong>
		</label><br>
		<em><?php esc_html_e( 'Renders the year, dot and stem in grey instead of accent orange (for vision / not-yet-reached entries).', 'deon-energy' ); ?></em>
	</p>
	<p>
		<em><?php esc_html_e( 'Title = phase label above the description (e.g. INCEPTION). Featured image = card photo. Order = drag-sort via the Order field under Page Attributes.', 'deon-energy' ); ?></em>
	</p>
	<?php
}

function deon_milestone_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_milestone_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_milestone_nonce'] ) ), 'deon_milestone_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_milestone_year'] ) ) {
		update_post_meta( $post_id, '_deon_milestone_year', sanitize_text_field( wp_unslash( $_POST['deon_milestone_year'] ) ) );
	}
	if ( isset( $_POST['deon_milestone_desc'] ) ) {
		update_post_meta( $post_id, '_deon_milestone_desc', sanitize_textarea_field( wp_unslash( $_POST['deon_milestone_desc'] ) ) );
	}
	update_post_meta( $post_id, '_deon_milestone_future', isset( $_POST['deon_milestone_future'] ) ? '1' : '' );
	if ( isset( $_POST['deon_milestone_icon'] ) ) {
		update_post_meta( $post_id, '_deon_milestone_icon', esc_url_raw( wp_unslash( $_POST['deon_milestone_icon'] ) ) );
	}
}
add_action( 'save_post_deon_milestone', 'deon_milestone_meta_save' );

/**
 * Growth Timeline milestones, normalised for the About page carousel.
 * Returns an empty array when no milestones exist so the template can fall
 * back to the designed placeholders.
 *
 * @return array<int,array<string,mixed>>
 */
function deon_get_milestones() {
	$query = new WP_Query( array(
		'post_type'              => 'deon_milestone',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	) );

	$milestones = array();

	foreach ( $query->posts as $post ) {
		$milestones[] = array(
			'year'  => get_post_meta( $post->ID, '_deon_milestone_year', true ),
			'phase' => get_the_title( $post ),
			'desc'  => get_post_meta( $post->ID, '_deon_milestone_desc', true ),
			'done'  => '1' !== get_post_meta( $post->ID, '_deon_milestone_future', true ),
			'image' => get_the_post_thumbnail_url( $post, 'medium_large' ),
			'icon'  => get_post_meta( $post->ID, '_deon_milestone_icon', true ),
		);
	}

	return $milestones;
}

/**
 * Custom Post Type: Job (Careers page — open positions).
 * ACF-free, matching the deon_project / deon_leader pattern. Each job renders
 * as a position card in the Open Positions list. menu_order drives sort.
 */
function deon_register_job_cpt() {
	register_post_type( 'deon_job', array(
		'labels'        => array(
			'name'          => __( 'Jobs', 'deon-energy' ),
			'singular_name' => __( 'Job', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Job', 'deon-energy' ),
			'edit_item'     => __( 'Edit Job', 'deon-energy' ),
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'has_archive'        => false,
		'rewrite'            => array( 'slug' => 'careers/role', 'with_front' => false ),
		'menu_icon'          => 'dashicons-businessperson',
		'menu_position'      => 22,
		'supports'           => array( 'title', 'editor', 'page-attributes' ), // title = job title; editor = "About the Role" body; page-attributes = menu_order for drag-sort.
		'hierarchical'       => false,
	) );
}
add_action( 'init', 'deon_register_job_cpt' );

/**
 * Meta box: department, location, type, description, apply target for deon_job.
 */
function deon_job_meta_box_register() {
	add_meta_box(
		'deon_job_details',
		__( 'Job Details', 'deon-energy' ),
		'deon_job_meta_box_html',
		'deon_job',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'deon_job_meta_box_register' );

function deon_job_meta_box_html( $post ) {
	wp_nonce_field( 'deon_job_save', 'deon_job_nonce' );
	$department = get_post_meta( $post->ID, '_deon_job_department', true );
	$location   = get_post_meta( $post->ID, '_deon_job_location', true );
	$type       = get_post_meta( $post->ID, '_deon_job_type', true );
	$seniority  = get_post_meta( $post->ID, '_deon_job_seniority', true );
	$travel     = get_post_meta( $post->ID, '_deon_job_travel', true );
	$desc       = get_post_meta( $post->ID, '_deon_job_desc', true );
	$respons    = get_post_meta( $post->ID, '_deon_job_responsibilities', true );
	$requires   = get_post_meta( $post->ID, '_deon_job_requirements', true );
	$apply      = get_post_meta( $post->ID, '_deon_job_apply', true );
	?>
	<p>
		<label for="deon_job_department"><strong><?php esc_html_e( 'Department (eyebrow)', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_job_department" name="deon_job_department" value="<?php echo esc_attr( $department ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. INFRASTRUCTURE', 'deon-energy' ); ?>">
	</p>
	<p>
		<label for="deon_job_location"><strong><?php esc_html_e( 'Location', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_job_location" name="deon_job_location" value="<?php echo esc_attr( $location ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. Mumbai, India', 'deon-energy' ); ?>">
	</p>
	<p>
		<label for="deon_job_type"><strong><?php esc_html_e( 'Employment Type', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_job_type" name="deon_job_type" value="<?php echo esc_attr( $type ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. Full-time', 'deon-energy' ); ?>">
	</p>
	<hr style="margin:16px 0;border:0;border-top:1px solid #dcdcde;">
	<p><em><?php esc_html_e( 'Role Analytics sidebar (detail page)', 'deon-energy' ); ?></em></p>
	<p>
		<label for="deon_job_seniority"><strong><?php esc_html_e( 'Seniority', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_job_seniority" name="deon_job_seniority" value="<?php echo esc_attr( $seniority ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. Lead/Senior', 'deon-energy' ); ?>">
	</p>
	<p>
		<label for="deon_job_travel"><strong><?php esc_html_e( 'Travel', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_job_travel" name="deon_job_travel" value="<?php echo esc_attr( $travel ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. 20% EMEA', 'deon-energy' ); ?>">
	</p>
	<hr style="margin:16px 0;border:0;border-top:1px solid #dcdcde;">
	<p>
		<label for="deon_job_responsibilities"><strong><?php esc_html_e( 'Core Responsibilities', 'deon-energy' ); ?></strong> <em>(<?php esc_html_e( 'one per line', 'deon-energy' ); ?>)</em></label><br>
		<textarea id="deon_job_responsibilities" name="deon_job_responsibilities" rows="5" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( "Lead the end-to-end technical design of 50MW+ PV systems.\nCoordinate with global partners on grid interconnection.", 'deon-energy' ); ?>"><?php echo esc_textarea( $respons ); ?></textarea>
	</p>
	<p>
		<label for="deon_job_requirements"><strong><?php esc_html_e( 'Professional Requirements', 'deon-energy' ); ?></strong> <em>(<?php esc_html_e( 'one per line — LABEL | body', 'deon-energy' ); ?>)</em></label><br>
		<textarea id="deon_job_requirements" name="deon_job_requirements" rows="5" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( "Educational Foundation | Master's degree in Electrical Engineering or related field.\nTechnical Proficiency | 8+ years in solar PV engineering with PVSyst, AutoCAD.", 'deon-energy' ); ?>"><?php echo esc_textarea( $requires ); ?></textarea>
	</p>
	<p>
		<label for="deon_job_desc"><strong><?php esc_html_e( 'Short Description', 'deon-energy' ); ?></strong> <em>(<?php esc_html_e( 'shown on card expand', 'deon-energy' ); ?>)</em></label><br>
		<textarea id="deon_job_desc" name="deon_job_desc" rows="3" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'One or two sentences about the role. Revealed when the card is expanded.', 'deon-energy' ); ?>"><?php echo esc_textarea( $desc ); ?></textarea>
	</p>
	<p>
		<label for="deon_job_apply"><strong><?php esc_html_e( 'Apply URL or Email', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_job_apply" name="deon_job_apply" value="<?php echo esc_attr( $apply ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'https://… or careers@deonenergy.com — blank falls back to Contact page', 'deon-energy' ); ?>">
	</p>
	<?php
}

function deon_job_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_job_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_job_nonce'] ) ), 'deon_job_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_job_department'] ) ) {
		update_post_meta( $post_id, '_deon_job_department', sanitize_text_field( wp_unslash( $_POST['deon_job_department'] ) ) );
	}
	if ( isset( $_POST['deon_job_location'] ) ) {
		update_post_meta( $post_id, '_deon_job_location', sanitize_text_field( wp_unslash( $_POST['deon_job_location'] ) ) );
	}
	if ( isset( $_POST['deon_job_type'] ) ) {
		update_post_meta( $post_id, '_deon_job_type', sanitize_text_field( wp_unslash( $_POST['deon_job_type'] ) ) );
	}
	if ( isset( $_POST['deon_job_seniority'] ) ) {
		update_post_meta( $post_id, '_deon_job_seniority', sanitize_text_field( wp_unslash( $_POST['deon_job_seniority'] ) ) );
	}
	if ( isset( $_POST['deon_job_travel'] ) ) {
		update_post_meta( $post_id, '_deon_job_travel', sanitize_text_field( wp_unslash( $_POST['deon_job_travel'] ) ) );
	}
	if ( isset( $_POST['deon_job_responsibilities'] ) ) {
		update_post_meta( $post_id, '_deon_job_responsibilities', sanitize_textarea_field( wp_unslash( $_POST['deon_job_responsibilities'] ) ) );
	}
	if ( isset( $_POST['deon_job_requirements'] ) ) {
		update_post_meta( $post_id, '_deon_job_requirements', sanitize_textarea_field( wp_unslash( $_POST['deon_job_requirements'] ) ) );
	}
	if ( isset( $_POST['deon_job_desc'] ) ) {
		update_post_meta( $post_id, '_deon_job_desc', sanitize_textarea_field( wp_unslash( $_POST['deon_job_desc'] ) ) );
	}
	if ( isset( $_POST['deon_job_apply'] ) ) {
		update_post_meta( $post_id, '_deon_job_apply', sanitize_text_field( wp_unslash( $_POST['deon_job_apply'] ) ) );
	}
}
add_action( 'save_post_deon_job', 'deon_job_meta_save' );

/**
 * Resolve a job's apply target to a safe href. Accepts a URL or a bare email
 * (converted to mailto:); falls back to the Contact page when empty.
 */
function deon_job_apply_href( $apply ) {
	$apply = trim( (string) $apply );
	if ( '' === $apply ) {
		return home_url( '/contact/' );
	}
	if ( is_email( $apply ) ) {
		return 'mailto:' . sanitize_email( $apply );
	}
	return esc_url_raw( $apply );
}

/**
 * Convert a YouTube or Vimeo watch URL to an autoplay embed URL.
 */
function deon_hero_embed_url( $url ) {
	if ( empty( $url ) ) return '';
	if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
		$id   = $m[1];
		$args = array(
			'autoplay'       => 1,
			'mute'           => 1,
			'loop'           => 1,
			'playlist'       => $id, // required for loop on a single video
			'controls'       => 0,
			'showinfo'       => 0,
			'rel'            => 0,
			'modestbranding' => 1,
			'iv_load_policy' => 3, // no annotations
			'disablekb'      => 1, // no keyboard controls
			'fs'             => 0,  // no fullscreen button
			'playsinline'    => 1,
		);
		return 'https://www.youtube.com/embed/' . $id . '?' . http_build_query( $args );
	}
	if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
		return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&muted=1&loop=1&background=1';
	}
	return '';
}

/**
 * ACF: Hero Section field group (front page only).
 * Requires ACF plugin to be active.
 */
function deon_hero_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	acf_add_local_field_group( array(
		'key'    => 'group_deon_hero',
		'title'  => 'Hero Section',
		'fields' => array(
			// ---------- CONTENT ----------
			array( 'key' => 'field_hero_tab_content', 'label' => 'Content', 'type' => 'tab', 'placement' => 'top' ),
			array(
				'key'          => 'field_hero_eyebrow',
				'label'        => 'Eyebrow',
				'name'         => 'hero_eyebrow',
				'type'         => 'text',
				'placeholder'  => "Gujarat's Leading Solar EPC Company",
				'instructions' => 'Small line above the headline. Leave empty for the default.',
			),
			array(
				'key'          => 'field_hero_heading',
				'label'        => 'Headline',
				'name'         => 'hero_heading',
				'type'         => 'textarea',
				'rows'         => 3,
				'new_lines'    => '',
				'placeholder'  => "Building India's\nRenewable Energy\nFuture",
				'instructions' => 'One line per row — each line break renders as a new line in the hero. Leave empty for the default.',
			),
			array(
				'key'          => 'field_hero_btn1_label',
				'label'        => 'Primary Button — Label',
				'name'         => 'hero_btn1_label',
				'type'         => 'text',
				'placeholder'  => 'Explore Our Work',
				'wrapper'      => array( 'width' => '50' ),
			),
			array(
				'key'          => 'field_hero_btn1_url',
				'label'        => 'Primary Button — URL',
				'name'         => 'hero_btn1_url',
				'type'         => 'url',
				'wrapper'      => array( 'width' => '50' ),
				'instructions' => 'Falls back to /projects/ if empty.',
			),
			array(
				'key'          => 'field_hero_btn2_label',
				'label'        => 'Secondary Link — Label',
				'name'         => 'hero_btn2_label',
				'type'         => 'text',
				'placeholder'  => 'Our Story',
				'wrapper'      => array( 'width' => '50' ),
			),
			array(
				'key'          => 'field_hero_btn2_url',
				'label'        => 'Secondary Link — URL',
				'name'         => 'hero_btn2_url',
				'type'         => 'url',
				'wrapper'      => array( 'width' => '50' ),
				'instructions' => 'Falls back to /about/ if empty.',
			),
			// ---------- MEDIA ----------
			array( 'key' => 'field_hero_tab_media', 'label' => 'Background Media', 'type' => 'tab', 'placement' => 'top' ),
			array(
				'key'           => 'field_hero_type',
				'label'         => 'Media Type',
				'name'          => 'hero_type',
				'type'          => 'radio',
				'choices'       => array(
					'image'  => 'Single Image',
					'slider' => 'Image Slider',
					'video'  => 'Video',
				),
				'default_value' => 'image',
				'layout'        => 'horizontal',
				'instructions'  => 'Choose what fills the right panel of the hero.',
			),
			// --- image ---
			array(
				'key'               => 'field_hero_image',
				'label'             => 'Hero Image',
				'name'              => 'hero_image',
				'type'              => 'image',
				'return_format'     => 'array',
				'preview_size'      => 'medium',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'image',
				) ) ),
			),
			// --- slider (6 fixed image fields; repeater is ACF PRO only) ---
			array(
				'key'               => 'field_hero_slides_msg',
				'label'             => 'Slides',
				'name'              => '',
				'type'              => 'message',
				'message'           => 'Add up to 6 slides. Empty slots are skipped. Slides need at least 2 images to rotate.',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'slider',
				) ) ),
			),
			array(
				'key'               => 'field_hero_slide_1',
				'label'             => 'Slide 1',
				'name'              => 'hero_slide_1',
				'type'              => 'image',
				'return_format'     => 'array',
				'preview_size'      => 'thumbnail',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'slider',
				) ) ),
			),
			array(
				'key'               => 'field_hero_slide_2',
				'label'             => 'Slide 2',
				'name'              => 'hero_slide_2',
				'type'              => 'image',
				'return_format'     => 'array',
				'preview_size'      => 'thumbnail',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'slider',
				) ) ),
			),
			array(
				'key'               => 'field_hero_slide_3',
				'label'             => 'Slide 3',
				'name'              => 'hero_slide_3',
				'type'              => 'image',
				'return_format'     => 'array',
				'preview_size'      => 'thumbnail',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'slider',
				) ) ),
			),
			array(
				'key'               => 'field_hero_slide_4',
				'label'             => 'Slide 4',
				'name'              => 'hero_slide_4',
				'type'              => 'image',
				'return_format'     => 'array',
				'preview_size'      => 'thumbnail',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'slider',
				) ) ),
			),
			array(
				'key'               => 'field_hero_slide_5',
				'label'             => 'Slide 5',
				'name'              => 'hero_slide_5',
				'type'              => 'image',
				'return_format'     => 'array',
				'preview_size'      => 'thumbnail',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'slider',
				) ) ),
			),
			array(
				'key'               => 'field_hero_slide_6',
				'label'             => 'Slide 6',
				'name'              => 'hero_slide_6',
				'type'              => 'image',
				'return_format'     => 'array',
				'preview_size'      => 'thumbnail',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'slider',
				) ) ),
			),
			// --- video source toggle ---
			array(
				'key'               => 'field_hero_video_source',
				'label'             => 'Video Source',
				'name'              => 'hero_video_source',
				'type'              => 'radio',
				'choices'           => array( 'upload' => 'Upload MP4', 'embed' => 'YouTube / Vimeo URL' ),
				'default_value'     => 'upload',
				'layout'            => 'horizontal',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'video',
				) ) ),
			),
			// --- video: mp4 upload ---
			array(
				'key'               => 'field_hero_video_file',
				'label'             => 'Video File (MP4)',
				'name'              => 'hero_video_file',
				'type'              => 'file',
				'return_format'     => 'url',
				'mime_types'        => 'mp4',
				'conditional_logic' => array( array(
					array( 'field' => 'field_hero_type',         'operator' => '==', 'value' => 'video' ),
					array( 'field' => 'field_hero_video_source', 'operator' => '==', 'value' => 'upload' ),
				) ),
			),
			// --- video: embed url ---
			array(
				'key'               => 'field_hero_video_embed',
				'label'             => 'YouTube / Vimeo URL',
				'name'              => 'hero_video_embed',
				'type'              => 'url',
				'conditional_logic' => array( array(
					array( 'field' => 'field_hero_type',         'operator' => '==', 'value' => 'video' ),
					array( 'field' => 'field_hero_video_source', 'operator' => '==', 'value' => 'embed' ),
				) ),
			),
			// --- video: poster image ---
			array(
				'key'               => 'field_hero_video_poster',
				'label'             => 'Poster Image (fallback)',
				'name'              => 'hero_video_poster',
				'type'              => 'image',
				'return_format'     => 'url',
				'preview_size'      => 'thumbnail',
				'conditional_logic' => array( array( array(
					'field' => 'field_hero_type', 'operator' => '==', 'value' => 'video',
				) ) ),
			),
		),
		'location' => array( array( array(
			'param' => 'page_type', 'operator' => '==', 'value' => 'front_page',
		) ) ),
		'position' => 'normal',
		'style'    => 'default',
	) );
}
add_action( 'acf/init', 'deon_hero_acf_fields' );

/**
 * ACF fields for the homepage "Selected Work" section copy. The tiles themselves
 * come from the `deon_project` CPT ( deon_featured_projects() ) — this group only
 * owns the text column. Every field falls back to the designed default.
 */
function deon_home_projects_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	acf_add_local_field_group( array(
		'key'    => 'group_deon_home_projects',
		'title'  => 'Selected Work Section',
		'fields' => array(
			array(
				'key'          => 'field_home_projects_msg',
				'label'        => '',
				'name'         => '',
				'type'         => 'message',
				'message'      => 'The four tiles are pulled from <strong>Projects</strong>. Tick “Show on homepage” on a project to feature it, and set its Order under Page Attributes. Fewer than four featured projects? The newest ones fill the rest.',
			),
			array(
				'key'          => 'field_home_projects_eyebrow',
				'label'        => 'Eyebrow',
				'name'         => 'home_projects_eyebrow',
				'type'         => 'text',
				'placeholder'  => 'Selected Work',
			),
			array(
				'key'          => 'field_home_projects_heading',
				'label'        => 'Heading',
				'name'         => 'home_projects_heading',
				'type'         => 'textarea',
				'rows'         => 2,
				'new_lines'    => '',
				'placeholder'  => "Our Work Speaks for\nItself",
				'instructions' => 'Each line break renders as a new line.',
			),
			array(
				'key'          => 'field_home_projects_text',
				'label'        => 'Intro Paragraph',
				'name'         => 'home_projects_text',
				'type'         => 'textarea',
				'rows'         => 3,
				'new_lines'    => '',
				'placeholder'  => 'A curated selection of our most ambitious solar infrastructure projects across Western India.',
			),
			array(
				'key'          => 'field_home_projects_btn_label',
				'label'        => 'Button — Label',
				'name'         => 'home_projects_btn_label',
				'type'         => 'text',
				'placeholder'  => 'View All Projects',
				'wrapper'      => array( 'width' => '50' ),
			),
			array(
				'key'          => 'field_home_projects_btn_url',
				'label'        => 'Button — URL',
				'name'         => 'home_projects_btn_url',
				'type'         => 'url',
				'wrapper'      => array( 'width' => '50' ),
				'instructions' => 'Falls back to /projects/ if empty.',
			),
		),
		'location' => array( array( array(
			'param' => 'page_type', 'operator' => '==', 'value' => 'front_page',
		) ) ),
		'position' => 'normal',
		'style'    => 'default',
	) );
}
add_action( 'acf/init', 'deon_home_projects_acf_fields' );

/**
 * ACF fields for the ESG & Sustainability page (Hero, Impact Stats, CTA).
 * ACF FREE — flat fields only (no repeaters). Scoped to the `esg` page by slug
 * lookup so the group never clutters other page editors. Pillars / SDG /
 * Community stay hardcoded in their partials.
 */
function deon_esg_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	$esg_page = get_page_by_path( 'esg' );
	$esg_id   = $esg_page ? $esg_page->ID : 0;

	$fields = array(
		// ---------- HERO ----------
		array( 'key' => 'field_esg_tab_hero', 'label' => 'Hero', 'type' => 'tab', 'placement' => 'top' ),
		array(
			'key' => 'field_esg_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'esg_hero_eyebrow',
			'type' => 'text', 'placeholder' => 'Institutional Responsibility',
		),
		array(
			'key' => 'field_esg_hero_heading', 'label' => 'Heading', 'name' => 'esg_hero_heading',
			'type' => 'text', 'placeholder' => 'ESG & Sustainability',
		),
		array(
			'key' => 'field_esg_hero_intro', 'label' => 'Intro Paragraph', 'name' => 'esg_hero_intro',
			'type' => 'textarea', 'rows' => 3,
		),
		array(
			'key' => 'field_esg_hero_image', 'label' => 'Hero Image', 'name' => 'esg_hero_image',
			'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium',
			'instructions' => 'Right-side square. Rendered grayscale. Leave empty to use the default.',
		),
		// The ESG dark-band "Impact Stats" now live in Appearance → Customize →
		// Site Statistics → ESG (inc/site-stats.php), alongside every other stat
		// band. Formerly four ACF stat groups here.
	);

	// ---------- CTA ----------
	$fields[] = array( 'key' => 'field_esg_tab_cta', 'label' => 'CTA', 'type' => 'tab', 'placement' => 'top' );
	$fields[] = array(
		'key' => 'field_esg_cta_heading', 'label' => 'Heading', 'name' => 'esg_cta_heading',
		'type' => 'text', 'placeholder' => 'Transparent Progress. Tangible Change.',
	);
	$fields[] = array(
		'key' => 'field_esg_cta_report_label', 'label' => 'Report Button — Label', 'name' => 'esg_cta_report_label',
		'type' => 'text', 'placeholder' => 'Download 2024 Report', 'wrapper' => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key' => 'field_esg_cta_report_file', 'label' => 'Report Button — File (PDF)', 'name' => 'esg_cta_report_file',
		'type' => 'file', 'return_format' => 'url', 'mime_types' => 'pdf', 'wrapper' => array( 'width' => '50' ),
		'instructions' => 'Upload the report PDF. Falls back to # if empty.',
	);
	$fields[] = array(
		'key' => 'field_esg_cta_investor_label', 'label' => 'Investor Button — Label', 'name' => 'esg_cta_investor_label',
		'type' => 'text', 'placeholder' => 'Investor Relations', 'wrapper' => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key' => 'field_esg_cta_investor_url', 'label' => 'Investor Button — URL', 'name' => 'esg_cta_investor_url',
		'type' => 'url', 'wrapper' => array( 'width' => '50' ),
		'instructions' => 'Falls back to /investor-relations/ if empty.',
	);

	acf_add_local_field_group( array(
		'key'      => 'group_deon_esg',
		'title'    => 'ESG Page Content',
		'fields'   => $fields,
		'location' => array( array( array(
			'param' => 'page', 'operator' => '==', 'value' => $esg_id,
		) ) ),
		'position' => 'normal',
		'style'    => 'default',
	) );
}
add_action( 'acf/init', 'deon_esg_acf_fields' );

/**
 * Inline single-path SVG icons for the dropdown rows (orange via currentColor).
 * No image files / no plugin — colourable and crisp at any size.
 */
function deon_nav_icon_svg( $key ) {
	$icons = array(
		// Panel / solar module grid.
		'solutions'  => '<path fill="currentColor" d="M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z"/>',
		// Sun.
		'solar'      => '<path fill="currentColor" d="M12 7a5 5 0 100 10 5 5 0 000-10zm0-6l1.8 3.1L12 3 10.2 4.1 12 1zm0 22l-1.8-3.1L12 21l1.8-1.1L12 23zM1 12l3.1-1.8L3 12l1.1 1.8L1 12zm22 0l-3.1 1.8L21 12l-1.1-1.8L23 12zM4.2 4.2l3.3 1.2-1.9 1.9L4.2 4.2zm15.6 15.6l-3.3-1.2 1.9-1.9 1.4 3.1zM19.8 4.2l-1.4 3.1-1.9-1.9 3.3-1.2zM4.2 19.8l1.4-3.1 1.9 1.9-3.3 1.2z"/>',
		// Folder.
		'projects'   => '<path fill="currentColor" d="M3 5a2 2 0 012-2h4l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>',
		// Building.
		'about'      => '<path fill="currentColor" d="M4 21V4a1 1 0 011-1h9a1 1 0 011 1v6h4a1 1 0 011 1v10H4zm3-13h4V6H7v2zm0 4h4v-2H7v2zm0 4h4v-2H7v2zm10 0h2v-2h-2v2zm0-4h2v-2h-2v2z"/>',
		// People.
		'leadership' => '<path fill="currentColor" d="M9 11a3.5 3.5 0 100-7 3.5 3.5 0 000 7zm7 0a3 3 0 100-6 3 3 0 000 6zM2 19c0-3 3.5-5 7-5s7 2 7 5v1H2v-1zm15.5-4.5c2.6.4 4.5 2 4.5 4.5v1h-4v-1c0-1.7-.6-3.2-1.6-4.4l1.1-.1z"/>',
		// Briefcase.
		'careers'    => '<path fill="currentColor" d="M9 4a2 2 0 012-2h2a2 2 0 012 2v1h4a2 2 0 012 2v11a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4V4zm2 1h2V4h-2v1z"/>',
		// Bookmark.
		'blogs'      => '<path fill="currentColor" d="M6 3h12a1 1 0 011 1v17l-7-4-7 4V4a1 1 0 011-1z"/>',
		// Question circle.
		'faqs'       => '<path fill="currentColor" d="M12 2a10 10 0 100 20 10 10 0 000-20zm.1 15a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4zm1.9-6.2c-.5.6-1.2 1-1.5 1.5-.2.3-.2.7-.2 1.2h-1.9c0-.8.1-1.5.5-2 .4-.6 1.1-1 1.4-1.4.3-.5.2-1.3-.4-1.7-.6-.4-1.6-.3-2 .4-.2.3-.3.7-.3 1.1H7.2c0-1.9 1.5-3.3 3.4-3.3 2 0 3.4 1.2 3.4 3 0 .6-.2 1.1-.6 1.5z"/>',
		// Headset.
		'contact'    => '<path fill="currentColor" d="M12 3a8 8 0 00-8 8v5a3 3 0 003 3h1v-7H6v-1a6 6 0 0112 0v1h-2v7h1a3 3 0 003-3v-5a8 8 0 00-8-8zM8 22h6a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
		// Chart / investors.
		'investors'  => '<path fill="currentColor" d="M4 3v18h17v-2H6V3H4zm5 12h2V9H9v6zm4 0h2V6h-2v9zm4 0h2v-4h-2v4z"/>',
		// Wrench / EPC build.
		'epc'        => '<path fill="currentColor" d="M21.7 5.6l-3.4 3.4-2.3-2.3 3.4-3.4a5 5 0 00-6.6 5.9L3.6 18.4a2 2 0 102.8 2.8l9.2-9.2a5 5 0 006.1-6.4z"/>',
		// Ruler / drafting — system design.
		'design'     => '<path fill="currentColor" d="M14.8 2.3l6.9 6.9a1 1 0 010 1.4L10.6 21.7a1 1 0 01-1.4 0l-6.9-6.9a1 1 0 010-1.4L13.4 2.3a1 1 0 011.4 0zM7.4 12l1.4 1.4 1.4-1.4-1.4-1.4L7.4 12zm3.5-3.5l2.1 2.1 1.4-1.4-2.1-2.1-1.4 1.4z"/>',
		// House with panel — rooftop.
		'rooftop'    => '<path fill="currentColor" d="M12 2.6L1.5 10.4l1.2 1.6L4 11v9h16v-9l1.3 1 1.2-1.6L12 2.6zM8 13h8v5H8v-5z"/>',
		// Rupee / finance.
		'finance'    => '<path fill="currentColor" d="M12 2a10 10 0 100 20 10 10 0 000-20zM8 6h8v1.6h-2.6c.3.4.5.9.6 1.4H16v1.6h-2c-.3 2-1.9 3.3-4.1 3.5L14 18h-2.3l-4.1-4.3v-1.5h1.5c1.7 0 2.7-.6 3-1.6H8V9h4.1c-.3-.9-1.3-1.4-3-1.4H8V6z"/>',
		// Gear — operations & maintenance.
		'operations' => '<path fill="currentColor" d="M12 8a4 4 0 100 8 4 4 0 000-8zm9.4 4a9.4 9.4 0 00-.1-1.3l2.1-1.6-2-3.5-2.5 1a9.3 9.3 0 00-2.3-1.3L16.2 2h-4l-.4 2.6c-.8.3-1.6.8-2.3 1.3l-2.5-1-2 3.5 2.1 1.6a9.4 9.4 0 000 2.6l-2.1 1.6 2 3.5 2.5-1c.7.6 1.5 1 2.3 1.3l.4 2.6h4l.4-2.6c.8-.3 1.6-.7 2.3-1.3l2.5 1 2-3.5-2.1-1.6c.1-.4.1-.9.1-1.3z"/>',
		// Lightbulb — advisory.
		'advisory'   => '<path fill="currentColor" d="M12 2a7 7 0 00-4 12.7V17a1 1 0 001 1h6a1 1 0 001-1v-2.3A7 7 0 0012 2zM9 19h6v1a2 2 0 01-2 2h-2a2 2 0 01-2-2v-1z"/>',
		// Check badge — completed projects.
		'completed'  => '<path fill="currentColor" d="M12 2l2.4 1.8 3-.2.9 2.9 2.5 1.7-1.1 2.8 1.1 2.8-2.5 1.7-.9 2.9-3-.2L12 22l-2.4-1.8-3 .2-.9-2.9-2.5-1.7L4.3 13l-1.1-2.8 2.5-1.7.9-2.9 3 .2L12 2zm-1 13.5l5-5-1.4-1.4-3.6 3.6-1.6-1.6L8 12.5l3 3z"/>',
		// Progress clock — ongoing projects.
		'ongoing'    => '<path fill="currentColor" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 5v5.3l4 2.4-1 1.7-5-3V7h2z"/>',
		// Grid — all projects.
		'grid'       => '<path fill="currentColor" d="M3 3h8v5H3V3zm10 0h8v9h-8V3zM3 10h8v11H3V10zm10 4h8v7h-8v-7z"/>',
		// Megaphone — news & media.
		'news'       => '<path fill="currentColor" d="M20 4v16l-9-4v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2.6A3.5 3.5 0 013 12a3.5 3.5 0 012-3.2V8h4l9-4zM7 10v4h1.6l.4.2V9.8l-.4.2H7z"/>',
		// Document with lines — investor documents.
		'documents'  => '<path fill="currentColor" d="M6 2h8l5 5v15H6a1 1 0 01-1-1V3a1 1 0 011-1zm7 1.8V8h4.2L13 3.8zM8 12h8v1.7H8V12zm0 4h8v1.7H8V16z"/>',
		// Leaf — ESG & sustainability.
		'esg'        => '<path fill="currentColor" d="M20 3c0 9-4.6 14-11 14H6.7c-.3 1.2-.5 2.5-.6 4H4c.2-2.6.7-4.8 1.5-6.7C3.9 12.4 3 10.4 3 8a5 5 0 015-5c2 0 3.5 1 5 1 2.3 0 4.6-.4 7-1z"/>',
		// Fallback dot.
		'default'    => '<circle cx="12" cy="12" r="4" fill="currentColor"/>',
	);
	$path = isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['default'];
	return '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">' . $path . '</svg>';
}

/**
 * Map a menu item to [ icon_key, default_description ] by its page slug (or a
 * sanitized title for custom links). Lets the designed dropdown copy + icons
 * show even before the client fills the admin Description field.
 */
function deon_nav_item_meta( $item ) {
	$slug = '';
	if ( ! empty( $item->object_id ) && 'page' === ( $item->object ?? '' ) ) {
		$slug = get_post_field( 'post_name', $item->object_id );
	}
	if ( empty( $slug ) ) {
		$slug = sanitize_title( $item->title );
	}

	$map = array(
		'about'                    => array( 'about', __( 'Learn about our mission, values, and the story behind our company.', 'deon-energy' ) ),
		'about-deon-energy'        => array( 'about', __( 'Learn about our mission, values, and the story behind our company.', 'deon-energy' ) ),
		'about-us'                 => array( 'about', __( 'Learn about our mission, values, and the story behind our company.', 'deon-energy' ) ),
		'leadership'               => array( 'leadership', __( 'Meet the team guiding our company to success.', 'deon-energy' ) ),
		'careers'                  => array( 'careers', __( 'Join our team and build a meaningful career with us.', 'deon-energy' ) ),
		'blogs'                    => array( 'blogs', __( 'News and updates.', 'deon-energy' ) ),
		'knowledge-hub'            => array( 'blogs', __( 'News and updates.', 'deon-energy' ) ),
		'faq'                      => array( 'faqs', __( 'Frequently asked questions.', 'deon-energy' ) ),
		'faqs'                     => array( 'faqs', __( 'Frequently asked questions.', 'deon-energy' ) ),
		'contact'                  => array( 'contact', __( 'Reach our team for assistance.', 'deon-energy' ) ),
		'contact-us'               => array( 'contact', __( 'Reach our team for assistance.', 'deon-energy' ) ),
		'solar-savings-calculator' => array( 'solar', __( 'Estimate your solar savings in seconds.', 'deon-energy' ) ),
		'calculator'               => array( 'solar', __( 'Estimate your solar savings in seconds.', 'deon-energy' ) ),
		'investor-relations'       => array( 'investors', __( 'Reports, disclosures, and announcements.', 'deon-energy' ) ),
		'investor-documents'       => array( 'documents', __( 'Download filings, policies, and annual reports.', 'deon-energy' ) ),
		'investors'                => array( 'investors', __( 'Reports, disclosures, and announcements.', 'deon-energy' ) ),
		'solutions'                => array( 'solutions', '' ),
		'project-gallery'          => array( 'projects', '' ),
		'projects'                 => array( 'projects', '' ),
		'esg'                      => array( 'esg', __( 'Our environmental, social, and governance commitments.', 'deon-energy' ) ),
		'news-media'               => array( 'news', __( 'Press coverage and company announcements.', 'deon-energy' ) ),
		'news-and-media'           => array( 'news', __( 'Press coverage and company announcements.', 'deon-energy' ) ),
		'press-coverage'           => array( 'news', __( 'Press coverage and company announcements.', 'deon-energy' ) ),
		// Solutions children (one per section of the Solutions page).
		'solar-epc'                => array( 'epc', __( 'Turnkey engineering, procurement, and construction.', 'deon-energy' ) ),
		'system-design-compliance' => array( 'design', __( 'Optimised layouts approved to GEDA, GUVNL, and MNRE norms.', 'deon-energy' ) ),
		'commercial-rooftop-solar' => array( 'rooftop', __( 'Cut scope 2 emissions and energy cost on your own roof.', 'deon-energy' ) ),
		'industrial-financial-models' => array( 'finance', __( 'CAPEX, OPEX, and zero-upfront ownership options.', 'deon-energy' ) ),
		'operations-maintenance'   => array( 'operations', __( 'Predictive analytics, cleaning automation, thermal imaging.', 'deon-energy' ) ),
		'advisory-consultancy'     => array( 'advisory', __( 'Bankability studies and independent technical reviews.', 'deon-energy' ) ),
		// Projects children.
		'completed-projects'       => array( 'completed', __( 'Commissioned plants delivering power today.', 'deon-energy' ) ),
		'ongoing-projects'         => array( 'ongoing', __( 'Sites currently under construction.', 'deon-energy' ) ),
		'all-projects'             => array( 'grid', __( 'Browse the full project gallery.', 'deon-energy' ) ),
	);

	if ( isset( $map[ $slug ] ) ) {
		return $map[ $slug ];
	}
	// Unmapped items still get a sensible icon by their parent section context.
	return array( 'default', '' );
}

/**
 * Custom nav walker for the primary menu.
 *
 * Renders a rich dropdown for submenu items: each child shows an orange icon,
 * a bold title, and a one-line description. The description comes from the menu
 * item's admin Description field, falling back to designed copy (see
 * deon_nav_item_meta). Icons are mapped by page slug — no plugin required.
 */
class Deon_Nav_Walker extends Walker_Nav_Menu {

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$args    = (object) $args;
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id_attr = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id_attr = $id_attr ? ' id="' . esc_attr( $id_attr ) . '"' : '';

		$output .= ( $depth ? str_repeat( "\t", $depth ) : '' ) . '<li' . $id_attr . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts           = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title        = apply_filters( 'the_title', $item->title, $item->ID );
		$has_children = in_array( 'menu-item-has-children', $classes, true );
		$item_output  = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';

		// Plain text rows at every level (Solex-style dropdown — no icon/description).
		$link_before  = isset( $args->link_before ) ? $args->link_before : '';
		$link_after   = isset( $args->link_after ) ? $args->link_after : '';
		$item_output .= $link_before . $title . $link_after;

		$item_output .= '</a>';

		// Top-level parents get a real <button> caret: opens the panel on click and
		// on keyboard (Enter/Space) as well as hover, and carries aria-expanded.
		if ( 0 === $depth && $has_children ) {
			$item_output .= deon_nav_submenu_toggle( $title );
		}

		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/**
 * Caret toggle button rendered next to every top-level parent item.
 * Keeps the parent link navigable while giving the dropdown a proper
 * click/keyboard control with aria-expanded state.
 */
function deon_nav_submenu_toggle( $label ) {
	return sprintf(
		'<button type="button" class="sub-menu-toggle" aria-expanded="false" aria-label="%s" data-submenu-toggle><span class="sub-menu-toggle__caret" aria-hidden="true"></span></button>',
		esc_attr( sprintf( /* translators: %s: menu item label. */ __( 'Toggle %s submenu', 'deon-energy' ), $label ) )
	);
}

/**
 * Structure of the primary menu (client change log v1, July 2026).
 *
 * Eight top-level items in this exact order, plus a single "Get a Quote" button
 * rendered separately in header.php:
 *   About Deon | Solutions | Projects | ESG | Resources | Investors | Contact | Solar Calculator
 *
 * Each child is [ label, path, icon key, one-line description ].
 *
 * @return array
 */
function deon_primary_menu_tree() {
	$tree = array(
		array(
			'label'    => __( 'About Deon', 'deon-energy' ),
			'path'     => '/about/',
			'children' => array(
				array( __( 'About Us', 'deon-energy' ), '/about/', 'about', __( 'Learn about our mission, values, and the story behind our company.', 'deon-energy' ) ),
				array( __( 'Leadership', 'deon-energy' ), '/leadership/', 'leadership', __( 'Meet the team guiding our company to success.', 'deon-energy' ) ),
				array( __( 'Careers', 'deon-energy' ), '/careers/', 'careers', __( 'Join our team and build a meaningful career with us.', 'deon-energy' ) ),
			),
		),
		array(
			// Plain link, no dropdown (client request): Solutions goes straight to
			// the page; its own section anchors cover the sub-capabilities.
			'label' => __( 'Solutions', 'deon-energy' ),
			'path'  => '/solutions/',
		),
		array(
			// Plain link, no dropdown (client request): the gallery's own filter
			// chips cover project categories, so the header needs no submenu.
			'label' => __( 'Projects', 'deon-energy' ),
			'path'  => '/projects/',
		),
		array(
			'label' => __( 'ESG', 'deon-energy' ),
			'path'  => '/esg/',
		),
		array(
			'label'    => __( 'Resources', 'deon-energy' ),
			'path'     => '/knowledge-hub/',
			'children' => array(
				array( __( 'Blogs', 'deon-energy' ), '/knowledge-hub/', 'blogs', __( 'News and updates.', 'deon-energy' ) ),
				array( __( 'News and Media', 'deon-energy' ), '/news-media/', 'news', __( 'Press coverage and company announcements.', 'deon-energy' ) ),
				array( __( 'FAQs', 'deon-energy' ), '/faq/', 'faqs', __( 'Frequently asked questions.', 'deon-energy' ) ),
			),
		),
		array(
			'label'    => __( 'Investors', 'deon-energy' ),
			'path'     => '/investor-relations/',
			'children' => array(
				array( __( 'Investor Relations', 'deon-energy' ), '/investor-relations/', 'investors', __( 'Reports, disclosures, and announcements.', 'deon-energy' ) ),
				array( __( 'Investor Documents', 'deon-energy' ), '/investor-documents/', 'documents', __( 'Download filings, policies, and annual reports.', 'deon-energy' ) ),
			),
		),
		array(
			'label' => __( 'Contact', 'deon-energy' ),
			'path'  => '/contact/',
		),
		// Solar Calculator is NOT a nav item — it renders as an outline pill button
		// in the header actions group (header.php), beside "Get a Quote".
	);

	return apply_filters( 'deon_primary_menu_tree', $tree );
}


/**
 * Fallback for the primary menu before the client assigns one in the admin.
 * Renders the full change-log navigation, markup-identical to Deon_Nav_Walker
 * so the dark dropdown panel, icons and descriptions look the same either way.
 */
function deon_primary_menu_fallback() {
	global $wp;
	$current = '/' . trailingslashit( ltrim( $wp->request, '/' ) ); // e.g. "/solutions/"

	echo '<ul class="site-nav">';

	foreach ( deon_primary_menu_tree() as $top ) {
		$children = isset( $top['children'] ) ? $top['children'] : array();
		$classes  = array( 'menu-item' );

		if ( $children ) {
			$classes[] = 'menu-item-has-children';
		}
		if ( $current === $top['path'] ) {
			$classes[] = 'current-menu-item';
		} else {
			foreach ( $children as $child ) {
				if ( $current === strtok( $child[1], '#' ) ) {
					$classes[] = 'current-menu-parent';
					break;
				}
			}
		}

		printf(
			'<li class="%s"><a href="%s">%s</a>',
			esc_attr( implode( ' ', $classes ) ),
			esc_url( home_url( $top['path'] ) ),
			esc_html( $top['label'] )
		);

		if ( $children ) {
			echo deon_nav_submenu_toggle( $top['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside.
			echo '<ul class="sub-menu">';
			foreach ( $children as $child ) {
				list( $label, $path ) = array_pad( $child, 2, '' );
				printf(
					'<li class="menu-item"><a href="%s">%s</a></li>',
					esc_url( home_url( $path ) ),
					esc_html( $label )
				);
			}
			echo '</ul>';
		}

		echo '</li>';
	}

	echo '</ul>';
}

/**
 * Show the menu-item Description field by default in Appearance → Menus, so the
 * client can override the designed dropdown copy without hunting in Screen Options.
 */
function deon_nav_menu_show_description( $columns ) {
	$columns['description'] = __( 'Description', 'deon-energy' );
	return $columns;
}
add_filter( 'manage_nav-menus_columns', 'deon_nav_menu_show_description', 11 );

/**
 * Custom Post Type: Office (Contact page location cards).
 * Client adds / edits / reorders offices in the WP admin. Works on ACF-free
 * (options pages & repeaters are ACF PRO only), matching the deon_project pattern.
 */
function deon_register_office_cpt() {
	register_post_type( 'deon_office', array(
		'labels'        => array(
			'name'          => __( 'Offices', 'deon-energy' ),
			'singular_name' => __( 'Office', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Office', 'deon-energy' ),
			'edit_item'     => __( 'Edit Office', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-location',
		'menu_position' => 22,
		'supports'      => array( 'title', 'page-attributes' ), // title = office name; page-attributes = menu_order for drag-sort.
		'hierarchical'  => false,
	) );
}
add_action( 'init', 'deon_register_office_cpt' );

/**
 * Meta box: Address + Map URL + Icon for deon_office.
 */
function deon_office_meta_box_register() {
	add_meta_box(
		'deon_office_details',
		__( 'Office Details', 'deon-energy' ),
		'deon_office_meta_box_html',
		'deon_office',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'deon_office_meta_box_register' );

function deon_office_meta_box_html( $post ) {
	wp_nonce_field( 'deon_office_save', 'deon_office_nonce' );
	$address = get_post_meta( $post->ID, '_deon_office_address', true );
	$map_url = get_post_meta( $post->ID, '_deon_office_map_url', true );
	$icon    = get_post_meta( $post->ID, '_deon_office_icon', true );
	$icons   = deon_office_icons();
	?>
	<p>
		<label for="deon_office_address"><strong><?php esc_html_e( 'Address', 'deon-energy' ); ?></strong></label><br>
		<textarea id="deon_office_address" name="deon_office_address" rows="3" style="width:100%;margin-top:4px;" placeholder="D-604, 605, 606 Westgate, Near YMCA Club&#10;S.G. Highway, Makarba, Ahmedabad-380051, Gujarat, India."><?php echo esc_textarea( $address ); ?></textarea>
	</p>
	<p>
		<label for="deon_office_map_url"><strong><?php esc_html_e( 'Map URL (Google Maps link)', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_office_map_url" name="deon_office_map_url" value="<?php echo esc_attr( $map_url ); ?>" style="width:100%;margin-top:4px;" placeholder="https://maps.google.com/?q=...">
	</p>
	<p>
		<label for="deon_office_icon"><strong><?php esc_html_e( 'Icon', 'deon-energy' ); ?></strong></label><br>
		<select id="deon_office_icon" name="deon_office_icon" style="margin-top:4px;">
			<?php foreach ( $icons as $key => $svg ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>><?php echo esc_html( ucfirst( $key ) ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

function deon_office_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_office_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_office_nonce'] ) ), 'deon_office_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_office_address'] ) ) {
		update_post_meta( $post_id, '_deon_office_address', sanitize_textarea_field( wp_unslash( $_POST['deon_office_address'] ) ) );
	}
	if ( isset( $_POST['deon_office_map_url'] ) ) {
		update_post_meta( $post_id, '_deon_office_map_url', esc_url_raw( wp_unslash( $_POST['deon_office_map_url'] ) ) );
	}
	if ( isset( $_POST['deon_office_icon'] ) ) {
		$icon = sanitize_key( wp_unslash( $_POST['deon_office_icon'] ) );
		update_post_meta( $post_id, '_deon_office_icon', array_key_exists( $icon, deon_office_icons() ) ? $icon : 'pin' );
	}
}
add_action( 'save_post_deon_office', 'deon_office_meta_save' );

/**
 * Inline SVG icons for office cards. Keyed by the icon meta value.
 * Exact Figma glyphs (filled); fill uses currentColor so CSS controls colour.
 */
function deon_office_icons() {
	return array(
		'pin'      => '<svg viewBox="0 0 16 20" fill="currentColor" width="100%" height="100%"><path d="M8 10C8.55 10 9.02083 9.80417 9.4125 9.4125C9.80417 9.02083 10 8.55 10 8C10 7.45 9.80417 6.97917 9.4125 6.5875C9.02083 6.19583 8.55 6 8 6C7.45 6 6.97917 6.19583 6.5875 6.5875C6.19583 6.97917 6 7.45 6 8C6 8.55 6.19583 9.02083 6.5875 9.4125C6.97917 9.80417 7.45 10 8 10V10M8 20C5.31667 17.7167 3.3125 15.5958 1.9875 13.6375C0.6625 11.6792 0 9.86667 0 8.2C0 5.7 0.804167 3.70833 2.4125 2.225C4.02083 0.741667 5.88333 0 8 0C10.1167 0 11.9792 0.741667 13.5875 2.225C15.1958 3.70833 16 5.7 16 8.2C16 9.86667 15.3375 11.6792 14.0125 13.6375C12.6875 15.5958 10.6833 17.7167 8 20V20Z"/></svg>',
		'building' => '<svg viewBox="0 0 20 18" fill="currentColor" width="100%" height="100%"><path d="M0 18V0H10V4H20V18H0V18M2 16H4V14H2V16V16M2 12H4V10H2V12V12M2 8H4V6H2V8V8M2 4H4V2H2V4V4M6 16H8V14H6V16V16M6 12H8V10H6V12V12M6 8H8V6H6V8V8M6 4H8V2H6V4V4M10 16H18V6H10V8H12V10H10V12H12V14H10V16V16M14 10V8H16V10H14V10M14 14V12H16V14H14V14Z"/></svg>',
		'globe'    => '<svg viewBox="0 0 24 23" fill="currentColor" width="100%" height="100%"><path d="M6 23C5.16667 23 4.45833 22.7083 3.875 22.125C3.29167 21.5417 3 20.8333 3 20C3 19.1667 3.29167 18.4583 3.875 17.875C4.45833 17.2917 5.16667 17 6 17C6.23333 17 6.45 17.025 6.65 17.075C6.85 17.125 7.04167 17.1917 7.225 17.275L8.65 15.5C8.18333 14.9833 7.85833 14.4 7.675 13.75C7.49167 13.1 7.45 12.45 7.55 11.8L5.525 11.125C5.24167 11.5417 4.88333 11.875 4.45 12.125C4.01667 12.375 3.53333 12.5 3 12.5C2.16667 12.5 1.45833 12.2083 0.875 11.625C0.291667 11.0417 0 10.3333 0 9.5C0 8.66667 0.291667 7.95833 0.875 7.375C1.45833 6.79167 2.16667 6.5 3 6.5C3.83333 6.5 4.54167 6.79167 5.125 7.375C5.70833 7.95833 6 8.66667 6 9.5C6 9.53333 6 9.56667 6 9.6C6 9.63333 6 9.66667 6 9.7L8.025 10.4C8.35833 9.8 8.80417 9.29167 9.3625 8.875C9.92083 8.45833 10.55 8.19167 11.25 8.075V5.9C10.6 5.71667 10.0625 5.3625 9.6375 4.8375C9.2125 4.3125 9 3.7 9 3C9 2.16667 9.29167 1.45833 9.875 0.875C10.4583 0.291667 11.1667 0 12 0C12.8333 0 13.5417 0.291667 14.125 0.875C14.7083 1.45833 15 2.16667 15 3C15 3.7 14.7833 4.3125 14.35 4.8375C13.9167 5.3625 13.3833 5.71667 12.75 5.9V8.075C13.45 8.19167 14.0792 8.45833 14.6375 8.875C15.1958 9.29167 15.6417 9.8 15.975 10.4L18 9.7C18 9.66667 18 9.63333 18 9.6C18 9.56667 18 9.53333 18 9.5C18 8.66667 18.2917 7.95833 18.875 7.375C19.4583 6.79167 20.1667 6.5 21 6.5C21.8333 6.5 22.5417 6.79167 23.125 7.375C23.7083 7.95833 24 8.66667 24 9.5C24 10.3333 23.7083 11.0417 23.125 11.625C22.5417 12.2083 21.8333 12.5 21 12.5C20.4667 12.5 19.9792 12.375 19.5375 12.125C19.0958 11.875 18.7417 11.5417 18.475 11.125L16.45 11.8C16.55 12.45 16.5083 13.0958 16.325 13.7375C16.1417 14.3792 15.8167 14.9667 15.35 15.5L16.775 17.25C16.9583 17.1667 17.15 17.1042 17.35 17.0625C17.55 17.0208 17.7667 17 18 17C18.8333 17 19.5417 17.2917 20.125 17.875C20.7083 18.4583 21 19.1667 21 20C21 20.8333 20.7083 21.5417 20.125 22.125C19.5417 22.7083 18.8333 23 18 23C17.1667 23 16.4583 22.7083 15.875 22.125C15.2917 21.5417 15 20.8333 15 20C15 19.6667 15.0542 19.3458 15.1625 19.0375C15.2708 18.7292 15.4167 18.45 15.6 18.2L14.175 16.425C13.4917 16.8083 12.7625 17 11.9875 17C11.2125 17 10.4833 16.8083 9.8 16.425L8.4 18.2C8.58333 18.45 8.72917 18.7292 8.8375 19.0375C8.94583 19.3458 9 19.6667 9 20C9 20.8333 8.70833 21.5417 8.125 22.125C7.54167 22.7083 6.83333 23 6 23V23Z"/></svg>',
	);
}

/**
 * Contact-page subject options (dropdown + inquiry storage).
 */
function deon_inquiry_subjects() {
	return array(
		'general'     => __( 'General Inquiry', 'deon-energy' ),
		'project'     => __( 'Project Quote', 'deon-energy' ),
		'investor'    => __( 'Investor Relations', 'deon-energy' ),
		'partnership' => __( 'Partnership', 'deon-energy' ),
		'careers'     => __( 'Careers', 'deon-energy' ),
	);
}

/**
 * Custom Post Type: Contact Inquiry.
 * Stores every contact-form submission so the client can read them in the WP
 * admin. Not publicly queryable; entries are created by the form handler only.
 */
function deon_register_inquiry_cpt() {
	register_post_type( 'deon_inquiry', array(
		'labels'        => array(
			'name'          => __( 'Inquiries', 'deon-energy' ),
			'singular_name' => __( 'Inquiry', 'deon-energy' ),
			'edit_item'     => __( 'View Inquiry', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-email-alt',
		'menu_position' => 23,
		'supports'      => array( 'title' ), // message shown read-only in the details meta box
		'capabilities'  => array( 'create_posts' => 'do_not_allow' ), // block manual creation; form handler inserts.
		'map_meta_cap'  => true,
	) );
}
add_action( 'init', 'deon_register_inquiry_cpt' );

/**
 * Admin list columns for inquiries: email, subject, date received.
 */
function deon_inquiry_columns( $columns ) {
	return array(
		'cb'             => $columns['cb'],
		'title'          => __( 'Name', 'deon-energy' ),
		'deon_email'     => __( 'Email', 'deon-energy' ),
		'deon_org'       => __( 'Organization', 'deon-energy' ),
		'deon_subject'   => __( 'Subject', 'deon-energy' ),
		'date'           => __( 'Received', 'deon-energy' ),
	);
}
add_filter( 'manage_deon_inquiry_posts_columns', 'deon_inquiry_columns' );

function deon_inquiry_column_content( $column, $post_id ) {
	if ( 'deon_email' === $column ) {
		$email = get_post_meta( $post_id, '_deon_inquiry_email', true );
		if ( $email ) {
			printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) );
		}
	} elseif ( 'deon_org' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_deon_inquiry_org', true ) );
	} elseif ( 'deon_subject' === $column ) {
		$subjects = deon_inquiry_subjects();
		$key      = get_post_meta( $post_id, '_deon_inquiry_subject', true );
		echo esc_html( isset( $subjects[ $key ] ) ? $subjects[ $key ] : $key );
	}
}
add_action( 'manage_deon_inquiry_posts_custom_column', 'deon_inquiry_column_content', 10, 2 );

/**
 * Read-only details meta box on the "View Inquiry" edit screen.
 * The edit screen only shows title + editor by default, so the captured
 * email / organization / subject (stored as post meta) are invisible there.
 * This surfaces every submitted field in one summary panel.
 */
function deon_inquiry_add_meta_box() {
	add_meta_box(
		'deon_inquiry_details',
		__( 'Inquiry Details', 'deon-energy' ),
		'deon_inquiry_details_box',
		'deon_inquiry',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_deon_inquiry', 'deon_inquiry_add_meta_box' );

function deon_inquiry_details_box( $post ) {
	$email    = get_post_meta( $post->ID, '_deon_inquiry_email', true );
	$org      = get_post_meta( $post->ID, '_deon_inquiry_org', true );
	$subjects = deon_inquiry_subjects();
	$key      = get_post_meta( $post->ID, '_deon_inquiry_subject', true );
	$subject  = isset( $subjects[ $key ] ) ? $subjects[ $key ] : $key;
	$received = get_the_date( 'F j, Y \a\t g:i a', $post );

	$rows = array(
		__( 'Name / Subject', 'deon-energy' ) => esc_html( get_the_title( $post ) ),
		__( 'Email', 'deon-energy' )          => $email ? sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) ) : '&mdash;',
		__( 'Organization', 'deon-energy' )   => $org ? esc_html( $org ) : '&mdash;',
		__( 'Subject', 'deon-energy' )        => $subject ? esc_html( $subject ) : '&mdash;',
		__( 'Received', 'deon-energy' )       => esc_html( $received ),
	);

	echo '<table class="widefat striped" style="border:0;"><tbody>';
	foreach ( $rows as $label => $value ) {
		printf(
			'<tr><th scope="row" style="width:180px;">%s</th><td>%s</td></tr>',
			esc_html( $label ),
			$value // already escaped above
		);
	}
	echo '</tbody></table>';

	$message = trim( wp_strip_all_tags( $post->post_content ) );
	echo '<p style="margin:1em 0 .35em;"><strong>' . esc_html__( 'Message', 'deon-energy' ) . '</strong></p>';
	echo '<div style="padding:12px;background:#f6f7f7;border:1px solid #dcdcde;white-space:pre-wrap;">';
	echo $message !== '' ? esc_html( $message ) : esc_html__( '(no message)', 'deon-energy' );
	echo '</div>';
}

/**
 * Contact recipient — Customizer field (defaults to admin email).
 */
function deon_contact_customizer( $wp_customize ) {
	$wp_customize->add_section( 'deon_contact', array(
		'title'    => __( 'Contact Form', 'deon-energy' ),
		'priority' => 31,
	) );
	$wp_customize->add_setting( 'deon_contact_recipient', array(
		'default'           => get_option( 'admin_email' ),
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'deon_contact_recipient', array(
		'label'       => __( 'Inquiry notification email', 'deon-energy' ),
		'description' => __( 'Where contact-form submissions are emailed.', 'deon-energy' ),
		'section'     => 'deon_contact',
		'type'        => 'email',
	) );
}
add_action( 'customize_register', 'deon_contact_customizer' );

/**
 * Resolve the client notification recipient (Customizer setting, admin_email
 * fallback). Shared by the contact-form and newsletter handlers so the address
 * and its fallback live in one place.
 */
function deon_notification_recipient() {
	$recipient = get_theme_mod( 'deon_contact_recipient', get_option( 'admin_email' ) );
	return is_email( $recipient ) ? $recipient : get_option( 'admin_email' );
}

/**
 * Handle contact-form submission: validate, store as deon_inquiry, email client.
 * Posts to admin-post.php (works for logged-out visitors via the nopriv hook).
 */
function deon_handle_contact_submit() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/contact/' );

	// Nonce.
	if ( ! isset( $_POST['deon_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_contact_nonce'] ) ), 'deon_contact_submit' ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'error', $redirect ) . '#contact-form' );
		exit;
	}

	// Honeypot — bots fill this hidden field; humans never see it.
	if ( ! empty( $_POST['deon_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'success', $redirect ) . '#contact-form' ); // silent drop.
		exit;
	}

	$name    = isset( $_POST['deon_name'] ) ? sanitize_text_field( wp_unslash( $_POST['deon_name'] ) ) : '';
	$email   = isset( $_POST['deon_email'] ) ? sanitize_email( wp_unslash( $_POST['deon_email'] ) ) : '';
	$org     = isset( $_POST['deon_org'] ) ? sanitize_text_field( wp_unslash( $_POST['deon_org'] ) ) : '';
	$subject = isset( $_POST['deon_subject'] ) ? sanitize_key( wp_unslash( $_POST['deon_subject'] ) ) : '';
	$message = isset( $_POST['deon_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['deon_message'] ) ) : '';

	$subjects = deon_inquiry_subjects();
	if ( ! array_key_exists( $subject, $subjects ) ) {
		$subject = 'general';
	}

	// Required fields.
	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		wp_safe_redirect( add_query_arg( 'contact', 'error', $redirect ) . '#contact-form' );
		exit;
	}

	// Store the inquiry.
	$inquiry_id = wp_insert_post( array(
		'post_type'    => 'deon_inquiry',
		'post_status'  => 'publish',
		/* translators: 1: sender name, 2: subject label */
		'post_title'   => sprintf( __( '%1$s — %2$s', 'deon-energy' ), $name, $subjects[ $subject ] ),
		'post_content' => $message,
	) );

	if ( ! is_wp_error( $inquiry_id ) && $inquiry_id ) {
		update_post_meta( $inquiry_id, '_deon_inquiry_email', $email );
		update_post_meta( $inquiry_id, '_deon_inquiry_org', $org );
		update_post_meta( $inquiry_id, '_deon_inquiry_subject', $subject );
	}

	// Email the client.
	$recipient = deon_notification_recipient();
	$site  = get_bloginfo( 'name' );
	$body  = sprintf( "%s: %s\n", __( 'Name', 'deon-energy' ), $name );
	$body .= sprintf( "%s: %s\n", __( 'Email', 'deon-energy' ), $email );
	$body .= sprintf( "%s: %s\n", __( 'Organization', 'deon-energy' ), $org );
	$body .= sprintf( "%s: %s\n\n", __( 'Subject', 'deon-energy' ), $subjects[ $subject ] );
	$body .= sprintf( "%s:\n%s\n", __( 'Message', 'deon-energy' ), $message );

	$headers = array(
		'Reply-To: ' . $name . ' <' . $email . '>',
	);
	wp_mail(
		$recipient,
		/* translators: 1: site name, 2: subject label */
		sprintf( __( '[%1$s] New inquiry: %2$s', 'deon-energy' ), $site, $subjects[ $subject ] ),
		$body,
		$headers
	);

	wp_safe_redirect( add_query_arg( 'contact', 'success', $redirect ) . '#contact-form' );
	exit;
}
add_action( 'admin_post_deon_contact_submit', 'deon_handle_contact_submit' );
add_action( 'admin_post_nopriv_deon_contact_submit', 'deon_handle_contact_submit' );

/**
 * ===========================================================================
 * Knowledge Hub — editable modules (Impact & Execution, In The Press).
 * The blog grid uses native posts; the Resource Archive reuses deon_document.
 * These two CPTs back the remaining designed sections so the client can edit
 * them in wp-admin. ACF-free, matching the deon_project / deon_leader pattern.
 * ===========================================================================
 */

/**
 * Custom Post Type: Case Study (Knowledge Hub — "Impact & Execution").
 * Title = project name (accent eyebrow). Challenge / Solution / Result meta.
 */
function deon_register_case_study_cpt() {
	register_post_type( 'deon_case_study', array(
		'labels'        => array(
			'name'          => __( 'Case Studies', 'deon-energy' ),
			'singular_name' => __( 'Case Study', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Case Study', 'deon-energy' ),
			'edit_item'     => __( 'Edit Case Study', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-analytics',
		'menu_position' => 24,
		'supports'      => array( 'title', 'page-attributes' ), // title = project name; page-attributes = menu_order for drag-sort.
		'hierarchical'  => false,
	) );
}
add_action( 'init', 'deon_register_case_study_cpt' );

function deon_case_study_meta_box_register() {
	add_meta_box(
		'deon_case_study_details',
		__( 'Case Study Details', 'deon-energy' ),
		'deon_case_study_meta_box_html',
		'deon_case_study',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'deon_case_study_meta_box_register' );

function deon_case_study_meta_box_html( $post ) {
	wp_nonce_field( 'deon_case_study_save', 'deon_case_study_nonce' );
	$challenge = get_post_meta( $post->ID, '_deon_cs_challenge', true );
	$solution  = get_post_meta( $post->ID, '_deon_cs_solution', true );
	$result    = get_post_meta( $post->ID, '_deon_cs_result', true );
	?>
	<p>
		<label for="deon_cs_challenge"><strong><?php esc_html_e( 'Challenge', 'deon-energy' ); ?></strong></label><br>
		<textarea id="deon_cs_challenge" name="deon_cs_challenge" rows="2" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. High saline environment causing rapid component corrosion and energy loss.', 'deon-energy' ); ?>"><?php echo esc_textarea( $challenge ); ?></textarea>
	</p>
	<p>
		<label for="deon_cs_solution"><strong><?php esc_html_e( 'Solution', 'deon-energy' ); ?></strong></label><br>
		<textarea id="deon_cs_solution" name="deon_cs_solution" rows="2" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. Deployment of specialized salt-resistant module coatings and automated cleaning.', 'deon-energy' ); ?>"><?php echo esc_textarea( $solution ); ?></textarea>
	</p>
	<p>
		<label for="deon_cs_result"><strong><?php esc_html_e( 'Result (accent headline)', 'deon-energy' ); ?></strong></label><br>
		<input type="text" id="deon_cs_result" name="deon_cs_result" value="<?php echo esc_attr( $result ); ?>" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. +18% Efficiency', 'deon-energy' ); ?>">
		<br><span style="color:#646970;"><?php esc_html_e( 'Set the project name in the title. Drag-sort with the Order field (Page Attributes).', 'deon-energy' ); ?></span>
	</p>
	<?php
}

function deon_case_study_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_case_study_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_case_study_nonce'] ) ), 'deon_case_study_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_cs_challenge'] ) ) {
		update_post_meta( $post_id, '_deon_cs_challenge', sanitize_textarea_field( wp_unslash( $_POST['deon_cs_challenge'] ) ) );
	}
	if ( isset( $_POST['deon_cs_solution'] ) ) {
		update_post_meta( $post_id, '_deon_cs_solution', sanitize_textarea_field( wp_unslash( $_POST['deon_cs_solution'] ) ) );
	}
	if ( isset( $_POST['deon_cs_result'] ) ) {
		update_post_meta( $post_id, '_deon_cs_result', sanitize_text_field( wp_unslash( $_POST['deon_cs_result'] ) ) );
	}
}
add_action( 'save_post_deon_case_study', 'deon_case_study_meta_save' );

/**
 * Custom Post Type: Press Mention (Knowledge Hub — "In The Press").
 * Title = outlet name; featured image = outlet logo; quote + article URL meta.
 */
function deon_register_press_cpt() {
	register_post_type( 'deon_press', array(
		'labels'        => array(
			'name'          => __( 'Press Mentions', 'deon-energy' ),
			'singular_name' => __( 'Press Mention', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Press Mention', 'deon-energy' ),
			'edit_item'     => __( 'Edit Press Mention', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-megaphone',
		'menu_position' => 25,
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ), // title = outlet; thumbnail = logo; page-attributes = menu_order.
		'hierarchical'  => false,
	) );
}
add_action( 'init', 'deon_register_press_cpt' );

function deon_press_meta_box_register() {
	add_meta_box(
		'deon_press_details',
		__( 'Press Mention Details', 'deon-energy' ),
		'deon_press_meta_box_html',
		'deon_press',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'deon_press_meta_box_register' );

function deon_press_meta_box_html( $post ) {
	wp_nonce_field( 'deon_press_save', 'deon_press_nonce' );
	$quote = get_post_meta( $post->ID, '_deon_press_quote', true );
	$url   = get_post_meta( $post->ID, '_deon_press_url', true );
	?>
	<p>
		<label for="deon_press_quote"><strong><?php esc_html_e( 'Quote', 'deon-energy' ); ?></strong></label><br>
		<textarea id="deon_press_quote" name="deon_press_quote" rows="3" style="width:100%;margin-top:4px;" placeholder="<?php esc_attr_e( 'e.g. Deon Energy Limited sets a new benchmark for corporate transparency in the Gujarat solar sector.', 'deon-energy' ); ?>"><?php echo esc_textarea( $quote ); ?></textarea>
		<br><span style="color:#646970;"><?php esc_html_e( 'Quotation marks are added automatically. Set the outlet name in the title and its logo via the Featured Image box.', 'deon-energy' ); ?></span>
	</p>
	<p>
		<label for="deon_press_url"><strong><?php esc_html_e( 'Article URL', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_press_url" name="deon_press_url" value="<?php echo esc_attr( $url ); ?>" style="width:100%;margin-top:4px;" placeholder="https://…">
		<br><span style="color:#646970;"><?php esc_html_e( 'The "View Article" link. Leave empty to hide the link. Opens in a new tab.', 'deon-energy' ); ?></span>
	</p>
	<?php
}

function deon_press_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_press_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_press_nonce'] ) ), 'deon_press_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_press_quote'] ) ) {
		update_post_meta( $post_id, '_deon_press_quote', sanitize_textarea_field( wp_unslash( $_POST['deon_press_quote'] ) ) );
	}
	if ( isset( $_POST['deon_press_url'] ) ) {
		update_post_meta( $post_id, '_deon_press_url', esc_url_raw( wp_unslash( $_POST['deon_press_url'] ) ) );
	}
}
add_action( 'save_post_deon_press', 'deon_press_meta_save' );

/**
 * Custom Post Type: Client (homepage logo ticker).
 * Title = client name; featured image = logo; menu_order = ticker order.
 * The ticker section hides entirely until at least one client is published,
 * so the homepage never ships unverified client names.
 */
function deon_register_client_cpt() {
	register_post_type( 'deon_client', array(
		'labels'        => array(
			'name'          => __( 'Clients', 'deon-energy' ),
			'singular_name' => __( 'Client', 'deon-energy' ),
			'add_new_item'  => __( 'Add New Client', 'deon-energy' ),
			'edit_item'     => __( 'Edit Client', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 26,
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ), // title = name; thumbnail = logo; page-attributes = menu_order.
		'hierarchical'  => false,
	) );
}
add_action( 'init', 'deon_register_client_cpt' );

function deon_client_meta_box_register() {
	add_meta_box(
		'deon_client_details',
		__( 'Client Details', 'deon-energy' ),
		'deon_client_meta_box_html',
		'deon_client',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'deon_client_meta_box_register' );

function deon_client_meta_box_html( $post ) {
	wp_nonce_field( 'deon_client_save', 'deon_client_nonce' );
	$url = get_post_meta( $post->ID, '_deon_client_url', true );
	?>
	<p style="color:#646970;margin-top:0;">
		<?php esc_html_e( 'Set the client name in the title and upload the logo via the Featured Image box (transparent PNG or SVG works best). Use Page Attributes → Order to control the ticker sequence.', 'deon-energy' ); ?>
	</p>
	<p>
		<label for="deon_client_url"><strong><?php esc_html_e( 'Website URL', 'deon-energy' ); ?></strong></label><br>
		<input type="url" id="deon_client_url" name="deon_client_url" value="<?php echo esc_attr( $url ); ?>" style="width:100%;margin-top:4px;" placeholder="https://…">
		<br><span style="color:#646970;"><?php esc_html_e( 'Optional. Links the logo in the ticker. Leave empty for a non-clickable logo.', 'deon-energy' ); ?></span>
	</p>
	<?php
}

function deon_client_meta_save( $post_id ) {
	if ( ! isset( $_POST['deon_client_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['deon_client_nonce'] ) ), 'deon_client_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['deon_client_url'] ) ) {
		update_post_meta( $post_id, '_deon_client_url', esc_url_raw( wp_unslash( $_POST['deon_client_url'] ) ) );
	}
}
add_action( 'save_post_deon_client', 'deon_client_meta_save' );

/**
 * Clients list screen: show the logo, and sort by ticker order by default.
 */
function deon_client_admin_columns( $columns ) {
	return array_merge(
		array( 'cb' => $columns['cb'], 'deon_logo' => __( 'Logo', 'deon-energy' ) ),
		$columns
	);
}
add_filter( 'manage_deon_client_posts_columns', 'deon_client_admin_columns' );

function deon_client_admin_column_html( $column, $post_id ) {
	if ( 'deon_logo' !== $column ) {
		return;
	}
	if ( has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail( $post_id, array( 80, 40 ), array( 'style' => 'width:80px;height:40px;object-fit:contain;' ) );
	} else {
		echo '<span style="color:#d63638;">' . esc_html__( 'No logo', 'deon-energy' ) . '</span>';
	}
}
add_action( 'manage_deon_client_posts_custom_column', 'deon_client_admin_column_html', 10, 2 );

function deon_client_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'deon_client' !== $query->get( 'post_type' ) ) {
		return;
	}
	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'deon_client_admin_order' );

/**
 * Published clients for the homepage logo ticker, in menu_order.
 *
 * @param int $limit Maximum clients to return.
 * @return WP_Post[] Empty when no clients exist — the ticker then hides.
 */
function deon_client_logos( $limit = 12 ) {
	return get_posts( array(
		'post_type'      => 'deon_client',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $limit,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	) );
}

/**
 * Format the "PDF • 12.4 MB" meta line for a deon_document file, deriving the
 * extension + size from the attached Media Library file. Returns '' when the
 * document has no file yet. Used by the Knowledge Hub Resource Archive.
 */
function deon_document_file_meta( $post_id ) {
	$file = (string) get_post_meta( $post_id, '_deon_document_file', true );
	if ( ! $file ) {
		return '';
	}

	$ext  = strtoupper( pathinfo( wp_parse_url( $file, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	$size = '';

	$att_id = attachment_url_to_postid( $file );
	if ( $att_id ) {
		$path = get_attached_file( $att_id );
		if ( $path && file_exists( $path ) ) {
			$size = size_format( filesize( $path ), 1 );
		}
	}

	if ( $ext && $size ) {
		/* translators: 1: file type (e.g. PDF), 2: human-readable file size (e.g. 12.4 MB) */
		return sprintf( __( '%1$s • %2$s', 'deon-energy' ), $ext, $size );
	}
	return $ext ? $ext : '';
}

/**
 * Latest published posts for the homepage "Recent Articles" section.
 * Returns a WP_Query; the partial falls back to designed placeholders when
 * no posts exist yet so the homepage still renders for sign-off.
 */
function deon_recent_posts_query( $count = 3 ) {
	return new WP_Query( array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $count,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );
}

/**
 * Reading-time badge for a post ("6 MIN READ"), shown on the Knowledge Hub
 * listing rows where Technical Papers shows the PDF size. 200 wpm, minimum 1.
 * Returns '' when the post has no body text.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function deon_post_reading_time( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$content = trim( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ) );

	if ( '' === $content ) {
		return '';
	}

	$minutes = max( 1, (int) ceil( str_word_count( $content ) / 200 ) );

	/* translators: %d: estimated reading time in minutes */
	return sprintf( _n( '%d MIN READ', '%d MIN READ', $minutes, 'deon-energy' ), $minutes );
}

/**
 * Knowledge Hub listing shows 6 article rows per page regardless of the
 * "blog pages show at most" reading setting.
 */
function deon_blog_posts_per_page( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_home() ) {
		$query->set( 'posts_per_page', 6 );
	}
}
add_action( 'pre_get_posts', 'deon_blog_posts_per_page' );

/**
 * Resolve a page URL by slug, trying each candidate in turn.
 *
 * The footer's link list is hardcoded (it only falls back to a real WP menu),
 * so a slug that does not match the page the client actually published shipped
 * a dead link — that is how "Solar Calculator" (/solar-calculator/) and "FAQs"
 * (/faqs/) 404'd against the real `calculator` / `faq` pages. Resolving at
 * render time means renaming a page in wp-admin can't break the footer again.
 *
 * @param string|array $slugs    Candidate slugs, best match first. Nested
 *                               paths ('legal/terms') work too.
 * @param string       $fallback Path used when no candidate exists. Empty
 *                               string (default) means "no link" — the caller
 *                               is expected to drop the item rather than point
 *                               at a 404.
 * @return string Permalink, the fallback URL, or '' when neither resolves.
 */
function deon_page_link( $slugs, $fallback = '' ) {
	foreach ( (array) $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page && 'publish' === get_post_status( $page ) ) {
			return (string) get_permalink( $page );
		}
	}

	return $fallback ? home_url( $fallback ) : '';
}

/**
 * Inline glyph for a hero / card meta item.
 *
 * One icon set shared by the Project Gallery cards
 * ( template-parts/projects/grid.php ) and the shared inner-page hero fact row
 * ( template-parts/global/page-hero.php ), so a project reads the same in the
 * listing and on its own page. Colour + size come from CSS (currentColor,
 * 1em box); an unknown key returns '' and the caller falls back to its bullet.
 *
 * @param string $key location|capacity|date|status.
 * @return string SVG markup, safe to echo unescaped.
 */
function deon_meta_icon( $key ) {
	$paths = array(
		'location' => 'M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5Z',
		'capacity' => 'M13 2 4.5 13.5H11l-1 8.5 8.5-11.5H12l1-8.5Z',
		'date'     => 'M7 2v2H5.5A2.5 2.5 0 0 0 3 6.5v13A2.5 2.5 0 0 0 5.5 22h13a2.5 2.5 0 0 0 2.5-2.5v-13A2.5 2.5 0 0 0 18.5 4H17V2h-2v2H9V2H7Zm12 8v9.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V10h14Z',
		'status'   => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm-1.1 14.4-4-4 1.6-1.6 2.4 2.4 5.1-5.1 1.6 1.6-6.7 6.7Z',
	);

	$key = (string) $key;
	if ( ! isset( $paths[ $key ] ) ) {
		return '';
	}

	return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="' . esc_attr( $paths[ $key ] ) . '"/></svg>';
}

/**
 * Breadcrumb trail. Rendered site-wide (except the homepage) as a thin strip
 * between the header and the page hero. Builds the trail from the current
 * context: pages include their ancestors; single posts link back to the
 * Knowledge Hub; archives / search / 404 get a sensible label.
 */
function deon_breadcrumb() {
	if ( is_front_page() ) {
		return;
	}

	$crumbs = array();
	$crumbs[] = array( 'label' => __( 'Home', 'deon-energy' ), 'url' => home_url( '/' ) );

	if ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor_id ) {
			$crumbs[] = array( 'label' => get_the_title( $ancestor_id ), 'url' => get_permalink( $ancestor_id ) );
		}
		$crumbs[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_single() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		if ( 'post' === get_post_type() && $posts_page ) {
			$crumbs[] = array( 'label' => get_the_title( $posts_page ), 'url' => get_permalink( $posts_page ) );
		} elseif ( 'deon_project' === get_post_type() ) {
			// The listing page ships on slug `projects` (titled "Project Gallery");
			// `project-gallery` is only accepted as a legacy alias. Hard-coding the
			// latter sent the crumb to a 404 on every project detail page.
			$gallery = get_page_by_path( 'projects' );
			if ( ! $gallery ) {
				$gallery = get_page_by_path( 'project-gallery' );
			}
			$crumbs[] = array(
				'label' => $gallery ? get_the_title( $gallery ) : __( 'Project Gallery', 'deon-energy' ),
				'url'   => $gallery ? get_permalink( $gallery ) : home_url( '/projects/' ),
			);
		} elseif ( 'deon_job' === get_post_type() ) {
			$careers = get_page_by_path( 'careers' );
			$crumbs[] = array(
				'label' => $careers ? get_the_title( $careers ) : __( 'Careers', 'deon-energy' ),
				'url'   => $careers ? get_permalink( $careers ) : home_url( '/careers/' ),
			);
		}
		$crumbs[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_home() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		$crumbs[] = array( 'label' => $posts_page ? get_the_title( $posts_page ) : __( 'Knowledge Hub', 'deon-energy' ), 'url' => '' );
	} elseif ( is_search() ) {
		$crumbs[] = array( 'label' => sprintf( __( 'Search: %s', 'deon-energy' ), get_search_query() ), 'url' => '' );
	} elseif ( is_404() ) {
		$crumbs[] = array( 'label' => __( 'Page not found', 'deon-energy' ), 'url' => '' );
	} elseif ( is_archive() ) {
		$crumbs[] = array( 'label' => wp_strip_all_tags( get_the_archive_title() ), 'url' => '' );
	} else {
		$crumbs[] = array( 'label' => wp_get_document_title(), 'url' => '' );
	}

	echo '<nav class="deon-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'deon-energy' ) . '">';
	echo '<div class="deon-breadcrumb__inner">';
	$last = count( $crumbs ) - 1;
	foreach ( $crumbs as $i => $crumb ) {
		if ( $crumb['url'] && $i !== $last ) {
			echo '<a class="deon-breadcrumb__link" href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['label'] ) . '</a>';
		} else {
			echo '<span class="deon-breadcrumb__current" aria-current="page">' . esc_html( $crumb['label'] ) . '</span>';
		}
		if ( $i !== $last ) {
			echo '<span class="deon-breadcrumb__sep" aria-hidden="true">/</span>';
		}
	}
	echo '</div></nav>';
}

/**
 * ===========================================================================
 * Newsletter subscription.
 * Two designed forms (Contact page + Technical Papers sidebar) post here via
 * admin-ajax. Subscribers are stored as the private deon_subscriber CPT so the
 * client can view/export them in wp-admin. Email delivery to a mailing provider
 * (Mailchimp / an ESP) stays out of SOW scope — this captures + stores leads.
 * ACF-free, mirrors the deon_inquiry pattern.
 * ===========================================================================
 */

/**
 * Custom Post Type: Newsletter Subscriber.
 * Title = email. Source (which form) stored as meta. Manual creation blocked;
 * the AJAX handler inserts.
 */
function deon_register_subscriber_cpt() {
	register_post_type( 'deon_subscriber', array(
		'labels'        => array(
			'name'          => __( 'Subscribers', 'deon-energy' ),
			'singular_name' => __( 'Subscriber', 'deon-energy' ),
			'edit_item'     => __( 'View Subscriber', 'deon-energy' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-email',
		'menu_position' => 25,
		'supports'      => array( 'title' ),
		'capabilities'  => array( 'create_posts' => 'do_not_allow' ), // block manual creation; handler inserts.
		'map_meta_cap'  => true,
	) );
}
add_action( 'init', 'deon_register_subscriber_cpt' );

/**
 * Admin list columns for subscribers: email, source form, date subscribed.
 */
function deon_subscriber_columns( $columns ) {
	return array(
		'cb'             => $columns['cb'],
		'title'          => __( 'Email', 'deon-energy' ),
		'deon_source'    => __( 'Source', 'deon-energy' ),
		'date'           => __( 'Subscribed', 'deon-energy' ),
	);
}
add_filter( 'manage_deon_subscriber_posts_columns', 'deon_subscriber_columns' );

function deon_subscriber_column_content( $column, $post_id ) {
	if ( 'deon_source' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_deon_subscriber_source', true ) );
	}
}
add_action( 'manage_deon_subscriber_posts_custom_column', 'deon_subscriber_column_content', 10, 2 );

/**
 * Handle a newsletter subscription (AJAX).
 * Validates the nonce + honeypot + email, dedupes on the email, stores a
 * deon_subscriber, and replies JSON for the inline feedback UI in main.js.
 * Works for logged-out visitors via the nopriv hook.
 */
function deon_handle_newsletter_subscribe() {
	// Nonce.
	if ( ! check_ajax_referer( 'deon_newsletter', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Session expired. Please refresh and try again.', 'deon-energy' ) ), 403 );
	}

	// Honeypot — bots fill this hidden field; humans never see it. Silent OK.
	if ( ! empty( $_POST['deon_hp'] ) ) {
		wp_send_json_success( array( 'message' => __( 'Thanks — you are subscribed.', 'deon-energy' ) ) );
	}

	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : 'newsletter';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'deon-energy' ) ), 400 );
	}

	// Dedupe on the email (stored as the post title). Already subscribed → friendly note.
	$existing = get_posts( array(
		'post_type'              => 'deon_subscriber',
		'post_status'            => 'publish',
		'title'                  => $email,
		'posts_per_page'         => 1,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	) );
	if ( ! empty( $existing ) ) {
		wp_send_json_success( array( 'message' => __( 'You are already subscribed.', 'deon-energy' ) ) );
	}

	$subscriber_id = wp_insert_post( array(
		'post_type'   => 'deon_subscriber',
		'post_status' => 'publish',
		'post_title'  => $email,
	) );

	if ( is_wp_error( $subscriber_id ) || ! $subscriber_id ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'deon-energy' ) ), 500 );
	}

	update_post_meta( $subscriber_id, '_deon_subscriber_source', $source );

	// Notify the client so new leads do not sit unseen in wp-admin.
	$recipient = deon_notification_recipient();
	$site = get_bloginfo( 'name' );
	wp_mail(
		$recipient,
		/* translators: %s: site name */
		sprintf( __( '[%s] New newsletter subscriber', 'deon-energy' ), $site ),
		/* translators: 1: email, 2: source form */
		sprintf( __( "Email: %1\$s\nSource: %2\$s\n", 'deon-energy' ), $email, $source )
	);

	wp_send_json_success( array( 'message' => __( 'Thanks — you are subscribed.', 'deon-energy' ) ) );
}
add_action( 'wp_ajax_deon_newsletter', 'deon_handle_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_deon_newsletter', 'deon_handle_newsletter_subscribe' );

/**
 * ===========================================================================
 * Contact Form 7 — failure copy.
 * CF7's stock "There was an error trying to send your message. Please try
 * again later." tells an applicant nothing: they cannot know whether the
 * application was received, and it offers no way forward. In practice this
 * status (`mail_failed`) means wp_mail() could not hand the message to a mail
 * transport — the host blocked PHP mail or SMTP is not configured. The
 * submission itself is already stored (Flamingo → Inbound Messages), so the
 * honest message is "we have it, but delivery is unconfirmed — here is a
 * direct address".
 *
 * Filtered theme-side on purpose: the CF7 Messages tab lives in the database
 * and resets whenever the client rebuilds the form.
 * ===========================================================================
 */
function deon_cf7_failure_message( $output, $status ) {
	$email = 'info@deonenergy.in';

	if ( 'mail_failed' === $status ) {
		return sprintf(
			/* translators: %s: contact email address. */
			__( 'Your details were received, but we could not confirm the email notification. So nothing is missed, please also send your application to %s.', 'deon-energy' ),
			$email
		);
	}

	if ( 'aborted' === $status ) {
		return sprintf(
			/* translators: %s: contact email address. */
			__( 'The submission could not be completed. Please try again, or email your application to %s.', 'deon-energy' ),
			$email
		);
	}

	if ( 'upload_failed' === $status || 'upload_file_type_invalid' === $status || 'upload_file_too_large' === $status ) {
		return __( 'That file could not be uploaded. Attach a PDF or Word document under 10MB and try again.', 'deon-energy' );
	}

	return $output;
}
add_filter( 'wpcf7_display_message', 'deon_cf7_failure_message', 10, 2 );


/* ============================================================================
 * Phase 2 performance: default image performance attributes
 * ------------------------------------------------------------------------
 * WordPress core adds loading="lazy" to <img> from the_content since 5.5,
 * but only for images inside filtered content. Templates that hand-roll
 * <img> tags get nothing. Everything below the first fold should be lazy
 * anyway; we already tagged the important ones by hand in template files.
 * This filter is a belt-and-braces safety net for future images added via
 * the block editor / custom fields — it never overrides an explicit
 * loading= or decoding= attribute an author already wrote.
 * ========================================================================= */
function deon_default_img_attrs( $content ) {
	if ( is_admin() || empty( $content ) || false === stripos( $content, '<img' ) ) {
		return $content;
	}
	return preg_replace_callback( '/<img\b([^>]*)>/i', function( $m ) {
		$attrs = $m[1];
		if ( false === stripos( $attrs, 'loading=' ) ) {
			$attrs .= ' loading="lazy"';
		}
		if ( false === stripos( $attrs, 'decoding=' ) ) {
			$attrs .= ' decoding="async"';
		}
		return '<img' . $attrs . '>';
	}, $content );
}
add_filter( 'the_content',      'deon_default_img_attrs', 20 );
add_filter( 'post_thumbnail_html', 'deon_default_img_attrs', 20 );
add_filter( 'widget_text',       'deon_default_img_attrs', 20 );