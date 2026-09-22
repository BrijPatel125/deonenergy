<?php
/**
 * Careers — Life at Deon. Left: heading, paragraph, photo card.
 * Right: 2x2 grid of culture value cards (real designed icons). Static.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_uri = get_template_directory_uri();

$deon_culture = array(
	array(
		'icon'  => 'careers-icon-innovation.svg',
		'w'     => 23,
		'h'     => 30,
		'title' => __( 'Practical Work', 'deon-energy' ),
		'desc'  => __( 'Real plants for real customers. Little sits between your work and the outcome on site.', 'deon-energy' ),
	),
	array(
		'icon'  => 'careers-icon-collaboration.svg',
		'w'     => 33,
		'h'     => 24,
		'title' => __( 'Cross-Functional Teams', 'deon-energy' ),
		'desc'  => __( 'Engineering, procurement, finance and O&M work side by side. Decisions get made with full context, not in silos.', 'deon-energy' ),
	),
	array(
		'icon'  => 'careers-icon-sustainability.svg',
		'w'     => 26,
		'h'     => 26,
		'title' => __( 'Environmental Impact', 'deon-energy' ),
		'desc'  => __( 'Every plant displaces grid electricity. The impact is measurable and compounds over time.', 'deon-energy' ),
	),
	array(
		'icon'  => 'careers-icon-integrity.svg',
		'w'     => 33,
		'h'     => 32,
		'title' => __( 'Straight Culture', 'deon-energy' ),
		'desc'  => __( 'We say what we mean. Feedback is direct, decisions are explained, and mistakes are for learning.', 'deon-energy' ),
	),
);
?>

<section class="w-full bg-white border-y border-deon-border">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-16 md:py-(--section-pad)">
		<div class="md:grid md:grid-cols-12 md:gap-x-6 md:items-center">

			<!-- Left column -->
			<div class="md:col-span-4 flex flex-col items-start gap-8" data-anim>
				<h2 class="font-sans font-extrabold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
					<?php esc_html_e( 'Life at Deon', 'deon-energy' ); ?>
				</h2>
				<p class="font-sans text-lead leading-[1.8] text-deon-body">
					<?php esc_html_e( 'You will build infrastructure that still generates power decades from now. People do well here when they take engineering seriously and want to see their work on real sites — not just on slides.', 'deon-energy' ); ?>
				</p>
				<div class="w-full border border-deon-border overflow-hidden">
					<img src="<?php echo esc_url( $deon_uri . '/assets/img/careers-life.jpg' ); ?>"
					     alt="<?php esc_attr_e( 'Team at work inside Deon Energy', 'deon-energy' ); ?>"
					     class="block w-full h-[300px] md:h-[355px] object-cover" loading="lazy">
				</div>
			</div>

			<!-- Right column: 2x2 culture cards -->
			<div class="md:col-span-7 md:col-start-6 grid grid-cols-1 sm:grid-cols-2 auto-rows-min gap-6 mt-8 md:mt-0" data-anim-stagger>
				<?php foreach ( $deon_culture as $card ) : ?>
					<div class="careers-culture-card bg-white border border-deon-border shadow-[0_2px_10px_rgba(0,0,0,0.06)] p-[33px] flex flex-col items-start gap-4">
						<span class="careers-culture-card__iconwrap">
						<img src="<?php echo esc_url( $deon_uri . '/assets/img/' . $card['icon'] ); ?>"
						     alt="" aria-hidden="true"
						     width="<?php echo esc_attr( $card['w'] ); ?>" height="<?php echo esc_attr( $card['h'] ); ?>"
						     style="width:<?php echo esc_attr( $card['w'] ); ?>px;height:<?php echo esc_attr( $card['h'] ); ?>px;">
						</span>
						<h3 class="pt-2 font-sans font-normal! text-h3 leading-snug text-deon-heading">
							<?php echo esc_html( $card['title'] ); ?>
						</h3>
						<p class="font-sans leading-relaxed text-deon-body">
							<?php echo esc_html( $card['desc'] ); ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>