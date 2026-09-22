<?php
/**
 * Technical Papers — the paper list.
 *
 * Driven entirely by the existing deon_document CPT filed under the
 * `technical-papers` document type (seeded by inc/news-media.php). Each paper's
 * abstract, cover image, category eyebrow and optional summary link come from the
 * "Technical Paper display" meta box; the download file + PDF size auto-derive
 * from the "Document Details" file via deon_document_file_meta().
 *
 * Layout mirrors the client design (Revised_Website_Change_Log_v1 → Technical
 * Papers): a left sidebar with an icon Category FILTER (active-state highlight)
 * plus a Newsletter box, and a featured-whitepaper row per paper (cover image +
 * category•date eyebrow + PDF-size badge + title + abstract + Download / Read
 * Summary). The category filter is client-side (main.js, [data-tp-filter]) — it
 * shows/hides paper rows in place, no reload, so the single page still lists
 * every paper. With no papers, the section degrades to a tasteful "coming soon"
 * state — same behaviour as the News & Media library and Knowledge Hub modules.
 * No fake papers are ever shown.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_papers  = function_exists( 'deon_tp_papers' ) ? deon_tp_papers( -1 ) : null;
$deon_has     = $deon_papers instanceof WP_Query && $deon_papers->have_posts();
$deon_cats    = function_exists( 'deon_tp_category_list' ) ? deon_tp_category_list() : array();
$deon_sidebar = $deon_has && count( $deon_cats ) > 1;

$deon_dl_svg  = '<svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M8 12L3 7L4.4 5.55L7 8.15V0H9V8.15L11.6 5.55L13 7L8 12ZM2 16C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V11H2V14H14V11H16V14C16 14.55 15.8042 15.0208 15.4125 15.4125C15.0208 15.8042 14.55 16 14 16H2Z" fill="currentColor"/></svg>';
// Generic tag/document glyph shown beside every category (categories are free
// text, so no per-category icon map — one consistent mark, per the design).
$deon_cat_svg = '<svg class="w-[14px] h-[14px] shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M2 4.5A1.5 1.5 0 0 1 3.5 3H8l6 6-4.5 4.5L2 6V4.5Z"/><circle cx="5.25" cy="6.25" r="1" fill="currentColor" stroke="none"/></svg>';
?>
<section id="technical-papers" class="w-full bg-white">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">

		<?php if ( ! $deon_has ) : ?>

			<div class="border border-deon-divider bg-deon-bg px-[28px] py-[52px] text-center" data-anim>
				<p class="font-display font-bold text-h3 leading-[1.3] text-deon-heading mb-2">
					<?php esc_html_e( 'Technical papers coming soon.', 'deon-energy' ); ?>
				</p>
				<p class="font-sans font-normal text-lead leading-[1.6] text-deon-body max-w-[54ch] mx-auto!">
					<?php esc_html_e( 'Our engineering and research teams are preparing a library of downloadable whitepapers and technical reports. Published papers will be listed here as they are released.', 'deon-energy' ); ?>
				</p>
			</div>

		<?php else : ?>

			<div class="grid grid-cols-1 <?php echo $deon_sidebar ? 'lg:grid-cols-12' : ''; ?> gap-[40px] lg:gap-[48px] items-start">

				<?php if ( $deon_sidebar ) : ?>
				<aside class="lg:col-span-3">
					<div class="lg:sticky lg:top-[100px] flex flex-col gap-[28px]" data-tp-filters>

						<nav aria-label="<?php esc_attr_e( 'Filter technical papers by category', 'deon-energy' ); ?>" data-anim>
							<p class="font-sans font-bold text-eyebrow tracking-[1.6px] uppercase text-deon-body">
								<?php esc_html_e( 'Categories', 'deon-energy' ); ?>
							</p>
							<hr class="mt-[12px] mb-[6px] border-0 border-t border-deon-divider">
							<ul class="m-0 p-0 list-none flex flex-col">
								<li>
									<button type="button" data-tp-filter="all" aria-current="true"
									        class="w-full flex items-center gap-[10px] text-left py-[9px] pl-[12px] border-l-2 border-deon-accent font-sans font-semibold leading-[1.4] text-deon-heading bg-deon-bg transition-colors">
										<?php echo $deon_cat_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?>
										<?php esc_html_e( 'All Papers', 'deon-energy' ); ?>
									</button>
								</li>
								<?php foreach ( $deon_cats as $deon_cat ) : ?>
								<li>
									<button type="button" data-tp-filter="<?php echo esc_attr( sanitize_title( $deon_cat ) ); ?>"
									        class="w-full flex items-center gap-[10px] text-left py-[9px] pl-[12px] border-l-2 border-transparent font-sans font-normal leading-[1.4] text-deon-body bg-transparent transition-colors hover:text-deon-accent">
										<?php echo $deon_cat_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?>
										<?php echo esc_html( $deon_cat ); ?>
									</button>
								</li>
								<?php endforeach; ?>
							</ul>
						</nav>

						<?php
						/*
						 * Newsletter box. Submits via admin-ajax to
						 * deon_handle_newsletter_subscribe() (functions.php); leads
						 * are stored as the deon_subscriber CPT. ESP delivery stays
						 * out of SOW scope.
						 */
						?>
						<div class="border border-deon-divider bg-deon-bg p-[24px]" data-anim>
							<p class="font-sans font-bold text-eyebrow tracking-[1.6px] uppercase text-deon-body mb-[10px]">
								<?php esc_html_e( 'Newsletter', 'deon-energy' ); ?>
							</p>
							<p class="font-sans font-normal leading-[1.6] text-deon-body mb-[14px]">
								<?php esc_html_e( 'Get technical updates delivered to your inbox.', 'deon-energy' ); ?>
							</p>
							<?php
							get_template_part( 'template-parts/global/newsletter-form', null, array(
								'source'      => 'technical-papers',
								'variant'     => 'sidebar',
								'input_id'    => 'deon-tp-newsletter-email',
								'placeholder' => __( 'Email Address', 'deon-energy' ),
								'label'       => __( 'Email address', 'deon-energy' ),
							) );
							?>
						</div>

					</div>
				</aside>
				<?php endif; ?>

				<div class="<?php echo $deon_sidebar ? 'lg:col-span-9' : ''; ?> flex flex-col" data-anim-stagger data-tp-list>
					<?php
					while ( $deon_papers->have_posts() ) :
						$deon_papers->the_post();

						$deon_id       = get_the_ID();
						$deon_cat      = trim( (string) get_post_meta( $deon_id, '_deon_tp_category', true ) );
						$deon_cslug    = '' !== $deon_cat ? sanitize_title( $deon_cat ) : '';
						$deon_abstract = (string) get_post_meta( $deon_id, '_deon_tp_abstract', true );
						$deon_image    = (string) get_post_meta( $deon_id, '_deon_tp_image', true );
						$deon_summary  = (string) get_post_meta( $deon_id, '_deon_tp_summary_url', true );
						$deon_file     = (string) get_post_meta( $deon_id, '_deon_document_file', true );
						$deon_meta     = function_exists( 'deon_document_file_meta' ) ? deon_document_file_meta( $deon_id ) : '';
						$deon_year     = (string) get_post_meta( $deon_id, '_deon_document_year', true );
						$deon_date     = '' !== $deon_year ? $deon_year : get_the_date( 'M Y' );

						$deon_eyebrow  = trim( strtoupper( $deon_cat ) . ( ( '' !== $deon_cat && '' !== $deon_date ) ? ' • ' : '' ) . strtoupper( $deon_date ) );
						?>
						<article data-tp-cat="<?php echo esc_attr( $deon_cslug ); ?>"
						         class="grid grid-cols-1 md:grid-cols-3 gap-[24px] md:gap-[32px] py-[36px] border-b border-deon-divider first:pt-0 scroll-mt-[100px]">

							<?php if ( '' !== $deon_image ) : ?>
							<figure class="m-0 md:col-span-1">
								<img src="<?php echo esc_url( $deon_image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"
								     class="w-full aspect-[4/3] object-cover"
								     loading="lazy" decoding="async" width="480" height="360">
							</figure>
							<?php endif; ?>

							<div class="<?php echo ( '' !== $deon_image ) ? 'md:col-span-2' : 'md:col-span-3'; ?> flex flex-col gap-[12px]">

								<div class="flex items-start justify-between gap-4">
									<?php if ( '' !== $deon_eyebrow ) : ?>
									<p class="font-sans font-bold text-eyebrow tracking-[1.4px] uppercase text-deon-accent">
										<?php echo esc_html( $deon_eyebrow ); ?>
									</p>
									<?php endif; ?>
									<?php if ( '' !== $deon_meta ) : ?>
									<span class="shrink-0 font-sans font-normal text-[12px] leading-[16px] text-deon-body whitespace-nowrap">
										<?php echo esc_html( $deon_meta ); ?>
									</span>
									<?php endif; ?>
								</div>

								<h2 class="font-display font-extrabold text-h2 leading-[1.2] tracking-[-0.01em] text-deon-heading">
									<?php the_title(); ?>
								</h2>

								<?php if ( '' !== $deon_abstract ) : ?>
								<p class="font-sans font-normal leading-[1.6] text-deon-body max-w-[62ch]">
									<?php echo esc_html( $deon_abstract ); ?>
								</p>
								<?php endif; ?>

								<?php if ( $deon_file || $deon_summary ) : ?>
								<div class="flex flex-wrap items-center gap-x-[28px] gap-y-[10px] mt-[4px]">
									<?php if ( $deon_file ) : ?>
									<a href="<?php echo esc_url( $deon_file ); ?>" download
									   class="inline-flex items-center gap-2 font-sans font-bold text-[12px] tracking-[1.2px] uppercase text-deon-heading transition-colors hover:text-deon-accent">
										<?php echo $deon_dl_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?>
										<?php esc_html_e( 'Download Whitepaper', 'deon-energy' ); ?>
									</a>
									<?php endif; ?>
									<?php if ( $deon_summary ) : ?>
									<a href="<?php echo esc_url( $deon_summary ); ?>" target="_blank" rel="noopener noreferrer"
									   class="inline-flex items-center gap-1.5 font-sans font-bold text-[12px] tracking-[1.2px] uppercase text-deon-body transition-colors hover:text-deon-accent">
										<?php esc_html_e( 'Read Summary', 'deon-energy' ); ?>
										<svg class="w-[10px] h-[10px]" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M1 5h8M5 1l4 4-4 4"/></svg>
									</a>
									<?php endif; ?>
								</div>
								<?php endif; ?>

							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>

					<?php if ( $deon_sidebar ) : ?>
					<p data-tp-empty hidden
					   class="hidden py-[48px] text-center font-sans font-normal text-lead leading-[1.6] text-deon-body">
						<?php esc_html_e( 'No papers in this category yet.', 'deon-energy' ); ?>
					</p>
					<?php endif; ?>
				</div>

			</div>

		<?php endif; ?>

	</div>
</section>
