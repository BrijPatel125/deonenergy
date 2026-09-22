<?php
/**
 * Leadership — shared 3D flip profile card (Board of Directors + Core Team).
 *
 * Front face = the primary photo (Featured Image). Back face = the second photo
 * (the "Second Photo" / _deon_leader_casual). A card WITHOUT a second photo is
 * rendered as a plain static portrait — no flip, no hover/tap interaction. When
 * a second photo IS set, the card flips (hover on fine pointers, tap on coarse
 * pointers — data-leader-flip → main.js). Name + role sit beneath.
 *
 * Expected $args: name, role, bio, linkedin, x, photo, casual, size ('lg'|'sm')
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_c = wp_parse_args(
	$args,
	array(
		'name'     => '',
		'role'     => '',
		'bio'      => '',
		'linkedin' => '',
		'x'        => '',
		'photo'    => '',
		'casual'   => '',
		'size'     => 'sm',
	)
);

if ( ! $deon_c['name'] ) {
	return;
}

// Flip effect removed (client request) — every card is now a plain static
// portrait. The second photo field ('casual') is kept in admin for a possible
// future treatment, but is intentionally not rendered here.
$deon_back     = $deon_c['casual'];
$deon_has_flip = false;
?>

<div class="deon-leader-card flex flex-col gap-4">

	<div class="relative">

		<?php if ( $deon_has_flip ) : ?>
		<div class="deon-flip aspect-[4/5] rounded-[6px]"
		     data-leader-flip
		     tabindex="0"
		     role="button"
		     aria-expanded="false"
		     aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person's name. */ __( 'Flip the photo of %s', 'deon-energy' ), $deon_c['name'] ) ); ?>">
			<div class="deon-flip__inner rounded-[6px]">

				<!-- Front: primary photo -->
				<div class="deon-flip__face deon-flip__front bg-deon-icon-bg border border-deon-border rounded-[6px]">
					<?php if ( $deon_c['photo'] ) : ?>
						<img src="<?php echo esc_url( $deon_c['photo'] ); ?>"
						     alt="<?php echo esc_attr( $deon_c['name'] ); ?>"
						     class="absolute inset-0 w-full h-full object-cover object-top"
						     loading="lazy" decoding="async">
					<?php endif; ?>
				</div>

				<!-- Back: the second photo -->
				<div class="deon-flip__face deon-flip__back bg-deon-icon-bg border border-deon-border rounded-[6px]">
					<img src="<?php echo esc_url( $deon_back ); ?>"
					     alt=""
					     class="absolute inset-0 w-full h-full object-cover object-top"
					     loading="lazy" decoding="async" aria-hidden="true">
				</div>

			</div>
		</div>
		<?php else : ?>
		<!-- No second photo: plain static portrait, no flip interaction. -->
		<div class="deon-flip-static relative aspect-[4/5] rounded-[6px] overflow-hidden bg-deon-icon-bg border border-deon-border">
			<?php if ( $deon_c['photo'] ) : ?>
				<img src="<?php echo esc_url( $deon_c['photo'] ); ?>"
				     alt="<?php echo esc_attr( $deon_c['name'] ); ?>"
				     class="absolute inset-0 w-full h-full object-cover object-top"
				     loading="lazy" decoding="async">
			<?php endif; ?>
		</div>
		<?php endif; ?>

	</div>

	<div class="flex flex-col gap-1">
		<h3 class="font-display font-semibold text-h3 leading-tight text-deon-heading">
			<?php echo esc_html( $deon_c['name'] ); ?>
		</h3>
		<?php if ( $deon_c['role'] ) : ?>
		<p class="font-sans font-medium text-eyebrow uppercase tracking-[0.1em] text-deon-accent">
			<?php echo esc_html( $deon_c['role'] ); ?>
		</p>
		<?php endif; ?>
	</div>

</div>