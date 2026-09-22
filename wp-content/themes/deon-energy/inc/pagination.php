<?php
/**
 * Shared pagination — ONE pagination UI for the whole site.
 *
 * Client request (2026-08): every listing that pages — Project Gallery,
 * Knowledge Hub, Press Coverage, Leadership — must look and behave the same.
 * The markup is a `.deon-pager` block styled in `assets/css/main.css` (not
 * Tailwind) because main.css is the only stylesheet loaded on BOTH the Phase 1
 * pages and the Tailwind Phase 2 pages, and the JS-built pagers (projects,
 * leadership) have to land on those same classes.
 *
 * Server-rendered pagers call deon_pagination(); the client-side pagers in
 * main.js build the identical class names by hand.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Page numbers to render, with '…' gaps for long runs.
 *
 * Always keeps page 1, the last page, and a window around the current page, so
 * a 40-page archive still renders a fixed-width control.
 *
 * @param int $current Current page (1-based).
 * @param int $total   Total pages.
 * @return array<int|string> Page numbers, with the string '…' marking a gap.
 */
function deon_pagination_page_list( $current, $total ) {
	$current = max( 1, (int) $current );
	$total   = max( 1, (int) $total );

	// Short runs need no truncation at all.
	if ( $total <= 7 ) {
		return range( 1, $total );
	}

	$window = array( 1, $total );
	for ( $p = $current - 1; $p <= $current + 1; $p++ ) {
		if ( $p > 1 && $p < $total ) {
			$window[] = $p;
		}
	}
	// Keep the control a stable width when the current page sits at either end.
	if ( $current <= 3 ) {
		$window = array_merge( $window, array( 2, 3, 4 ) );
	}
	if ( $current >= $total - 2 ) {
		$window = array_merge( $window, array( $total - 3, $total - 2, $total - 1 ) );
	}

	$window = array_values( array_unique( array_filter( $window, function ( $p ) use ( $total ) {
		return $p >= 1 && $p <= $total;
	} ) ) );
	sort( $window );

	$out  = array();
	$prev = 0;
	foreach ( $window as $p ) {
		if ( $prev && $p - $prev > 1 ) {
			$out[] = '…';
		}
		$out[] = $p;
		$prev  = $p;
	}

	return $out;
}

/**
 * Render the shared pagination control.
 *
 * Renders nothing when there is one page or less.
 *
 * @param array $args {
 *     @type int      $current Current page (1-based). Default 1.
 *     @type int      $total   Total pages. Required.
 *     @type callable $url     fn( int $page ): string — link for a page. Required.
 *     @type string   $label   nav aria-label. Default "Pagination".
 *     @type bool     $echo    Echo (true, default) or return the markup.
 * }
 * @return string Markup when $echo is false, otherwise ''.
 */
function deon_pagination( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'current' => 1,
		'total'   => 0,
		'url'     => null,
		'label'   => __( 'Pagination', 'deon-energy' ),
		'echo'    => true,
	) );

	$total   = (int) $args['total'];
	$current = min( max( 1, (int) $args['current'] ), max( 1, $total ) );

	if ( $total <= 1 || ! is_callable( $args['url'] ) ) {
		return '';
	}

	$url  = $args['url'];
	$prev = $current > 1 ? call_user_func( $url, $current - 1 ) : '';
	$next = $current < $total ? call_user_func( $url, $current + 1 ) : '';

	ob_start();
	?>
	<nav class="deon-pager" aria-label="<?php echo esc_attr( $args['label'] ); ?>">

		<?php if ( $prev ) : ?>
		<a class="deon-pager__btn deon-pager__btn--nav" href="<?php echo esc_url( $prev ); ?>"
		   rel="prev" aria-label="<?php esc_attr_e( 'Previous page', 'deon-energy' ); ?>">
			<?php deon_pagination_arrow( 'prev' ); ?>
		</a>
		<?php else : ?>
		<span class="deon-pager__btn deon-pager__btn--nav is-disabled" aria-hidden="true"><?php deon_pagination_arrow( 'prev' ); ?></span>
		<?php endif; ?>

		<?php foreach ( deon_pagination_page_list( $current, $total ) as $deon_p ) : ?>
			<?php if ( '…' === $deon_p ) : ?>
			<span class="deon-pager__gap" aria-hidden="true">…</span>
			<?php elseif ( (int) $deon_p === $current ) : ?>
			<span class="deon-pager__btn is-current" aria-current="page"><?php echo esc_html( $deon_p ); ?></span>
			<?php else : ?>
			<a class="deon-pager__btn" href="<?php echo esc_url( call_user_func( $url, (int) $deon_p ) ); ?>"><?php echo esc_html( $deon_p ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if ( $next ) : ?>
		<a class="deon-pager__btn deon-pager__btn--nav" href="<?php echo esc_url( $next ); ?>"
		   rel="next" aria-label="<?php esc_attr_e( 'Next page', 'deon-energy' ); ?>">
			<?php deon_pagination_arrow( 'next' ); ?>
		</a>
		<?php else : ?>
		<span class="deon-pager__btn deon-pager__btn--nav is-disabled" aria-hidden="true"><?php deon_pagination_arrow( 'next' ); ?></span>
		<?php endif; ?>

	</nav>
	<?php
	$html = (string) ob_get_clean();

	if ( $args['echo'] ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built + escaped above.
		return '';
	}

	return $html;
}

/**
 * The designed prev/next arrow. Same glyph the Knowledge Hub pager shipped with,
 * mirrored for `prev`. Kept here so PHP and main.js draw the same icon.
 *
 * @param string $dir 'prev' or 'next'.
 */
function deon_pagination_arrow( $dir = 'next' ) {
	$path = ( 'prev' === $dir )
		? 'M3.825 9L9.425 14.6L8 16L0 8L8 0L9.425 1.4L3.825 7H16V9H3.825Z'
		: 'M12.175 9H0V7H12.175L6.575 1.4L8 0L16 8L8 16L6.575 14.6L12.175 9Z';
	printf(
		'<svg class="deon-pager__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="%s" fill="currentColor"/></svg>',
		esc_attr( $path )
	);
}

/**
 * Adapter for the main WP loop (Knowledge Hub listing, archives, search).
 *
 * @param string $label nav aria-label.
 */
function deon_the_posts_pagination( $label = '' ) {
	global $wp_query;

	$total = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 0;
	if ( $total <= 1 ) {
		return;
	}

	deon_pagination( array(
		'current' => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
		'total'   => $total,
		'url'     => 'get_pagenum_link',
		'label'   => $label ? $label : __( 'Posts pagination', 'deon-energy' ),
	) );
}
