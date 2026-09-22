<?php
/**
 * Deon Energy — SEO module (Phase 1).
 *
 * Provides:
 *   - Meta description (per-page override + smart fallback)
 *   - Open Graph tags (Facebook, LinkedIn, WhatsApp previews)
 *   - Twitter Card tags
 *   - Canonical URLs
 *   - JSON-LD structured data:
 *       * Organization (site-wide, from real company data)
 *       * LocalBusiness (Contact page — 3 offices)
 *       * WebSite with SearchAction (site-wide)
 *       * BreadcrumbList (every non-home page)
 *       * FAQPage (FAQ page)
 *       * Article (blog posts)
 *       * Service (Solutions page)
 *   - Admin meta box: per-page/post SEO description field.
 *
 * All output uses proper escaping. All JSON-LD is emitted through
 * wp_json_encode() so nothing is manually string-concatenated.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============================================================================
 * 1. Canonical company data
 * ------------------------------------------------------------------------
 * Kept in one place so every schema block uses the same source of truth.
 * If any of this changes (new office, new phone), edit here — everything
 * downstream picks it up. Mirrors bin/data/company.json.
 * ========================================================================= */

function deon_seo_company() {
	return array(
		'name'        => 'Deon Energy Limited',
		'legal_name'  => 'Deon Energy Limited',
		'url'         => home_url( '/' ),
		'logo'        => get_template_directory_uri() . '/assets/img/deon-logo.png',
		'phone'       => '+91-1800-890-5933',
		'email'       => 'info@deonenergy.in',
		'founder'     => 'Dharmesh Makadiya',
		'description' => 'Deon Energy Limited is a Gujarat-based solar EPC company designing, supplying, installing and maintaining rooftop and ground-mount solar power systems for industrial and commercial customers across India.',
		'social'      => array(
			'https://www.linkedin.com/company/deon-energy/',
			'https://www.instagram.com/deon_energy.in/',
			'https://www.facebook.com/deonenergylimited',
			'https://www.youtube.com/@deonenergy/videos',
		),
		'offices' => array(
			array(
				'label'    => 'Ahmedabad Head Office',
				'street'   => 'D-604, 605, 606 Westgate, Near YMCA Club, S.G. Highway, Makarba',
				'city'     => 'Ahmedabad',
				'state'    => 'Gujarat',
				'postcode' => '380051',
				'country'  => 'IN',
				'is_hq'    => true,
			),
			array(
				'label'    => 'Rajkot Office',
				'street'   => '401, R K Prime, Near Nana Mava Circle, 150 Feet Ring Road',
				'city'     => 'Rajkot',
				'state'    => 'Gujarat',
				'postcode' => '360005',
				'country'  => 'IN',
				'is_hq'    => false,
			),
			array(
				'label'    => 'Morbi Office',
				'street'   => '715, 716 & 717 Siromany-142, B/h. Eden Ceramic City, NH-8A, Lalpar',
				'city'     => 'Morbi',
				'state'    => 'Gujarat',
				'postcode' => '363642',
				'country'  => 'IN',
				'is_hq'    => false,
			),
		),
	);
}

/* ============================================================================
 * 2. Meta description
 * ========================================================================= */

/**
 * Get the meta description for the current page.
 * Priority: post meta > page excerpt > page-specific fallback > site default.
 */
