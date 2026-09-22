<?php
/**
 * About page — Section 02: Vision & Mission.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="about-vm">
	<div class="deon-container about-vm__grid">

		<div class="about-vm__card about-vm__card--vision">
			<img
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/about-icon-vision.svg' ); ?>"
				alt=""
				class="about-vm__icon"
				aria-hidden="true"
			/>
			<h2 class="about-vm__h3"><?php esc_html_e( 'Our Vision', 'deon-energy' ); ?></h2>
			<p class="about-vm__text"><?php esc_html_e( 'To be a trusted long-term partner for Indian businesses going solar — recognised for engineering discipline, honest commercials, and lasting performance.', 'deon-energy' ); ?></p>
		</div>

		<div class="about-vm__card about-vm__card--mission">
			<img
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/about-icon-mission.svg' ); ?>"
				alt=""
				class="about-vm__icon"
				aria-hidden="true"
			/>
			<h2 class="about-vm__h3"><?php esc_html_e( 'Our Mission', 'deon-energy' ); ?></h2>
			<p class="about-vm__text"><?php esc_html_e( 'To deliver reliable solar systems through precise design, quality procurement, disciplined execution, and long-term support that protects performance throughout the asset lifecycle.', 'deon-energy' ); ?></p>
		</div>

	</div>
</section>
