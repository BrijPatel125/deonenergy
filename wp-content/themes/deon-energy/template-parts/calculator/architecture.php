<?php
/**
 * Solar Calculator — "Behind the Numbers" section (PDF §11): how the estimate
 * is calculated, plus the indicative-only qualifier.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$img = get_template_directory_uri() . '/assets/img';

$features = array(
	array(
		'title' => __( 'Regional Irradiance', 'deon-energy' ),
		'desc'  => __( 'Standard regional solar-resource data for the project location.', 'deon-energy' ),
	),
	array(
		'title' => __( 'Module Performance Assumptions', 'deon-energy' ),
		'desc'  => __( 'Typical module output and degradation over the plant design life.', 'deon-energy' ),
	),
	array(
		'title' => __( 'System', 'deon-energy' ),
		'desc'  => __( 'Standard system losses — wiring, inverter conversion, soiling and temperature.', 'deon-energy' ),
	),
	array(
		'title' => __( 'Financial Model', 'deon-energy' ),
		'desc'  => __( 'Tariff offset against indicative system cost to give a first-order payback.', 'deon-energy' ),
	),
);

?>

<section class="bg-deon-bg py-16 md:py-20">
	<div class="mx-auto grid max-w-[1280px] grid-cols-1 items-center gap-10 px-6 md:grid-cols-12 md:gap-8 md:px-16">

		<div class="flex flex-col gap-6 md:col-span-5 md:gap-[30.9px]" data-anim>
			<h2 class="font-sans text-h2 font-extrabold leading-tight tracking-[-0.5px] text-deon-heading">
				<?php esc_html_e( 'How this estimate is calculated.', 'deon-energy' ); ?>
			</h2>
			<p class="font-sans text-lead leading-relaxed text-deon-body">
				<?php esc_html_e( 'Uses standard regional irradiance data and typical solar performance assumptions to provide a first-order estimate for planning — not a substitute for a site-specific engineering study.', 'deon-energy' ); ?>
			</p>
			<ul class="m-0 flex list-none flex-col gap-4 p-0">
				<?php foreach ( $features as $feature ) : ?>
					<li class="flex items-start gap-4">
						<img src="<?php echo esc_url( $img . '/calc-check.svg' ); ?>" alt="" width="20" height="20" class="size-5 shrink-0 mt-0.5">
						<span class="flex flex-col gap-1">
							<span class="text-[12px] font-bold tracking-[1.2px] text-deon-heading"><?php echo esc_html( $feature['title'] ); ?></span>
							<span class="font-sans text-[13px] leading-[1.5] text-deon-body"><?php echo esc_html( $feature['desc'] ); ?></span>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="font-sans text-[13px] italic leading-[1.55] text-deon-body">
				<?php esc_html_e( 'Model outputs are indicative only. Not financial advice. Actual project economics depend on your site, tariff and financing structure.', 'deon-energy' ); ?>
			</p>
		</div>

		<div class="border border-deon-border bg-deon-icon-bg p-px md:col-span-5 md:col-start-8" data-anim>
			<img
				src="<?php echo esc_url( $img . '/calc-solar-panel.png' ); ?>"
				alt="<?php esc_attr_e( 'Solar panel engineering detail', 'deon-energy' ); ?>"
				class="h-60 w-full object-cover md:h-90"
				loading="lazy"
			>
		</div>
	</div>
</section>
