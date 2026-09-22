<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
/**
 * Project Gallery — filter chips.
 *
 * Chips are real links to `?type=<term-slug>` so the filtered view is
 * bookmarkable, shareable and works with JS disabled (the grid filters
 * server-side). main.js intercepts the click, filters instantly and swaps the
 * URL with history.pushState — no reload.
 */
$deon_types  = function_exists( 'deon_project_type_terms' ) ? deon_project_type_terms() : array();
$deon_active = function_exists( 'deon_active_project_type' ) ? deon_active_project_type() : '';

$deon_chip_base   = 'shrink-0 flex items-center justify-center px-[25px] py-[9px] border
                     font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase cursor-pointer no-underline';
// `!` (important) is required: the unlayered `a { color: inherit }` reset in
// main.css otherwise beats Tailwind's layered text-colour utilities on these
// <a> chips, leaving the active chip's label the inherited grey, not white.
$deon_chip_on     = 'bg-deon-dark border-deon-dark text-white!';
$deon_chip_off    = 'border-deon-border text-deon-heading!';

/**
 * Chip URL. Falls back to a plain query string when the helper is not wired yet.
 *
 * @param string $slug Term slug or 'all'.
 * @return string
 */
$deon_chip_url = function ( $slug ) {
	if ( function_exists( 'deon_project_filter_url' ) ) {
		return deon_project_filter_url( $slug );
	}
	$base = trailingslashit( get_permalink() );
	return ( 'all' === $slug || '' === $slug ) ? $base . '#project-filters' : add_query_arg( 'type', rawurlencode( $slug ), $base ) . '#project-filters';
};
?>

<section class="w-full" id="project-filters">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] pt-(--section-pad) pb-6">
		<div class="border-b border-deon-border pb-[25px] flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between lg:gap-8">

		<div class="flex items-center gap-2 min-w-0 lg:flex-1" data-carousel data-carousel-step="page">

			<button type="button" data-carousel-prev style="display:none"
			        class="inline-flex shrink-0 w-9 h-9 items-center justify-center border border-deon-border bg-white text-deon-heading transition-colors hover:border-deon-accent disabled:opacity-30 disabled:cursor-not-allowed"
			        aria-label="<?php esc_attr_e( 'Scroll filters left', 'deon-energy' ); ?>">
				<span aria-hidden="true">&larr;</span>
			</button>

			<div class="flex gap-[16px] items-center overflow-x-auto min-w-0
			            [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
			     data-anim-stagger data-project-filters data-carousel-track>

			<a href="<?php echo esc_url( $deon_chip_url( 'all' ) ); ?>" data-filter="all"
			   <?php echo '' === $deon_active ? 'aria-current="true"' : ''; ?>
			   class="<?php echo esc_attr( $deon_chip_base . ' ' . ( '' === $deon_active ? $deon_chip_on : $deon_chip_off ) ); ?>">
				<?php esc_html_e( 'ALL', 'deon-energy' ); ?>
			</a>

			<?php if ( $deon_types ) : ?>
				<?php foreach ( $deon_types as $deon_type ) : ?>
					<?php $deon_is_on = ( $deon_active === $deon_type->slug ); ?>
				<a href="<?php echo esc_url( $deon_chip_url( $deon_type->slug ) ); ?>"
				   data-filter="<?php echo esc_attr( $deon_type->slug ); ?>"
				   <?php echo $deon_is_on ? 'aria-current="true"' : ''; ?>
				   class="<?php echo esc_attr( $deon_chip_base . ' ' . ( $deon_is_on ? $deon_chip_on : $deon_chip_off ) ); ?>">
					<?php
						// WP stores term names HTML-encoded (C&amp;I Captive); decode
						// then escape once so "&" isn't double-encoded. The chip's
						// `uppercase` class handles casing — no strtoupper (it would
						// mangle the entity).
						echo esc_html( html_entity_decode( $deon_type->name, ENT_QUOTES ) );
					?>
				</a>
				<?php endforeach; ?>
			<?php else : ?>
				<?php
				/* Fallback chips shown before any taxonomy terms are created — the
				   category set named in the approved copy (PDF §5). No term
				   exists yet, so ?type= on these degrades to "all"; they still
				   filter client-side against the cards' data-category. Once real
				   deon_project_type terms exist they replace this list entirely. */
				foreach ( array( 'Rooftop', 'Industrial', 'Ground Mount', 'Open Access' ) as $deon_label ) :
					$deon_slug = sanitize_title( $deon_label );
					?>
				<a href="<?php echo esc_url( $deon_chip_url( $deon_slug ) ); ?>"
				   data-filter="<?php echo esc_attr( $deon_slug ); ?>"
				   class="<?php echo esc_attr( $deon_chip_base . ' ' . $deon_chip_off ); ?>">
					<?php echo esc_html( strtoupper( $deon_label ) ); ?>
				</a>
				<?php endforeach; ?>
			<?php endif; ?>

			</div>

			<button type="button" data-carousel-next style="display:none"
			        class="inline-flex shrink-0 w-9 h-9 items-center justify-center border border-deon-border bg-white text-deon-heading transition-colors hover:border-deon-accent disabled:opacity-30 disabled:cursor-not-allowed"
			        aria-label="<?php esc_attr_e( 'Scroll filters right', 'deon-energy' ); ?>">
				<span aria-hidden="true">&rarr;</span>
			</button>

		</div>

		<div class="relative shrink-0 w-full lg:w-[300px]" data-anim>
			<span class="absolute left-4 top-1/2 -translate-y-1/2 text-deon-body pointer-events-none" aria-hidden="true">
				<svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="8.5" cy="8.5" r="5.5"/><path d="M17 17l-4.5-4.5"/>
				</svg>
			</span>
			<label for="project-search" class="sr-only"><?php esc_html_e( 'Search projects', 'deon-energy' ); ?></label>
			<input type="search" id="project-search" data-project-search
			       placeholder="<?php esc_attr_e( 'Search by name, location, capacity…', 'deon-energy' ); ?>"
			       class="w-full border border-deon-border bg-white pl-11 pr-4 py-[11px] font-sans text-[14px] leading-[20px] text-deon-heading placeholder:text-deon-body focus:border-deon-accent focus:outline-none">
		</div>

		</div>
	</div>
</section>
