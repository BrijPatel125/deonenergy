<?php
/**
 * Deon Energy — Phase 3 SEO: AI / LLM ranking module (GEO / AEO).
 *
 * Provides:
 *   - llms.txt served at /llms.txt (the emerging llmstxt.org standard for
 *     LLM crawlers — ChatGPT, Claude, Perplexity, Gemini use this to
 *     understand what the site is about)
 *   - robots.txt tuned to explicitly allow AI training crawlers on
 *     content, disallow them on admin/private paths
 *   - Extended schema for Answer-Engine Optimization (AEO):
 *       * HowTo schema for procedural blog posts (tag "howto")
 *       * SpeakableSpecification on FAQ + articles (voice assistants)
 *       * AboutPage / ContactPage / CollectionPage schema
 *       * Publisher.knowsAbout entity list (helps AI understand our topics)
 *       * Enhanced Article schema (wordCount, articleSection)
 *   - Answer-first content helpers: [deon_answer]...[/deon_answer]
 *     shortcode that renders a highlighted answer box AI overviews love
 *     to feature.
 *
 * Depends on inc/seo.php (Phase 1) for company data + Organization schema.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============================================================================
 * 1. llms.txt — served at /llms.txt via a rewrite rule
 * ------------------------------------------------------------------------
 * The llmstxt.org standard: a plain-text file at site root that tells
 * LLM crawlers what the site is about, in a format they can actually
 * parse (unlike a normal sitemap). ChatGPT and Perplexity read this
 * during their crawl.
 * ========================================================================= */

/**
 * Register /llms.txt as a virtual URL.
 */
function deon_register_llms_txt() {
	add_rewrite_rule( '^llms\.txt$', 'index.php?deon_llms=1', 'top' );
}
add_action( 'init', 'deon_register_llms_txt' );

function deon_register_llms_txt_query_var( $vars ) {
	$vars[] = 'deon_llms';
	return $vars;
}
add_filter( 'query_vars', 'deon_register_llms_txt_query_var' );

/**
 * Serve the file when /llms.txt is requested.
 */
function deon_serve_llms_txt() {
	if ( ! (int) get_query_var( 'deon_llms' ) ) {
		return;
	}
	if ( ! function_exists( 'deon_seo_company' ) ) {
		status_header( 404 );
		exit;
	}
	$c    = deon_seo_company();
	$site = home_url();

	nocache_headers();
	header( 'Content-Type: text/plain; charset=UTF-8' );

	$lines = array();
	$lines[] = '# ' . $c['name'];
	$lines[] = '';
	$lines[] = '> ' . $c['description'];
	$lines[] = '';
	$lines[] = '## Company';
	$lines[] = '- [About Deon Energy](' . $site . '/about/): Company background, history, headquarters in Ahmedabad, and offices in Rajkot and Morbi.';
	$lines[] = '- [Leadership](' . $site . '/leadership/): Founders, board of directors, and senior management team.';
	$lines[] = '- [ESG & Sustainability](' . $site . '/esg/): Environmental impact, safety practices, and governance approach.';
	$lines[] = '- [Careers](' . $site . '/careers/): Open roles across Ahmedabad, Rajkot, and Morbi offices.';
	$lines[] = '';
	$lines[] = '## Services';
	$lines[] = '- [Solar EPC](' . $site . '/solutions/): Turnkey rooftop and ground-mount solar plants — engineering, procurement, construction, and commissioning.';
	$lines[] = '- [Operations & Maintenance](' . $site . '/solutions/): Long-term O&M contracts with defined uptime targets, monthly generation reporting, preventive schedules.';
	$lines[] = '- [Solar Advisory](' . $site . '/solutions/): Feasibility studies, bankability reports, lender\'s engineer services, and asset valuation.';
	$lines[] = '';
	$lines[] = '## Projects';
	$lines[] = '- [Projects Portfolio](' . $site . '/projects/): Solar installations delivered for industrial and commercial customers across Gujarat.';
	$lines[] = '';
	$lines[] = '## Investor Relations';
	$lines[] = '- [Investor Relations](' . $site . '/investors/): SEBI filings, DRHP, annual reports, disclosures, and shareholder information.';
	$lines[] = '';
	$lines[] = '## Resources';
	$lines[] = '- [FAQs](' . $site . '/faqs/): Common questions about solar EPC, subsidies, approvals, financing, and O&M.';
	$lines[] = '- [Blog / Insights](' . $site . '/blog/): Articles on solar economics, technology, and policy for Indian businesses.';
	$lines[] = '- [Contact](' . $site . '/contact/): Ahmedabad head office, Rajkot office, Morbi office. Phone: ' . $c['phone'] . '. Email: ' . $c['email'] . '.';
	$lines[] = '';
	$lines[] = '## Key Facts';
	$lines[] = '- Company type: Solar EPC (Engineering, Procurement, Construction)';
	$lines[] = '- Founded by: ' . $c['founder'];
	$lines[] = '- Headquarters: Ahmedabad, Gujarat, India';
	$lines[] = '- Additional offices: Rajkot, Morbi';
	$lines[] = '- Regulatory bodies engaged with: MNRE, GEDA, GUVNL, CEIG';
	$lines[] = '- Customer segments: Industrial and commercial businesses';
	$lines[] = '- Primary service area: Gujarat and pan-India';
	$lines[] = '- Approvals handled: DISCOM, net-metering, CEIG clearances, MNRE compliance';
	$lines[] = '- Commercial models offered: CAPEX (customer-owned) and OPEX / PPA (Deon-owned)';
	$lines[] = '';
	$lines[] = '## Contact';
	$lines[] = 'For business inquiries: ' . $c['email'];
	$lines[] = 'Phone: ' . $c['phone'];
	foreach ( $c['social'] as $url ) {
		$lines[] = 'Social: ' . $url;
	}

	echo implode( "\n", $lines );
	exit;
}
add_action( 'template_redirect', 'deon_serve_llms_txt' );

