<?php
/**
 * Homepage — About strip (orange band).
 *
 * @package Deon_Energy
 */

$deon_about_stats = array(
	array( __( 'Leadership', 'deon-energy' ), __( 'Founders with deep experience in renewable energy and business development', 'deon-energy' ) ),
	array( __( 'Approach', 'deon-energy' ), __( 'Design decisions optimised for lifecycle generation, not just day-one output', 'deon-energy' ) ),
);
?>
<section class="about-strip" aria-label="<?php esc_attr_e( 'About Deon Energy', 'deon-energy' ); ?>">
	<div class="about-strip__inner deon-container">
		<div class="about-strip__lead">
			<h2 class="about-strip__title"><?php echo wp_kses_post( __( 'Backed by hands-on<br>operating experience.', 'deon-energy' ) ); ?></h2>
			<p class="about-strip__text"><?php esc_html_e( 'Deon Energy is founder-led, with leadership that has spent years inside the renewable and industrial infrastructure sector. Our reference point is what a good plant looks like ten years after commissioning, not just at handover.', 'deon-energy' ); ?></p>
		</div>

		<div class="about-strip__stats">
			<?php foreach ( $deon_about_stats as $stat ) : ?>
				<div class="about-strip__stat">
					<span class="about-strip__stat-label"><?php echo esc_html( $stat[0] ); ?></span>
					<p class="about-strip__stat-value"><?php echo esc_html( $stat[1] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
