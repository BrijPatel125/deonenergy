<?php
/**
 * Investor Relations — Investor Documents.
 *
 * Solex-style, responsive in two shapes off ONE set of markup (client feedback,
 * 2026-08 — desktop showed every document at once, mobile forced a long scroll):
 *
 *  - Desktop (lg+): CATEGORIES sidebar + a stage on the right. Clicking a
 *    category swaps the stage to that category's documents only — never the
 *    whole archive, so the list stays short however many files are uploaded.
 *  - Mobile / tablet: the same panels become an ACCORDION inside the categories
 *    box. Tapping a category expands its documents directly underneath it — no
 *    scrolling off to find them, matching solex.in on a phone.
 *
 * The panels are rendered once, inside the desktop stage; main.js relocates them
 * into each category row ([data-doc-home]) below lg and back on resize. With JS
 * off every panel simply stays visible in the stage.
 *
 * Fully dynamic:
 *  - Categories come from the deon_investor_category taxonomy (editable at
 *    Investor Documents → Categories). Every category renders, even with zero
 *    documents yet — its panel shows a "Document Not Available" placeholder
 *    instead of being dropped, so a newly created category is visible right away.
 *  - A category can have a Parent Category set (the taxonomy's own hierarchy
 *    field). When it does, it does NOT get its own row in the sidebar — it
 *    becomes a labeled sub-section inside the parent's panel instead: its
 *    name stays visible as a heading, followed by its own documents, or a
 *    "Document Not Available" placeholder if it has none yet.
 *  - Docs with no category fall into a trailing "Other Documents" row (this one
 *    IS dropped when empty — it's a catch-all, not a real category). A doc in
 *    several categories appears under each.
 *  - File sizes auto-derived via deon_document_file_meta().
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_uri = get_template_directory_uri();

/* Categories — the dedicated deon_investor_category taxonomy. Fully client-managed
 * under Investor Documents → Categories; no shared-taxonomy bleed, no fallback. */
$deon_terms = get_terms( array(
	'taxonomy'   => 'deon_investor_category',
	'hide_empty' => false,
	'orderby'    => 'slug', // Slugs are numeric-prefixed (see the seeder in inc/investor-documents.php) so this sorts them in the intended sequence, not alphabetically by name.
) );

/* Parent → children map, built from the taxonomy's own hierarchy (the
 * "Parent Category" field when creating/editing a term). Used only to nest
 * sub-categories under their parent in the sidebar — the document panels
 * themselves stay keyed flat by slug either way, so a click on a child
 * category still swaps the stage to its own documents exactly like a
 * top-level one does. */
$deon_children_of = array(); // parent slug => array of child term objects
$deon_child_slugs = array(); // flat list, so the top-level loop can skip them
if ( $deon_terms && ! is_wp_error( $deon_terms ) ) {
	$deon_term_by_id = array();
	foreach ( $deon_terms as $deon_t ) {
		$deon_term_by_id[ $deon_t->term_id ] = $deon_t;
	}
	foreach ( $deon_terms as $deon_t ) {
		if ( $deon_t->parent && isset( $deon_term_by_id[ $deon_t->parent ] ) ) {
			$deon_parent_slug = $deon_term_by_id[ $deon_t->parent ]->slug;
			$deon_children_of[ $deon_parent_slug ][] = $deon_t;
			$deon_child_slugs[] = $deon_t->slug;
		}
	}
}

/* Disclosure list — every investor document. Empty until the client/seeder adds any. */
$deon_docs = get_posts( array(
	'post_type'      => 'deon_investor_doc',
	'posts_per_page' => -1,
	'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
) );

/* Seed one bucket per category (in taxonomy order) so the rows render in the
 * same order the sidebar lists them, then a trailing catch-all bucket. */
$deon_groups = array();
if ( $deon_terms && ! is_wp_error( $deon_terms ) ) {
	foreach ( $deon_terms as $deon_term ) {
		$deon_groups[ $deon_term->slug ] = array( 'name' => $deon_term->name, 'rows' => array() );
	}
}
$deon_groups['other'] = array( 'name' => __( 'Other Documents', 'deon-energy' ), 'rows' => array() );

