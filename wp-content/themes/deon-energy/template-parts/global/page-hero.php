<?php
/**
 * Shared inner-page hero (redesign wave 1 — client change log v1, §"Update Hero
 * Sections" / §"Add Relevant Hero Images").
 *
 * ONE hero shape for every inner page: eyebrow → H1 → one-line lead → optional
 * badge / stats / CTA / search, with a relevant image panel on the right.
 * Styling lives in `assets/css/main.css` under the "Page hero" block (BEM, not
 * Tailwind) because the Phase 1 pages (About, Solutions) never load
 * tailwind.dist.css — a single CSS home is the only way the shape can actually
 * stay identical across both halves of the theme.
 *
 * Excluded by explicit client request: the Project Gallery hero
 * (template-parts/projects/hero.php) stays as-is.
 *
 * Usage:
 *   get_template_part( 'template-parts/global/page-hero', null, array(
 *       'eyebrow'   => __( 'Careers', 'deon-energy' ),
 *       'title'     => __( 'Join Deon Energy', 'deon-energy' ),
 *       'lead'      => __( 'One-line intro.', 'deon-energy' ),
 *       'image'     => $uri . '/assets/img/project-4.jpg',
 *       'image_alt' => __( 'Two engineers …', 'deon-energy' ),
 *       'bg'        => 'white',              // 'white' | 'offwhite' (default)
 *       'bg_image'  => $url,                 // full-bleed fallback background,
 *                                            // used only when the "Hero
 *                                            // Background" meta box is empty
 *       'badge'     => 'IPO Status: …',      // optional pill
 *       'stats'     => array( array( 'value' => '12', 'label' => 'Open Positions' ) ),
 *       'cta'       => array( 'label' => '…', 'url' => '…' ),
 *       'search'    => true,                 // FAQ live-filter input
 *       'back'      => array( 'label' => '…', 'url' => '…' ),
 *   ) );
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_a = is_array( $args ) ? $args : array();

$deon_title = isset( $deon_a['title'] ) ? (string) $deon_a['title'] : '';
if ( '' === $deon_title ) {
	return;
}

$deon_eyebrow = isset( $deon_a['eyebrow'] ) ? (string) $deon_a['eyebrow'] : '';
$deon_lead    = isset( $deon_a['lead'] ) ? (string) $deon_a['lead'] : '';
/*
 * Hero images disabled site-wide (client decision, change log v1 audit):
 * the approved page PDFs show plain text-only heroes, so every inner-page hero
 * renders as eyebrow → title → lead with no image panel. Adapters still pass
 * `image`/`image_alt`; they are ignored here. To re-enable real client images
 * later, delete the two override lines below.
 */
$deon_image   = '';
$deon_alt     = '';
// $deon_image = isset( $deon_a['image'] ) ? (string) $deon_a['image'] : '';
// $deon_alt   = isset( $deon_a['image_alt'] ) ? (string) $deon_a['image_alt'] : '';
$deon_badge   = isset( $deon_a['badge'] ) ? (string) $deon_a['badge'] : '';
$deon_stats   = ( isset( $deon_a['stats'] ) && is_array( $deon_a['stats'] ) ) ? $deon_a['stats'] : array();
$deon_cta     = ( isset( $deon_a['cta'] ) && is_array( $deon_a['cta'] ) ) ? $deon_a['cta'] : array();
$deon_back    = ( isset( $deon_a['back'] ) && is_array( $deon_a['back'] ) ) ? $deon_a['back'] : array();
$deon_search  = ! empty( $deon_a['search'] );
$deon_meta    = ( isset( $deon_a['meta'] ) && is_array( $deon_a['meta'] ) ) ? $deon_a['meta'] : array();

$deon_bg      = ( isset( $deon_a['bg'] ) && 'white' === $deon_a['bg'] ) ? 'white' : 'offwhite';
$deon_classes = 'deon-phero deon-phero--' . $deon_bg;
if ( '' === $deon_image ) {
	$deon_classes .= ' deon-phero--plain';
}

// Optional full-bleed background media (image/video), set per-page in wp-admin
// via the "Hero Background" meta box. Empty array => keep the plain colour hero.
$deon_hero_bg = function_exists( 'deon_get_hero_bg' ) ? deon_get_hero_bg() : array();
// Caller-supplied fallback (e.g. a project's featured image). The per-page
// meta box always wins; this only fills an otherwise plain colour hero.
if ( empty( $deon_hero_bg ) && ! empty( $deon_a['bg_image'] ) ) {
	$deon_hero_bg = array(
		'type'  => 'image',
		'image' => (string) $deon_a['bg_image'],
	);
}
if ( ! empty( $deon_hero_bg ) ) {
	$deon_classes .= ' deon-phero--media';
}
?>

