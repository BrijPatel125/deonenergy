<?php
/**
 * About page — Section 05: Growth Timeline (serpentine journey).
 *
 * A snake-like vertical timeline (see client reference): milestones alternate
 * left/right along a thick weaving path. Each milestone is a node — a circular
 * photo, or a default pin icon when no image is set — with year + phase +
 * description on the outer side.
 *
 * The weaving path and node coordinates are computed in JS (main.js) so the
 * curve always threads through the visible nodes and re-flows on resize /
 * expand. Without JS or on narrow screens the section degrades to a plain
 * left-aligned vertical list (CSS base styles).
 *
 * When more than DEON_JOURNEY_INITIAL milestones exist the overflow is hidden
 * behind a "Show all" toggle so the section never dumps everything at once.
 *
 * Driven entirely by the deon_milestone CPT. No milestones published means no
 * section — seed via bin/seed-milestones.php (see CLAUDE.md).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_milestones = deon_get_milestones();

if ( empty( $deon_milestones ) ) {
	return;
}

$deon_initial   = 6; // Milestones shown before the "Show all" toggle.
$deon_total     = count( $deon_milestones );
$deon_collapsed = $deon_total > $deon_initial;

/*
 * Decorative icon circle that sits on the ribbon (matches the client blue
 * reference — each milestone pairs a line icon with its photo). The CPT has no
 * icon field, so a small energy-themed set is cycled by index. Single-path,
 * currentColor stroke.
 */
$deon_icons = array(
	// Sun / launch.
	'<svg viewBox="0 0 24 24" fill="none" focusable="false"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M19.1 4.9L17 7M7 17l-2.1 2.1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
	// Bolt / power.
	'<svg viewBox="0 0 24 24" fill="none" focusable="false"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
	// Solar panel.
	'<svg viewBox="0 0 24 24" fill="none" focusable="false"><path d="M4 15h16l-2-9H6l-2 9ZM12 6v9M4 15l-1 3h18l-1-3M9 6l-1 9M15 6l1 9" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M12 18v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
	// Leaf / sustainability.
	'<svg viewBox="0 0 24 24" fill="none" focusable="false"><path d="M4 20c0-8 6-14 16-14 0 10-6 14-14 14a6 6 0 0 1-2 0Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 18c3-4 6-6 10-8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
	// Globe / reach.
	'<svg viewBox="0 0 24 24" fill="none" focusable="false"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M3 12h18M12 3c3 3.2 3 14.8 0 18M12 3c-3 3.2-3 14.8 0 18" stroke="currentColor" stroke-width="1.6"/></svg>',
	// Award / certification.
	'<svg viewBox="0 0 24 24" fill="none" focusable="false"><circle cx="12" cy="9" r="5" stroke="currentColor" stroke-width="1.6"/><path d="m9 13-2 8 5-3 5 3-2-8" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
);
$deon_icon_count = count( $deon_icons );
?>
<section class="about-journey">
	<div class="deon-container about-journey__inner">

		<h2 class="about-journey__h2"><?php esc_html_e( 'Growth Timeline', 'deon-energy' ); ?></h2>

		<div class="about-journey__timeline" data-journey>

			<svg class="about-journey__svg" aria-hidden="true" focusable="false" preserveAspectRatio="none">
				<path class="about-journey__path about-journey__path--edge" d="" fill="none"></path>
				<path class="about-journey__path about-journey__path--casing" d="" fill="none"></path>
				<path class="about-journey__path about-journey__path--fill" d="" fill="none"></path>
			</svg>

			<ol class="about-journey__list">
				<?php foreach ( $deon_milestones as $deon_i => $deon_m ) :
					$deon_side   = ( 0 === $deon_i % 2 ) ? 'left' : 'right';
					$deon_future = empty( $deon_m['done'] );
					$deon_over   = $deon_collapsed && $deon_i >= $deon_initial;
					$deon_icon   = $deon_icons[ $deon_i % $deon_icon_count ];

					$deon_item_class  = 'about-journey__item about-journey__item--' . $deon_side;
					$deon_item_class .= $deon_future ? ' about-journey__item--future' : '';
				?>
				<li class="<?php echo esc_attr( $deon_item_class ); ?>"<?php echo $deon_over ? ' data-overflow hidden' : ''; ?>>

					<span class="about-journey__icon" aria-hidden="true">
						<?php if ( ! empty( $deon_m['icon'] ) ) : ?>
							<img src="<?php echo esc_url( $deon_m['icon'] ); ?>" alt="" loading="lazy" width="32" height="32">
						<?php else : ?>
							<?php echo $deon_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?>
						<?php endif; ?>
					</span>

					<span class="about-journey__link" aria-hidden="true"></span>

					<div class="about-journey__content">
						<span class="about-journey__photo<?php echo empty( $deon_m['image'] ) ? ' about-journey__photo--placeholder' : ''; ?>">
							<?php if ( ! empty( $deon_m['image'] ) ) : ?>
								<img src="<?php echo esc_url( $deon_m['image'] ); ?>" alt="" loading="lazy" width="200" height="200">
							<?php else : ?>
								<svg width="34" height="34" viewBox="0 0 24 24" fill="none" focusable="false" aria-hidden="true"><path d="M12 21s7-5.686 7-11a7 7 0 1 0-14 0c0 5.314 7 11 7 11Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg>
							<?php endif; ?>
						</span>

						<div class="about-journey__text">
							<?php if ( ! empty( $deon_m['year'] ) ) : ?>
								<p class="about-journey__year"><?php echo esc_html( $deon_m['year'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $deon_m['phase'] ) ) : ?>
								<p class="about-journey__phase"><?php echo esc_html( $deon_m['phase'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $deon_m['desc'] ) ) : ?>
								<p class="about-journey__desc"><?php echo esc_html( $deon_m['desc'] ); ?></p>
							<?php endif; ?>
						</div>
					</div>

				</li>
				<?php endforeach; ?>
			</ol>

		</div>

		<?php if ( $deon_collapsed ) : ?>
		<div class="about-journey__more">
			<button type="button" class="about-journey__toggle" data-journey-toggle aria-expanded="false"
				data-label-more="<?php echo esc_attr( sprintf( __( 'Show all %d milestones', 'deon-energy' ), $deon_total ) ); ?>"
				data-label-less="<?php esc_attr_e( 'Show fewer milestones', 'deon-energy' ); ?>">
				<?php echo esc_html( sprintf( __( 'Show all %d milestones', 'deon-energy' ), $deon_total ) ); ?>
			</button>
		</div>
		<?php endif; ?>

	</div>
</section>