foreach ( $deon_docs as $deon_doc ) {
	$deon_year    = (string) get_post_meta( $deon_doc->ID, '_deon_document_year', true );
	$deon_terms_p = get_the_terms( $deon_doc->ID, 'deon_investor_category' );
	$deon_terms_p = ( $deon_terms_p && ! is_wp_error( $deon_terms_p ) ) ? $deon_terms_p : array();
	$deon_badge   = ( $deon_year && preg_match( '/(\d{2})\D*$/', $deon_year, $deon_m ) ) ? $deon_m[1] : 'DOC';

	// Optional per-document flag: when set, the row shows "View" (opens in a
	// new tab, no download attribute) instead of "Download". Defaults to
	// downloadable — false/unset behaves exactly as before.
	$deon_view_only = (bool) get_post_meta( $deon_doc->ID, '_deon_document_view_only', true );

	// File type only (e.g. "PDF") for display — the file-size portion that
	// deon_document_file_meta() appends (e.g. "PDF • 25.5 KB") is dropped.
	$deon_full_meta = (string) deon_document_file_meta( $deon_doc->ID );
	$deon_type_only = trim( preg_replace( '/\s*[•·].*$/u', '', $deon_full_meta ) );

	// If the document's main content editor has text in it, the row becomes
	// its own expandable sub-section (contact details, registrar info, etc.)
	// instead of a flat file row — written the normal way, in the post's
	// content editor in wp-admin, no extra field needed.
	$deon_body_raw  = trim( (string) $deon_doc->post_content );
	$deon_body_html = ( '' !== $deon_body_raw ) ? apply_filters( 'the_content', $deon_body_raw ) : '';

	$deon_row = array(
		'badge'        => $deon_badge,
		'title'        => get_the_title( $deon_doc->ID ),
		'sub'          => $deon_year,
		'meta'         => $deon_type_only,
		'url'          => (string) get_post_meta( $deon_doc->ID, '_deon_document_file', true ),
		'downloadable' => ! $deon_view_only,
		'body'         => $deon_body_html,
	);

	if ( $deon_terms_p ) {
		// A doc filed under several categories is listed under each of them.
		foreach ( $deon_terms_p as $deon_term_p ) {
			if ( ! isset( $deon_groups[ $deon_term_p->slug ] ) ) {
				$deon_groups[ $deon_term_p->slug ] = array( 'name' => $deon_term_p->name, 'rows' => array() );
			}
			$deon_groups[ $deon_term_p->slug ]['rows'][] = $deon_row;
		}
	} else {
		$deon_groups['other']['rows'][] = $deon_row;
	}
}

// A category with a Parent Category set doesn't get its own row in the
// sidebar. Instead it becomes a labeled sub-section inside the parent's
// panel — its name stays visible as a heading, with its own documents (or
// a "not available" placeholder) underneath, rather than disappearing
// anonymously into the parent's flat list.
foreach ( $deon_child_slugs as $deon_child_slug ) {
	if ( empty( $deon_groups[ $deon_child_slug ] ) ) {
		continue;
	}
	$deon_parent_slug = '';
	foreach ( $deon_children_of as $deon_p_slug => $deon_kids ) {
		foreach ( $deon_kids as $deon_kid ) {
			if ( $deon_kid->slug === $deon_child_slug ) {
				$deon_parent_slug = $deon_p_slug;
				break 2;
			}
		}
	}
	if ( $deon_parent_slug && isset( $deon_groups[ $deon_parent_slug ] ) ) {
		if ( ! isset( $deon_groups[ $deon_parent_slug ]['subgroups'] ) ) {
			$deon_groups[ $deon_parent_slug ]['subgroups'] = array();
		}
		$deon_groups[ $deon_parent_slug ]['subgroups'][] = $deon_groups[ $deon_child_slug ];
	}
	unset( $deon_groups[ $deon_child_slug ] );
}

// Drop the catch-all "Other Documents" bucket only if nothing landed there.
// Real categories stay even when empty, so every category is always visible
// in the sidebar/accordion — an empty one just shows a not-available panel.
if ( empty( $deon_groups['other']['rows'] ) ) {
	unset( $deon_groups['other'] );
}

$deon_first_slug = $deon_groups ? array_key_first( $deon_groups ) : '';

/* Shared row renderer — used for a category's own documents and again
 * inside a folded sub-category, so both stay pixel-identical. */
