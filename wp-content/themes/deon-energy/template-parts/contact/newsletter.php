<?php
/**
 * Contact — Newsletter Branding section.
 * Submits via admin-ajax to deon_handle_newsletter_subscribe() (functions.php);
 * captured leads are stored as the deon_subscriber CPT. main.js wires the
 * inline feedback. ESP/Mailchimp delivery stays out of SOW scope.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<section class="w-full relative bg-deon-heading overflow-hidden">
	<!-- Desaturated (grayscale) ridgeline image, matching Figma's saturation-blend treatment. -->
	<div class="absolute inset-0 bg-[url('../img/contact-newsletter-bg.png')] bg-cover bg-center grayscale" aria-hidden="true"></div>
	<!-- Dark overlay matching the design's rgba(0,0,0,0.8) treatment. -->
	<div class="absolute inset-0 bg-deon-dark/80"></div>

	<div class="relative max-w-[1280px] mx-auto px-4 py-[80px] md:px-16 md:py-(--section-pad)">
		<div class="md:grid md:grid-cols-12">
			<div class="md:col-span-6 flex flex-col gap-[24px]" data-anim>

				<h2 class="font-display font-normal! text-white! text-hero leading-tight">
					<?php esc_html_e( 'Stay informed on', 'deon-energy' ); ?><br><?php esc_html_e( 'renewable policy.', 'deon-energy' ); ?>
				</h2>

				<?php
				get_template_part( 'template-parts/global/newsletter-form', null, array(
					'source'      => 'contact',
					'variant'     => 'contact',
					'input_id'    => 'deon-newsletter-email',
					'placeholder' => __( 'Your corporate email', 'deon-energy' ),
					'label'       => __( 'Your corporate email', 'deon-energy' ),
				) );
				?>

			</div>
		</div>
	</div>
</section>
