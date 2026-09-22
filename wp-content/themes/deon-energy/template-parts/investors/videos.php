<?php
/**
 * Investor Relations — Video Library.
 *
 * Client-managed gallery (deon_investor_video CPT). Each card shows a poster +
 * play button; clicking opens the shared lightbox ([data-video-trigger] in
 * main.js) with a YouTube/Vimeo iframe or an inline <video> for MP4s.
 *
 * Renders nothing until at least one video is published (graceful hide).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_videos = deon_get_investor_videos();
if ( ! $deon_videos ) {
	return; // No videos — hide the whole section.
}
?>

<section class="w-full bg-deon-bg border-b border-deon-border">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-16 md:py-(--section-pad)">

		<div class="flex flex-col gap-3 mb-10 md:max-w-[640px]" data-anim>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-accent">
				<?php esc_html_e( 'Video Library', 'deon-energy' ); ?>
			</span>
			<h2 class="font-sans font-normal text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
				<?php esc_html_e( 'Investor Presentations & Disclosures', 'deon-energy' ); ?>
			</h2>
		</div>

		<div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
		     data-video-pager data-page-size="6" data-page-size-mobile="2">
			<?php foreach ( $deon_videos as $deon_v ) : ?>
				<figure class="flex flex-col gap-3" data-anim data-video-item>
					<button type="button"
						class="group relative block w-full aspect-video overflow-hidden bg-deon-dark border border-deon-border cursor-pointer"
						data-video-trigger
						data-video-type="<?php echo esc_attr( $deon_v['type'] ); ?>"
						data-video-src="<?php echo esc_url( $deon_v['embed'] ); ?>"
						aria-label="<?php echo esc_attr( sprintf( __( 'Play video: %s', 'deon-energy' ), $deon_v['title'] ) ); ?>">
						<?php if ( $deon_v['poster'] ) : ?>
							<img src="<?php echo esc_url( $deon_v['poster'] ); ?>" alt=""
								class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
								loading="lazy" aria-hidden="true">
						<?php endif; ?>
						<span class="absolute inset-0 bg-black/10 transition-colors group-hover:bg-black/25" aria-hidden="true"></span>
						<span class="absolute left-1/2 top-1/2 flex h-16 w-16 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-[0_4px_20px_rgba(0,0,0,0.25)] transition-transform group-hover:scale-110" aria-hidden="true">
							<svg width="20" height="24" viewBox="0 0 20 24" fill="none" class="ml-1">
								<path d="M0 2.3v19.4a1 1 0 0 0 1.53.85l16-9.7a1 1 0 0 0 0-1.7l-16-9.7A1 1 0 0 0 0 2.3Z" fill="#e8930a"/>
							</svg>
						</span>
					</button>
					<figcaption class="flex flex-col gap-1">
						<span class="font-sans font-bold leading-snug text-deon-heading">
							<?php echo esc_html( $deon_v['title'] ); ?>
						</span>
						<?php if ( $deon_v['caption'] ) : ?>
							<span class="font-sans font-normal text-[12px] leading-[14px] tracking-[1.2px] uppercase text-deon-eyebrow">
								<?php echo esc_html( $deon_v['caption'] ); ?>
							</span>
						<?php endif; ?>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>

		<!-- Pagination — revealed by JS only when there is more than one page. -->
		<nav class="mt-10 hidden items-center justify-center gap-4" data-video-nav aria-label="<?php esc_attr_e( 'Video pages', 'deon-energy' ); ?>">
			<button type="button" data-video-prev
				class="inline-flex h-11 w-11 items-center justify-center border border-deon-border text-deon-heading transition-colors hover:bg-deon-heading/5 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
				aria-label="<?php esc_attr_e( 'Previous videos', 'deon-energy' ); ?>">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-eyebrow" data-video-status aria-live="polite"></span>
			<button type="button" data-video-next
				class="inline-flex h-11 w-11 items-center justify-center border border-deon-border text-deon-heading transition-colors hover:bg-deon-heading/5 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
				aria-label="<?php esc_attr_e( 'Next videos', 'deon-energy' ); ?>">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</nav>

	</div>
</section>
