<?php
/**
 * Homepage — Why Deon Energy (6 feature columns).
 *
 * @package Deon_Energy
 */

$deon_why = array(
	array( __( 'End-to-End EPC', 'deon-energy' ), __( 'Engineering, procurement and construction under one roof — from design to grid synchronisation and handover.', 'deon-energy' ) ),
	array( __( 'Transparent Commercials', 'deon-energy' ), __( 'Clear costing, honest payback ranges, and milestone-linked reporting — you see the assumptions, not just the number.', 'deon-energy' ) ),
	array( __( 'Tier-1 Component Sourcing', 'deon-energy' ), __( 'Modules and inverters from bankable, warranty-backed manufacturers with established India service networks.', 'deon-energy' ) ),
	array( __( 'Regulatory Handling', 'deon-energy' ), __( 'We handle DISCOM, net-metering, CEIG and MNRE paperwork — so your team stays focused on operations.', 'deon-energy' ) ),
	array( __( 'Post-Commissioning O&M', 'deon-energy' ), __( 'Long-term O&M contracts with defined uptime targets, monthly generation reporting, and preventive schedules.', 'deon-energy' ) ),
	array( __( 'Safety on Site', 'deon-energy' ), __( 'HSE protocols on every site — height-work permits, safety drills, and PPE compliance are standard, not optional.', 'deon-energy' ) ),
);
?>
<section class="why deon-section" aria-label="<?php esc_attr_e( 'Why choose Deon Energy', 'deon-energy' ); ?>">
	<div class="why__inner deon-container">
		<div class="why__lead">
			<p class="deon-eyebrow"><?php esc_html_e( 'What Sets Us Apart', 'deon-energy' ); ?></p>
			<h2 class="why__heading"><?php echo wp_kses_post( __( 'Engineering discipline.<br>Financial clarity.<br>Long-term ownership.', 'deon-energy' ) ); ?></h2>
			<p class="why__text"><?php esc_html_e( 'We engineer every plant for its full design life and back every number with the assumptions behind it — one accountable partner from feasibility through years of operation.', 'deon-energy' ); ?></p>
		</div>

		<div class="why__grid">
			<?php foreach ( $deon_why as $item ) : ?>
				<article class="why__item">
					<h3 class="why__item-title"><?php echo esc_html( $item[0] ); ?></h3>
					<p class="why__item-text"><?php echo esc_html( $item[1] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>