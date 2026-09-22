<?php
/**
 * Single article — Knowledge Hub post.
 *
 * Warm centered hero (category eyebrow + display-face title + meta), featured
 * image, then the post body scoped in .deon-prose (see main.css), tags and a
 * back link. No dedicated Figma frame — built to the site design language.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$deon_hub = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/knowledge-hub/' );
?>

<?php while ( have_posts() ) : the_post();
	$deon_cats = get_the_category();
	$deon_cat  = ( $deon_cats && ! is_wp_error( $deon_cats ) ) ? $deon_cats[0] : null;
	$deon_img  = get_the_post_thumbnail_url( get_the_ID(), 'full' );

	// Reading time (~200 wpm) — the reference "READ TIME" meta.
	$deon_words   = str_word_count( wp_strip_all_tags( get_the_content() ) );
	$deon_minutes = max( 1, (int) ceil( $deon_words / 200 ) );
	/* translators: %d: estimated reading time in minutes. */
	$deon_read = sprintf( _n( '%d min read', '%d min read', $deon_minutes, 'deon-energy' ), $deon_minutes );

	$deon_permalink = get_permalink();
	$deon_share_ttl = wp_strip_all_tags( get_the_title() );
?>

<article>
	<?php
	/* Shared inner-page hero (redesign wave 1). The featured image now sits in
	   the hero panel instead of a separate full-width band below it. */
	get_template_part( 'template-parts/global/page-hero', null, array(
		'eyebrow'   => $deon_cat ? $deon_cat->name : __( 'Knowledge Hub', 'deon-energy' ),
		'title'     => wp_strip_all_tags( get_the_title() ),
		'image'     => $deon_img ? $deon_img : get_template_directory_uri() . '/assets/img/blog-1.png',
		'image_alt' => $deon_img
			? wp_strip_all_tags( get_the_title() )
			: __( 'Rows of photovoltaic modules under a clear midday sun', 'deon-energy' ),
		'bg'        => 'offwhite',
		'meta'      => array( get_the_author(), get_the_date(), $deon_read ),
		// No hero back link — the breadcrumb below the title already returns to
		// the Knowledge Hub, and the article ends with a "Back to Knowledge Hub"
		// button (client request, 2026-08-07).
	) );
	?>

	<div class="max-w-[1440px] mx-auto px-4 md:px-[80px]">

		<div class="pt-[40px] md:pt-[56px]"></div>

		<div class="max-w-[720px] mx-auto flex items-center gap-[12px] pb-[24px] border-b border-deon-divider">
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-body"><?php esc_html_e( 'Share', 'deon-energy' ); ?></span>
			<div class="flex items-center gap-[8px]">
				<a href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $deon_permalink ) ); ?>"
				   target="_blank" rel="noopener noreferrer"
				   class="flex items-center justify-center w-[34px] h-[34px] border border-deon-border text-deon-body hover:border-deon-accent hover:text-deon-accent transition-colors"
				   aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'deon-energy' ); ?>">
					<svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5A2.5 2.5 0 1 1 5 8.5a2.5 2.5 0 0 1-.02-5zM3 9h4v12H3V9zm6 0h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.5c0-1.3-.02-3-1.83-3-1.83 0-2.11 1.43-2.11 2.9V21H9V9z"/></svg>
				</a>
				<a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . rawurlencode( $deon_permalink ) . '&text=' . rawurlencode( $deon_share_ttl ) ); ?>"
				   target="_blank" rel="noopener noreferrer"
				   class="flex items-center justify-center w-[34px] h-[34px] border border-deon-border text-deon-body hover:border-deon-accent hover:text-deon-accent transition-colors"
				   aria-label="<?php esc_attr_e( 'Share on X', 'deon-energy' ); ?>">
					<svg class="w-[14px] h-[14px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 2h3.3l-7.2 8.2L23.5 22h-6.6l-5.2-6.8L5.7 22H2.4l7.7-8.8L1.5 2h6.8l4.7 6.2L18.9 2zm-1.2 18h1.8L7.2 3.8H5.3L17.7 20z"/></svg>
				</a>
				<a href="<?php echo esc_url( 'mailto:?subject=' . rawurlencode( $deon_share_ttl ) . '&body=' . rawurlencode( $deon_permalink ) ); ?>"
				   class="flex items-center justify-center w-[34px] h-[34px] border border-deon-border text-deon-body hover:border-deon-accent hover:text-deon-accent transition-colors"
				   aria-label="<?php esc_attr_e( 'Share via email', 'deon-energy' ); ?>">
					<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1"/><path d="M3 7l9 6 9-6"/></svg>
				</a>
			</div>
		</div>

		<div class="pt-[32px]"></div>

		<div class="deon-prose max-w-[720px] mx-auto pb-[40px]">
			<?php the_content(); ?>
		</div>

		<div class="max-w-[720px] mx-auto pb-[80px] md:pb-(--section-pad) flex flex-col gap-[32px]">
			<?php
			$deon_tags = get_the_tags();
			if ( $deon_tags && ! is_wp_error( $deon_tags ) ) : ?>
			<div class="flex flex-wrap gap-[8px] pt-[32px] border-t border-deon-divider">
				<?php foreach ( $deon_tags as $deon_tag ) : ?>
				<a href="<?php echo esc_url( get_tag_link( $deon_tag->term_id ) ); ?>"
				   class="font-sans font-normal text-[12px] leading-[12px] tracking-[0.5px] uppercase text-deon-body border border-deon-border px-[12px] py-[8px] hover:border-deon-accent hover:text-deon-accent transition-colors">
					<?php echo esc_html( $deon_tag->name ); ?>
				</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<a href="<?php echo esc_url( $deon_hub ); ?>" class="flex items-center gap-[8px] text-deon-accent hover:opacity-80 group w-fit">
				<svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3.825 9L9.425 14.6L8 16L0 8L8 0L9.425 1.4L3.825 7H16V9H3.825Z" fill="currentColor"/></svg>
				<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase"><?php esc_html_e( 'Back to Knowledge Hub', 'deon-energy' ); ?></span>
			</a>
		</div>

	</div>

	<?php
	/* Related Insights — reference "Explore More / Related Insights" 3-card grid.
	   Same category first, then latest posts as fallback; current post excluded. */
	$deon_related = new WP_Query( array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'post__not_in'        => array( get_the_ID() ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'category__in'        => ( $deon_cat ? array( $deon_cat->term_id ) : array() ),
	) );
	if ( ! $deon_related->have_posts() ) {
		$deon_related = new WP_Query( array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		) );
	}
	if ( $deon_related->have_posts() ) : ?>
	<section class="bg-deon-bg border-t border-deon-divider mt-[40px] md:mt-[56px]">
		<div class="max-w-[1440px] mx-auto px-4 md:px-[80px] py-(--section-pad)">
			<div class="flex items-end justify-between gap-[24px] mb-[32px]">
				<div class="flex flex-col gap-[8px]">
					<span class="font-sans font-bold text-eyebrow tracking-[1.2px] uppercase text-deon-accent"><?php esc_html_e( 'Explore More', 'deon-energy' ); ?></span>
					<h2 class="font-display font-bold text-h2 text-deon-heading"><?php esc_html_e( 'Related Insights', 'deon-energy' ); ?></h2>
				</div>
				<a href="<?php echo esc_url( $deon_hub ); ?>" class="hidden sm:inline-block font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-heading border-b border-deon-heading pb-[4px] hover:text-deon-accent hover:border-deon-accent transition-colors whitespace-nowrap">
					<?php esc_html_e( 'View All Articles', 'deon-energy' ); ?>
				</a>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
				<?php while ( $deon_related->have_posts() ) : $deon_related->the_post(); ?>
					<?php get_template_part( 'template-parts/blog/card-image' ); ?>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php endif; wp_reset_postdata(); ?>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
