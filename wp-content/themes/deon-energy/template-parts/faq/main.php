<?php
/**
 * FAQ — body: category sidebar + per-category accordion groups.
 *
 * Data comes from deon_faq_groups() (deon_faq CPT grouped by category). Falls
 * back to the four designed categories until the client publishes real FAQs.
 * Sidebar links + accordion + live search are wired in main.js via the
 * data-faq-* hooks.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_faq_groups = deon_faq_groups();

if ( empty( $deon_faq_groups ) ) {
	// Design fallbacks — render before the client publishes real FAQs.
	$deon_faq_groups = array(
		array(
			'name' => __( 'Solar Technology', 'deon-energy' ), 'slug' => 'solar-technology', 'icon' => 'solar',
			'items' => array(
				array( 'question' => __( 'How does Deon Energy protect long-term plant performance?', 'deon-energy' ), 'answer' => wpautop( __( 'Through three things — component selection (Tier-1 modules and inverters with strong warranty backing), site-specific engineering (spacing, tilt and cable design matched to actual site conditions), and a structured O&M contract with monitoring and scheduled preventive work. Long-term generation is engineered, not assumed.', 'deon-energy' ) ) ),
				array( 'question' => __( 'What is the difference between industrial and commercial grade solar systems?', 'deon-energy' ), 'answer' => wpautop( __( 'The core equipment is often similar, but the design approach differs. Industrial systems tend to be larger, on stronger structures, integrated with higher-voltage evacuation, and built to withstand harsher site conditions (heat, dust, humidity, chemical exposure). Commercial systems are smaller, typically LT-connected, and optimised for the tariff and consumption pattern of a commercial building. We engineer to the load and the environment, not to a template.', 'deon-energy' ) ) ),
			),
		),
		array(
			'name' => __( 'Commercial Projects', 'deon-energy' ), 'slug' => 'commercial-projects', 'icon' => 'commercial',
			'items' => array(
				array( 'question' => __( 'What kind of payback period should we expect for a commercial solar system?', 'deon-energy' ), 'answer' => wpautop( __( 'Payback varies significantly by tariff, load profile, roof or land availability, financing structure and applicable subsidies. As a broad range for eligible commercial and industrial customers in Gujarat under a CAPEX model, payback typically falls within 3–6 years, but a proper site-specific assessment is the only way to give you a defensible number.', 'deon-energy' ) ) . '<p class="deon-faq__note"><em>' . esc_html__( 'Illustrative range only, not a commitment. Actual payback depends on your tariff, load profile, site conditions, and financing.', 'deon-energy' ) . '</em></p>' ),
				array( 'question' => __( 'Which regions in India does Deon Energy operate in?', 'deon-energy' ), 'answer' => wpautop( __( 'Our head office is in Ahmedabad, with regional offices in Rajkot and Morbi. Gujarat is our primary operating market, and we selectively take on projects in other states where we can deliver to the same technical and safety standard.', 'deon-energy' ) ) ),
			),
		),
		array(
			'name' => __( 'Investment & Commercials', 'deon-energy' ), 'slug' => 'investment-commercials', 'icon' => 'investment',
			'items' => array(
				array( 'question' => __( 'What does a proposal from Deon typically include?', 'deon-energy' ), 'answer' => wpautop( __( 'A written proposal includes plant sizing based on your load and available area, the recommended equipment configuration, estimated annual generation and revenue/savings modelling (with assumptions stated), CAPEX vs OPEX options where relevant, delivery timeline, and applicable regulatory and safety compliance. Every commercial number carries the assumptions behind it.', 'deon-energy' ) ) ),
				array( 'question' => __( 'Are Deon Energy’s financial or investment reports publicly available?', 'deon-energy' ), 'answer' => wpautop( __( 'For queries relating to the company’s financial position, filings or investor information, please write to our investor relations contact. We will share what is publicly available and direct you to the right disclosures.', 'deon-energy' ) ) ),
			),
		),
		array(
			'name' => __( 'Maintenance & Support', 'deon-energy' ), 'slug' => 'maintenance-support', 'icon' => 'maintenance',
			'items' => array(
				array( 'question' => __( 'What is included in a Deon O&M contract?', 'deon-energy' ), 'answer' => wpautop( __( 'A typical O&M contract covers scheduled preventive maintenance (module cleaning cycles, inverter servicing, structural checks), corrective maintenance within defined response times, 24/7 remote monitoring, monthly generation and uptime reporting, and coordination with the DISCOM for any grid-side issues. Contract scope is confirmed in writing project by project.', 'deon-energy' ) ) ),
				array( 'question' => __( 'How quickly does your team respond to an issue on-site?', 'deon-energy' ), 'answer' => wpautop( __( 'Response times are defined contractually and depend on the plant location and criticality tier. For plants under active O&M, we commit to defined SLA windows and track performance against them month by month.', 'deon-energy' ) ) ),
			),
		),
	);
}
?>

<section class="w-full bg-deon-warm">
	<div class="max-w-[1280px] mx-auto px-4 py-14 md:px-[60px] md:py-[72px]
	            grid grid-cols-1 md:grid-cols-[220px_1fr] gap-10 md:gap-[64px]">

		<!-- Category sidebar -->
		<aside class="md:sticky md:top-24 self-start" data-anim>
			<p class="font-sans font-semibold text-[13px] leading-[1.4] tracking-[0.5px] text-deon-heading
			          pl-3 border-l-2 border-deon-accent">
				<?php esc_html_e( 'Categories', 'deon-energy' ); ?>
			</p>
			<nav class="mt-5 flex flex-col gap-1">
				<?php foreach ( $deon_faq_groups as $i => $g ) : ?>
				<a href="#faq-cat-<?php echo esc_attr( $g['slug'] ); ?>"
				   data-faq-cat-link
				   class="px-3 py-2.5 font-sans font-medium text-[14px] leading-[1.4]
				          text-deon-body hover:text-deon-heading hover:bg-white transition
				          <?php echo 0 === $i ? 'bg-white text-deon-heading font-semibold' : ''; ?>">
					<?php echo esc_html( $g['name'] ); ?>
				</a>
				<?php endforeach; ?>
			</nav>
		</aside>

		<!-- Accordion groups -->
		<div class="flex flex-col gap-12" data-faq-list>
			<?php foreach ( $deon_faq_groups as $g ) : ?>
			<div id="faq-cat-<?php echo esc_attr( $g['slug'] ); ?>" data-faq-group class="scroll-mt-24" data-anim>
				<h2 class="flex items-center gap-2.5 font-sans font-semibold text-h3 leading-snug text-deon-heading">
					<span class="w-5 h-5 text-deon-accent shrink-0"><?php echo deon_faq_icon_svg( $g['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php echo esc_html( $g['name'] ); ?>
				</h2>

				<div class="mt-5 flex flex-col gap-3">
					<?php foreach ( $g['items'] as $item ) : ?>
					<div data-faq-item
					     data-faq-text="<?php echo esc_attr( wp_strip_all_tags( $item['question'] . ' ' . $item['answer'] ) ); ?>"
					     class="bg-white overflow-hidden shadow-[0_2px_10px_rgba(0,0,0,0.06)]">
						<button type="button" data-faq-toggle aria-expanded="false"
						        class="group w-full flex items-center justify-between gap-4 text-left cursor-pointer appearance-none border-0 bg-white
						               px-6 py-[18px] transition-colors hover:bg-deon-bg">
							<?php /* Explicit size: Tailwind preflight is off, so a <button> keeps the
							         UA's 13.33px default and the question rendered SMALLER than its own
							         18px answer (client note 2026-08). Bold + 17/18px puts it above the
							         answer body and below the 22px group heading. */ ?>
							<span class="font-sans font-bold text-[17px] md:text-[18px] leading-snug text-deon-heading"><?php echo esc_html( $item['question'] ); ?></span>
							<svg class="w-5 h-5 shrink-0 text-deon-body transition-all duration-200 group-aria-expanded:rotate-180 group-aria-expanded:text-deon-accent"
							     viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M4 6l4 4 4-4"/>
							</svg>
						</button>
						<div data-faq-panel hidden class="px-6 pb-6">
							<div class="border-t border-deon-divider pt-4 font-sans font-normal leading-relaxed text-deon-body deon-prose">
								<?php echo wp_kses_post( $item['answer'] ); ?>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>

			<p data-faq-empty hidden class="font-sans text-deon-body">
				<?php esc_html_e( 'No answers match your search.', 'deon-energy' ); ?>
			</p>
		</div>

	</div>
</section>
