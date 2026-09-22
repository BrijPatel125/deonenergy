<?php
/**
 * Contact — Direct Channels section (PDF §12).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Direct channels, exactly as the approved copy lists them (PDF §12). The
 * previous trio (Solar Project / Investors / Partnership) was invented and
 * pointed at an "Investor Portal" that does not exist — a login portal is
 * explicitly out of scope (SOW §4).
 */
// Same source the footer uses: Customize → Social Links → WhatsApp number.
$deon_wa_number = preg_replace( '/\D+/', '', (string) get_theme_mod( 'deon_whatsapp_number', '18008905933' ) );
$deon_wa        = $deon_wa_number
	? 'https://api.whatsapp.com/send?phone=' . rawurlencode( $deon_wa_number ) . '&text=Hello'
	: '';

$deon_channels = array(
	array(
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="100%" height="100%"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/></svg>',
		'title' => __( 'General Enquiries', 'deon-energy' ),
		'text'  => __( 'Project enquiries, proposals and anything else about our work.', 'deon-energy' ),
		'link'  => 'info@deonenergy.in',
		'url'   => 'mailto:info@deonenergy.in',
	),
	array(
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="100%" height="100%"><path d="M4 4h13v16H4z"/><path d="M17 8h3v9a3 3 0 0 1-3 3"/><path d="M7 8h7M7 12h7M7 16h4"/></svg>',
		'title' => __( 'Media Enquiries', 'deon-energy' ),
		'text'  => __( 'Interviews, project information or additional context for journalists and analysts.', 'deon-energy' ),
		'link'  => 'media@deonenergy.in',
		'url'   => 'mailto:media@deonenergy.in',
	),
	array(
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="100%" height="100%"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/></svg>',
		'title' => __( 'Toll-Free', 'deon-energy' ),
		'text'  => __( 'Speak to the team during business hours.', 'deon-energy' ),
		'link'  => '1800 890 5933',
		'url'   => 'tel:18008905933',
	),
	array(
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="100%" height="100%"><path d="M21 11.5a8.4 8.4 0 0 1-12.5 7.3L3 20.5l1.8-5.3A8.5 8.5 0 1 1 21 11.5Z"/><path d="M8.5 9.5c0 3.3 2.7 6 6 6"/></svg>',
		'title' => __( 'WhatsApp', 'deon-energy' ),
		'text'  => __( 'Send us a message and we will pick it up from there.', 'deon-energy' ),
		'link'  => __( 'Message us on WhatsApp', 'deon-energy' ),
		'url'   => $deon_wa,
	),
);

?>

<section class="w-full bg-deon-warm border-t border-deon-border">
	<div class="max-w-[1280px] mx-auto px-4 py-[80px] flex flex-col gap-[48px]
	            md:px-16 md:py-(--section-pad) md:gap-12">

		<div class="flex flex-col items-center" data-anim>
			<h2 class="font-sans font-extrabold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading text-center pb-[16px]">
				<?php esc_html_e( 'Direct Channels', 'deon-energy' ); ?>
			</h2>
			<span class="block w-[96px] h-[4px] bg-deon-accent"></span>
		</div>

		<div class="grid grid-cols-1 gap-[24px] sm:grid-cols-2 lg:grid-cols-4" data-anim-stagger>
			<?php
			foreach ( $deon_channels as $deon_ch ) :
				if ( '' === $deon_ch['url'] ) {
					continue;
				}
				?>
				<div class="bg-white border border-deon-border p-[32px] flex flex-col items-center text-center">
					<div class="w-[64px] h-[64px] mb-[32px] bg-deon-bg border border-deon-border flex items-center justify-center">
						<span class="block w-[26px] h-[26px] text-deon-accent">
							<?php echo $deon_ch['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG. ?>
						</span>
					</div>
					<h4 class="font-sans font-normal! text-h3 leading-snug text-deon-heading mb-[16px]!">
						<?php echo esc_html( $deon_ch['title'] ); ?>
					</h4>
					<p class="font-sans leading-relaxed text-deon-body mb-[32px]! max-w-[276px]">
						<?php echo esc_html( $deon_ch['text'] ); ?>
					</p>
					<a href="<?php echo esc_url( $deon_ch['url'] ); ?>"<?php echo ( 0 === strpos( $deon_ch['url'], 'http' ) ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
						class="mt-auto inline-block border-b border-deon-heading pb-[1px] font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] text-deon-heading break-words hover:text-deon-accent hover:border-deon-accent">
						<?php echo esc_html( $deon_ch['link'] ); ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
