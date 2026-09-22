<?php
/**
 * Single Case Study template.
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) :
	the_post();
	$post_id  = get_the_ID();

	$client   = get_post_meta( $post_id, '_deon_cs_client_name', true );
	$sector   = get_post_meta( $post_id, '_deon_cs_client_sector', true );
	$location = get_post_meta( $post_id, '_deon_cs_location', true );
	$kwp      = get_post_meta( $post_id, '_deon_cs_capacity_kwp', true );
	$type     = get_post_meta( $post_id, '_deon_cs_system_type', true );
	$commissioned = get_post_meta( $post_id, '_deon_cs_commissioned_date', true );
	$generation   = get_post_meta( $post_id, '_deon_cs_annual_generation', true );
	$payback  = get_post_meta( $post_id, '_deon_cs_payback_years', true );
	$co2      = get_post_meta( $post_id, '_deon_cs_co2_avoided', true );
	$problem  = get_post_meta( $post_id, '_deon_cs_problem', true );
	$solution = get_post_meta( $post_id, '_deon_cs_solution', true );
	$results  = get_post_meta( $post_id, '_deon_cs_results', true );
	$quote    = get_post_meta( $post_id, '_deon_cs_quote', true );
	$q_author = get_post_meta( $post_id, '_deon_cs_quote_author', true );
	$q_role   = get_post_meta( $post_id, '_deon_cs_quote_role', true );
	?>

	<style>
	.deon-cs{font-family:'Plus Jakarta Sans',sans-serif;color:#1f2733;line-height:1.6}
	.deon-cs__hero{background:#fafaf7;padding:64px 24px 48px}
	.deon-cs__hero-inner{max-width:1120px;margin:0 auto}
	.deon-cs__eyebrow{font-size:12px;font-weight:700;letter-spacing:0.14em;color:#F29022;text-transform:uppercase;margin:0 0 12px}
	.deon-cs__title{font-size:36px;font-weight:700;line-height:1.15;margin:0 0 12px;max-width:820px}
	.deon-cs__meta{display:flex;flex-wrap:wrap;gap:24px;font-size:14px;color:#4d5563;margin:16px 0 0}
	.deon-cs__meta span strong{color:#1f2733;font-weight:600}
	.deon-cs__hero-img{margin-top:32px;border-radius:8px;overflow:hidden;max-height:480px}
	.deon-cs__hero-img img{width:100%;height:auto;display:block}
	.deon-cs__kpis{background:#fff;padding:48px 24px;border-bottom:1px solid #e5e1d8}
	.deon-cs__kpis-inner{max-width:1120px;margin:0 auto;display:grid;grid-template-columns:1fr;gap:24px}
	@media (min-width:768px){.deon-cs__kpis-inner{grid-template-columns:repeat(4,1fr)}}
	.deon-cs__kpi{border-left:3px solid #F29022;padding:8px 0 8px 20px}
	.deon-cs__kpi-value{font-size:32px;font-weight:700;line-height:1;color:#1f2733;margin:0}
	.deon-cs__kpi-label{font-size:12px;color:#4d5563;margin:8px 0 0;text-transform:uppercase;letter-spacing:0.06em}
	.deon-cs__body{padding:64px 24px;background:#fff}
	.deon-cs__body-inner{max-width:820px;margin:0 auto}
	.deon-cs__body-inner h2{font-size:24px;font-weight:700;margin:40px 0 12px;color:#1f2733}
	.deon-cs__body-inner h2:first-child{margin-top:0}
	.deon-cs__body-inner p{font-size:16px;line-height:1.75;margin:0 0 16px;color:#1f2733}
	.deon-cs__quote{background:#fafaf7;border-left:4px solid #F29022;padding:32px;margin:48px 0;border-radius:4px}
	.deon-cs__quote-text{font-size:18px;font-style:italic;line-height:1.6;margin:0 0 16px;color:#1f2733}
	.deon-cs__quote-author{font-size:14px;color:#4d5563;margin:0}
	.deon-cs__quote-author strong{color:#1f2733;font-weight:700}
	.deon-cs__cta{background:#fafaf7;padding:56px 24px;text-align:center;border-top:1px solid #e5e1d8}
	.deon-cs__cta-heading{font-size:22px;font-weight:700;margin:0 0 20px;color:#1f2733}
	.deon-cs__cta-btn{display:inline-block;background:#F29022;color:#fff;padding:14px 32px;border-radius:24px;text-decoration:none;font-weight:600;transition:background .15s}
	.deon-cs__cta-btn:hover{background:#d9800f;color:#fff}
	</style>

	<article class="deon-cs">
		<section class="deon-cs__hero">
			<div class="deon-cs__hero-inner">
				<p class="deon-cs__eyebrow"><?php esc_html_e( 'Case Study', 'deon-energy' ); ?></p>
				<h1 class="deon-cs__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p style="font-size:18px;color:#4d5563;max-width:720px;margin:0;"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="deon-cs__meta">
					<?php if ( $client ) : ?><span><strong><?php echo esc_html( $client ); ?></strong></span><?php endif; ?>
					<?php if ( $sector ) : ?><span><?php echo esc_html( $sector ); ?></span><?php endif; ?>
					<?php if ( $location ) : ?><span><?php echo esc_html( $location ); ?></span><?php endif; ?>
					<?php if ( $commissioned ) : ?><span><?php esc_html_e( 'Commissioned', 'deon-energy' ); ?> <?php echo esc_html( $commissioned ); ?></span><?php endif; ?>
				</div>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="deon-cs__hero-img">
						<?php the_post_thumbnail( 'full', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<?php if ( $kwp || $generation || $payback || $co2 ) : ?>
		<section class="deon-cs__kpis">
			<div class="deon-cs__kpis-inner">
				<?php if ( $kwp ) : ?>
					<div class="deon-cs__kpi">
						<p class="deon-cs__kpi-value"><?php echo esc_html( $kwp ); ?> kWp</p>
						<p class="deon-cs__kpi-label"><?php echo esc_html( $type ? $type . ' capacity' : 'Installed capacity' ); ?></p>
					</div>
				<?php endif; ?>
				<?php if ( $generation ) : ?>
					<div class="deon-cs__kpi">
						<p class="deon-cs__kpi-value"><?php echo esc_html( $generation ); ?> MWh</p>
						<p class="deon-cs__kpi-label"><?php esc_html_e( 'Annual generation', 'deon-energy' ); ?></p>
					</div>
				<?php endif; ?>
				<?php if ( $payback ) : ?>
					<div class="deon-cs__kpi">
						<p class="deon-cs__kpi-value"><?php echo esc_html( $payback ); ?> yrs</p>
						<p class="deon-cs__kpi-label"><?php esc_html_e( 'Estimated payback', 'deon-energy' ); ?></p>
					</div>
				<?php endif; ?>
				<?php if ( $co2 ) : ?>
					<div class="deon-cs__kpi">
						<p class="deon-cs__kpi-value"><?php echo esc_html( $co2 ); ?> t</p>
						<p class="deon-cs__kpi-label"><?php esc_html_e( 'CO₂ avoided / year', 'deon-energy' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php endif; ?>

		<section class="deon-cs__body">
			<div class="deon-cs__body-inner">
				<?php if ( $problem ) : ?>
					<h2><?php esc_html_e( 'The challenge', 'deon-energy' ); ?></h2>
					<p><?php echo esc_html( $problem ); ?></p>
				<?php endif; ?>
				<?php if ( $solution ) : ?>
					<h2><?php esc_html_e( 'Our approach', 'deon-energy' ); ?></h2>
					<p><?php echo esc_html( $solution ); ?></p>
				<?php endif; ?>
				<?php if ( $results ) : ?>
					<h2><?php esc_html_e( 'The outcome', 'deon-energy' ); ?></h2>
					<p><?php echo esc_html( $results ); ?></p>
				<?php endif; ?>

				<?php the_content(); ?>

				<?php if ( $quote ) : ?>
					<blockquote class="deon-cs__quote">
						<p class="deon-cs__quote-text">&ldquo;<?php echo esc_html( $quote ); ?>&rdquo;</p>
						<?php if ( $q_author || $q_role ) : ?>
							<p class="deon-cs__quote-author">
								<?php if ( $q_author ) : ?><strong><?php echo esc_html( $q_author ); ?></strong><?php endif; ?>
								<?php if ( $q_role ) : ?><br><?php echo esc_html( $q_role ); ?><?php endif; ?>
							</p>
						<?php endif; ?>
					</blockquote>
				<?php endif; ?>
			</div>
		</section>

		<section class="deon-cs__cta">
			<h2 class="deon-cs__cta-heading"><?php esc_html_e( 'Looking at solar for your site?', 'deon-energy' ); ?></h2>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="deon-cs__cta-btn">
				<?php esc_html_e( 'Request a site assessment', 'deon-energy' ); ?>
			</a>
		</section>
	</article>

	<?php
endwhile;

get_footer();
