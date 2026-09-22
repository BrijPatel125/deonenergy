<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$has_acf = function_exists( 'get_field' );

$cta_heading        = $has_acf ? get_field( 'esg_cta_heading' ) : '';
$cta_report_label   = $has_acf ? get_field( 'esg_cta_report_label' ) : '';
$cta_report_file    = $has_acf ? get_field( 'esg_cta_report_file' ) : '';
$cta_investor_label = $has_acf ? get_field( 'esg_cta_investor_label' ) : '';
$cta_investor_url   = $has_acf ? get_field( 'esg_cta_investor_url' ) : '';

$cta_heading        = $cta_heading ? $cta_heading : __( 'Transparent Progress. Tangible Change.', 'deon-energy' );
$cta_report_label   = $cta_report_label ? $cta_report_label : __( 'Download 2024 Report', 'deon-energy' );
// No file attached = no button. The old '#' fallback shipped a dead link
// promising a report that may not exist.
$cta_report_url     = $cta_report_file ? $cta_report_file : '';
$cta_investor_label = $cta_investor_label ? $cta_investor_label : __( 'Investor Relations', 'deon-energy' );
$cta_investor_url   = $cta_investor_url ? $cta_investor_url : home_url( '/investor-relations/' );
?>

<section class="w-full bg-deon-accent border-t border-b border-deon-border">
	<div class="max-w-[1280px] mx-auto px-4 py-[80px] flex flex-col gap-8 items-center
	            md:px-16 md:py-(--section-pad)" data-anim>

		<h2 class="font-display font-normal text-h2 leading-tight text-white text-center">
			<?php echo esc_html( $cta_heading ); ?>
		</h2>

		<div class="flex flex-col gap-4 items-center w-full
		            sm:flex-row sm:justify-center sm:gap-6">
			<?php if ( $cta_report_url ) : ?>
			<a href="<?php echo esc_url( $cta_report_url ); ?>" target="_blank" rel="noopener"
			   class="bg-deon-dark text-white! font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase
			          px-[40px] py-[21px] text-center transition-opacity hover:opacity-90">
				<?php echo esc_html( $cta_report_label ); ?>
			</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( $cta_investor_url ); ?>"
			   class="bg-deon-dark text-white! font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase
			          px-[41px] py-[21px] text-center transition-opacity hover:opacity-90">
				<?php echo esc_html( $cta_investor_label ); ?>
			</a>
		</div>

	</div>
</section>