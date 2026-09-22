<?php
/**
 * Shared projects CTA — used by page-projects.php and single-deon_project.php.
 *
 * Light band: the one dark #1D1B20 band on the Project Gallery page is now the
 * verified portfolio stat strip ( template-parts/projects/stats.php ).
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<section class="w-full bg-deon-bg border-t border-deon-divider">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)
	            flex flex-col items-center text-center gap-5 md:gap-6" data-anim>

		<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-accent">
			<?php esc_html_e( 'Partner With Deon', 'deon-energy' ); ?>
		</span>

		<h2 class="font-display font-extrabold text-h2 leading-[1.15] tracking-[-0.5px] text-deon-heading max-w-[760px]">
			<?php esc_html_e( 'Considering solar for your facility?', 'deon-energy' ); ?>
		</h2>

		<p class="font-sans font-normal text-lead leading-[1.75] text-deon-body max-w-[680px]">
			<?php esc_html_e( 'Talk to us about a site assessment, feasibility study, or a tailored EPC proposal.', 'deon-energy' ); ?>
		</p>

		<div class="flex flex-col gap-4 items-stretch pt-2 w-full max-w-[440px]
		            sm:flex-row sm:justify-center sm:items-center sm:w-auto sm:max-w-none">

			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
			   class="inline-flex items-center justify-center bg-deon-accent px-[40px] py-[17px]
			          font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white!
			          transition-opacity hover:opacity-90">
				<?php esc_html_e( 'Request a Proposal', 'deon-energy' ); ?>
			</a>

			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
			   class="inline-flex items-center justify-center border border-deon-heading px-[41px] py-[17px]
			          font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-heading
			          transition-colors hover:bg-deon-dark hover:text-white!">
				<?php esc_html_e( 'Discuss a Project', 'deon-energy' ); ?>
			</a>

		</div>
	</div>
</section>
