<?php
/**
 * Knowledge Hub — single "In The Press" card.
 *
 * Must run inside a deon_press loop (expects the current post context).
 * Shared by the Knowledge Hub strip (template-parts/blog/press.php) and the
 * paginated Press Coverage archive (page-press-coverage.php).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_quote = get_post_meta( get_the_ID(), '_deon_press_quote', true );
$deon_url   = get_post_meta( get_the_ID(), '_deon_press_url', true );
?>
<article class="group relative bg-white border border-deon-divider p-[24px] flex flex-col gap-3
                transition-all duration-300 hover:border-deon-accent hover:-translate-y-1
                hover:shadow-[0_12px_32px_rgba(0,0,0,0.08)]">

	<span class="font-display text-[38px] leading-[0.7] text-deon-accent/30 select-none" aria-hidden="true">&ldquo;</span>

	<?php if ( $deon_quote ) : ?>
	<blockquote class="font-sans font-medium text-[19px] leading-[1.55] text-deon-heading">
		<?php echo esc_html( $deon_quote ); ?>
	</blockquote>
	<?php endif; ?>

	<div class="mt-auto pt-5 border-t border-deon-divider flex items-center justify-between gap-4">
		<?php if ( has_post_thumbnail() ) : ?>
		<span class="flex items-center h-[32px]">
			<?php
			// `!` on height: the unlayered `img { height: auto }` reset in main.css
			// otherwise beats the layered Tailwind height and the logo renders at
			// its full thumbnail size. Fixed 32px box + object-contain keeps every
			// press logo the same visual size regardless of the source image.
			the_post_thumbnail( 'thumbnail', array(
				'class' => 'h-[32px]! w-auto max-w-[130px] object-contain object-left grayscale opacity-70 transition duration-300 group-hover:grayscale-0 group-hover:opacity-100',
				'alt'   => get_the_title(),
			) );
			?>
		</span>
		<?php else : ?>
		<span class="font-sans font-bold text-[13px] leading-[16px] text-deon-heading"><?php the_title(); ?></span>
		<?php endif; ?>

		<?php if ( $deon_url ) : ?>
		<a href="<?php echo esc_url( $deon_url ); ?>" target="_blank" rel="noopener noreferrer"
		   aria-label="<?php echo esc_attr( sprintf( __( 'Read the article in %s', 'deon-energy' ), get_the_title() ) ); ?>"
		   class="shrink-0 inline-flex items-center gap-1.5 font-sans font-bold text-[10px] leading-[12px] tracking-[1px] uppercase text-deon-accent transition-colors hover:text-deon-heading">
			<?php esc_html_e( 'Read', 'deon-energy' ); ?>
			<svg class="w-[10px] h-[10px] transition-transform group-hover:translate-x-0.5" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M1 5h8M5 1l4 4-4 4"/></svg>
		</a>
		<?php endif; ?>
	</div>
</article>
