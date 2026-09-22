<?php
/**
 * Investor Documents — downloadable list, optionally filtered by ?type=slug.
 * Renders one section per document type (with files), each row = title + year +
 * download button. Reached from the Investor Resources cards.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_uri = get_template_directory_uri();

// Requested type (sanitised + validated against a real term).
$deon_req  = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '';
$deon_term = $deon_req ? get_term_by( 'slug', $deon_req, 'deon_document_type' ) : false;

// Which type slugs to render. One when filtered; otherwise every card type that
// has at least one document (registrar has none — it is contact info only).
if ( $deon_term ) {
	$deon_slugs = array( $deon_term->slug );
	$deon_title = $deon_term->name;
	$deon_intro = $deon_term->description;
} else {
	$deon_slugs = array();
	foreach ( deon_investor_resource_cards() as $deon_card ) {
		if ( 'registrar' !== $deon_card['slug'] && deon_document_type_count( $deon_card['slug'] ) > 0 ) {
			$deon_slugs[] = $deon_card['slug'];
		}
	}
	$deon_title = __( 'Investor Documents', 'deon-energy' );
	$deon_intro = __( 'Download regulatory filings, financial statements, and official investor communications.', 'deon-energy' );
}

$deon_dl_icon = $deon_uri . '/assets/img/investor-icon-download.svg';
?>

<?php
/* Shared inner-page hero (redesign wave 1). */
get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => __( 'Investor Relations', 'deon-energy' ),
	'title'     => $deon_title,
	'lead'      => $deon_intro ? wp_strip_all_tags( $deon_intro ) : '',
	'image'     => $deon_uri . '/assets/img/article-3.jpg',
	'image_alt' => __( 'Financial reporting dashboard showing portfolio performance', 'deon-energy' ),
	'bg'        => 'white',
	'back'      => array(
		'label' => __( 'Back to Investor Relations', 'deon-energy' ),
		'url'   => home_url( '/investor-relations/' ),
	),
) );
?>

<section class="w-full">
	<div class="max-w-[1280px] mx-auto px-4 pt-12 pb-24 flex flex-col gap-16 md:px-16 md:py-(--section-pad)" data-anim-stagger>
		<?php if ( empty( $deon_slugs ) ) : ?>
			<p class="font-sans font-normal leading-relaxed text-deon-body">
				<?php esc_html_e( 'No documents have been published yet. Please check back soon.', 'deon-energy' ); ?>
			</p>
		<?php else : ?>
			<?php
			foreach ( $deon_slugs as $deon_slug ) :
				$deon_type = get_term_by( 'slug', $deon_slug, 'deon_document_type' );
				$deon_q    = new WP_Query( array(
					'post_type'      => 'deon_document',
					'posts_per_page' => -1,
					'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
					'tax_query'      => array(
						array( 'taxonomy' => 'deon_document_type', 'field' => 'slug', 'terms' => $deon_slug ),
					),
				) );
				if ( ! $deon_q->have_posts() ) {
					continue;
				}
				?>
				<div class="flex flex-col gap-6">
					<?php if ( ! $deon_term && $deon_type ) : ?>
						<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.5px] text-deon-heading">
							<?php echo esc_html( $deon_type->name ); ?>
						</h2>
					<?php endif; ?>

					<ul class="flex flex-col border-t border-deon-border">
						<?php
						while ( $deon_q->have_posts() ) :
							$deon_q->the_post();
							$deon_file = get_post_meta( get_the_ID(), '_deon_document_file', true );
							$deon_year = get_post_meta( get_the_ID(), '_deon_document_year', true );
							?>
							<li class="flex flex-col gap-4 border-b border-deon-border py-6 sm:flex-row sm:items-center sm:justify-between">
								<div class="flex flex-col gap-1">
									<span class="font-sans font-normal text-h3 leading-snug text-deon-heading">
										<?php echo esc_html( get_the_title() ); ?>
									</span>
									<?php if ( $deon_year ) : ?>
										<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-eyebrow">
											<?php echo esc_html( $deon_year ); ?>
										</span>
									<?php endif; ?>
								</div>
								<?php if ( $deon_file ) : ?>
									<a href="<?php echo esc_url( $deon_file ); ?>" download
									   class="inline-flex items-center justify-between gap-4 border border-deon-heading px-[25px] py-[17px] shrink-0 hover:bg-deon-heading/5 transition-colors">
										<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-heading">
											<?php esc_html_e( 'Download', 'deon-energy' ); ?>
										</span>
										<img src="<?php echo esc_url( $deon_dl_icon ); ?>" alt="" width="16" height="20" aria-hidden="true">
									</a>
								<?php else : ?>
									<span class="font-sans font-normal text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-body/60 shrink-0">
										<?php esc_html_e( 'File coming soon', 'deon-energy' ); ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endwhile; ?>
					</ul>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</section>
