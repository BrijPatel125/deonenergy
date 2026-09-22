<?php
/**
 * Deon Energy — Analytics module.
 *
 * One place for every measurement tag on the site.
 *
 * Provides:
 *   - Google Tag Manager (recommended umbrella — one script, manages
 *     everything else via GTM interface without more code)
 *   - Google Analytics 4 (direct install — for when GTM isn't used)
 *   - Meta / Facebook Pixel
 *   - LinkedIn Insight Tag
 *   - Microsoft Clarity (session replay + heatmaps)
 *
 * Every field is a Customizer setting — the marketing team pastes IDs in
 * Appearance → Customize → Analytics, no code edits needed. If GTM is set,
 * the other tags are optional; typically you'd set GTM alone and manage
 * every other pixel inside the GTM interface (cleaner, versioned, editable
 * without a developer).
 *
 * Every tag respects the visitor's cookie consent — if `deon_cookie_consent`
 * cookie is not set to "granted", tags do NOT load. That keeps you compliant
 * with the IT Act and GDPR.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============================================================================
 * 1. Customizer — analytics fields
 * ========================================================================= */

function deon_analytics_customizer( $wp_customize ) {
	$wp_customize->add_section( 'deon_analytics', array(
		'title'       => __( 'Analytics & Tracking', 'deon-energy' ),
		'priority'    => 200,
		'description' => __( 'Paste tracking IDs here — no code edits needed. If you set the GTM Container ID, you can manage every other tag inside GTM and leave the fields below blank.', 'deon-energy' ),
	) );

	$fields = array(
		'deon_analytics_gtm'      => array(
			'label'       => __( 'Google Tag Manager Container ID', 'deon-energy' ),
			'description' => __( 'Format: GTM-XXXXXXX. Recommended umbrella — manages every other tag from the GTM interface.', 'deon-energy' ),
			'default'     => '',
		),
		'deon_analytics_ga4'      => array(
			'label'       => __( 'Google Analytics 4 Measurement ID', 'deon-energy' ),
			'description' => __( 'Format: G-XXXXXXXXXX. Only needed if not using GTM.', 'deon-energy' ),
			'default'     => '',
		),
		'deon_analytics_meta'     => array(
			'label'       => __( 'Meta / Facebook Pixel ID', 'deon-energy' ),
			'description' => __( 'Numeric ID (e.g. 1234567890123456).', 'deon-energy' ),
			'default'     => '',
		),
		'deon_analytics_linkedin' => array(
			'label'       => __( 'LinkedIn Insight Tag Partner ID', 'deon-energy' ),
			'description' => __( 'Numeric ID for LinkedIn Insight Tag.', 'deon-energy' ),
			'default'     => '',
		),
		'deon_analytics_clarity'  => array(
			'label'       => __( 'Microsoft Clarity Project ID', 'deon-energy' ),
			'description' => __( 'Session replay + heatmaps — free. Alphanumeric ID.', 'deon-energy' ),
			'default'     => '',
		),
	);

	foreach ( $fields as $id => $config ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $config['default'],
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'label'       => $config['label'],
			'description' => $config['description'],
			'section'     => 'deon_analytics',
			'type'        => 'text',
		) );
	}
}
add_action( 'customize_register', 'deon_analytics_customizer' );

/* ============================================================================
 * 2. Consent gate
 * ------------------------------------------------------------------------
 * Read the visitor's consent cookie. Returns true if they've accepted
 * analytics tracking. Returns FALSE otherwise (blocking tags until they do).
 * The cookie is set by the consent banner in inc/cookie-consent.php.
 * ========================================================================= */

function deon_analytics_has_consent() {
	// Admin views never load analytics — you'd be self-inflating your numbers.
	if ( is_admin() || is_customize_preview() ) {
		return false;
	}
	// Logged-in editors/admins are excluded so internal traffic doesn't skew data.
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return false;
	}
	// Do-Not-Track browser signal — respect it.
	if ( isset( $_SERVER['HTTP_DNT'] ) && '1' === $_SERVER['HTTP_DNT'] ) {
		return false;
	}
	// The consent cookie is set by the banner in inc/cookie-consent.php.
	if ( isset( $_COOKIE['deon_cookie_consent'] ) && 'granted' === $_COOKIE['deon_cookie_consent'] ) {
		return true;
	}
	return false;
}

/* ============================================================================
 * 3. GTM — head snippet
 * ========================================================================= */

function deon_analytics_gtm_head() {
	if ( ! deon_analytics_has_consent() ) {
		return;
	}
	$gtm_id = trim( (string) get_theme_mod( 'deon_analytics_gtm', '' ) );
	if ( empty( $gtm_id ) || ! preg_match( '/^GTM-[A-Z0-9]+$/i', $gtm_id ) ) {
		return;
	}
	?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','<?php echo esc_js( $gtm_id ); ?>');</script>
	<!-- End Google Tag Manager -->
	<?php
}
add_action( 'wp_head', 'deon_analytics_gtm_head', 1 );

/**
 * GTM noscript — right after opening <body>. Hooks into wp_body_open (WP 5.2+).
 */
function deon_analytics_gtm_body() {
	if ( ! deon_analytics_has_consent() ) {
		return;
	}
	$gtm_id = trim( (string) get_theme_mod( 'deon_analytics_gtm', '' ) );
	if ( empty( $gtm_id ) || ! preg_match( '/^GTM-[A-Z0-9]+$/i', $gtm_id ) ) {
		return;
	}
	?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?php
}
add_action( 'wp_body_open', 'deon_analytics_gtm_body', 1 );

