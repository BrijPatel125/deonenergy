<?php
/**
 * Homepage — Core Energy Services (3 cards).
 *
 * @package Deon_Energy
 */

$deon_services = array(
	array(
		'title' => __( 'EPC Services', 'deon-energy' ),
		'text'  => __( 'Turnkey solar power plants for industrial, commercial and open-access customers — engineered, built and grid-connected under a single contract.', 'deon-energy' ),
		'url'   => '/solutions/',
		'icon'  => 'service-epc.svg',
	),
	array(
		'title' => __( 'Operations & Maintenance', 'deon-energy' ),
		'text'  => __( 'Preventive and corrective maintenance, remote monitoring, and generation reporting to protect long-term plant yield.', 'deon-energy' ),
		'url'   => '/solutions/',
		'icon'  => 'service-om.svg',
	),
	array(
		'title' => __( 'Solar Advisory', 'deon-energy' ),
		'text'  => __( 'Feasibility studies, financial modelling, and structuring support — CAPEX vs OPEX, open-access, group captive, and net-metering options.', 'deon-energy' ),
		'url'   => '/solutions/',
		'icon'  => 'service-advisory.svg',
	),
);

$deon_arrow = '<svg viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M1 5h8M5 1l4 4-4 4"/></svg>';
?>
<section class="services deon-section" aria-label="<?php esc_attr_e( 'Core energy services', 'deon-energy' ); ?>">
	<div class="services__inner deon-container">
		<header class="services__head">
			<p class="deon-eyebrow"><?php esc_html_e( 'Our Expertise', 'deon-energy' ); ?></p>
			<h2 class="services__h2"><?php esc_html_e( 'Core Energy Services', 'deon-energy' ); ?></h2>
		</header>

		<div class="services__grid">
			<?php foreach ( $deon_services as $service ) : ?>
				<article class="service-card">
					<img
						class="service-card__icon"
						src="<?php echo esc_url( DEON_URI . '/assets/img/' . $service['icon'] ); ?>"
						alt=""
						aria-hidden="true"
					>
					<h3 class="service-card__title"><?php echo esc_html( $service['title'] ); ?></h3>
					<p class="service-card__text"><?php echo esc_html( $service['text'] ); ?></p>
					<span class="service-card__divider" aria-hidden="true"></span>
					<a class="service-card__link" href="<?php echo esc_url( home_url( $service['url'] ) ); ?>">
						<?php esc_html_e( 'View Capability', 'deon-energy' ); ?>
						<?php echo $deon_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
