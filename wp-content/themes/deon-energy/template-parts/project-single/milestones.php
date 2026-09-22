<?php
/**
 * Project detail — technical milestones. Numbered build/commissioning steps
 * (01, 02, 03…) from the _deon_milestones repeater. Hidden when none exist.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_miles = function_exists( 'deon_project_milestones' ) ? deon_project_milestones( get_the_ID() ) : array();
if ( empty( $deon_miles ) ) {
	return;
}
?>

<section class="w-full bg-white">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">
		<div class="flex items-center gap-4 mb-8 md:mb-10" data-anim>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-accent shrink-0">
				<?php esc_html_e( 'Technical Milestones', 'deon-energy' ); ?>
			</span>
			<div class="bg-deon-border h-px flex-1 min-w-0"></div>
		</div>

		<ol class="max-w-[820px] flex flex-col" data-anim-stagger>
			<?php foreach ( $deon_miles as $deon_i => $deon_m ) : ?>
			<li class="flex items-start gap-6 py-6 border-t border-deon-border first:border-t-0">
				<span class="font-display font-extrabold text-[clamp(28px,4vw,40px)] leading-none text-deon-border shrink-0 w-[52px]">
					<?php echo esc_html( str_pad( $deon_i + 1, 2, '0', STR_PAD_LEFT ) ); ?>
				</span>
				<div class="flex flex-col gap-1 pt-1">
					<?php if ( '' !== $deon_m['title'] ) : ?>
					<h3 class="font-sans font-bold text-[16px] leading-[1.4] text-deon-heading">
						<?php echo esc_html( $deon_m['title'] ); ?>
					</h3>
					<?php endif; ?>
					<?php if ( '' !== $deon_m['desc'] ) : ?>
					<p class="font-sans font-normal text-[14px] leading-[1.6] text-deon-body">
						<?php echo esc_html( $deon_m['desc'] ); ?>
					</p>
					<?php endif; ?>
				</div>
			</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
