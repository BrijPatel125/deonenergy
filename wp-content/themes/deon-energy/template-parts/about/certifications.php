<?php
/**
 * About page — Section 07: Certifications & Compliance.
 *
 * Badges come from the `deon_certification` CPT (inc/certifications.php) —
 * client-managed in wp-admin: title = code, Featured Image = badge artwork,
 * Label meta = the line under the code, menu_order = sequence.
 *
 * The section renders NOTHING until a badge is published. That is deliberate:
 * the approved copy (PDF §2) lists this section by heading only, and an ISO
 * registration Deon does not hold is not a placeholder we can ship. Seed the
 * ISO set on a fresh install with `wp eval-file bin/seed-certifications.php`.
 *
 * Code can also inject badges without wp-admin:
 *
 *     add_filter( 'deon_about_certifications', function ( $certs ) {
 *         $certs[] = array( 'icon' => 'about-cert-iso9001.svg', 'code' => 'ISO 9001:2015', 'label' => 'Quality Mgmt' );
 *         return $certs;
 *     } );
 *
 * Each row takes either `icon_url` (a full URL, what the CPT returns) or
 * `icon` (a filename in assets/img/ — about-cert-iso9001.svg,
 * about-cert-iso14001.svg, about-cert-iso45001.svg, about-cert-compliance.svg,
 * about-cert-esg.svg are bundled).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Certification badges shown on the About page.
 *
 * @param array $certs Each: icon_url|icon, code, label. Defaults to the
 *                     published deon_certification posts.
 */
$deon_certs = apply_filters(
	'deon_about_certifications',
	function_exists( 'deon_get_certifications' ) ? deon_get_certifications() : array()
);

if ( empty( $deon_certs ) || ! is_array( $deon_certs ) ) {
	return;
}
?>
<section class="about-certs">
	<div class="deon-container">
		<div class="about-certs__head">
			<h2 class="about-certs__h2"><?php esc_html_e( 'Certifications & Compliance', 'deon-energy' ); ?></h2>
			<p class="about-certs__sub"><?php esc_html_e( 'Compliance and quality standards at every level.', 'deon-energy' ); ?></p>
		</div>
		<div class="about-certs__row">
			<?php foreach ( $deon_certs as $deon_cert ) : ?>
			<div class="about-certs__badge">
				<?php
				// CPT badges arrive as a full URL; filtered rows may name a bundled asset instead.
				$deon_cert_icon = ! empty( $deon_cert['icon_url'] )
					? $deon_cert['icon_url']
					: ( ! empty( $deon_cert['icon'] ) ? get_template_directory_uri() . '/assets/img/' . $deon_cert['icon'] : '' );
				?>
				<?php if ( $deon_cert_icon ) : ?>
				<img
					src="<?php echo esc_url( $deon_cert_icon ); ?>"
					alt=""
					class="about-certs__badge-icon"
					aria-hidden="true"
				/>
				<?php endif; ?>
				<span class="about-certs__badge-code"><?php echo esc_html( $deon_cert['code'] ?? '' ); ?></span>
				<span class="about-certs__badge-label"><?php echo esc_html( $deon_cert['label'] ?? '' ); ?></span>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
