<?php
/**
 * Case Study archive template.
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<style>
.deon-cs-arch{background:#fff;color:#1f2733;font-family:'Plus Jakarta Sans',sans-serif}
.deon-cs-arch__hero{padding:56px 24px 32px;background:#fafaf7;border-bottom:1px solid #e5e1d8}
.deon-cs-arch__hero-inner{max-width:1120px;margin:0 auto}
.deon-cs-arch__eyebrow{font-size:12px;font-weight:700;letter-spacing:0.14em;color:#F29022;text-transform:uppercase;margin:0 0 8px}
.deon-cs-arch__title{font-size:36px;font-weight:700;line-height:1.15;margin:0 0 12px}
.deon-cs-arch__intro{font-size:16px;color:#4d5563;max-width:720px;margin:0}
.deon-cs-arch__grid{max-width:1120px;margin:0 auto;padding:48px 24px;display:grid;grid-template-columns:1fr;gap:32px}
@media (min-width:768px){.deon-cs-arch__grid{grid-template-columns:1fr 1fr}}
@media (min-width:1080px){.deon-cs-arch__grid{grid-template-columns:1fr 1fr 1fr}}
.deon-cs-arch__card{background:#fff;border:1px solid #e5e1d8;border-radius:6px;overflow:hidden;transition:transform .18s,box-shadow .18s;display:flex;flex-direction:column}
.deon-cs-arch__card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,0.06)}
.deon-cs-arch__card-img{aspect-ratio:16/9;background:#fafaf7;overflow:hidden}
.deon-cs-arch__card-img img{width:100%;height:100%;object-fit:cover;display:block}
.deon-cs-arch__card-body{padding:20px 24px;display:flex;flex-direction:column;flex-grow:1}
.deon-cs-arch__card-label{font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#F29022;margin:0 0 8px}
.deon-cs-arch__card-title{font-size:18px;font-weight:700;color:#1f2733;margin:0 0 8px;line-height:1.3}
.deon-cs-arch__card-title a{color:inherit;text-decoration:none}
.deon-cs-arch__card-title a:hover{color:#F29022}
.deon-cs-arch__card-excerpt{font-size:13px;color:#4d5563;line-height:1.55;margin:0 0 16px;flex-grow:1}
.deon-cs-arch__card-meta{display:flex;gap:12px;flex-wrap:wrap;font-size:12px;color:#4d5563;padding-top:12px;border-top:1px solid #e5e1d8;margin-top:auto}
.deon-cs-arch__card-meta strong{color:#1f2733;font-weight:600}
.deon-cs-arch__empty{padding:64px 24px;text-align:center;color:#4d5563}
</style>

<div class="deon-cs-arch">
	<section class="deon-cs-arch__hero">
		<div class="deon-cs-arch__hero-inner">
			<p class="deon-cs-arch__eyebrow"><?php esc_html_e( 'Case studies', 'deon-energy' ); ?></p>
			<h1 class="deon-cs-arch__title"><?php esc_html_e( 'Real projects, real results', 'deon-energy' ); ?></h1>
			<p class="deon-cs-arch__intro">
				<?php esc_html_e( 'Detailed accounts of solar plants Deon Energy has designed, built, and continues to operate for industrial and commercial customers across Gujarat.', 'deon-energy' ); ?>
			</p>
		</div>
	</section>

	<?php if ( have_posts() ) : ?>
		<div class="deon-cs-arch__grid">
			<?php while ( have_posts() ) : the_post();
				$kwp    = get_post_meta( get_the_ID(), '_deon_cs_capacity_kwp', true );
				$loc    = get_post_meta( get_the_ID(), '_deon_cs_location', true );
				$sector = get_post_meta( get_the_ID(), '_deon_cs_client_sector', true );
			?>
				<article class="deon-cs-arch__card">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="deon-cs-arch__card-img">
							<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
						</div>
					<?php endif; ?>
					<div class="deon-cs-arch__card-body">
						<p class="deon-cs-arch__card-label"><?php echo esc_html( $sector ? $sector : 'Case study' ); ?></p>
						<h2 class="deon-cs-arch__card-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						<p class="deon-cs-arch__card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
						<div class="deon-cs-arch__card-meta">
							<?php if ( $kwp ) : ?><span><strong><?php echo esc_html( $kwp ); ?></strong> kWp</span><?php endif; ?>
							<?php if ( $loc ) : ?><span><?php echo esc_html( $loc ); ?></span><?php endif; ?>
						</div>
					</div>
				</article>
			<?php endwhile; ?>
		</div>

		<?php if ( function_exists( 'deon_pagination' ) ) : deon_pagination(); endif; ?>
	<?php else : ?>
		<div class="deon-cs-arch__empty">
			<p><?php esc_html_e( 'Case studies coming soon.', 'deon-energy' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<?php get_footer();
