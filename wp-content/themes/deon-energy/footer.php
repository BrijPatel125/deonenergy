<?php
/**
 * Global footer — dark (#1D1B20) rebuild per client change log v1 §6.
 *
 * Contact data is real, scraped from deonenergy.in → bin/data/company.json.
 * Addresses / phone / email are content: escaped on output, never translated.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -- Offices: driven entirely by the deon_office CPT (same source as the
 * Contact page) — no hardcoded fallback. Add/edit offices under Offices in
 * wp-admin; the section below hides itself when none are published. --------- */
$deon_office_posts = get_posts(
	array(
		'post_type'      => 'deon_office',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'no_found_rows'  => true,
	)
);

$deon_offices = array();
foreach ( $deon_office_posts as $deon_office_post ) {
	$deon_offices[] = array(
		'label'   => get_the_title( $deon_office_post ),
		'address' => (string) get_post_meta( $deon_office_post->ID, '_deon_office_address', true ),
		'map'     => (string) get_post_meta( $deon_office_post->ID, '_deon_office_map_url', true ),
	);
}

$deon_email      = 'info@deonenergy.in';
$deon_phone      = '18008905933';          // Toll-free — the only number published live.
$deon_phone_disp = '1800 890 5933';

// WhatsApp number is client-managed (Customize → Social Links → WhatsApp number,
// digits only). Blank hides both the "WhatsApp us" link and the floating button.
$deon_wa_number = preg_replace( '/\D+/', '', (string) get_theme_mod( 'deon_whatsapp_number', '18008905933' ) );
$deon_whatsapp  = $deon_wa_number ? 'https://api.whatsapp.com/send?phone=' . rawurlencode( $deon_wa_number ) . '&text=Hello' : '';

/*
 * Social — editable under Appearance → Customize → Social Links. Each URL falls
 * back to the live channel below when its Customizer field is left blank; clear
 * a field to hide that icon. (Twitter/X is dormant on the live site: omitted.)
 */
$deon_social_defaults = array(
	'instagram' => array( 'Instagram', 'https://www.instagram.com/deon_energy.in/' ),
	'facebook'  => array( 'Facebook', 'https://www.facebook.com/deonenergylimited' ),
	'linkedin'  => array( 'LinkedIn', 'https://www.linkedin.com/company/deon-energy/' ),
	'youtube'   => array( 'YouTube', 'https://www.youtube.com/@deonenergy/videos' ),
);

$deon_social = array();
foreach ( $deon_social_defaults as $deon_key => $deon_default ) {
	$deon_url = get_theme_mod( "deon_social_{$deon_key}", $deon_default[1] );
	if ( $deon_url ) {
		$deon_social[ $deon_key ] = array( $deon_default[0], $deon_url );
	}
}

/*
 * -- Navigation: hardcoded fallback, mirrors the new site structure -------
 *
 * Every href is resolved through deon_page_link() rather than written as a
 * literal path: the list is only a *fallback* for sites with no Footer menu, so
 * a slug that drifts from the published page silently ships a 404 — which is
 * exactly what happened to Solar Calculator (/solar-calculator/ vs the real
 * `calculator`) and FAQs (/faqs/ vs `faq`). First candidate that exists wins;
 * the trailing path is the last-resort fallback.
 */
$deon_footer_cols = array(
	array(
		'heading' => __( 'Company', 'deon-energy' ),
		'links'   => array(
			array( __( 'About Us', 'deon-energy' ), deon_page_link( array( 'about', 'about-us', 'about-deon-energy' ), '/about/' ) ),
			array( __( 'Leadership', 'deon-energy' ), deon_page_link( array( 'leadership', 'our-leadership' ), '/leadership/' ) ),
			array( __( 'Careers', 'deon-energy' ), deon_page_link( array( 'careers', 'career' ), '/careers/' ) ),
			array( __( 'ESG & Sustainability', 'deon-energy' ), deon_page_link( array( 'esg', 'esg-sustainability' ), '/esg/' ) ),
			array( __( 'Investor Relations', 'deon-energy' ), deon_page_link( array( 'investor-relations', 'investors' ), '/investor-relations/' ) ),
		),
	),
	array(
		'heading' => __( 'What We Do', 'deon-energy' ),
		'links'   => array(
			array( __( 'Solutions', 'deon-energy' ), deon_page_link( array( 'solutions', 'core-capabilities' ), '/solutions/' ) ),
			array( __( 'Projects', 'deon-energy' ), deon_page_link( array( 'projects', 'project-gallery' ), '/projects/' ) ),
			array( __( 'Solar Calculator', 'deon-energy' ), deon_page_link( array( 'calculator', 'solar-calculator', 'solar-savings-calculator' ), '/calculator/' ) ),
			array( __( 'FAQs', 'deon-energy' ), deon_page_link( array( 'faq', 'faqs' ), '/faq/' ) ),
		),
	),
	array(
		'heading' => __( 'Resources', 'deon-energy' ),
		'links'   => array(
			array( __( 'Blogs', 'deon-energy' ), deon_page_link( array( 'knowledge-hub', 'blog', 'insights' ), '/knowledge-hub/' ) ),
			array( __( 'News and Media', 'deon-energy' ), deon_page_link( array( 'news-media', 'news-and-media', 'news' ), '/news-media/' ) ),
			array( __( 'Contact', 'deon-energy' ), deon_page_link( array( 'contact', 'contact-us' ), '/contact/' ) ),
		),
	),
);

