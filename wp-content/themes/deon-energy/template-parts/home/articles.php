<?php
/**
 * Homepage — Recent Articles (3 blog cards).
 *
 * Wired to the 3 latest published posts. Renders nothing until real posts
 * exist — no placeholder articles (PDF §7).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_hub   = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/knowledge-hub/' );
$deon_query = deon_recent_posts_query( 3 );
$deon_items = array();

if ( $deon_query->have_posts() ) {
	while ( $deon_query->have_posts() ) {
		$deon_query->the_post();
		$deon_cats = get_the_category();
		$deon_items[] = array(
			'date'     => get_the_date( 'M j, Y' ),
			'category' => ( $deon_cats && ! is_wp_error( $deon_cats ) ) ? $deon_cats[0]->name : '',
			'title'    => get_the_title(),
			'excerpt'  => wp_trim_words( get_the_excerpt(), 20, '…' ),
			'img_url'  => get_the_post_thumbnail_url( get_the_ID(), 'large' ),
			'url'      => get_permalink(),
		);
	}
	wp_reset_postdata();
} else {
	/*
	 * No posts, no section. The approved copy is explicit (PDF §7 Knowledge
	 * Hub): the example article excerpts are tone references and "do not
	 * publish placeholder posts" — so the homepage must not render invented
	 * article cards either. This block used to ship three designed
	 * placeholders that read as real Deon articles.
	 */
	return;
}

$deon_arrow = '<svg viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M1 5h8M5 1l4 4-4 4"/></svg>';
?>
<section class="articles deon-section" aria-label="<?php esc_attr_e( 'Recent articles', 'deon-energy' ); ?>">
	<div class="articles__inner deon-container">
		<header class="articles__head">
			<p class="deon-eyebrow"><?php esc_html_e( 'Our Blogs', 'deon-energy' ); ?></p>
			<h2 class="section-h2"><?php esc_html_e( 'Recent Articles', 'deon-energy' ); ?></h2>
		</header>

		<div class="articles__grid">
			<?php foreach ( $deon_items as $post ) : ?>
				<article class="post-card">
					<a class="post-card__media" href="<?php echo esc_url( $post['url'] ); ?>" aria-label="<?php echo esc_attr( $post['title'] ); ?>">
						<?php if ( $post['img_url'] ) : ?>
						<img
							src="<?php echo esc_url( $post['img_url'] ); ?>"
							alt="<?php echo esc_attr( $post['title'] ); ?>"
						>
						<?php endif; ?>
					</a>
					<div class="post-card__body">
						<div class="post-card__meta">
							<span class="post-card__date"><?php echo esc_html( $post['date'] ); ?></span>
							<?php if ( $post['category'] ) : ?>
							<span class="post-card__cat"><?php echo esc_html( $post['category'] ); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="post-card__title"><?php echo esc_html( $post['title'] ); ?></h3>
						<p class="post-card__excerpt"><?php echo esc_html( $post['excerpt'] ); ?></p>
						<a class="post-card__link" href="<?php echo esc_url( $post['url'] ); ?>">
							<?php esc_html_e( 'Read More', 'deon-energy' ); ?>
							<?php echo $deon_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
