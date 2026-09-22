<?php
/**
 * Careers — dark "Don't see the right role?" CTA. Inset dark block with a
 * decorative border circle. Button links to the Contact page. Static.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<section class="w-full">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-16 md:py-(--section-pad)">
		<div class="relative overflow-hidden bg-deon-dark px-6 py-16 md:p-[96px] flex flex-col items-center text-center gap-6" data-anim>

			<!-- Decorative circle -->
			<span aria-hidden="true"
			      class="absolute -top-32 -right-32 w-64 h-64 rounded-full border border-white/10"></span>

			<h2 class="relative font-sans font-extrabold! text-hero leading-tight tracking-[-0.5px] text-white!">
				<?php esc_html_e( "Don't see the right role?", 'deon-energy' ); ?>
			</h2>
			<p class="relative max-w-[672px] pb-6 font-sans text-lead leading-relaxed text-white/70">
				<?php esc_html_e( 'Send your CV and a note on what you are looking for. We keep a talent register and reach out when a matching role opens.', 'deon-energy' ); ?>
			</p>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
			   class="relative inline-flex items-center justify-center bg-deon-accent py-5 px-12 font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white! transition-opacity hover:opacity-90">
				<?php esc_html_e( 'Submit Your Resume', 'deon-energy' ); ?>
			</a>

		</div>
	</div>
</section>