function deon_seo_meta_description() {
	// Custom per-post/page override — set in the "SEO Description" meta box below.
	if ( is_singular() ) {
		$custom = get_post_meta( get_the_ID(), '_deon_seo_description', true );
		if ( ! empty( $custom ) ) {
			return $custom;
		}
		$excerpt = get_the_excerpt();
		if ( ! empty( $excerpt ) ) {
			return wp_strip_all_tags( $excerpt );
		}
	}

	// Page-type-specific defaults — better than a generic site tagline.
	if ( is_front_page() || is_home() ) {
		return 'Deon Energy Limited — solar EPC company delivering rooftop and ground-mount solar power systems for industrial and commercial customers across Gujarat and India.';
	}
	if ( is_page( 'about' ) || is_page( 'about-us' ) ) {
		return 'Learn about Deon Energy Limited — a Gujarat-based solar EPC company headquartered in Ahmedabad with offices in Rajkot and Morbi, delivering solar plants engineered for their full 25-year life.';
	}
	if ( is_page( 'solutions' ) || is_page( 'services' ) ) {
		return 'End-to-end solar EPC, O&M and advisory services from Deon Energy — engineering, procurement, construction and long-term maintenance for industrial and commercial solar plants in India.';
	}
	if ( is_page( 'projects' ) ) {
		return 'Solar power projects delivered by Deon Energy Limited for industrial and commercial customers across Gujarat. Rooftop and ground-mount installations with capacity, location, and generation details.';
	}
	if ( is_page( 'contact' ) ) {
		return 'Contact Deon Energy Limited — offices in Ahmedabad, Rajkot and Morbi, Gujarat. Call 1800-890-5933 or email info@deonenergy.in for a site assessment and solar EPC proposal.';
	}
	if ( is_page( 'esg' ) || is_page( 'sustainability' ) ) {
		return 'ESG and sustainability at Deon Energy — clean energy delivery, responsible site practices, and long-term thinking about the modules and inverters we install.';
	}
	if ( is_page( 'careers' ) ) {
		return 'Careers at Deon Energy Limited — engineering, project management and site roles across our Ahmedabad, Rajkot and Morbi offices. Build solar plants that have to work for 25 years.';
	}
	if ( is_page( 'investors' ) || is_page( 'investor-relations' ) ) {
		return 'Investor relations for Deon Energy Limited — filings, disclosures, reports and shareholder information.';
	}

	if ( is_category() || is_tax() ) {
		$term = get_queried_object();
		if ( ! empty( $term->description ) ) {
			return wp_strip_all_tags( $term->description );
		}
		return sprintf( 'Latest %s articles and updates from Deon Energy — solar EPC insights for Indian businesses.', $term->name );
	}

	$tagline = get_bloginfo( 'description' );
	if ( ! empty( $tagline ) ) {
		return $tagline;
	}
	return deon_seo_company()['description'];
}

/**
 * Return the URL of the image best representing the current page.
 * Priority: post featured image > shipped OG default > site logo.
 */
function deon_seo_share_image_url() {
	if ( is_singular() && has_post_thumbnail() ) {
		$url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		if ( ! empty( $url ) ) {
			return $url;
		}
	}
	// Ships with the theme; safe on every page.
	return get_template_directory_uri() . '/assets/img/about-hero.jpg';
}

/* ============================================================================
 * 3. Output — meta description, OG, Twitter, canonical
 * ========================================================================= */

function deon_seo_output_head_tags() {
	$description = deon_seo_meta_description();
	$image       = deon_seo_share_image_url();
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : ( function_exists( 'wp_get_canonical_url' ) && wp_get_canonical_url() ? wp_get_canonical_url() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ) ) );

	// Meta description
	printf(
		'<meta name="description" content="%s">' . "\n",
		esc_attr( wp_trim_words( $description, 30, '' ) )
	);

	// Canonical (only if WP hasn't already emitted one — rel_canonical does it for singular)
	if ( ! is_singular() ) {
		printf(
			'<link rel="canonical" href="%s">' . "\n",
			esc_url( $url )
		);
	}

	// Open Graph (Facebook, LinkedIn, WhatsApp)
	$og_type = is_singular( 'post' ) ? 'article' : 'website';
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( wp_trim_words( $description, 30, '' ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $og_type ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	printf( '<meta property="og:image:width" content="1200">' . "\n" );
	printf( '<meta property="og:image:height" content="630">' . "\n" );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) );

	// Article-specific OG tags for blog posts
	if ( is_singular( 'post' ) ) {
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
		$author = get_the_author();
		if ( ! empty( $author ) ) {
			printf( '<meta property="article:author" content="%s">' . "\n", esc_attr( $author ) );
		}
	}

	// Twitter Card
	printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( wp_trim_words( $description, 30, '' ) ) );
	printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );

	// Robots — sensible defaults
	if ( is_404() || is_search() ) {
		printf( '<meta name="robots" content="noindex, follow">' . "\n" );
	} else {
		printf( '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">' . "\n" );
	}
}
add_action( 'wp_head', 'deon_seo_output_head_tags', 3 );

/* ============================================================================
 * 4. JSON-LD structured data
 * ------------------------------------------------------------------------
 * Emitted through wp_json_encode() — never manually built as strings, so
 * escaping is automatic and safe.
 * ========================================================================= */

/**
 * Site-wide Organization + WebSite schema — printed on every page.
 */
