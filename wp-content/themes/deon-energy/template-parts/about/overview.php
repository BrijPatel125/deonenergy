<?php
/**
 * About page — Section 01: Company Overview.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="about-overview">
	<div class="deon-container about-overview__grid">

		<div class="about-overview__text">
			<h2 class="about-overview__h2"><?php esc_html_e( 'About Deon Energy Limited', 'deon-energy' ); ?></h2>
			<div class="about-overview__body">
				<p class="about-overview__lead"><?php esc_html_e( 'Deon Energy Limited is a solar EPC company designing, supplying, installing and maintaining solar power systems for industrial and commercial customers across India. Headquartered in Ahmedabad, with offices in Rajkot and Morbi, Deon handles the full plant lifecycle — engineering, procurement, construction, and approvals with MNRE and the relevant state agencies (GEDA, GUVNL, CEIG in Gujarat, and their equivalents elsewhere).', 'deon-energy' ); ?></p>
				<p class="about-overview__lead"><?php esc_html_e( 'Founder-led and involved beyond commissioning, Deon stays on through operations and maintenance — because a plant\'s real value shows up over its full 25-year life. This accountability spans Solar EPC, O&M, and Solar Advisory.', 'deon-energy' ); ?></p>
			</div>
			<blockquote class="about-overview__blockquote">
				<p><?php esc_html_e( '"A solar plant rarely fails on day one. It underperforms slowly, in ways a customer only notices years later. Our job is to make those years boring — predictable generation, honest numbers, no surprises."', 'deon-energy' ); ?></p>
				<footer class="about-overview__quote-footer">
					<cite><?php esc_html_e( '— Dharmesh Makadiya, Chairman & MD', 'deon-energy' ); ?></cite>
				</footer>
			</blockquote>
		</div>

		<div class="about-overview__media">
			<div class="about-overview__img-wrap">
				<img
					src="<?php echo esc_url( deon_site_image_url( 'deon_img_about_hero', 'about-hero.jpg' ) ); ?>"
					alt="<?php esc_attr_e( 'Deon Energy solar construction project', 'deon-energy' ); ?>"
					class="about-overview__img"
				/>
			</div>
			<div class="about-overview__hq">
				<div class="about-overview__hq-text">
					<span class="about-overview__hq-label"><?php esc_html_e( 'Headquarters', 'deon-energy' ); ?></span>
					<span class="about-overview__hq-value"><?php esc_html_e( 'Ahmedabad, Gujarat', 'deon-energy' ); ?></span>
				</div>
				<img
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/about-location-icon.svg' ); ?>"
					alt=""
					class="about-overview__hq-icon"
					aria-hidden="true"
				/>
			</div>
		</div>

	</div>
</section>