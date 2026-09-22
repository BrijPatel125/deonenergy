<?php
/**
 * Template Name: Sitemap
 *
 * Human-readable HTML sitemap. Client change log v1 §3.
 *
 * Create a Page named "Sitemap" with slug `sitemap`; WordPress resolves this
 * template from the slug automatically (page-{slug}.php), or it can be picked
 * explicitly under Page Attributes → Template — same convention as
 * page-press-coverage.php.
 *
 * Everything below is generated from live content, never hardcoded, so the page
 * stays correct as content is added:
 *   - all published Pages, hierarchically
 *   - all published Posts, grouped by category
 *   - every custom post type whose singles are actually publicly viewable
 *     (`is_post_type_viewable()`) — today that is only `deon_project`; the rest
 *     (deon_press, deon_document, deon_case_study, deon_job, deon_leader) are
 *     registered `public => false` in functions.php and are therefore listed as
 *     counts only, never linked to a 404
 *   - categories and tags
 *
 * The hero reuses template-parts/news/hero.php, which is a thin adapter over the
 * shared inner-page hero.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

/**
 * Custom post types worth surfacing, in display order.
 * label => post type. Only viewable types are linked.
 */
$deon_sitemap_cpts = array(
	'deon_project'    => __( 'Projects', 'deon-energy' ),
	'deon_case_study' => __( 'Case Studies', 'deon-energy' ),
	'deon_press'      => __( 'Press Coverage', 'deon-energy' ),
	'deon_document'   => __( 'Documents & Downloads', 'deon-energy' ),
	'deon_job'        => __( 'Open Positions', 'deon-energy' ),
	'deon_leader'     => __( 'Leadership', 'deon-energy' ),
);

$deon_link_class = 'font-sans leading-[1.6] text-deon-body transition-colors hover:text-deon-accent';
?>