$deon_render_doc_row = function ( $deon_row ) use ( $deon_uri ) {
	if ( '' !== $deon_row['body'] ) :
		?>
		<!-- Sub-section: expandable content block (contact details, registrar info, etc.) written straight into the document's content editor in wp-admin. -->
		<li class="border-b border-deon-border last:border-b-0" data-doc-item>
			<details class="group/sub">
				<summary class="cursor-pointer list-none flex items-center justify-between gap-4 bg-deon-bg px-5 py-4 [&::-webkit-details-marker]:hidden">
					<span class="font-sans font-normal leading-snug text-deon-heading">
						<?php echo esc_html( $deon_row['title'] ); ?>
					</span>
					<svg class="w-[12px] h-[12px] shrink-0 text-deon-heading transition-transform duration-200 group-open/sub:rotate-45" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
						<path d="M6 1v10M1 6h10"/>
					</svg>
				</summary>
				<div class="px-5 py-6 font-sans text-[14px] leading-relaxed text-deon-body [&_p]:mb-3 [&_a]:text-deon-accent [&_strong]:text-deon-heading">
					<?php echo wp_kses_post( $deon_row['body'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content, kses'd. ?>
					<?php if ( $deon_row['url'] ) : ?>
					<div class="flex items-center gap-3 pt-4">
						<a href="<?php echo esc_url( $deon_row['url'] ); ?>" target="_blank" rel="noopener"
						   class="inline-flex items-center justify-center w-10 h-10 border border-deon-border hover:bg-deon-heading/5 transition-colors"
						   aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'deon-energy' ), $deon_row['title'] ) ); ?>">
							<img src="<?php echo esc_url( $deon_uri . '/assets/img/investor-icon-external.svg' ); ?>" alt="" width="14" height="14" aria-hidden="true">
						</a>
						<a href="<?php echo esc_url( $deon_row['url'] ); ?>" download
						   class="inline-flex items-center justify-center w-10 h-10 border border-deon-border hover:bg-deon-heading/5 transition-colors"
						   aria-label="<?php echo esc_attr( sprintf( __( 'Download %s', 'deon-energy' ), $deon_row['title'] ) ); ?>">
							<img src="<?php echo esc_url( $deon_uri . '/assets/img/investor-icon-download.svg' ); ?>" alt="" width="12" height="15" aria-hidden="true">
						</a>
					</div>
					<?php endif; ?>
				</div>
			</details>
		</li>
		<?php
	else :
		?>
		<li class="flex items-center gap-4 border-b border-deon-border py-5 sm:gap-5 sm:py-6 last:border-b-0 data-[view=grid]:flex-col data-[view=grid]:items-start data-[view=grid]:border data-[view=grid]:border-deon-border data-[view=grid]:p-6 data-[view=grid]:gap-4" data-doc-item>
			<a href="<?php echo esc_url( $deon_row['url'] ? $deon_row['url'] : '#' ); ?>"<?php echo $deon_row['url'] ? ' target="_blank" rel="noopener"' : ' aria-disabled="true" tabindex="-1"'; ?>
			   class="group/doc flex items-center gap-4 sm:gap-5 min-w-0 flex-1<?php echo $deon_row['url'] ? '' : ' pointer-events-none'; ?>">
				<span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 shrink-0 bg-deon-bg group-data-[inline]/panel:bg-white font-sans font-bold text-[13px] tracking-[0.5px] uppercase text-deon-accent" aria-hidden="true">
					<?php echo esc_html( $deon_row['badge'] ); ?>
				</span>
				<div class="flex flex-col gap-1 min-w-0">
					<span class="font-sans font-bold leading-snug text-deon-heading transition-colors group-hover/doc:text-deon-accent">
						<?php echo esc_html( $deon_row['title'] ); ?>
					</span>
					<?php if ( $deon_row['sub'] ) : ?>
						<span class="font-sans font-normal text-[12px] leading-[14px] tracking-[1.2px] uppercase text-deon-eyebrow">
							<?php echo esc_html( $deon_row['sub'] ); ?>
						</span>
					<?php endif; ?>
				</div>
			</a>
			<span class="ml-auto flex items-center gap-3 shrink-0 data-[view=grid]:ml-0 data-[view=grid]:mt-auto">
				<?php if ( $deon_row['meta'] ) : ?>
					<span class="hidden sm:inline font-sans font-normal text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-eyebrow">
						<?php echo esc_html( $deon_row['meta'] ); ?>
					</span>
				<?php endif; ?>
				<?php if ( $deon_row['downloadable'] ) : ?>
					<a href="<?php echo esc_url( $deon_row['url'] ? $deon_row['url'] : '#' ); ?>"<?php echo $deon_row['url'] ? ' target="_blank" rel="noopener"' : ''; ?>
					   class="inline-flex items-center justify-center w-10 h-10 border border-deon-border hover:bg-deon-heading/5 transition-colors"
					   aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'deon-energy' ), $deon_row['title'] ) ); ?>">
						<img src="<?php echo esc_url( $deon_uri . '/assets/img/investor-icon-external.svg' ); ?>" alt="" width="14" height="14" aria-hidden="true">
					</a>
					<a href="<?php echo esc_url( $deon_row['url'] ? $deon_row['url'] : '#' ); ?>"<?php echo $deon_row['url'] ? ' download' : ''; ?>
					   class="inline-flex items-center justify-center w-10 h-10 border border-deon-border hover:bg-deon-heading/5 transition-colors"
					   aria-label="<?php echo esc_attr( sprintf( __( 'Download %s', 'deon-energy' ), $deon_row['title'] ) ); ?>">
						<img src="<?php echo esc_url( $deon_uri . '/assets/img/investor-icon-download.svg' ); ?>" alt="" width="12" height="15" aria-hidden="true">
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( $deon_row['url'] ? $deon_row['url'] : '#' ); ?>"<?php echo $deon_row['url'] ? ' target="_blank" rel="noopener"' : ''; ?>
					   class="inline-flex items-center justify-center w-10 h-10 border border-deon-border hover:bg-deon-heading/5 transition-colors"
					   aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'deon-energy' ), $deon_row['title'] ) ); ?>">
						<img src="<?php echo esc_url( $deon_uri . '/assets/img/investor-icon-external.svg' ); ?>" alt="" width="14" height="14" aria-hidden="true">
					</a>
				<?php endif; ?>
			</span>
		</li>
		<?php
	endif;
};
?>

