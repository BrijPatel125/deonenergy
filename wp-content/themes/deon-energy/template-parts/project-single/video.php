<?php
/**
 * Project detail — story video. One cinematic video (YouTube / Vimeo / MP4)
 * from _deon_video_url. Poster + play button; the player loads on click
 * ( [data-project-video] handler in assets/js/main.js ). Hidden when unset.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_url = function_exists( 'deon_project_video_url' ) ? deon_project_video_url( get_the_ID() ) : '';
$deon_src = ( $deon_url && function_exists( 'deon_video_source' ) ) ? deon_video_source( $deon_url ) : array( 'type' => '', 'embed' => '', 'thumb' => '' );

if ( empty( $deon_src['type'] ) ) {
	return;
}

$deon_caption = get_post_meta( get_the_ID(), '_deon_video_caption', true );
$deon_poster  = get_post_meta( get_the_ID(), '_deon_video_poster', true );
if ( ! $deon_poster ) {
	$deon_poster = $deon_src['thumb'];
}
if ( ! $deon_poster ) {
	$deon_poster = get_the_post_thumbnail_url( get_the_ID(), 'large' );
}
?>

<section class="w-full bg-white">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">
		<div class="relative w-full aspect-video overflow-hidden bg-deon-dark group cursor-pointer"
		     data-project-video
		     data-video-type="<?php echo esc_attr( $deon_src['type'] ); ?>"
		     data-video-embed="<?php echo esc_url( $deon_src['embed'] ); ?>"
		     data-anim>

			<?php if ( $deon_poster ) : ?>
			<img src="<?php echo esc_url( $deon_poster ); ?>"
			     alt="<?php echo esc_attr( get_the_title() ); ?>"
			     class="absolute inset-0 w-full h-full object-cover"
			     data-video-poster loading="lazy">
			<?php endif; ?>

			<span class="absolute inset-0 bg-black/25 transition-colors group-hover:bg-black/35" aria-hidden="true" data-video-scrim></span>

			<button type="button"
			        class="absolute inset-0 flex items-center justify-center"
			        data-video-play
			        aria-label="<?php esc_attr_e( 'Play video', 'deon-energy' ); ?>">
				<span class="flex items-center justify-center w-[76px] h-[76px] rounded-full bg-white/15 border border-white/70 backdrop-blur-sm transition-transform group-hover:scale-110">
					<svg width="26" height="30" viewBox="0 0 26 30" fill="none" aria-hidden="true">
						<path d="M25 15L0.25 29.29V0.71L25 15Z" fill="#ffffff"/>
					</svg>
				</span>
			</button>

			<?php if ( $deon_caption ) : ?>
			<div class="absolute left-0 bottom-0 p-6 md:p-10 pointer-events-none" data-video-caption>
				<span class="block font-sans font-bold text-[11px] leading-[12px] tracking-[2.4px] uppercase text-white/70 mb-2">
					<?php esc_html_e( 'Cinematic Tour', 'deon-energy' ); ?>
				</span>
				<span class="block font-display font-bold text-[clamp(22px,3vw,34px)] leading-[1.15] text-white">
					<?php echo esc_html( $deon_caption ); ?>
				</span>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