/*
 * Legal links. No fallback path on purpose: neither page exists yet, and a
 * hardcoded /privacy-policy/ + /terms/ just shipped two 404s. Each link appears
 * only once the client publishes the page (any of the usual slugs), so the row
 * is honest either way. Privacy also honours Settings → Privacy, which is where
 * WordPress records the site's policy page.
 */
$deon_legal = array();

$deon_privacy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
if ( ! $deon_privacy_url ) {
	$deon_privacy_url = deon_page_link( array( 'privacy-policy', 'privacy' ) );
}
if ( $deon_privacy_url ) {
	$deon_legal[] = array( __( 'Privacy Policy', 'deon-energy' ), $deon_privacy_url );
}

$deon_terms_url = deon_page_link( array( 'terms-of-use', 'terms', 'terms-and-conditions', 'terms-conditions' ) );
if ( $deon_terms_url ) {
	$deon_legal[] = array( __( 'Terms of Use', 'deon-energy' ), $deon_terms_url );
}

/* Inline social glyphs — currentColor so they invert cleanly on the dark bar. */
$deon_social_svg = array(
	'instagram' => '<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="2.5" y="2.5" width="19" height="19" rx="5.5"/><circle cx="12" cy="12" r="4"/><circle cx="17.6" cy="6.4" r="1.1" fill="currentColor" stroke="none"/></svg>',
	'facebook'  => '<svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true" focusable="false"><path d="M14.5 8.6V6.9c0-.8.2-1.2 1.4-1.2h1.5V2.8c-.3 0-1.2-.1-2.3-.1-2.4 0-4 1.4-4 4.1v1.8H8.6v3.1h2.5V21h3.4v-9.3h2.5l.4-3.1h-2.9z"/></svg>',
	'linkedin'  => '<svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true" focusable="false"><path d="M6.9 21H3.6V9.4h3.3V21zM5.2 8A1.9 1.9 0 1 1 5.2 4.2 1.9 1.9 0 0 1 5.2 8zM21 21h-3.3v-5.6c0-1.4 0-3.1-1.9-3.1s-2.2 1.5-2.2 3v5.7H10.3V9.4h3.1V11h.1c.5-.9 1.6-1.8 3.2-1.8 3.4 0 4.1 2.2 4.1 5.2V21z"/></svg>',
	'youtube'   => '<svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true" focusable="false"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8zM10 15.1V8.9l5.2 3.1-5.2 3.1z"/></svg>',
);
?>
</main><!-- .site-main -->

