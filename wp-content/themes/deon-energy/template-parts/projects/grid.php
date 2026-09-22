<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
/**
 * Project Gallery — grid.
 *
 * `?type=<term-slug>` (validated in inc/project-filters.php) filters the view
 * server-side: every card is rendered, but non-matching cards ship hidden, so
 * a JS-disabled visitor sees exactly the requested subset and main.js can then
 * re-filter instantly on chip clicks without a round trip.
 */
$deon_active = function_exists( 'deon_active_project_type' ) ? deon_active_project_type() : '';

$deon_query = new WP_Query( array(
	'post_type'      => 'deon_project',
	'post_status'    => 'publish',   // Only published projects — draft/pending posts have no public URL and would 404 when their card is clicked.
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );

// How many projects the active filter actually matches — drives the empty state.
$deon_match_count = 0;
if ( $deon_active ) {
	foreach ( $deon_query->posts as $deon_post ) {
		if ( has_term( $deon_active, 'deon_project_type', $deon_post ) ) {
			$deon_match_count++;
		}
	}
}
?>

<section class="w-full" id="project-grid">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] pt-6 pb-(--section-pad)
	            grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3"
	     data-anim-stagger data-project-grid
	     data-active-filter="<?php echo esc_attr( $deon_active ? $deon_active : 'all' ); ?>">

		<?php if ( $deon_query->have_posts() ) : ?>

			<?php while ( $deon_query->have_posts() ) : $deon_query->the_post(); ?>
			<?php
			$deon_terms    = get_the_terms( get_the_ID(), 'deon_project_type' );
			$deon_term     = ( $deon_terms && ! is_wp_error( $deon_terms ) ) ? $deon_terms[0] : null;
			$deon_cat_slug = $deon_term ? $deon_term->slug : '';
			$deon_cat_name = $deon_term ? strtoupper( $deon_term->name ) : '';
			$deon_capacity = get_post_meta( get_the_ID(), '_deon_capacity', true );
			$deon_location = get_post_meta( get_the_ID(), '_deon_location', true );
			// Commissioned is free text ("Q3 2023", "2024") — the card shows just
			// the year, so pull the first 4-digit run and fall back to the raw value.
			$deon_year = get_post_meta( get_the_ID(), '_deon_commissioned', true );
			if ( $deon_year && preg_match( '/\d{4}/', $deon_year, $deon_m ) ) {
				$deon_year = $deon_m[0];
			}
			$deon_desc = trim( wp_strip_all_tags( get_the_excerpt() ) );
			$deon_img  = get_the_post_thumbnail_url( get_the_ID(), 'large' );
			// Short location for the meta row — the city/first segment only, so the
			// three facts stay on one line; the full string still feeds the search.
			$deon_place  = $deon_location ? trim( explode( ',', $deon_location )[0] ) : '';
			$deon_hidden = ( $deon_active && $deon_cat_slug !== $deon_active );
			// Free-text haystack for the client-side search (title + location +
			// capacity + type + year), lower-cased once here so main.js just substring-matches.
			$deon_haystack = strtolower( trim( implode( ' ', array_filter( array(
				get_the_title(), $deon_location, $deon_capacity, $deon_cat_name, $deon_year,
			) ) ) ) );
			?>

			<article class="group relative bg-white border border-deon-border p-px flex flex-col transition-colors hover:border-deon-accent"
			         data-category="<?php echo esc_attr( $deon_cat_slug ); ?>"
			         data-search="<?php echo esc_attr( $deon_haystack ); ?>"
			         <?php echo $deon_hidden ? 'hidden style="display:none"' : ''; ?>>

				<a href="<?php the_permalink(); ?>" class="absolute inset-0 z-10" aria-label="<?php the_title_attribute(); ?>"></a>

				<div class="bg-deon-warm relative overflow-hidden shrink-0 aspect-[16/10]">
					<?php if ( $deon_img ) : ?>
					<img
						src="<?php echo esc_url( $deon_img ); ?>"
						alt="<?php the_title_attribute(); ?>"
						class="w-full h-full object-cover"
						loading="lazy"
					/>
					<?php endif; ?>
					<?php if ( $deon_cat_name ) : ?>
					<div class="absolute top-4 left-4 bg-white/90 border border-deon-accent px-1">
						<span class="font-sans font-normal text-[10px] leading-[15px] uppercase text-deon-accent">
							<?php echo esc_html( $deon_cat_name ); ?>
						</span>
					</div>
					<?php endif; ?>
					<?php /* Hover affordance — the whole card is already a link, so this
					         is decorative and stays out of the tab order. */ ?>
					<div class="absolute inset-0 hidden lg:flex items-center justify-center
					            opacity-0 transition-opacity duration-300 group-hover:opacity-100" aria-hidden="true">
						<span class="bg-white text-deon-accent font-sans font-bold text-[14px] leading-[20px]
						             px-6 py-3 shadow-[0_4px_16px_rgba(0,0,0,0.12)]">
							<?php esc_html_e( 'View Details', 'deon-energy' ); ?>
						</span>
					</div>
				</div>

				<div class="p-[24px] flex flex-col gap-[8px] grow">
					<h3 class="font-display font-bold text-h3 leading-[1.35] text-deon-heading">
						<?php the_title(); ?>
					</h3>
					<?php if ( $deon_desc ) : ?>
					<p class="font-sans font-normal text-[14px] leading-[22px] text-deon-body line-clamp-2">
						<?php echo esc_html( $deon_desc ); ?>
					</p>
					<?php endif; ?>
					<?php if ( $deon_capacity || $deon_place || $deon_year ) : ?>
					<div class="mt-auto pt-[16px] border-t border-deon-divider
					            flex flex-wrap items-center gap-x-4 gap-y-2
					            font-sans font-normal text-[13px] leading-[18px] text-deon-body">
						<?php if ( $deon_capacity ) : ?>
						<span class="inline-flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5 shrink-0 text-deon-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13 2 4.5 13.5H11l-1 8.5 8.5-11.5H12l1-8.5Z"/></svg>
							<?php echo esc_html( $deon_capacity ); ?>
						</span>
						<?php endif; ?>
						<?php if ( $deon_place ) : ?>
						<span class="inline-flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5 shrink-0 text-deon-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5Z"/></svg>
							<?php echo esc_html( $deon_place ); ?>
						</span>
						<?php endif; ?>
						<?php if ( $deon_year ) : ?>
						<span class="inline-flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5 shrink-0 text-deon-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 2v2H5.5A2.5 2.5 0 0 0 3 6.5v13A2.5 2.5 0 0 0 5.5 22h13a2.5 2.5 0 0 0 2.5-2.5v-13A2.5 2.5 0 0 0 18.5 4H17V2h-2v2H9V2H7Zm12 8v9.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V10h14Z"/></svg>
							<?php echo esc_html( $deon_year ); ?>
						</span>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>

			</article>
			<?php endwhile; wp_reset_postdata(); ?>

			<?php
			/* Valid filter, no projects in it. Kept in the DOM so main.js can
			   toggle it on later filters without a reload. */
			$deon_empty_hidden = ( ! $deon_active || $deon_match_count > 0 );
			?>
			<p class="col-span-full text-center font-sans text-lead text-deon-body py-16"
			   data-filter-empty <?php echo $deon_empty_hidden ? 'hidden style="display:none"' : ''; ?>>
				<?php esc_html_e( 'No projects in this category yet.', 'deon-energy' ); ?>
				<a href="<?php echo esc_url( function_exists( 'deon_project_filter_url' ) ? deon_project_filter_url( 'all' ) : '#project-filters' ); ?>"
				   class="text-deon-accent underline" data-filter="all">
					<?php esc_html_e( 'View all projects', 'deon-energy' ); ?>
				</a>
			</p>

		<?php else : ?>

			<p class="col-span-3 text-center font-sans text-lead text-deon-body py-16">
				<?php esc_html_e( 'No projects yet. Add projects from the WordPress admin.', 'deon-energy' ); ?>
			</p>

		<?php endif; ?>

	</div>

	<?php /* Pagination — built by main.js from the matching cards into the shared
	         `.deon-pager` markup (inc/pagination.php); hidden until JS runs
	         (JS-off visitors see every card, which is fine). */ ?>
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] pb-(--section-pad)">
		<nav class="deon-pager" data-project-pagination style="display:none"
		     aria-label="<?php esc_attr_e( 'Project pages', 'deon-energy' ); ?>"></nav>
	</div>
</section>