<?php
/**
 * FAQ — "Still have questions?" support band (warm peach).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_contact = get_page_by_path( 'contact' );
$deon_contact_url = $deon_contact ? get_permalink( $deon_contact ) : home_url( '/contact/' );
?>

<section class="w-full bg-deon-warm border-t border-deon-border">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-[60px] md:py-[72px]
	            flex flex-col items-center text-center" data-anim>
		<span class="w-11 h-11 bg-deon-icon-bg flex items-center justify-center text-deon-accent" aria-hidden="true">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
				<path d="M17 12a2 2 0 0 1-2 2H7l-4 3V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
			</svg>
		</span>
		<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.3px] text-deon-heading mt-5">
			<?php esc_html_e( 'Still Have Questions?', 'deon-energy' ); ?>
		</h2>
		<p class="font-sans font-normal text-lead leading-relaxed text-deon-body mt-2.5 max-w-[460px]">
			<?php esc_html_e( "Our team is available for technical, commercial or regulatory questions. Get in touch and we'll route your query to the right person.", 'deon-energy' ); ?>
		</p>

		<div class="flex flex-col sm:flex-row items-center gap-3 mt-7">
			<a href="<?php echo esc_url( $deon_contact_url ); ?>"
			   class="inline-flex items-center justify-center bg-deon-accent px-6 py-3
			          font-sans font-semibold text-[14px] text-white! hover:opacity-90 transition">
				<?php esc_html_e( 'Contact Our Team', 'deon-energy' ); ?>
			</a>
			<a href="<?php echo esc_url( $deon_contact_url ); ?>"
			   class="inline-flex items-center justify-center border border-deon-border bg-white px-6 py-3
			          font-sans font-semibold text-[14px] text-deon-heading hover:border-deon-accent transition">
				<?php esc_html_e( 'Request a Site Assessment', 'deon-energy' ); ?>
			</a>
		</div>
	</div>
</section>