<section class="<?php echo esc_attr( $deon_classes ); ?>">

	<?php if ( ! empty( $deon_hero_bg ) ) : ?>
	<div class="deon-phero__bg" aria-hidden="true">
		<?php if ( 'image' === $deon_hero_bg['type'] ) : ?>
			<img class="deon-phero__bg-img" src="<?php echo esc_url( $deon_hero_bg['image'] ); ?>" alt="" loading="eager" decoding="async">
		<?php elseif ( 'video' === $deon_hero_bg['type'] ) : ?>
			<?php if ( 'embed' === $deon_hero_bg['video_source'] ) : ?>
			<iframe class="deon-phero__video deon-phero__video--embed"
				src="<?php echo esc_url( deon_hero_embed_url( $deon_hero_bg['video_embed'] ) ); ?>"
				frameborder="0" allow="autoplay; encrypted-media" allowfullscreen loading="lazy"
				title="<?php esc_attr_e( 'Hero video', 'deon-energy' ); ?>"></iframe>
			<div class="deon-phero__poster"<?php echo $deon_hero_bg['poster'] ? ' style="background-image:url(' . esc_url( $deon_hero_bg['poster'] ) . ')"' : ''; ?>></div>
			<?php else : ?>
			<video class="deon-phero__video" autoplay muted loop playsinline<?php echo $deon_hero_bg['poster'] ? ' poster="' . esc_url( $deon_hero_bg['poster'] ) . '"' : ''; ?>>
				<source src="<?php echo esc_url( $deon_hero_bg['video_file'] ); ?>" type="video/mp4">
			</video>
			<?php endif; ?>
		<?php endif; ?>
		<div class="deon-phero__scrim"></div>
	</div>
	<?php endif; ?>

	<div class="deon-phero__inner">

		<div class="deon-phero__content" data-anim>

			<?php if ( ! empty( $deon_back['url'] ) && ! empty( $deon_back['label'] ) ) : ?>
			<a class="deon-phero__back" href="<?php echo esc_url( $deon_back['url'] ); ?>">
				<span aria-hidden="true">&larr;</span> <?php echo esc_html( $deon_back['label'] ); ?>
			</a>
			<?php endif; ?>

			<?php if ( '' !== $deon_eyebrow ) : ?>
			<p class="deon-phero__eyebrow"><?php echo esc_html( $deon_eyebrow ); ?></p>
			<?php endif; ?>

			<h1 class="deon-phero__title"><?php echo esc_html( $deon_title ); ?></h1>

			<?php
			/* Breadcrumb trail — rendered just below the title on every page
			   (client request 2026-08). deon_breadcrumb() self-guards the front
			   page, so this is a no-op there. */
			if ( function_exists( 'deon_breadcrumb' ) ) {
				deon_breadcrumb();
			}
			?>

			<?php
			/*
			 * Lead/description removed site-wide by client request (2026-07):
			 * inner-page heroes are eyebrow → centered title only. The `lead`
			 * arg is still accepted by callers but intentionally not rendered.
			 */
			?>

			<?php if ( $deon_meta ) : ?>
			<ul class="deon-phero__meta">
				<?php
				/*
				 * A meta item is either a plain string (square bullet, the
				 * original shape) or array( 'icon' => <key>, 'text' => … ),
				 * which swaps the bullet for the matching glyph — same icon set
				 * the Project Gallery cards use (template-parts/projects/grid.php).
				 */
				foreach ( $deon_meta as $deon_item ) :
					$deon_text = is_array( $deon_item ) ? (string) ( isset( $deon_item['text'] ) ? $deon_item['text'] : '' ) : (string) $deon_item;
					if ( '' === $deon_text ) {
						continue;
					}
					$deon_icon_key = is_array( $deon_item ) && ! empty( $deon_item['icon'] ) ? (string) $deon_item['icon'] : '';
					$deon_icon     = function_exists( 'deon_meta_icon' ) ? deon_meta_icon( $deon_icon_key ) : '';
					?>
					<li<?php echo $deon_icon ? ' class="deon-phero__meta--icon"' : ''; ?>>
						<?php if ( $deon_icon ) : ?>
						<span class="deon-phero__meta-icon" aria-hidden="true"><?php echo $deon_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hard-coded SVG from deon_meta_icon(). ?></span>
						<?php endif; ?>
						<?php echo esc_html( $deon_text ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<?php if ( '' !== $deon_badge ) : ?>
			<p class="deon-phero__badge">
				<span class="deon-phero__badge-dot" aria-hidden="true"></span>
				<?php echo esc_html( $deon_badge ); ?>
			</p>
			<?php endif; ?>

			<?php if ( $deon_stats ) : ?>
			<ul class="deon-phero__stats">
				<?php foreach ( $deon_stats as $deon_stat ) : ?>
					<?php if ( empty( $deon_stat['value'] ) ) { continue; } ?>
					<li class="deon-phero__stat">
						<span class="deon-phero__stat-value"><?php echo esc_html( $deon_stat['value'] ); ?></span>
						<?php if ( ! empty( $deon_stat['label'] ) ) : ?>
						<span class="deon-phero__stat-label"><?php echo esc_html( $deon_stat['label'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<?php if ( $deon_search ) : ?>
			<div class="deon-phero__search">
				<span class="deon-phero__search-icon" aria-hidden="true">
					<svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="8.5" cy="8.5" r="5.5"/><path d="M17 17l-4.5-4.5"/>
					</svg>
				</span>
				<label for="faq-search" class="deon-phero__sr"><?php esc_html_e( 'Search FAQs', 'deon-energy' ); ?></label>
				<input type="search" id="faq-search" data-faq-search
				       placeholder="<?php esc_attr_e( 'Search for answers about solar tech, ROI, maintenance…', 'deon-energy' ); ?>">
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $deon_cta['url'] ) && ! empty( $deon_cta['label'] ) ) : ?>
			<p class="deon-phero__actions">
				<a class="deon-phero__cta" href="<?php echo esc_url( $deon_cta['url'] ); ?>">
					<?php echo esc_html( $deon_cta['label'] ); ?>
				</a>
			</p>
			<?php endif; ?>

		</div>

		<?php if ( '' !== $deon_image ) : ?>
		<figure class="deon-phero__media" data-anim>
			<img src="<?php echo esc_url( $deon_image ); ?>"
			     alt="<?php echo esc_attr( $deon_alt ); ?>"
			     loading="eager" decoding="async" width="800" height="600">
		</figure>
		<?php endif; ?>

	</div>
</section>