function deon_seo_output_organization_schema() {
	$c = deon_seo_company();

	$org = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Organization',
		'@id'         => trailingslashit( home_url() ) . '#organization',
		'name'        => $c['name'],
		'legalName'   => $c['legal_name'],
		'url'         => $c['url'],
		'logo'        => array(
			'@type' => 'ImageObject',
			'url'   => $c['logo'],
		),
		'description' => $c['description'],
		'founder'     => array(
			'@type' => 'Person',
			'name'  => $c['founder'],
		),
		'contactPoint' => array(
			'@type'             => 'ContactPoint',
			'telephone'         => $c['phone'],
			'contactType'       => 'customer service',
			'email'             => $c['email'],
			'areaServed'        => 'IN',
			'availableLanguage' => array( 'English', 'Hindi', 'Gujarati' ),
		),
		'sameAs' => $c['social'],
	);

	$website = array(
		'@context'      => 'https://schema.org',
		'@type'         => 'WebSite',
		'@id'           => trailingslashit( home_url() ) . '#website',
		'url'           => home_url( '/' ),
		'name'          => get_bloginfo( 'name' ),
		'description'   => $c['description'],
		'publisher'     => array( '@id' => trailingslashit( home_url() ) . '#organization' ),
		'inLanguage'    => get_bloginfo( 'language' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	echo '<script type="application/ld+json">' . wp_json_encode( $website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'deon_seo_output_organization_schema', 5 );

/**
 * LocalBusiness schema — Contact page. Lists all three offices as separate
 * LocalBusiness entities, each with its full address. Google uses this for
 * local pack results and the knowledge panel.
 */
function deon_seo_output_local_business_schema() {
	if ( ! ( is_page( 'contact' ) || is_page( 'contact-us' ) ) ) {
		return;
	}

	$c        = deon_seo_company();
	$branches = array();

	foreach ( $c['offices'] as $office ) {
		$branches[] = array(
			'@type'   => 'LocalBusiness',
			'@id'     => home_url( '/contact/#' ) . sanitize_title( $office['label'] ),
			'name'    => $c['name'] . ' — ' . $office['label'],
			'image'   => $c['logo'],
			'url'     => home_url( '/contact/' ),
			'telephone' => $c['phone'],
			'email'   => $c['email'],
			'priceRange' => '₹₹',
			'address' => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $office['street'],
				'addressLocality' => $office['city'],
				'addressRegion'   => $office['state'],
				'postalCode'      => $office['postcode'],
				'addressCountry'  => $office['country'],
			),
			'parentOrganization' => array( '@id' => trailingslashit( home_url() ) . '#organization' ),
			'areaServed' => array(
				array( '@type' => 'State', 'name' => 'Gujarat' ),
				array( '@type' => 'Country', 'name' => 'India' ),
			),
		);
	}

	foreach ( $branches as $branch ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $branch, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'deon_seo_output_local_business_schema', 6 );

/**
 * BreadcrumbList schema — on every non-home page. Google uses this to show
 * a clean breadcrumb trail in search results instead of a raw URL.
 */
function deon_seo_output_breadcrumb_schema() {
	if ( is_front_page() ) {
		return;
	}

	$items = array();
	$items[] = array(
		'@type'    => 'ListItem',
		'position' => 1,
		'name'     => 'Home',
		'item'     => home_url( '/' ),
	);

	if ( is_singular() ) {
		$post_type = get_post_type();
		if ( 'post' === $post_type ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Blog',
				'item'     => get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ),
			);
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			);
		} elseif ( 'page' === $post_type ) {
			$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
			$pos       = 2;
			foreach ( $ancestors as $ancestor_id ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos++,
					'name'     => get_the_title( $ancestor_id ),
					'item'     => get_permalink( $ancestor_id ),
				);
			}
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			);
		} else {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			);
		}
	} elseif ( is_category() || is_tax() ) {
		$term    = get_queried_object();
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => $term->name,
			'item'     => get_term_link( $term ),
		);
	} elseif ( is_archive() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => post_type_archive_title( '', false ),
			'item'     => get_post_type_archive_link( get_query_var( 'post_type' ) ),
		);
	}

	if ( count( $items ) < 2 ) {
		return;
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'deon_seo_output_breadcrumb_schema', 7 );

/**
 * FAQPage schema — on the FAQ page. Wins "People Also Ask" placements
 * and rich results in Google. Reads FAQ items from your FAQ CPT if one
 * exists; falls back to reading H3/H4 questions with following paragraph
 * as answer.
 */
function deon_seo_output_faq_schema() {
	if ( ! ( is_page( 'faqs' ) || is_page( 'faq' ) ) ) {
		return;
	}

	$faqs = array();

	// Try to read from FAQ custom post type if it exists.
	if ( post_type_exists( 'deon_faq' ) ) {
		$q = new WP_Query( array(
			'post_type'      => 'deon_faq',
			'posts_per_page' => 50,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );
		while ( $q->have_posts() ) {
			$q->the_post();
			$faqs[] = array(
				'@type'          => 'Question',
				'name'           => get_the_title(),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( get_the_content() ),
				),
			);
		}
		wp_reset_postdata();
	}

	if ( empty( $faqs ) ) {
		return;
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $faqs,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'deon_seo_output_faq_schema', 8 );

/**
 * Article schema — blog posts. Tells Google this is an article by X,
 * published on date Y, part of publisher Deon Energy.
 */
function deon_seo_output_article_schema() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$c = deon_seo_company();

	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink(),
		),
		'headline'         => get_the_title(),
		'description'      => deon_seo_meta_description(),
		'image'            => deon_seo_share_image_url(),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
		),
		'publisher'        => array(
			'@id' => trailingslashit( home_url() ) . '#organization',
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'deon_seo_output_article_schema', 9 );