/* ============================================================================
 * 2. robots.txt — tuned for AI crawlers + regular search bots
 * ------------------------------------------------------------------------
 * WordPress serves a virtual /robots.txt. We filter it to explicitly allow
 * AI training bots on public content (opt-in visibility) while blocking
 * them from admin/private URLs. Also points crawlers to the XML sitemap.
 * ========================================================================= */

function deon_filter_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output; // don't touch during site-in-progress "discourage search engines" mode
	}

	$sitemap = home_url( '/wp-sitemap.xml' );
	$llms    = home_url( '/llms.txt' );

	$rules = array();
	$rules[] = 'User-agent: *';
	$rules[] = 'Disallow: /wp-admin/';
	$rules[] = 'Disallow: /wp-login.php';
	$rules[] = 'Disallow: /?s=';
	$rules[] = 'Disallow: /search/';
	$rules[] = 'Disallow: /*?replytocom=';
	$rules[] = 'Allow: /wp-admin/admin-ajax.php';
	$rules[] = '';
	// AI crawlers — explicitly allowed on public content.
	// Business decision: making the site visible to AI grows brand
	// mentions in ChatGPT, Perplexity, Gemini answers. If you ever
	// want to opt out, change Allow: / below to Disallow: /.
	foreach ( array(
		'GPTBot',           // OpenAI (ChatGPT training + Search)
		'OAI-SearchBot',    // OpenAI search
		'ChatGPT-User',     // ChatGPT browsing on user request
		'ClaudeBot',        // Anthropic Claude
		'anthropic-ai',
		'Claude-Web',
		'PerplexityBot',    // Perplexity
		'Perplexity-User',
		'Google-Extended',  // Gemini training
		'Applebot-Extended',
		'CCBot',            // Common Crawl (feeds many models)
		'cohere-ai',
		'Meta-ExternalAgent',
	) as $bot ) {
		$rules[] = 'User-agent: ' . $bot;
		$rules[] = 'Allow: /';
		$rules[] = 'Disallow: /wp-admin/';
		$rules[] = '';
	}
	$rules[] = 'Sitemap: ' . $sitemap;
	$rules[] = 'Host: ' . wp_parse_url( home_url(), PHP_URL_HOST );
	$rules[] = '# LLM index: ' . $llms;

	return implode( "\n", $rules ) . "\n";
}
add_filter( 'robots_txt', 'deon_filter_robots_txt', 10, 2 );

