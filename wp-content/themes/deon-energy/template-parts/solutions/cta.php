<?php
/**
 * Solutions page — CTA section.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="sol-cta">
	<div class="deon-container sol-cta__inner" data-anim>
		<h2 class="sol-cta__title"><?php esc_html_e( 'Ready to plan your solar project?', 'deon-energy' ); ?></h2>
		<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="deon-btn deon-btn--dark sol-cta__btn">
			<?php esc_html_e( 'Start the Conversation', 'deon-energy' ); ?>
		</a>
	</div>
</section>
