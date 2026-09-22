<?php
/**
 * Investor Relations — IPO Context.
 * Left: context copy. Right: a wider content area, with the image itself
 * kept at its original size (centered inside that area) — uploaded from the
 * "Investor Relations Content" box on this page's edit screen
 * (field: IPO Context — image).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_uri     = get_template_directory_uri();
$deon_ipo_img = deon_ir_field( '_deon_ir_image', $deon_uri . '/assets/img/investor-ipo-context.jpg' );
?>

<section class="w-full bg-white border-b border-deon-border">
	<div class="max-w-[1280px] mx-auto px-4 py-16 flex flex-col gap-10
	            md:px-16 md:py-(--section-pad) md:flex-row md:items-center md:justify-between md:gap-16">

		<div class="flex flex-col gap-6 md:max-w-[452px]" data-anim>
			<h2 class="font-sans font-normal text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
				<?php esc_html_e( 'Initial Public Offering Context', 'deon-energy' ); ?>
			</h2>
			<p class="font-sans font-normal text-lead leading-relaxed text-deon-body">
				<?php esc_html_e( 'Deon Energy Limited is entering a new era of transparency and public accountability. Our IPO represents a significant milestone in our mission to lead the global transition toward sustainable energy infrastructure. We are committed to creating long-term value for our shareholders through disciplined capital allocation and operational excellence in large-scale solar developments.', 'deon-energy' ); ?>
			</p>
		</div>

		<div class="w-full md:max-w-[560px]" data-anim>
			<div class="w-full md:max-w-[420px] md:ml-auto">
				<img src="<?php echo esc_url( $deon_ipo_img ); ?>"
				     alt="<?php esc_attr_e( 'Deon Energy solar installation', 'deon-energy' ); ?>"
				     class="w-full h-auto object-cover aspect-[4/3]" />
			</div>
		</div>

	</div>
</section>