<section id="investor-documents" class="w-full bg-white border-b border-deon-border scroll-mt-[calc(var(--header-h)+24px)]">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-16 md:py-(--section-pad)">
		<div class="flex flex-col gap-8 lg:grid lg:grid-cols-[280px_1fr] lg:gap-8 lg:items-start" data-doc-app>

			<!-- CATEGORIES — sidebar filter on desktop, accordion below lg -->
			<aside class="border border-deon-border bg-white lg:sticky lg:top-[calc(var(--header-h)+24px)] lg:order-1" data-anim data-doc-nav>
				<div class="bg-deon-dark px-6 py-4">
					<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white">
						<?php esc_html_e( 'Categories', 'deon-energy' ); ?>
					</span>
				</div>
				<ul class="flex flex-col list-none m-0 p-0">
					<?php foreach ( $deon_groups as $deon_slug => $deon_group ) : ?>
						<?php $deon_on = ( $deon_slug === $deon_first_slug ); ?>
						<li class="border-t border-deon-divider first:border-t-0">
							<button type="button" data-doc-btn="doc-<?php echo esc_attr( $deon_slug ); ?>"
								aria-controls="doc-<?php echo esc_attr( $deon_slug ); ?>"
								aria-expanded="<?php echo $deon_on ? 'true' : 'false'; ?>"
								class="group flex w-full items-center justify-between gap-3 text-left appearance-none cursor-pointer px-6 py-3.5 font-sans text-[13px] leading-snug transition-colors border-0 border-l-[3px] hover:bg-deon-bg hover:text-deon-heading <?php echo $deon_on
									? 'bg-deon-bg border-deon-accent font-bold text-deon-heading'
									: 'bg-transparent border-transparent font-normal text-deon-accent'; ?>">
								<span><?php echo esc_html( $deon_group['name'] ); ?></span>
								<!-- Chevron is the accordion affordance; the desktop sidebar is a filter, not a disclosure. -->
								<svg class="lg:hidden shrink-0 transition-transform group-aria-expanded:rotate-180" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
									<path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="square"/>
								</svg>
							</button>
							<!-- Panel lands here below lg (main.js moves it out of the stage). -->
							<div data-doc-home="doc-<?php echo esc_attr( $deon_slug ); ?>"></div>
						</li>
					<?php endforeach; ?>
					<?php if ( ! $deon_groups ) : ?>
						<li class="border-t border-deon-divider first:border-t-0">
							<span class="block px-6 py-3.5 font-sans text-[13px] leading-snug text-deon-eyebrow">
								<?php esc_html_e( 'No documents yet', 'deon-energy' ); ?>
							</span>
						</li>
					<?php endif; ?>
				</ul>
			</aside>

			<!-- Main column -->
			<div class="flex flex-col gap-10 lg:order-2">

				<?php if ( $deon_groups ) : ?>
					<!-- Desktop-only header: active category name + grid/list toggle.
					     Below lg the accordion row itself names the category. -->
					<div class="hidden lg:flex items-center justify-between gap-4" data-anim>
						<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.5px] text-deon-heading" data-doc-heading>
							<?php echo esc_html( $deon_groups[ $deon_first_slug ]['name'] ); ?>
						</h2>
						<span class="inline-flex items-center gap-1 text-deon-eyebrow">
							<button type="button" data-doc-view="grid" aria-label="<?php esc_attr_e( 'Grid view', 'deon-energy' ); ?>"
								class="inline-flex items-center justify-center w-9 h-9 border border-deon-border bg-white text-deon-heading hover:bg-deon-bg transition-colors cursor-pointer">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="currentColor" aria-hidden="true"><rect x="0" y="0" width="6" height="6"/><rect x="8" y="0" width="6" height="6"/><rect x="0" y="8" width="6" height="6"/><rect x="8" y="8" width="6" height="6"/></svg>
							</button>
							<button type="button" data-doc-view="list" aria-label="<?php esc_attr_e( 'List view', 'deon-energy' ); ?>" aria-current="true"
								class="inline-flex items-center justify-center w-9 h-9 border border-deon-border bg-deon-dark text-white transition-colors cursor-pointer">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="currentColor" aria-hidden="true"><rect x="0" y="1" width="14" height="2"/><rect x="0" y="6" width="14" height="2"/><rect x="0" y="11" width="14" height="2"/></svg>
							</button>
						</span>
					</div>

					<!-- Stage — desktop home for the panels. main.js empties it below lg. -->
					<div class="flex flex-col" data-doc-stage>
						<?php foreach ( $deon_groups as $deon_slug => $deon_group ) : ?>
							<!-- data-inline is set by main.js while the panel sits inside the accordion. -->
							<div id="doc-<?php echo esc_attr( $deon_slug ); ?>" data-doc-panel
							     class="group/panel flex flex-col gap-5 data-[inline]:gap-0 data-[inline]:border-t data-[inline]:border-deon-divider data-[inline]:bg-deon-bg data-[inline]:px-4 data-[inline]:py-1">
								<?php if ( $deon_group['rows'] ) : ?>
								<!-- Tailwind preflight is off: without list-none/m-0/p-0 the UA's
							     40px padding-inline-start indents every document row. -->
							<ul class="flex flex-col list-none m-0 p-0 border-t border-deon-border group-data-[inline]/panel:border-t-0" data-doc-list>
									<?php foreach ( $deon_group['rows'] as $deon_row ) : $deon_render_doc_row( $deon_row ); endforeach; ?>
								</ul>
								<?php elseif ( empty( $deon_group['subgroups'] ) ) : ?>
								<div class="border-t border-deon-border group-data-[inline]/panel:border-t-0 py-10">
									<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-eyebrow">
										<?php esc_html_e( 'Document Not Available', 'deon-energy' ); ?>
									</span>
									<p class="mt-2 font-sans font-normal leading-relaxed text-deon-body/70">
										<?php esc_html_e( 'This document is not currently available. Please check back here for updates.', 'deon-energy' ); ?>
									</p>
								</div>
								<?php endif; ?>

								<?php if ( ! empty( $deon_group['subgroups'] ) ) : ?>
									<?php foreach ( $deon_group['subgroups'] as $deon_sub ) : ?>
									<div class="border-t border-deon-border group-data-[inline]/panel:border-t-0" data-doc-item>
										<details class="group/sub">
											<summary class="cursor-pointer list-none flex items-center justify-between gap-4 bg-deon-bg px-5 py-4 [&::-webkit-details-marker]:hidden">
												<span class="font-sans font-normal leading-snug text-deon-heading">
													<?php echo esc_html( $deon_sub['name'] ); ?>
												</span>
												<svg class="w-[12px] h-[12px] shrink-0 text-deon-heading transition-transform duration-200 group-open/sub:rotate-45" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
													<path d="M6 1v10M1 6h10"/>
												</svg>
											</summary>
											<div class="border-t border-deon-border">
												<?php if ( $deon_sub['rows'] ) : ?>
												<ul class="flex flex-col list-none m-0 p-0" data-doc-list>
													<?php foreach ( $deon_sub['rows'] as $deon_row ) : $deon_render_doc_row( $deon_row ); endforeach; ?>
												</ul>
												<?php else : ?>
												<div class="px-5 py-8">
													<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-eyebrow">
														<?php esc_html_e( 'Document Not Available', 'deon-energy' ); ?>
													</span>
													<p class="mt-2 font-sans font-normal leading-relaxed text-deon-body/70">
														<?php esc_html_e( 'This document is not currently available. Please check back here for updates.', 'deon-energy' ); ?>
													</p>
												</div>
												<?php endif; ?>
											</div>
										</details>
									</div>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="font-sans font-normal text-deon-body py-6">
						<?php esc_html_e( 'No investor documents published yet.', 'deon-energy' ); ?>
					</p>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>