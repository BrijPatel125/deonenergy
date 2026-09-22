<?php
/**
 * Solar Calculator — Main interface (inputs + results panel).
 *
 * INTERACTIVE. Inputs drive a client-side estimation model in
 * assets/js/calc.js (enqueued as `deon-calc` on this template). All output
 * figures update live; no server round-trip. The model is an on-site estimate
 * only — a site visit is still required for a binding offer (see disclaimer).
 *
 * Markup ships the designed sample defaults so the panel reads correctly with
 * JS disabled; calc.js recomputes on load and on every input change.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$img = get_template_directory_uri() . '/assets/img';

// Model constants surfaced to JS via data-* so tuning stays in one place.
$cfg = array(
	'yield'        => 1500,  // kWh per kWp per year (regional specific yield, sunny/India base).
	'sqftPerKwp'   => 100,   // Roof area (sq.ft) required per kWp installed.
	'costResi'     => 55000, // Installed system cost ₹/kWp — residential.
	'costCommer'   => 45000, // Installed system cost ₹/kWp — commercial.
	'escalation'   => 0.03,  // Annual tariff escalation for the 10-year projection.
	'orientSouth'  => 1.0,  // Production factor by roof orientation.
	'orientEW'     => 0.85,
	'orientNorth'  => 0.6,
);
?>

<section class="bg-white py-12 md:py-16">
	<div class="mx-auto max-w-[1280px] px-6 md:px-16">
	<div id="calc"
	     class="grid grid-cols-1 border border-deon-border md:grid-cols-12"
	     data-yield="<?php echo esc_attr( $cfg['yield'] ); ?>"
	     data-sqft-per-kwp="<?php echo esc_attr( $cfg['sqftPerKwp'] ); ?>"
	     data-cost-resi="<?php echo esc_attr( $cfg['costResi'] ); ?>"
	     data-cost-commer="<?php echo esc_attr( $cfg['costCommer'] ); ?>"
	     data-escalation="<?php echo esc_attr( $cfg['escalation'] ); ?>"
	     data-orient-south="<?php echo esc_attr( $cfg['orientSouth'] ); ?>"
	     data-orient-ew="<?php echo esc_attr( $cfg['orientEW'] ); ?>"
	     data-orient-north="<?php echo esc_attr( $cfg['orientNorth'] ); ?>">

		<?php // ---------- LEFT: INPUTS ---------- ?>
		<div class="bg-white md:col-span-7" data-anim>
			<div class="flex flex-col gap-12 p-6 md:gap-16 md:p-12">

				<div class="flex flex-col gap-12">

					<?php // --- Monthly bill slider --- ?>
					<div class="flex flex-col gap-6">
						<div class="flex items-end justify-between">
							<span class="text-[12px] font-bold uppercase tracking-[1.2px] text-deon-body"><?php esc_html_e( 'Avg. Monthly Electricity Bill', 'deon-energy' ); ?></span>
							<span id="calc-bill-out" class="font-display text-h3 font-bold tracking-[0.2px] text-deon-accent">₹25,000</span>
						</div>
						<input type="range" id="calc-bill" name="calc-bill" min="1000" max="100000" step="500" value="25000"
						       aria-label="<?php esc_attr_e( 'Average monthly electricity bill', 'deon-energy' ); ?>"
						       class="w-full cursor-pointer appearance-none bg-transparent
						              [&::-webkit-slider-runnable-track]:h-[2px] [&::-webkit-slider-runnable-track]:bg-deon-border
						              [&::-webkit-slider-thumb]:-mt-[7px] [&::-webkit-slider-thumb]:size-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:bg-deon-accent
						              [&::-moz-range-track]:h-[2px] [&::-moz-range-track]:bg-deon-border
						              [&::-moz-range-thumb]:size-4 [&::-moz-range-thumb]:rounded-none [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:bg-deon-accent">
						<div class="flex items-start justify-between text-[10px] leading-[15px] text-deon-body/60">
							<span>₹1,000</span>
							<span>₹1,00,000+</span>
						</div>
					</div>

					<?php // --- Technical inputs grid --- ?>
					<div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
						<div class="flex flex-col gap-[8.5px]">
							<label for="calc-roof" class="text-[12px] font-bold tracking-[1.2px] text-deon-body"><?php esc_html_e( 'Available Roof Area (Sq.Ft)', 'deon-energy' ); ?></label>
							<input type="number" id="calc-roof" name="calc-roof" min="100" step="10" value="1200"
							       class="h-[58px] w-full border border-deon-border px-3 text-[16px] text-deon-body/70 outline-none focus:border-deon-accent">
						</div>
						<div class="flex flex-col gap-[8.5px]">
							<label for="calc-tariff" class="text-[12px] font-bold tracking-[1.2px] text-deon-body"><?php esc_html_e( 'Current Tariff Rate (₹/kWh)', 'deon-energy' ); ?></label>
							<input type="number" id="calc-tariff" name="calc-tariff" min="0.5" step="0.1" value="8"
							       class="h-[58px] w-full border border-deon-border px-3 text-[16px] text-deon-body/70 outline-none focus:border-deon-accent">
						</div>
					</div>

					<?php // --- Orientation + installation type toggles --- ?>
					<div class="grid grid-cols-1 gap-8 pt-4 sm:grid-cols-2">
						<div class="flex flex-col gap-[16.5px]">
							<span class="text-[12px] font-bold tracking-[1.2px] text-deon-body"><?php esc_html_e( 'Roof Orientation', 'deon-energy' ); ?></span>
							<div class="flex gap-2" role="group" data-calc-toggle="orient">
								<button type="button" data-value="south" aria-pressed="true" class="calc-seg is-active border border-deon-heading px-[17px] py-[9px] text-[10px] leading-[15px] text-deon-heading"><?php esc_html_e( 'SOUTH', 'deon-energy' ); ?></button>
								<button type="button" data-value="ew" aria-pressed="false" class="calc-seg border border-deon-border px-[17px] py-[9px] text-[10px] leading-[15px] text-deon-body/60"><?php esc_html_e( 'EAST/WEST', 'deon-energy' ); ?></button>
								<button type="button" data-value="north" aria-pressed="false" class="calc-seg border border-deon-border px-[17px] py-[9px] text-[10px] leading-[15px] text-deon-body/60"><?php esc_html_e( 'NORTH', 'deon-energy' ); ?></button>
							</div>
						</div>
						<div class="flex flex-col gap-[16.5px]">
							<span class="text-[12px] font-bold tracking-[1.2px] text-deon-body"><?php esc_html_e( 'Installation Type', 'deon-energy' ); ?></span>
							<div class="flex gap-2" role="group" data-calc-toggle="type">
								<button type="button" data-value="residential" aria-pressed="true" class="calc-seg is-active border border-deon-heading px-[17px] py-[9px] text-[10px] leading-[15px] text-deon-heading"><?php esc_html_e( 'RESIDENTIAL', 'deon-energy' ); ?></button>
								<button type="button" data-value="commercial" aria-pressed="false" class="calc-seg border border-deon-border px-[17px] py-[9px] text-[10px] leading-[15px] text-deon-body/60"><?php esc_html_e( 'COMMERCIAL', 'deon-energy' ); ?></button>
							</div>
						</div>
					</div>
				</div>

				<?php // --- Projected 10-year savings chart --- ?>
				<div class="flex flex-col gap-8 border-t border-deon-border pt-[49px]">
					<span class="text-[12px] font-bold tracking-[1.2px] text-deon-body"><?php esc_html_e( 'Projected 10-Year Savings Accumulation', 'deon-energy' ); ?></span>
					<div id="calc-bars" class="flex h-[150px] items-end justify-center gap-2 md:h-[192px]">
						<?php // Bars are (re)rendered by calc.js; static ramp shown as a no-JS fallback. ?>
						<?php
						$fallback = array( 10, 20, 30, 40, 55, 65, 75, 85, 92, 100 );
						foreach ( $fallback as $i => $h ) :
							$tone = $h >= 90 ? 'bg-deon-accent' : ( $h >= 50 ? 'bg-deon-accent/50' : 'bg-deon-border' );
							?>
							<div class="relative flex-1 <?php echo esc_attr( $tone ); ?>" style="height: <?php echo esc_attr( $h ); ?>%">
								<?php if ( count( $fallback ) - 1 === $i ) : ?>
									<span id="calc-bars-label" class="absolute -top-6 left-0 right-0 text-center text-[12px] font-bold tracking-[1.2px] text-deon-accent">₹16.5L</span>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

		<?php // ---------- RIGHT: RESULTS PANEL ---------- ?>
		<div class="flex flex-col justify-between gap-16 border-t border-deon-border bg-deon-warm p-6 md:col-span-5 md:border-t-0 md:border-l md:p-12" data-anim>

			<div class="flex flex-col gap-16">
				<?php
				$stats = array(
					array(
						'id'    => 'calc-size',
						'label' => __( 'Estimated System Size', 'deon-energy' ),
						'value' => '12.0',
						'unit'  => __( 'kWp', 'deon-energy' ),
						'note'  => '',
					),
					array(
						'id'    => 'calc-savings',
						'label' => __( 'Annual Energy Savings', 'deon-energy' ),
						'value' => '₹1,44,000',
						'unit'  => '',
						'note'  => __( 'Indicative only — based on standard production assumptions.', 'deon-energy' ),
					),
					array(
						'id'    => 'calc-payback',
						'label' => __( 'System Payback Period', 'deon-energy' ),
						'value' => '4.6',
						'unit'  => __( 'Years', 'deon-energy' ),
						'note'  => '',
					),
				);
				foreach ( $stats as $stat ) :
					?>
					<div class="flex gap-6">
						<span class="h-24 w-px shrink-0 bg-deon-accent"></span>
						<div class="flex flex-col gap-2">
							<span class="text-[12px] font-bold tracking-[1.2px] text-deon-body"><?php echo esc_html( $stat['label'] ); ?></span>
							<div class="flex items-baseline gap-2">
								<span id="<?php echo esc_attr( $stat['id'] ); ?>" class="whitespace-nowrap font-display text-[clamp(28px,4.6vw,42px)] font-bold leading-none tracking-[-0.64px] text-deon-accent"><?php
								// Plus Jakarta Sans carries a proper ₹ glyph — no font swap needed.
								echo esc_html( $stat['value'] );
								?></span>
								<?php if ( $stat['unit'] ) : ?>
									<span class="font-display text-h3 tracking-[0.2px] text-deon-body"><?php echo esc_html( $stat['unit'] ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( $stat['note'] ) : ?>
								<span class="text-[14px] italic leading-[21px] text-deon-body"><?php echo esc_html( $stat['note'] ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php // --- CTA + disclaimer --- ?>
			<div class="flex flex-col gap-6 pt-16">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="flex w-full items-center justify-center gap-4 bg-deon-heading py-6 text-[12px] font-bold tracking-[1.2px] text-white! transition-opacity hover:opacity-90">
					<?php esc_html_e( 'Request a Detailed Proposal', 'deon-energy' ); ?>
					<img src="<?php echo esc_url( $img . '/calc-arrow.svg' ); ?>" alt="" width="12" height="12" class="size-3">
				</a>
				<p class="px-8 text-center text-[12px] leading-[19.5px] text-deon-body/60">
					<?php esc_html_e( 'This calculator produces an indicative estimate based on standard assumptions for irradiance, module performance, and system losses. Actual system size, generation and payback depend on your specific site, load pattern, tariff, roof or land condition, applicable subsidies and financing choice. A site visit is required for a binding commercial proposal. Nothing on this page is a savings guarantee or financial advice.', 'deon-energy' ); ?>
				</p>
			</div>
		</div>
	</div>
	</div>
</section>