<footer class="site-footer">
	<div class="site-footer__inner deon-container">
		<div class="site-footer__brand">
			<a class="site-footer__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img
					class="site-footer__logo"
					src="<?php echo esc_url( DEON_URI . '/assets/img/deon-logo-white.png' ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					width="2374"
					height="563"
					loading="lazy"
				>
			</a>
			<p class="site-footer__about"><?php esc_html_e( "India's renewable energy partner for rooftop, industrial and utility-scale solar.", 'deon-energy' ); ?></p>

			<ul class="site-footer__social" aria-label="<?php esc_attr_e( 'Social media', 'deon-energy' ); ?>">
				<?php foreach ( $deon_social as $key => $data ) : ?>
					<li>
						<a href="<?php echo esc_url( $data[1] ); ?>" aria-label="<?php echo esc_attr( $data[0] ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo $deon_social_svg[ $key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="site-footer__nav site-footer__nav--menu" aria-label="<?php esc_attr_e( 'Footer', 'deon-energy' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 2,
						'items_wrap'     => '<ul class="site-footer__menu">%3$s</ul>',
					)
				);
				?>
			</nav>
		<?php else : ?>
			<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'deon-energy' ); ?>">
				<?php foreach ( $deon_footer_cols as $col ) : ?>
					<div class="site-footer__col">
						<h2 class="site-footer__heading"><?php echo esc_html( $col['heading'] ); ?></h2>
						<ul>
							<?php foreach ( $col['links'] as $link ) : ?>
								<?php if ( '' === $link[1] ) { continue; } // Page not published — no dead link. ?>
								<li><a href="<?php echo esc_url( $link[1] ); ?>"><?php echo esc_html( $link[0] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<div class="site-footer__col site-footer__contact">
			<h2 class="site-footer__heading"><?php esc_html_e( 'Get in Touch', 'deon-energy' ); ?></h2>
			<ul>
				<li>
					<a href="<?php echo esc_url( 'mailto:' . $deon_email ); ?>"><?php echo esc_html( $deon_email ); ?></a>
				</li>
				<li>
					<a href="<?php echo esc_url( 'tel:' . $deon_phone ); ?>">
						<?php echo esc_html( $deon_phone_disp ); ?>
						<span class="site-footer__tag"><?php esc_html_e( 'Toll free', 'deon-energy' ); ?></span>
					</a>
				</li>
				<?php if ( $deon_whatsapp ) : ?>
				<li>
					<a href="<?php echo esc_url( $deon_whatsapp ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp us', 'deon-energy' ); ?></a>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<?php if ( $deon_offices ) : ?>
	<div class="site-footer__offices">
		<div class="deon-container site-footer__offices-inner">
			<?php foreach ( $deon_offices as $office ) : ?>
				<div class="site-footer__office">
					<h2 class="site-footer__heading"><?php echo esc_html( $office['label'] ); ?></h2>
					<?php if ( $office['map'] ) : ?>
					<a href="<?php echo esc_url( $office['map'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $office['address'] ); ?>
					</a>
					<?php else : ?>
					<address><?php echo esc_html( $office['address'] ); ?></address>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="site-footer__bar">
		<div class="deon-container site-footer__bar-inner">
			<p>
				&copy;
				<?php
				printf(
					/* translators: 1: current year, 2: company name. */
					esc_html__( '%1$s %2$s. All rights reserved.', 'deon-energy' ),
					esc_html( wp_date( 'Y' ) ),
					esc_html( 'Deon Energy Limited' )
				);
				?>
			</p>
			<?php if ( $deon_legal ) : ?>
			<nav class="site-footer__legal" aria-label="<?php esc_attr_e( 'Legal', 'deon-energy' ); ?>">
				<?php foreach ( $deon_legal as $link ) : ?>
					<a href="<?php echo esc_url( $link[1] ); ?>"><?php echo esc_html( $link[0] ); ?></a>
				<?php endforeach; ?>
			</nav>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php if ( $deon_whatsapp ) : ?>
<a
	class="deon-whatsapp-float"
	href="<?php echo esc_url( $deon_whatsapp ); ?>"
	target="_blank"
	rel="noopener noreferrer"
	aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'deon-energy' ); ?>"
>
	<svg viewBox="0 0 32 32" width="32" height="32" fill="currentColor" aria-hidden="true" focusable="false"><path d="M16.04 3.2c-7.1 0-12.86 5.76-12.86 12.86 0 2.27.6 4.48 1.73 6.43L3.1 28.8l6.5-1.7a12.8 12.8 0 0 0 6.44 1.73h.01c7.1 0 12.86-5.76 12.86-12.86S23.14 3.2 16.04 3.2zm0 23.3h-.01a10.66 10.66 0 0 1-5.43-1.49l-.39-.23-3.86 1.01 1.03-3.76-.25-.4a10.62 10.62 0 0 1-1.63-5.66c0-5.9 4.8-10.69 10.7-10.69 2.86 0 5.54 1.11 7.56 3.13a10.62 10.62 0 0 1 3.13 7.57c0 5.9-4.8 10.69-10.69 10.69zm5.86-8c-.32-.16-1.9-.94-2.19-1.05-.29-.11-.5-.16-.72.16-.21.32-.82 1.05-1 1.26-.19.21-.37.24-.68.08-.32-.16-1.35-.5-2.57-1.59-.95-.85-1.59-1.9-1.78-2.22-.19-.32-.02-.49.14-.65.15-.14.32-.37.48-.56.16-.19.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.74-.99-2.38-.26-.62-.52-.54-.72-.55l-.61-.01c-.21 0-.56.08-.85.4-.29.32-1.11 1.09-1.11 2.66 0 1.57 1.14 3.09 1.3 3.3.16.21 2.25 3.43 5.44 4.81.76.33 1.35.52 1.81.67.76.24 1.46.21 2 .13.61-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.29-.21-.61-.37z"/></svg>
</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
