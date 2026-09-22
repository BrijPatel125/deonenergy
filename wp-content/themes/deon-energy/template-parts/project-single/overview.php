<?php
/**
 * Project detail — overview. The post editor body, scoped in .deon-prose.
 * Hidden when the editor is empty.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( '' === trim( (string) get_the_content() ) ) {
	return;
}
?>

<section class="w-full bg-white">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] pt-(--section-pad) pb-2">
		<div class="flex items-center gap-4 mb-8 md:mb-10" data-anim>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-accent shrink-0">
				<?php esc_html_e( 'Overview', 'deon-energy' ); ?>
			</span>
			<div class="bg-deon-border h-px flex-1 min-w-0"></div>
		</div>

		<div class="deon-prose max-w-[820px]" data-anim>
			<?php the_content(); ?>
		</div>
	</div>
</section>