/* ============================================================================
 * 3. Speakable schema — voice assistants (Google Assistant, Alexa)
 * ------------------------------------------------------------------------
 * Marks specific parts of a page as "speakable". Voice assistants read
 * these aloud when answering user questions. Applied to FAQ, blog posts,
 * and About page.
 * ========================================================================= */

function deon_seo_output_speakable_schema() {
	if ( ! ( is_singular( 'post' ) || is_page( array( 'faqs', 'faq', 'about', 'about-us' ) ) ) ) {
		return;
	}

	$speakable = array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebPage',
		'url'      => get_permalink() ? get_permalink() : home_url( '/' . get_query_var( 'pagename' ) ),
		'name'     => wp_get_document_title(),
		'speakable' => array(
			'@type'    => 'SpeakableSpecification',
			'cssSelector' => array( 'h1', 'h2', '.deon-answer', '.entry-summary', '.faq__answer' ),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $speakable, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'deon_seo_output_speakable_schema', 11 );

/* ============================================================================
 * 4. Page-type schema — AboutPage, ContactPage, CollectionPage
 * ------------------------------------------------------------------------
 * Google's crawlers rank pages higher when the page type is explicit.
 * ========================================================================= */

function deon_seo_output_page_type_schema() {
	$type = null;
	if ( is_page( array( 'about', 'about-us' ) ) ) {
		$type = 'AboutPage';
	} elseif ( is_page( array( 'contact', 'contact-us' ) ) ) {
		$type = 'ContactPage';
	} elseif ( is_page( 'projects' ) || is_post_type_archive( 'deon_project' ) ) {
		$type = 'CollectionPage';
	} elseif ( is_home() || is_archive() || is_search() ) {
		$type = 'CollectionPage';
	}

	if ( ! $type ) {
		return;
	}

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => $type,
		'url'         => wp_get_canonical_url() ? wp_get_canonical_url() : home_url( '/' ),
		'name'        => wp_get_document_title(),
		'description' => function_exists( 'deon_seo_meta_description' ) ? deon_seo_meta_description() : '',
		'isPartOf'    => array( '@id' => trailingslashit( home_url() ) . '#website' ),
		'about'       => array( '@id' => trailingslashit( home_url() ) . '#organization' ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'deon_seo_output_page_type_schema', 12 );

/* ============================================================================
 * 5. Enhanced Organization schema — knowsAbout entity list
 * ------------------------------------------------------------------------
 * Attach a list of topics we're authoritative on. When ChatGPT or Google's
 * AI is asked "who does commercial solar in Gujarat", entities that have
 * declared knowsAbout on those topics rank higher.
 * ========================================================================= */

function deon_seo_output_knowledge_graph_extension() {
	if ( ! is_front_page() ) {
		return;
	}
	$schema = array(
		'@context'     => 'https://schema.org',
		'@type'        => 'Organization',
		'@id'          => trailingslashit( home_url() ) . '#organization',
		'knowsAbout'   => array(
			'Solar power',
			'Solar EPC (Engineering, Procurement, Construction)',
			'Rooftop solar',
			'Ground-mount solar',
			'Commercial solar installations',
			'Industrial solar installations',
			'Solar operations and maintenance',
			'Solar plant O&M',
			'Solar advisory',
			'Solar feasibility studies',
			'Bankability reports',
			"Lender's engineer services",
			'Net metering',
			'MNRE compliance',
			'GEDA solar policy',
			'GUVNL power purchase',
			'CEIG electrical inspection',
			'CAPEX solar model',
			'OPEX solar model',
			'PPA (Power Purchase Agreement)',
			'Accelerated depreciation for solar',
			'Solar tax benefits India',
			'Renewable energy Gujarat',
		),
		'knowsLanguage' => array( 'en', 'hi', 'gu' ),
		'areaServed'    => array(
			array( '@type' => 'State', 'name' => 'Gujarat', 'containedInPlace' => array( '@type' => 'Country', 'name' => 'India' ) ),
			array( '@type' => 'Country', 'name' => 'India' ),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'deon_seo_output_knowledge_graph_extension', 13 );

/* ============================================================================
 * 6. Answer-first content shortcode: [deon_answer]...[/deon_answer]
 * ------------------------------------------------------------------------
 * Wraps a short direct answer in a styled box, marked up as Speakable and
 * as an Article's mainEntity when used in blog posts. Google's AI Overviews
 * and Perplexity love pulling from clearly-marked "answer boxes".
 *
 * Usage in a blog post editor: [deon_answer]A 100 kW commercial rooftop
 * solar plant needs roughly 8,000-10,000 sq ft of shadow-free roof and
 * costs approximately ₹40-50 lakh in Gujarat, before subsidies.[/deon_answer]
 * ========================================================================= */

function deon_answer_shortcode( $atts, $content = '' ) {
	if ( empty( $content ) ) {
		return '';
	}
	$content = wp_kses_post( $content );
	return sprintf(
		'<div class="deon-answer" itemprop="mainEntityOfPage"><strong class="deon-answer__label">Quick answer:</strong> <span class="deon-answer__body">%s</span></div>',
		do_shortcode( $content )
	);
}
add_shortcode( 'deon_answer', 'deon_answer_shortcode' );

/**
 * Minimal styling for the answer box — printed inline so it works without
 * touching main.css (which is a heavy re-upload).
 */
function deon_answer_inline_style() {
	if ( is_singular( 'post' ) || is_singular( 'page' ) ) {
		echo '<style>.deon-answer{background:#fff7ec;border-left:4px solid #F29022;padding:16px 20px;margin:24px 0;border-radius:4px;font-size:1.05em;line-height:1.6}.deon-answer__label{color:#F29022;font-weight:700;margin-right:6px}</style>' . "\n";
	}
}
add_action( 'wp_head', 'deon_answer_inline_style', 20 );

/* ============================================================================
 * 7. Enhanced Article schema — wordCount, articleSection, HowTo detection
 * ------------------------------------------------------------------------
 * Google uses wordCount and articleSection as depth/topic signals. A post
 * tagged "howto" also gets a HowTo schema on top of Article schema,
 * which unlocks "recipe-card" style rich results in search.
 * ========================================================================= */

function deon_seo_output_enhanced_article_schema() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post = get_post();
	if ( ! $post ) {
		return;
	}

	// Add wordCount + section to the existing Article schema (deon_seo_output_article_schema
	// in inc/seo.php already prints the core Article; this adds a second, richer variant
	// with fields Google specifically wants for AI-Overview eligibility).
	$categories = get_the_category();
	$section    = ! empty( $categories ) ? $categories[0]->name : '';
	$word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );

	$enhanced = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'mainEntityOfPage' => get_permalink(),
		'headline'         => get_the_title(),
		'articleSection'   => $section,
		'wordCount'        => $word_count,
		'inLanguage'       => get_bloginfo( 'language' ),
		'isAccessibleForFree' => true,
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
			'url'   => get_author_posts_url( (int) $post->post_author ),
		),
		'publisher'        => array( '@id' => trailingslashit( home_url() ) . '#organization' ),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $enhanced, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";

	// HowTo schema — only if the post is tagged 'howto' (author decision).
	if ( has_tag( 'howto', $post ) ) {
		$howto = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'HowTo',
			'name'        => get_the_title(),
			'description' => function_exists( 'deon_seo_meta_description' ) ? deon_seo_meta_description() : '',
			'author'      => array( '@type' => 'Person', 'name' => get_the_author() ),
			'publisher'   => array( '@id' => trailingslashit( home_url() ) . '#organization' ),
			'inLanguage'  => get_bloginfo( 'language' ),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $howto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'deon_seo_output_enhanced_article_schema', 14 );

/* ============================================================================
 * 8. Flush rewrite rules when this file is first loaded on a new install
 * ------------------------------------------------------------------------
 * The /llms.txt route needs the rewrite rules refreshed once. Uses a flag
 * so it only runs once (never on every load).
 * ========================================================================= */

function deon_ai_flush_rewrites_once() {
	if ( get_option( 'deon_ai_rewrites_flushed_v1' ) ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'deon_ai_rewrites_flushed_v1', 1 );
}
add_action( 'init', 'deon_ai_flush_rewrites_once', 99 );
