<?php
/**
 * Leadership — "Quiet Impact" values section (static).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_values = array(
	array(
		'icon'  => DEON_URI . '/assets/img/leadership-icon-authenticity.svg',
		'w'     => 22,
		'h'     => 22,
		'title' => __( 'Clarity', 'deon-energy' ),
		'desc'  => __( 'We make responsibilities, assumptions, and decisions understandable to customers, partners, employees, and shareholders.', 'deon-energy' ),
	),
	array(
		'icon'  => DEON_URI . '/assets/img/leadership-icon-precision.svg',
		'w'     => 20,
		'h'     => 19,
		'title' => __( 'Judgement', 'deon-energy' ),
		'desc'  => __( 'We evaluate technical and commercial decisions against safety, reliability, lifecycle performance, and long-term value.', 'deon-energy' ),
	),
);
?>

<section class="w-full bg-deon-warm border-t border-deon-divider">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-[clamp(60px,8vw,112px)]
	            grid grid-cols-1 gap-8 md:grid-cols-12 md:gap-12 md:items-start">

		<div class="md:col-span-4 flex flex-col gap-3" data-anim>
			<p class="font-sans font-semibold text-eyebrow uppercase tracking-[0.14em] text-deon-accent">
				<?php esc_html_e( 'Quiet Impact', 'deon-energy' ); ?>
			</p>
			<h2 class="font-display font-bold text-h2 leading-tight text-deon-heading">
				<?php esc_html_e( 'Substance over noise.', 'deon-energy' ); ?>
			</h2>
		</div>

		<div class="md:col-span-8 flex flex-col gap-8 md:gap-10" data-anim>
			<p class="font-sans text-lead leading-relaxed text-deon-body">
				<?php esc_html_e( 'Our leadership style is not built on slogans. It is built on decisions that hold up when you look at them again a year later. The two habits we return to most often are these.', 'deon-energy' ); ?>
			</p>

			<div class="grid grid-cols-1 gap-6 md:grid-cols-2" data-anim-stagger>
				<?php foreach ( $deon_values as $v ) : ?>
				<div class="bg-deon-bg border border-deon-divider rounded-[6px] p-6 flex flex-col gap-2">
					<img src="<?php echo esc_url( $v['icon'] ); ?>" alt="" aria-hidden="true"
					     width="<?php echo esc_attr( $v['w'] ); ?>" height="<?php echo esc_attr( $v['h'] ); ?>"
					     class="shrink-0 self-start mb-2" style="width:<?php echo esc_attr( $v['w'] ); ?>px;height:<?php echo esc_attr( $v['h'] ); ?>px;">
					<h3 class="font-display font-semibold text-h3 leading-tight text-deon-heading">
						<?php echo esc_html( $v['title'] ); ?>
					</h3>
					<p class="font-sans leading-relaxed text-deon-body">
						<?php echo esc_html( $v['desc'] ); ?>
					</p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>

	</div>
</section>
