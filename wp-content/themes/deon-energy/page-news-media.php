<?php
/**
 * Template Name: News & Media
 *
 * Clean, minimal newsroom hub — mirrors the client-approved design:
 *   1. Centered hero          — small eyebrow + big headline on cream ground
 *   2. Media Library          — left-aligned section head + press grid
 *   3. Brand Assets           — 3-column: intro/CTA + asset cards
 *   4. Media Inquiries band   — dark bottom band with press contact
 *
 * CSS lives in main.css under the `.news-*` block. Helper functions come
 * from inc/news-media.php (guarded with function_exists so the page can
 * never fatal-error on a fresh install).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

/* ---------- data fetch ---------- */
$deon_press_email = function_exists( 'deon_news_press_email' ) ? deon_news_press_email() : 'media@deonenergy.in';
$deon_brand_kit   = function_exists( 'deon_news_brand_kit_url' ) ? deon_news_brand_kit_url() : '';

$deon_press = null;
if ( post_type_exists( 'deon_press' ) ) {
	$deon_press = new WP_Query( array(
		'post_type'      => 'deon_press',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
}

$deon_brand_assets = function_exists( 'deon_news_documents' ) ? deon_news_documents( 'brand-assets', 4 ) : null;

// Hero background — same admin-managed meta box the other pages use.
// Set it in the page editor's "Hero Background" meta box in wp-admin.
$deon_news_hero_bg = function_exists( 'deon_get_hero_bg' ) ? deon_get_hero_bg( get_the_ID() ) : array();
$deon_has_hero_bg  = ! empty( $deon_news_hero_bg ) && ! empty( $deon_news_hero_bg['image'] );
?>

<main class="news-page">

	<!-- ============================================================
		 1. HERO — centred, minimal
		 ============================================================ -->
	<section class="news-hero<?php echo $deon_has_hero_bg ? ' news-hero--has-bg' : ''; ?>">
		<?php if ( $deon_has_hero_bg ) : ?>
			<img class="news-hero__bg" src="<?php echo esc_url( $deon_news_hero_bg['image'] ); ?>" alt="" loading="eager" decoding="async">
			<span class="news-hero__scrim" aria-hidden="true"></span>
		<?php endif; ?>
		<div class="news-hero__inner">
			<p class="news-eyebrow news-hero__eyebrow">Newsroom &amp; Assets</p>
			<h1 class="news-hero__title">News and Media</h1>
		</div>
	</section>

	<!-- ============================================================
		 2. MEDIA LIBRARY
		 ============================================================ -->
	<section class="news-section">
		<div class="news-section__inner">
			<header class="news-section__head">
				<h2 class="news-section__title">Media Library</h2>
				<p class="news-section__desc">Browse our latest visual productions and technical innovations.</p>
			</header>

			<?php if ( $deon_press && $deon_press->have_posts() ) : ?>
				<div class="news-press-grid">
					<?php while ( $deon_press->have_posts() ) : $deon_press->the_post();
						$publication = get_post_meta( get_the_ID(), '_deon_press_publication', true );
						$link        = get_post_meta( get_the_ID(), '_deon_press_url', true );
						if ( empty( $link ) ) { $link = get_permalink(); }
					?>
						<a class="news-press-card" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener">
							<?php if ( ! empty( $publication ) ) : ?>
								<p class="news-press-card__pub"><?php echo esc_html( $publication ); ?></p>
							<?php endif; ?>
							<h3 class="news-press-card__title"><?php the_title(); ?></h3>
							<div class="news-press-card__foot">
								<time><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></time>
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<path d="M7 17L17 7M17 7H8M17 7v9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</div>
						</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- ============================================================
		 3. BRAND ASSETS
		 ============================================================ -->
	<section class="news-section news-section--muted">
		<div class="news-section__inner">
			<div class="news-brand">
				<!-- Intro column with title, description, and master CTA -->
				<div class="news-brand__intro">
					<h2 class="news-section__title">Brand Assets</h2>
					<p class="news-section__desc">
						Official logos, colour palettes, and brand guidelines for press and partner use.
						All assets are available in vector and raster formats.
					</p>
					<?php if ( ! empty( $deon_brand_kit ) ) : ?>
						<a class="news-btn news-btn--primary" href="<?php echo esc_url( $deon_brand_kit ); ?>" download>
							<?php esc_html_e( 'Download Brand Kit', 'deon-energy' ); ?>
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M12 3v13m0 0l-5-5m5 5l5-5M5 21h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
					<?php endif; ?>
				</div>

				<!-- Asset cards column -->
				<div class="news-brand__cards">
					<?php if ( $deon_brand_assets instanceof WP_Query && $deon_brand_assets->have_posts() ) : ?>
						<?php while ( $deon_brand_assets->have_posts() ) : $deon_brand_assets->the_post();
							$file_url  = get_post_meta( get_the_ID(), '_deon_document_file_url', true );
							$card_desc = get_post_meta( get_the_ID(), '_deon_news_card_description', true );
							if ( empty( $file_url ) ) { $file_url = get_permalink(); }
						?>
							<a class="news-asset-card" href="<?php echo esc_url( $file_url ); ?>" download>
								<div class="news-asset-card__icon">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
										<path d="M4 4h5v5H4V4zM4 15h5v5H4v-5zM15 4h5v5h-5V4zM15 15h5v5h-5v-5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
									</svg>
								</div>
								<h3 class="news-asset-card__title"><?php the_title(); ?></h3>
								<?php if ( ! empty( $card_desc ) ) : ?>
									<p class="news-asset-card__desc"><?php echo esc_html( wp_trim_words( $card_desc, 18 ) ); ?></p>
								<?php else : ?>
									<p class="news-asset-card__desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
								<?php endif; ?>
								<span class="news-asset-card__cta">
									<?php esc_html_e( 'View Assets', 'deon-energy' ); ?>
									<svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</span>
							</a>
						<?php endwhile; wp_reset_postdata(); ?>
					<?php else : ?>
						<!-- Two placeholder cards mirroring the reference layout -->
						<div class="news-asset-card news-asset-card--placeholder">
							<div class="news-asset-card__icon">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
									<path d="M9 12a3 3 0 116 0 3 3 0 01-6 0z" stroke="currentColor" stroke-width="2"/>
								</svg>
							</div>
							<h3 class="news-asset-card__title"><?php esc_html_e( 'Identity &amp; Logo', 'deon-energy' ); ?></h3>
							<p class="news-asset-card__desc"><?php esc_html_e( 'Master logo files in CMYK and RGB versions for all background types.', 'deon-energy' ); ?></p>
							<span class="news-asset-card__cta news-asset-card__cta--muted">
								<?php esc_html_e( 'Coming soon', 'deon-energy' ); ?>
							</span>
						</div>
						<div class="news-asset-card news-asset-card--placeholder">
							<div class="news-asset-card__icon">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<path d="M3 6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6z" stroke="currentColor" stroke-width="2"/>
									<path d="M3 15l5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
								</svg>
							</div>
							<h3 class="news-asset-card__title"><?php esc_html_e( 'Press Imagery', 'deon-energy' ); ?></h3>
							<p class="news-asset-card__desc"><?php esc_html_e( 'High-resolution professional photography of our installations and leadership.', 'deon-energy' ); ?></p>
							<span class="news-asset-card__cta news-asset-card__cta--muted">
								<?php esc_html_e( 'Coming soon', 'deon-energy' ); ?>
							</span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- ============================================================
		 4. OPTIONAL EDITOR CONTENT
		 ============================================================ -->
	<?php
	while ( have_posts() ) : the_post();
		$deon_body = trim( get_the_content() );
		if ( '' === $deon_body ) { continue; }
		?>
		<section class="news-section">
			<div class="news-section__inner">
				<div class="news-prose"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	endwhile;
	?>

	<!-- ============================================================
		 5. MEDIA INQUIRIES — dark bottom band
		 ============================================================ -->
	<section class="news-contact-band">
		<div class="news-contact-band__inner">
			<div class="news-contact-band__lead">
				<h3 class="news-contact-band__title"><?php esc_html_e( 'Media Inquiries', 'deon-energy' ); ?></h3>
				<p class="news-contact-band__desc">
					<?php esc_html_e( 'Journalists and industry analysts looking for interviews or additional information may reach our global press office.', 'deon-energy' ); ?>
				</p>
			</div>
			<div class="news-contact-band__meta">
				<div class="news-contact-band__col">
					<p class="news-contact-band__label"><?php esc_html_e( 'Global PR', 'deon-energy' ); ?></p>
					<a class="news-contact-band__value" href="mailto:<?php echo esc_attr( $deon_press_email ); ?>">
						<?php echo esc_html( $deon_press_email ); ?>
					</a>
				</div>
				<div class="news-contact-band__col">
					<p class="news-contact-band__label"><?php esc_html_e( 'Availability', 'deon-energy' ); ?></p>
					<p class="news-contact-band__value"><?php esc_html_e( '24/7 Response', 'deon-energy' ); ?></p>
				</div>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>