/* ============================================================================
 * 4. GA4 direct — used when GTM isn't set
 * ------------------------------------------------------------------------
 * If the marketing team hasn't set up GTM yet, GA4 direct is the fallback.
 * ========================================================================= */

function deon_analytics_ga4_head() {
	if ( ! deon_analytics_has_consent() ) {
		return;
	}
	// If GTM is set, GA4 is likely managed inside GTM — skip direct install.
	if ( ! empty( trim( (string) get_theme_mod( 'deon_analytics_gtm', '' ) ) ) ) {
		return;
	}
	$ga4_id = trim( (string) get_theme_mod( 'deon_analytics_ga4', '' ) );
	if ( empty( $ga4_id ) || ! preg_match( '/^G-[A-Z0-9]+$/i', $ga4_id ) ) {
		return;
	}
	?>
	<!-- Google Analytics 4 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4_id ); ?>"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', '<?php echo esc_js( $ga4_id ); ?>', { 'anonymize_ip': true });
	</script>
	<!-- End Google Analytics 4 -->
	<?php
}
add_action( 'wp_head', 'deon_analytics_ga4_head', 2 );

/* ============================================================================
 * 5. Meta / Facebook Pixel
 * ========================================================================= */

function deon_analytics_meta_pixel_head() {
	if ( ! deon_analytics_has_consent() ) {
		return;
	}
	$pixel_id = trim( (string) get_theme_mod( 'deon_analytics_meta', '' ) );
	if ( empty( $pixel_id ) || ! ctype_digit( $pixel_id ) ) {
		return;
	}
	?>
	<!-- Meta Pixel -->
	<script>
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', '<?php echo esc_js( $pixel_id ); ?>');
	fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none"
	src="https://www.facebook.com/tr?id=<?php echo esc_attr( $pixel_id ); ?>&ev=PageView&noscript=1"/></noscript>
	<!-- End Meta Pixel -->
	<?php
}
add_action( 'wp_head', 'deon_analytics_meta_pixel_head', 3 );

/* ============================================================================
 * 6. LinkedIn Insight Tag
 * ========================================================================= */

function deon_analytics_linkedin_head() {
	if ( ! deon_analytics_has_consent() ) {
		return;
	}
	$partner_id = trim( (string) get_theme_mod( 'deon_analytics_linkedin', '' ) );
	if ( empty( $partner_id ) || ! ctype_digit( $partner_id ) ) {
		return;
	}
	?>
	<!-- LinkedIn Insight Tag -->
	<script>
	_linkedin_partner_id = "<?php echo esc_js( $partner_id ); ?>";
	window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
	window._linkedin_data_partner_ids.push(_linkedin_partner_id);
	</script>
	<script>
	(function(l){if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
	window.lintrk.q=[]}var s = document.getElementsByTagName("script")[0];
	var b = document.createElement("script");b.type = "text/javascript";b.async = true;
	b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
	s.parentNode.insertBefore(b, s);})(window.lintrk);
	</script>
	<noscript><img height="1" width="1" style="display:none;" alt=""
	src="https://px.ads.linkedin.com/collect/?pid=<?php echo esc_attr( $partner_id ); ?>&fmt=gif"/></noscript>
	<!-- End LinkedIn Insight Tag -->
	<?php
}
add_action( 'wp_head', 'deon_analytics_linkedin_head', 4 );

/* ============================================================================
 * 7. Microsoft Clarity — session replay + heatmaps (free, no PII)
 * ========================================================================= */

function deon_analytics_clarity_head() {
	if ( ! deon_analytics_has_consent() ) {
		return;
	}
	$clarity_id = trim( (string) get_theme_mod( 'deon_analytics_clarity', '' ) );
	if ( empty( $clarity_id ) || ! preg_match( '/^[a-z0-9]+$/i', $clarity_id ) ) {
		return;
	}
	?>
	<!-- Microsoft Clarity -->
	<script type="text/javascript">
	(function(c,l,a,r,i,t,y){
		c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
		t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
		y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
	})(window, document, "clarity", "script", "<?php echo esc_js( $clarity_id ); ?>");
	</script>
	<!-- End Microsoft Clarity -->
	<?php
}
add_action( 'wp_head', 'deon_analytics_clarity_head', 5 );

/* ============================================================================
 * 8. Data-layer event pushes for conversions
 * ------------------------------------------------------------------------
 * Emits GTM dataLayer events on high-value actions. The marketing team then
 * builds conversion triggers inside GTM without touching code again.
 *
 * Currently tracks: contact form submission (via ?form=success URL param),
 * job application (via ?applied=1), calculator completion (via ?calc=done).
 * You can wire more by adding entries to the map below.
 * ========================================================================= */

function deon_analytics_datalayer_events() {
	if ( ! deon_analytics_has_consent() ) {
		return;
	}
	$event_map = array(
		'form'    => 'contact_form_submitted',
		'applied' => 'job_application_submitted',
		'calc'    => 'solar_calculator_completed',
	);
	$fired = array();
	foreach ( $event_map as $qp => $event_name ) {
		if ( ! empty( $_GET[ $qp ] ) ) {
			$fired[] = $event_name;
		}
	}
	if ( empty( $fired ) ) {
		return;
	}
	?>
	<script>
	window.dataLayer = window.dataLayer || [];
	<?php foreach ( $fired as $event ) : ?>
	window.dataLayer.push({ 'event': '<?php echo esc_js( $event ); ?>' });
	<?php endforeach; ?>
	</script>
	<?php
}
add_action( 'wp_footer', 'deon_analytics_datalayer_events', 99 );