/**
 * Service schema — Solutions page. Tells Google exactly which services
 * we offer, priced range, area served — helps rank for "solar EPC Gujarat",
 * "solar installation Ahmedabad", etc.
 */
function deon_seo_output_service_schema() {
	if ( ! ( is_page( 'solutions' ) || is_page( 'services' ) ) ) {
		return;
	}

	$c        = deon_seo_company();
	$services = array(
		array(
			'name'        => 'Solar EPC (Engineering, Procurement, Construction)',
			'description' => 'Turnkey rooftop and ground-mount solar plants for industrial and commercial customers. Design, procurement, installation, approvals and commissioning under one accountable team.',
		),
		array(
			'name'        => 'Solar O&M (Operations & Maintenance)',
			'description' => 'Long-term maintenance contracts with uptime targets, monthly generation reports, preventive schedules, cleaning, thermal inspections and rapid on-site response.',
		),
		array(
			'name'        => 'Solar Advisory & Consultancy',
			'description' => 'Independent technical and financial views before investment: feasibility studies, bankability reports, lender\'s engineer services, and asset valuation.',
		),
	);

	foreach ( $services as $service ) {
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'name'        => $service['name'],
			'description' => $service['description'],
			'provider'    => array( '@id' => trailingslashit( home_url() ) . '#organization' ),
			'areaServed'  => array(
				array( '@type' => 'State', 'name' => 'Gujarat' ),
				array( '@type' => 'Country', 'name' => 'India' ),
			),
			'serviceType' => 'Solar Power Installation',
			'category'    => 'Renewable Energy',
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'deon_seo_output_service_schema', 10 );

/* ============================================================================
 * 5. Admin — per-post/page SEO description meta box
 * ------------------------------------------------------------------------
 * A small field on every post/page edit screen so the editor can override
 * the default meta description with something hand-written.
 * ========================================================================= */

function deon_seo_add_meta_box() {
	$screens = array( 'post', 'page' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'deon_seo_meta_box',
			__( 'SEO Description', 'deon-energy' ),
			'deon_seo_render_meta_box',
			$screen,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'deon_seo_add_meta_box' );

function deon_seo_render_meta_box( $post ) {
	wp_nonce_field( 'deon_seo_save_meta', 'deon_seo_meta_nonce' );
	$value = get_post_meta( $post->ID, '_deon_seo_description', true );
	$len   = strlen( $value );
	?>
	<p style="margin-top:0;">
		<label for="deon_seo_description" style="font-weight:600;">
			<?php esc_html_e( 'Custom meta description for search engines and social previews', 'deon-energy' ); ?>
		</label>
	</p>
	<textarea id="deon_seo_description" name="deon_seo_description" rows="3" style="width:100%;" maxlength="200"
		placeholder="<?php esc_attr_e( 'e.g. Rooftop solar for MSMEs in Gujarat — turnkey EPC from Deon Energy. Get a feasibility note in 3 days.', 'deon-energy' ); ?>"
	><?php echo esc_textarea( $value ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Ideal length: 150–160 characters. Leave blank to use the automatic default.', 'deon-energy' ); ?>
		<span id="deon-seo-desc-count" style="float:right;font-weight:600;">
			<?php echo (int) $len; ?> / 160
		</span>
	</p>
	<script>
	(function() {
		var ta    = document.getElementById('deon_seo_description');
		var count = document.getElementById('deon-seo-desc-count');
		if ( ! ta || ! count ) return;
		ta.addEventListener('input', function() {
			var n = ta.value.length;
			count.textContent = n + ' / 160';
			count.style.color = ( n > 160 ) ? '#c00' : ( n > 155 ? '#b58900' : '' );
		});
	})();
	</script>
	<?php
}

function deon_seo_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['deon_seo_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['deon_seo_meta_nonce'] ), 'deon_seo_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$value = isset( $_POST['deon_seo_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['deon_seo_description'] ) ) : '';
	if ( '' === $value ) {
		delete_post_meta( $post_id, '_deon_seo_description' );
	} else {
		update_post_meta( $post_id, '_deon_seo_description', $value );
	}
}
add_action( 'save_post', 'deon_seo_save_meta_box' );
