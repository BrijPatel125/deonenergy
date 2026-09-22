<?php
/**
 * Shared newsletter subscribe form.
 *
 * ONE source of truth for the JS↔markup contract the `[data-newsletter]` IIFE
 * in assets/js/main.js depends on: the `data-newsletter` / `data-source`
 * attributes, the off-screen honeypot (`deon_hp`), the `[data-newsletter-submit]`
 * button, and the `[data-newsletter-msg]` aria-live status region. Two visual
 * variants are branched off a `variant` arg (like page-hero's `bg`/`badge`),
 * so the plumbing can never drift between placements.
 *
 * Submits via admin-ajax to deon_handle_newsletter_subscribe() (functions.php);
 * captured leads are stored as the deon_subscriber CPT.
 *
 * Usage:
 *   get_template_part( 'template-parts/global/newsletter-form', null, array(
 *       'source'      => 'contact',              // stored + emailed as the lead source
 *       'variant'     => 'contact',              // 'contact' (dark underline) | 'sidebar' (light box)
 *       'input_id'    => 'deon-newsletter-email',
 *       'placeholder' => __( 'Your corporate email', 'deon-energy' ),
 *       'label'       => __( 'Your corporate email', 'deon-energy' ),
 *   ) );
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_a       = is_array( $args ) ? $args : array();
$deon_source  = isset( $deon_a['source'] ) ? (string) $deon_a['source'] : 'newsletter';
$deon_variant = isset( $deon_a['variant'] ) && 'sidebar' === $deon_a['variant'] ? 'sidebar' : 'contact';
$deon_input   = isset( $deon_a['input_id'] ) ? (string) $deon_a['input_id'] : 'deon-newsletter-email';
$deon_ph      = isset( $deon_a['placeholder'] ) ? (string) $deon_a['placeholder'] : __( 'Email Address', 'deon-energy' );
$deon_label   = isset( $deon_a['label'] ) ? (string) $deon_a['label'] : __( 'Email address', 'deon-energy' );
?>
<form class="flex flex-col gap-[10px]" data-newsletter data-source="<?php echo esc_attr( $deon_source ); ?>" aria-label="<?php esc_attr_e( 'Newsletter sign-up', 'deon-energy' ); ?>">

	<?php if ( 'sidebar' === $deon_variant ) : ?>

		<label for="<?php echo esc_attr( $deon_input ); ?>" class="sr-only"><?php echo esc_html( $deon_label ); ?></label>
		<input type="email" id="<?php echo esc_attr( $deon_input ); ?>" name="email" required autocomplete="email"
		       class="w-full border border-deon-divider bg-white px-[14px] py-[11px] font-sans text-[14px] text-deon-heading placeholder:text-deon-body/60 focus:outline-none focus:border-deon-accent"
		       placeholder="<?php echo esc_attr( $deon_ph ); ?>">
		<button type="submit" data-newsletter-submit
		        class="w-full bg-deon-dark text-white font-sans font-bold text-[11px] tracking-[1.2px] uppercase py-[12px] transition-colors hover:bg-deon-accent disabled:opacity-40 disabled:cursor-default">
			<?php esc_html_e( 'Subscribe', 'deon-energy' ); ?>
		</button>

	<?php else : ?>

		<div class="flex items-stretch border-b border-white/30">
			<label for="<?php echo esc_attr( $deon_input ); ?>" class="sr-only"><?php echo esc_html( $deon_label ); ?></label>
			<input type="email" id="<?php echo esc_attr( $deon_input ); ?>" name="email" required autocomplete="email" placeholder="<?php echo esc_attr( $deon_ph ); ?>"
				class="flex-1 min-w-0 bg-transparent border-0 px-[12px] py-[18px] font-sans text-[16px] text-white placeholder:text-[#6b7280] focus:outline-none">
			<button type="submit" data-newsletter-submit
				class="inline-flex items-center gap-[8px] bg-transparent border-0 appearance-none cursor-pointer px-[24px] py-[16px] font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-accent hover:opacity-70 disabled:opacity-40 disabled:cursor-default">
				<?php esc_html_e( 'Subscribe', 'deon-energy' ); ?>
				<svg class="w-[20px] h-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="12" x2="20" y2="12"/><polyline points="13 5 20 12 13 19"/></svg>
			</button>
		</div>

	<?php endif; ?>

	<?php // Honeypot — off-screen; bots fill it, humans never see it. ?>
	<input type="text" name="deon_hp" tabindex="-1" autocomplete="off" class="absolute left-[-9999px] w-px h-px overflow-hidden" aria-hidden="true">
	<p data-newsletter-msg role="status" aria-live="polite" class="hidden font-sans text-[14px] <?php echo 'sidebar' === $deon_variant ? 'text-deon-body' : 'text-white/90'; ?>"></p>
</form>
