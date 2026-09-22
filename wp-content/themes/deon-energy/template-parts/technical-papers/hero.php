<?php
/**
 * Technical Papers — page hero.
 *
 * Thin adapter over the shared inner-page hero (template-parts/global/page-hero)
 * so this page inherits the wave-1 hero shape automatically, identical to the
 * News & Media, Careers, and Investor heroes. Falls back to a structurally
 * identical inline hero if the shared partial is not present.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_a = is_array( $args ) ? $args : array();

$deon_eyebrow = isset( $deon_a['eyebrow'] ) ? (string) $deon_a['eyebrow'] : __( 'Research & Whitepapers', 'deon-energy' );
$deon_title   = isset( $deon_a['title'] ) ? (string) $deon_a['title'] : __( 'Technical Papers', 'deon-energy' );
$deon_lead    = isset( $deon_a['lead'] ) ? (string) $deon_a['lead'] : __( 'Peer-grade whitepapers, engineering reports, and technical studies from our research and delivery teams — free to download.', 'deon-energy' );
$deon_image   = isset( $deon_a['image'] ) ? (string) $deon_a['image'] : get_template_directory_uri() . '/assets/img/blog-1.png';
$deon_alt     = isset( $deon_a['image_alt'] ) ? (string) $deon_a['image_alt'] : __( 'Utility-scale solar array under clear sky', 'deon-energy' );
$deon_bg      = isset( $deon_a['bg'] ) ? (string) $deon_a['bg'] : 'offwhite';

if ( locate_template( 'template-parts/global/page-hero.php' ) ) {
	get_template_part( 'template-parts/global/page-hero', null, array(
		'eyebrow'   => $deon_eyebrow,
		'title'     => $deon_title,
		'lead'      => $deon_lead,
		'image'     => $deon_image,
		'image_alt' => $deon_alt,
		'bg'        => $deon_bg,
	) );
	return;
}

/* Fallback — same structure, Tailwind utilities. */
$deon_bg_class = ( 'white' === $deon_bg ) ? 'bg-white' : 'bg-deon-bg';
?>
<section class="w-full <?php echo esc_attr( $deon_bg_class ); ?> border-b border-deon-divider">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)
	            grid grid-cols-1 md:grid-cols-2 gap-[32px] items-center md:min-h-[380px]">

		<div class="flex flex-col gap-[16px]" data-anim>
			<?php if ( '' !== $deon_eyebrow ) : ?>
			<p class="font-sans font-bold text-eyebrow tracking-[2px] uppercase text-deon-accent">
				<?php echo esc_html( $deon_eyebrow ); ?>
			</p>
			<?php endif; ?>

			<h1 class="font-display font-extrabold text-hero leading-[1.1] tracking-[-0.02em] text-deon-heading">
				<?php echo esc_html( $deon_title ); ?>
			</h1>

			<?php if ( '' !== $deon_lead ) : ?>
			<p class="font-sans font-normal text-lead leading-[1.6] text-deon-body max-w-[52ch]">
				<?php echo esc_html( $deon_lead ); ?>
			</p>
			<?php endif; ?>
		</div>

		<?php if ( '' !== $deon_image ) : ?>
		<figure class="m-0" data-anim>
			<img src="<?php echo esc_url( $deon_image ); ?>" alt="<?php echo esc_attr( $deon_alt ); ?>"
			     class="w-full h-[clamp(220px,32vw,400px)] object-cover"
			     loading="eager" decoding="async" width="800" height="600">
		</figure>
		<?php endif; ?>

	</div>
</section>
