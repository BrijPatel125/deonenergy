<?php
/**
 * Homepage — Why Solar Power (4 centered icon cards with Figma icons).
 *
 * @package Deon_Energy
 */

$deon_solar = array(
	array(
		'title' => __( 'Lower Energy Cost', 'deon-energy' ),
		'text'  => __( 'Cuts your reliance on grid tariffs, which have kept rising across most Indian states.', 'deon-energy' ),
		'icon'  => 'icon-cost.svg',
	),
	array(
		'title' => __( 'Cleaner Operations', 'deon-energy' ),
		'text'  => __( 'Every unit generated on-site displaces one from the grid, cutting your operational emissions.', 'deon-energy' ),
		'icon'  => 'icon-emissions.svg',
	),
	array(
		'title' => __( 'Energy Predictability', 'deon-energy' ),
		'text'  => __( 'Locks in part of your energy cost for 20+ years, not just the next tariff cycle.', 'deon-energy' ),
		'icon'  => 'icon-autonomy.svg',
	),
	array(
		'title' => __( 'Asset You Own', 'deon-energy' ),
		'text'  => __( 'Under CAPEX, the plant is a depreciable asset on your books with measurable long-term output.', 'deon-energy' ),
		'icon'  => 'icon-appreciation.svg',
	),
);
?>
<section class="why-solar deon-section" aria-label="<?php esc_attr_e( 'Why solar power', 'deon-energy' ); ?>">
	<div class="why-solar__inner deon-container">
		<header class="section-head section-head--center">
			<p class="deon-eyebrow"><?php esc_html_e( 'Why Businesses Are Moving to Solar', 'deon-energy' ); ?></p>
			<h2 class="section-h2"><?php esc_html_e( 'The Case for Solar', 'deon-energy' ); ?></h2>
		</header>

		<div class="why-solar__grid">
			<?php foreach ( $deon_solar as $card ) : ?>
				<article class="solar-card">
					<span class="solar-card__icon">
						<img
							src="<?php echo esc_url( DEON_URI . '/assets/img/' . $card['icon'] ); ?>"
							alt=""
							aria-hidden="true"
						>
					</span>
					<h3 class="solar-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
					<p class="solar-card__text"><?php echo esc_html( $card['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>