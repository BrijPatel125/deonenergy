<?php
/**
 * Investor Relations — Direct Investor Support CTA. Static per SOW.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_email = deon_ir_field( '_deon_ir_email', 'investors@deonenergy.in' );
$deon_phone = deon_ir_field( '_deon_ir_phone', '1800 890 5933' );
?>

<section class="w-full">
	<div class="max-w-[1280px] mx-auto px-4 pt-16 pb-20 md:px-16 md:py-(--section-pad)">
		<div class="mx-auto w-full max-w-[800px] flex flex-col items-center text-center gap-6 border-2 border-dashed border-[#747878] p-8 md:p-[50px] md:gap-[23.2px]" data-anim>

			<h2 class="font-sans font-normal text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
				<?php esc_html_e( 'Direct Investor Support', 'deon-energy' ); ?>
			</h2>
			<p class="font-sans font-normal text-lead leading-[1.8] text-deon-body md:max-w-[660px]">
				<?php esc_html_e( 'For inquiries related to share transfer, dividends, or other compliance matters, please reach out to our dedicated Investor Relations desk.', 'deon-energy' ); ?>
			</p>

			<div class="flex flex-col items-center gap-6 sm:flex-row sm:items-center sm:gap-0">
				<div class="flex flex-col items-center gap-4 sm:px-8">
					<span class="font-sans font-normal text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-eyebrow">
						<?php esc_html_e( 'Email', 'deon-energy' ); ?>
					</span>
					<a href="mailto:<?php echo esc_attr( $deon_email ); ?>" class="font-sans font-normal text-lead leading-snug text-deon-heading hover:text-deon-accent transition-colors">
						<?php echo esc_html( $deon_email ); ?>
					</a>
				</div>

				<span class="hidden sm:block w-px h-8 bg-deon-border" aria-hidden="true"></span>

				<div class="flex flex-col items-center gap-4 sm:px-8">
					<span class="font-sans font-normal text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-eyebrow">
						<?php esc_html_e( 'Phone', 'deon-energy' ); ?>
					</span>
					<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $deon_phone ) ); ?>" class="font-sans font-normal text-lead leading-snug text-deon-heading hover:text-deon-accent transition-colors">
						<?php echo esc_html( $deon_phone ); ?>
					</a>
				</div>
			</div>

			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="inline-flex items-center justify-center bg-deon-dark px-10 py-4 mt-2 font-sans font-normal text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white! hover:bg-deon-heading transition-colors">
				<?php esc_html_e( 'Contact Investor Desk', 'deon-energy' ); ?>
			</a>

		</div>
	</div>
</section>