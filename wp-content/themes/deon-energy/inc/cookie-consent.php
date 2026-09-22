<?php
/**
 * Deon Energy — Cookie consent banner.
 *
 * ---
 * Cache-safe visibility check (client-side):
 * Older versions of this file gated banner rendering with a PHP check on
 * $_COOKIE['deon_cookie_consent']. That breaks the moment a page cache
 * (LiteSpeed Cache, WP Rocket, Hostinger server cache) captures a page
 * for a visitor who hadn't consented yet — the cached HTML then includes
 * the banner, and every subsequent visitor gets it regardless of their
 * own cookie state.
 *
 * The fix is to ALWAYS emit the banner HTML (hidden by default) so cached
 * pages are consistent, and let a tiny script decide visibility on the
 * client. Once a decision is made, we store it in BOTH a cookie (so PHP
 * analytics gating in inc/analytics.php still works) and localStorage
 * (belt-and-braces backup that survives even if the cookie is cleared by
 * a browser's privacy mode).
 * ---
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function deon_cookie_banner_render() {
	// Skip in admin, customizer preview, and REST/AJAX contexts.
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	// Link to the site's privacy policy, if it exists.
	$privacy = get_privacy_policy_url();
	if ( empty( $privacy ) ) {
		$policy = get_page_by_path( 'privacy-policy' );
		$privacy = $policy ? get_permalink( $policy ) : home_url( '/privacy-policy/' );
	}
	?>
	<style>
	.deon-cookie{position:fixed;z-index:9999;left:16px;right:16px;bottom:16px;max-width:640px;margin:0 auto;padding:20px 24px;background:#fff;border:1px solid #e5e5e5;border-left:4px solid #F29022;box-shadow:0 8px 32px rgba(0,0,0,0.12);border-radius:6px;font-family:'Plus Jakarta Sans',system-ui,-apple-system,sans-serif;font-size:14px;line-height:1.55;color:#1f2733;display:none;opacity:0;transform:translateY(20px);transition:opacity .35s ease-out,transform .35s ease-out}
	.deon-cookie.is-open{display:block}
	.deon-cookie.is-visible{opacity:1;transform:translateY(0)}
	.deon-cookie__title{font-weight:700;font-size:15px;margin:0 0 6px;color:#1f2733}
	.deon-cookie__text{margin:0 0 14px;color:#4d5563}
	.deon-cookie__text a{color:#F29022;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:2px}
	.deon-cookie__row{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
	.deon-cookie__btn{border:0;padding:9px 20px;border-radius:22px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s,color .15s,border-color .15s}
	.deon-cookie__btn--accept{background:#F29022;color:#fff}
	.deon-cookie__btn--accept:hover{background:#d9800f}
	.deon-cookie__btn--reject{background:transparent;color:#1f2733;border:1px solid #d1d5db}
	.deon-cookie__btn--reject:hover{background:#f9fafb;border-color:#9ca3af}
	.deon-cookie__link{color:#4d5563;font-size:12px;text-decoration:underline;margin-inline-start:auto;padding:4px 8px}
	@media (max-width:480px){.deon-cookie{padding:16px 18px;font-size:13px}.deon-cookie__row{gap:6px}.deon-cookie__btn{padding:8px 16px;font-size:12px}}
	</style>

	<div class="deon-cookie" role="dialog" aria-labelledby="deon-cookie-title" aria-describedby="deon-cookie-text" id="deon-cookie-banner">
		<p class="deon-cookie__title" id="deon-cookie-title">
			<?php esc_html_e( 'Your privacy matters to us', 'deon-energy' ); ?>
		</p>
		<p class="deon-cookie__text" id="deon-cookie-text">
			<?php
			printf(
				/* translators: %s = privacy policy link */
				esc_html__( 'We use essential cookies to make the site work, and analytics cookies to understand how visitors use our pages so we can improve them. You can accept or decline. Details in our %s.', 'deon-energy' ),
				'<a href="' . esc_url( $privacy ) . '">' . esc_html__( 'Privacy Policy', 'deon-energy' ) . '</a>'
			);
			?>
		</p>
		<div class="deon-cookie__row">
			<button type="button" class="deon-cookie__btn deon-cookie__btn--accept" data-deon-consent="granted">
				<?php esc_html_e( 'Accept all', 'deon-energy' ); ?>
			</button>
			<button type="button" class="deon-cookie__btn deon-cookie__btn--reject" data-deon-consent="denied">
				<?php esc_html_e( 'Reject non-essential', 'deon-energy' ); ?>
			</button>
			<a href="<?php echo esc_url( $privacy ); ?>" class="deon-cookie__link">
				<?php esc_html_e( 'Learn more', 'deon-energy' ); ?>
			</a>
		</div>
	</div>

	<script>
	(function(){
		var STORAGE_KEY = 'deon_cookie_consent';
		var COOKIE_NAME = 'deon_cookie_consent';
		var banner = document.getElementById('deon-cookie-banner');
		if (!banner) return;

		// Read consent from EITHER localStorage or cookie (belt-and-braces).
		function hasConsent() {
			try {
				if (localStorage.getItem(STORAGE_KEY)) return true;
			} catch(e) { /* private-mode localStorage can throw — ignore */ }
			return document.cookie.indexOf(COOKIE_NAME + '=') !== -1;
		}

		// Client-side visibility check — this is the important line.
		// Because the check happens in the browser (not in cached HTML), it
		// works correctly regardless of page-cache behavior.
		if (hasConsent()) {
			return; // decision made previously; nothing to show
		}

		// Reveal the banner after a small delay so it slides in politely.
		banner.classList.add('is-open');
		setTimeout(function(){ banner.classList.add('is-visible'); }, 300);

		function setCookie(name, value, days) {
			var expires = new Date();
			expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
			var secure = (location.protocol === 'https:') ? '; Secure' : '';
			document.cookie = name + '=' + value + '; expires=' + expires.toUTCString() +
				'; path=/; SameSite=Lax' + secure;
		}

		function dismiss(consentValue) {
			// Store in BOTH places so caching / private mode can't reintroduce
			// the banner on the next page load.
			try { localStorage.setItem(STORAGE_KEY, consentValue); } catch(e) {}
			setCookie(COOKIE_NAME, consentValue, 180);

			banner.classList.remove('is-visible');
			setTimeout(function(){ banner.classList.remove('is-open'); }, 350);

			// On accept, reload once so analytics tags in inc/analytics.php
			// fire on this same visit (they were skipped this pageview because
			// the cookie hadn't been set yet). On reject, no reload needed.
			if (consentValue === 'granted') {
				setTimeout(function(){ location.reload(); }, 400);
			}
		}

		var buttons = banner.querySelectorAll('[data-deon-consent]');
		for (var i = 0; i < buttons.length; i++) {
			buttons[i].addEventListener('click', function(){
				dismiss(this.getAttribute('data-deon-consent'));
			});
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'deon_cookie_banner_render', 999 );