<main class="bg-deon-bg deon-sitemap">

	<?php
	get_template_part( 'template-parts/news/hero', null, array(
		'eyebrow'   => __( 'Site Index', 'deon-energy' ),
		'title'     => __( 'Sitemap', 'deon-energy' ),
		'lead'      => __( 'Every page, project, and article on deonenergy.in — in one place, for readers and search engines alike.', 'deon-energy' ),
		'image'     => '',
		'image_alt' => '',
		'bg'        => 'offwhite',
	) );
	?>

	<!-- Pages -->
	<section class="w-full bg-white">
		<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad) flex flex-col gap-[28px]">

			<?php
			get_template_part( 'template-parts/news/section-head', null, array(
				'title' => __( 'Pages', 'deon-energy' ),
				'lead'  => __( 'The full published page structure of the site.', 'deon-energy' ),
			) );
			?>

			<?php
			$deon_pages = wp_list_pages( array(
				'title_li'    => '',
				'echo'        => false,
				'sort_column' => 'menu_order, post_title',
				'depth'       => 0,
			) );
			?>

			<?php if ( $deon_pages ) : ?>
			<ul class="deon-sitemap__pages list-none m-0 p-0 md:columns-2 lg:columns-3 gap-[40px] [&_a]:font-sans [&_a]:leading-[1.7] [&_a]:text-deon-body [&_a:hover]:text-deon-accent [&_ul]:list-none [&_ul]:pl-[18px] [&_li]:break-inside-avoid [&_li]:mb-[6px]" data-anim>
				<?php echo wp_kses_post( $deon_pages ); ?>
			</ul>
			<?php else : ?>
			<p class="font-sans text-deon-body"><?php esc_html_e( 'No pages published yet.', 'deon-energy' ); ?></p>
			<?php endif; ?>

		</div>
	</section>

	<!-- Custom post types -->
	<?php
	$deon_cpt_blocks = array();

	foreach ( $deon_sitemap_cpts as $deon_type => $deon_label ) {
		if ( ! post_type_exists( $deon_type ) ) {
			continue;
		}

		$deon_items = get_posts( array(
			'post_type'        => $deon_type,
			'post_status'      => 'publish',
			'numberposts'      => 200,
			'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'suppress_filters' => false,
		) );

		if ( ! $deon_items ) {
			continue;
		}

		$deon_cpt_blocks[] = array(
			'label'    => $deon_label,
			'items'    => $deon_items,
			// Only link singles that actually resolve — public / publicly_queryable.
			'viewable' => is_post_type_viewable( $deon_type ),
		);
	}
	?>

	<?php if ( $deon_cpt_blocks ) : ?>
	<section class="w-full bg-deon-bg">
		<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad) flex flex-col gap-[28px]">

			<?php
			get_template_part( 'template-parts/news/section-head', null, array(
				'title' => __( 'Content Library', 'deon-energy' ),
				'lead'  => __( 'Projects, case studies, documents, and other published records.', 'deon-energy' ),
			) );
			?>

			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[32px]" data-anim-stagger>
				<?php foreach ( $deon_cpt_blocks as $deon_block ) : ?>
				<div class="flex flex-col gap-[10px]">
					<h3 class="font-display font-bold text-h3 leading-[1.3] text-deon-heading">
						<?php echo esc_html( $deon_block['label'] ); ?>
						<span class="font-sans font-normal text-[13px] text-deon-body">(<?php echo esc_html( number_format_i18n( count( $deon_block['items'] ) ) ); ?>)</span>
					</h3>

					<ul class="list-none m-0 p-0 flex flex-col gap-[6px]">
						<?php foreach ( $deon_block['items'] as $deon_item ) : ?>
						<li>
							<?php if ( $deon_block['viewable'] ) : ?>
							<a href="<?php echo esc_url( get_permalink( $deon_item ) ); ?>" class="<?php echo esc_attr( $deon_link_class ); ?>">
								<?php echo esc_html( get_the_title( $deon_item ) ); ?>
							</a>
							<?php else : ?>
							<span class="font-sans leading-[1.6] text-deon-body"><?php echo esc_html( get_the_title( $deon_item ) ); ?></span>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>

					<?php if ( ! $deon_block['viewable'] ) : ?>
					<p class="font-sans text-[12px] leading-[1.5] text-deon-body/70 m-0">
						<?php esc_html_e( 'Shown within the relevant page — no standalone URL.', 'deon-energy' ); ?>
					</p>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>

		</div>
	</section>
	<?php endif; ?>

	<!-- Posts by category -->
	<?php
	$deon_categories = get_categories( array( 'hide_empty' => true, 'orderby' => 'name' ) );
	// Posts that carry no category at all, so nothing is silently omitted.
	$deon_uncat = get_posts( array(
		'post_status' => 'publish',
		'numberposts' => 200,
		'orderby'     => 'date',
		'order'       => 'DESC',
		'tax_query'   => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'category',
				'operator' => 'NOT EXISTS',
			),
		),
	) );
	?>

	<?php if ( $deon_categories || $deon_uncat ) : ?>
	<section class="w-full bg-white">
		<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad) flex flex-col gap-[28px]">

			<?php
			get_template_part( 'template-parts/news/section-head', null, array(
				'title' => __( 'Knowledge Hub Articles', 'deon-energy' ),
				'lead'  => __( 'Every published article, grouped by category.', 'deon-energy' ),
			) );
			?>

			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[32px]" data-anim-stagger>
				<?php foreach ( $deon_categories as $deon_cat ) : ?>
					<?php
					$deon_cat_posts = get_posts( array(
						'post_status' => 'publish',
						'numberposts' => 100,
						'cat'         => $deon_cat->term_id,
						'orderby'     => 'date',
						'order'       => 'DESC',
					) );
					if ( ! $deon_cat_posts ) {
						continue;
					}
					?>
					<div class="flex flex-col gap-[10px]">
						<h3 class="font-display font-bold text-h3 leading-[1.3] text-deon-heading">
							<a href="<?php echo esc_url( get_category_link( $deon_cat ) ); ?>" class="transition-colors hover:text-deon-accent">
								<?php echo esc_html( $deon_cat->name ); ?>
							</a>
						</h3>
						<ul class="list-none m-0 p-0 flex flex-col gap-[6px]">
							<?php foreach ( $deon_cat_posts as $deon_post ) : ?>
							<li>
								<a href="<?php echo esc_url( get_permalink( $deon_post ) ); ?>" class="<?php echo esc_attr( $deon_link_class ); ?>">
									<?php echo esc_html( get_the_title( $deon_post ) ); ?>
								</a>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>

				<?php if ( $deon_uncat ) : ?>
				<div class="flex flex-col gap-[10px]">
					<h3 class="font-display font-bold text-h3 leading-[1.3] text-deon-heading">
						<?php esc_html_e( 'Other Articles', 'deon-energy' ); ?>
					</h3>
					<ul class="list-none m-0 p-0 flex flex-col gap-[6px]">
						<?php foreach ( $deon_uncat as $deon_post ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $deon_post ) ); ?>" class="<?php echo esc_attr( $deon_link_class ); ?>">
								<?php echo esc_html( get_the_title( $deon_post ) ); ?>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
			</div>

		</div>
	</section>
	<?php endif; ?>

	<!-- Categories, tags, project types -->
	<?php
	$deon_tags      = get_tags( array( 'hide_empty' => true, 'orderby' => 'name' ) );
	$deon_proj_type = taxonomy_exists( 'deon_project_type' ) && is_taxonomy_viewable( 'deon_project_type' )
		? get_terms( array( 'taxonomy' => 'deon_project_type', 'hide_empty' => true, 'orderby' => 'name' ) )
		: array();
	$deon_proj_type = is_wp_error( $deon_proj_type ) ? array() : $deon_proj_type;

	$deon_term_groups = array();
	if ( $deon_categories ) {
		$deon_term_groups[] = array( 'label' => __( 'Categories', 'deon-energy' ), 'terms' => $deon_categories );
	}
	if ( $deon_tags ) {
		$deon_term_groups[] = array( 'label' => __( 'Tags', 'deon-energy' ), 'terms' => $deon_tags );
	}
	if ( $deon_proj_type ) {
		$deon_term_groups[] = array( 'label' => __( 'Project Types', 'deon-energy' ), 'terms' => $deon_proj_type );
	}
	?>

	<?php if ( $deon_term_groups ) : ?>
	<section class="w-full bg-deon-bg">
		<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad) flex flex-col gap-[28px]">

			<?php
			get_template_part( 'template-parts/news/section-head', null, array(
				'title' => __( 'Topics', 'deon-energy' ),
				'lead'  => __( 'Browse the site by subject.', 'deon-energy' ),
			) );
			?>

			<div class="flex flex-col gap-[28px]" data-anim>
				<?php foreach ( $deon_term_groups as $deon_group ) : ?>
				<div class="flex flex-col gap-[12px]">
					<h3 class="font-display font-bold text-h3 leading-[1.3] text-deon-heading">
						<?php echo esc_html( $deon_group['label'] ); ?>
					</h3>
					<ul class="list-none m-0 p-0 flex flex-wrap gap-[10px]">
						<?php foreach ( $deon_group['terms'] as $deon_term ) : ?>
							<?php $deon_term_link = get_term_link( $deon_term ); ?>
							<?php if ( is_wp_error( $deon_term_link ) ) { continue; } ?>
						<li>
							<a href="<?php echo esc_url( $deon_term_link ); ?>"
							   class="inline-flex items-center gap-1.5 bg-white border border-deon-divider px-[14px] py-[7px]
							          font-sans text-[13px] leading-[1.4] text-deon-body
							          transition-colors hover:border-deon-accent hover:text-deon-accent">
								<?php echo esc_html( $deon_term->name ); ?>
								<span class="text-[11px] text-deon-body/60"><?php echo esc_html( number_format_i18n( $deon_term->count ) ); ?></span>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endforeach; ?>
			</div>

		</div>
	</section>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
