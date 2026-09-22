<?php
/**
 * Knowledge Hub — image blog card.
 *
 * Used inside the main WP loop (list.php calls the_post() before this).
 * White card, #e5e5e5 border, soft shadow. Featured image on top with the
 * category badge overlaid on it, then date, title, and excerpt below.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_uri  = get_template_directory_uri();
$deon_cats = get_the_category();
$deon_cat  = ( $deon_cats && ! is_wp_error( $deon_cats ) ) ? $deon_cats[0]->name : '';

$deon_img = has_post_thumbnail()
	? get_the_post_thumbnail_url( get_the_ID(), 'large' )
	: $deon_uri . '/assets/img/blog-placeholder.jpg';
?>
<article class="bg-white border border-deon-divider shadow-[0px_2px_10px_0px_rgba(0,0,0,0.06)] flex flex-col overflow-hidden h-full">
	<a href="<?php the_permalink(); ?>" class="relative block aspect-[16/10] overflow-hidden bg-deon-divider">
		<img
			src="<?php echo esc_url( $deon_img ); ?>"
			alt="<?php echo esc_attr( get_the_title() ); ?>"
			class="w-full h-full object-cover"
			loading="lazy"
		>
		<?php if ( $deon_cat ) : ?>
		<span class="absolute top-4 left-4 inline-block max-w-[calc(100%-32px)] truncate bg-white rounded-md px-4 py-2 font-sans font-semibold text-[11px] leading-[16px] tracking-wide whitespace-nowrap text-deon-accent shadow-sm">
			<?php echo esc_html( strtolower( $deon_cat ) ); ?>
		</span>
		<?php endif; ?>
	</a>

	<div class="flex flex-col justify-between gap-[12px] p-[24px] flex-1">
		<div class="flex flex-col gap-[8px]">
			<span class="font-sans font-normal text-[13px] leading-[13px] text-deon-body/60">
				<?php echo esc_html( get_the_date( 'Y-m-d H:i:s' ) ); ?>
			</span>
			<h3 class="font-sans font-bold text-h3 leading-snug text-deon-heading line-clamp-2">
				<a href="<?php the_permalink(); ?>" class="hover:text-deon-accent transition-colors"><?php the_title(); ?></a>
			</h3>
			<p class="font-sans font-normal leading-relaxed text-deon-body line-clamp-2">
				<?php echo esc_html( wp_trim_words( get_the_excerpt(), 16, '…' ) ); ?>
			</p>
		</div>
		<a href="<?php the_permalink(); ?>" class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-accent hover:opacity-80">
			<?php esc_html_e( 'Read More', 'deon-energy' ); ?>
		</a>
	</div>
</article>