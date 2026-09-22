<?php
/**
 * Homepage — CTA banner.
 *
 * @package Deon_Energy
 */
?>
<section class="cta" aria-label="<?php esc_attr_e( 'Get started', 'deon-energy' ); ?>">
	<div class="cta__inner">
		<h2 class="cta__title"><?php esc_html_e( 'Ready to explore solar for your business?', 'deon-energy' ); ?></h2>
		<p class="cta__text"><?php esc_html_e( 'Talk to our team about a site assessment, a technical feasibility study, or a tailored proposal for your facility.', 'deon-energy' ); ?></p>
		<a class="cta__btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Schedule a Site Assessment', 'deon-energy' ); ?></a>
	</div>
</section>
