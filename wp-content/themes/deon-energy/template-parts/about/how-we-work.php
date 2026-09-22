<?php
/**
 * About page — Section 04: How We Work.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_principles = array(
	array( 'num' => '01', 'label' => __( 'Experience', 'deon-energy' ) ),
	array( 'num' => '02', 'label' => __( 'Longevity', 'deon-energy' ) ),
	array( 'num' => '03', 'label' => __( 'Safety First', 'deon-energy' ) ),
	array( 'num' => '04', 'label' => __( 'Technical Precision', 'deon-energy' ) ),
	array( 'num' => '05', 'label' => __( 'Quality Sourcing', 'deon-energy' ) ),
	array( 'num' => '06', 'label' => __( 'Resilience', 'deon-energy' ) ),
	array( 'num' => '07', 'label' => __( 'Execution Rigour', 'deon-energy' ) ),
	array( 'num' => '08', 'label' => __( 'Integrity', 'deon-energy' ) ),
);
?>
<section class="about-work">
	<div class="deon-container about-work__grid">

		<div class="about-work__lead">
			<h2 class="about-work__h2"><?php esc_html_e( 'Built on Engineering Discipline', 'deon-energy' ); ?></h2>
			<p class="about-work__text"><?php esc_html_e( 'Every project follows a structured approach — precise by design, measurable in execution, and built for reliable performance.', 'deon-energy' ); ?></p>
		</div>

		<div class="about-work__cards">
			<?php foreach ( $deon_principles as $deon_p ) : ?>
			<div class="about-work__card">
				<span class="about-work__num"><?php echo esc_html( $deon_p['num'] ); ?></span>
				<span class="about-work__label"><?php echo esc_html( $deon_p['label'] ); ?></span>
			</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>