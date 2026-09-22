<?php
/**
 * Careers — Hiring Process. White band, 4-step horizontal timeline with an
 * amber connector line behind the numbered circles. Static.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_steps = array(
	array(
		'num'   => '01',
		'title' => __( 'Application Review', 'deon-energy' ),
		'desc'  => __( 'We review your CV against the role — skills, experience and fit.', 'deon-energy' ),
	),
	array(
		'num'   => '02',
		'title' => __( 'Introductory Call', 'deon-energy' ),
		'desc'  => __( 'A short call: what you want, and an honest picture of the role.', 'deon-energy' ),
	),
	array(
		'num'   => '03',
		'title' => __( 'Technical Assessment', 'deon-energy' ),
		'desc'  => __( 'A practical, role-relevant assessment. Not theoretical.', 'deon-energy' ),
	),
	array(
		'num'   => '04',
		'title' => __( 'Team & Leadership Round', 'deon-energy' ),
		'desc'  => __( 'Meet your future team and a leader. Two-way — your questions matter.', 'deon-energy' ),
	),
	array(
		'num'   => '05',
		'title' => __( 'Offer & Onboarding', 'deon-energy' ),
		'desc'  => __( 'Clear offer, structured induction, and a 30/60/90-day plan to land well.', 'deon-energy' ),
	),
);

?>

<section class="w-full">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-16 md:py-(--section-pad)">
		<div class="flex flex-col gap-12 md:gap-12">

			<!-- Header -->
			<div class="flex flex-col items-center gap-4 text-center" data-anim>
				<h2 class="font-sans font-extrabold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
					<?php esc_html_e( 'Hiring Process', 'deon-energy' ); ?>
				</h2>
				<p class="font-sans text-lead leading-relaxed text-deon-body">
					<?php esc_html_e( 'A clear, respectful path from application to offer.', 'deon-energy' ); ?>
				</p>
			</div>

			<!-- Steps -->
			<div class="relative">
				<!-- Amber connector line (desktop only) -->
				<span aria-hidden="true"
				      class="hidden md:block absolute left-0 right-0 top-8 h-[2px] bg-deon-accent opacity-20"></span>

				<div class="relative grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-10 md:gap-6" data-anim-stagger>
					<?php foreach ( $deon_steps as $step ) : ?>
						<div class="careers-step flex flex-col items-center text-center gap-2">
							<div class="careers-step__circle w-16 h-16 rounded-full bg-white border-2 border-deon-accent shadow-[0_1px_1px_rgba(0,0,0,0.05)] flex items-center justify-center">
								<span class="careers-step__num font-sans font-bold text-[20px] leading-[28px] text-deon-accent">
									<?php echo esc_html( $step['num'] ); ?>
								</span>
							</div>
							<h3 class="pt-4 font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-heading">
								<?php echo esc_html( $step['title'] ); ?>
							</h3>
							<p class="px-4 font-sans text-[12px] leading-[16px] text-deon-body">
								<?php echo esc_html( $step['desc'] ); ?>
							</p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

		</div>
	</div>
</section>