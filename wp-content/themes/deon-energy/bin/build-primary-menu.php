<?php
/**
 * Build the approved primary navigation menu (change log v1).
 *
 * Rebuilds the menu assigned to the `primary` theme location to the approved
 * 7-item structure with nested dropdowns. Run once per environment:
 *
 *   wp eval-file wp-content/themes/deon-energy/bin/build-primary-menu.php
 *
 * Idempotent — wipes the current primary menu's items and rebuilds. Pages are
 * resolved by slug (domain-independent); custom links are stored relative so
 * they render correctly on any host. The client can edit everything afterward
 * in Appearance → Menus.
 *
 * Icons and dropdown descriptions are NOT set here — the theme derives them
 * from each linked item's slug (see deon_nav_item_meta() in functions.php).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Run via WP-CLI: wp eval-file wp-content/themes/deon-energy/bin/build-primary-menu.php\n" );
	return;
}

/*
 * Approved structure. Each top-level entry:
 *   label     — menu label (overrides the page title)
 *   page      — page slug to link (resolved to ID), or null for a custom URL
 *   url       — relative URL when `page` is null
 *   children  — array of the same shape, one level deep
 *
 * Page slugs match this build's pages; a slug that resolves to nothing is
 * skipped with a warning rather than creating a broken link.
 */
$deon_menu = array(
	array( 'label' => 'About Deon', 'page' => 'about', 'children' => array(
		array( 'label' => 'About Us',   'page' => 'about' ),
		array( 'label' => 'Leadership', 'page' => 'leadership' ),
	) ),
	// Plain link, no dropdown (client request) — the six capability sections are
	// reached by scrolling the Solutions page itself.
	array( 'label' => 'Solutions', 'page' => 'solutions' ),
	// Plain link, no dropdown (client request) — the gallery's own filter chips
	// cover project categories, so the header needs no submenu.
	array( 'label' => 'Projects', 'page' => 'projects' ),
	array( 'label' => 'ESG', 'page' => 'esg' ),
	// Careers sits under Resources (client request), not under About Deon.
	array( 'label' => 'Resources', 'page' => 'knowledge-hub', 'children' => array(
		array( 'label' => 'Blogs',          'page' => 'knowledge-hub' ),
		array( 'label' => 'News and Media', 'page' => 'news-media' ),
		array( 'label' => 'Careers',        'page' => 'careers' ),
		array( 'label' => 'FAQs',           'page' => 'faq' ),
	) ),
	// Single investor page → direct link, no dropdown (per change log).
	array( 'label' => 'Investors', 'page' => 'investor-relations' ),
	array( 'label' => 'Contact',   'page' => 'contact' ),
	// Solar Calculator is deliberately NOT in the header (client request); it is
	// still reachable from the footer menu.
);

// --- Resolve the menu assigned to the `primary` location, or create one. ---
$deon_locations = get_nav_menu_locations();
$deon_menu_obj  = false;

if ( ! empty( $deon_locations['primary'] ) ) {
	$deon_menu_obj = wp_get_nav_menu_object( $deon_locations['primary'] );
}
if ( ! $deon_menu_obj ) {
	$deon_menu_obj = wp_get_nav_menu_object( 'Header' );
}
if ( ! $deon_menu_obj ) {
	$deon_new_id = wp_create_nav_menu( 'Header' );
	if ( is_wp_error( $deon_new_id ) ) {
		WP_CLI::error( 'Could not create menu: ' . $deon_new_id->get_error_message() );
	}
	$deon_menu_obj = wp_get_nav_menu_object( $deon_new_id );
}
$deon_menu_id = (int) $deon_menu_obj->term_id;

// Assign it to the primary location.
$deon_locations['primary'] = $deon_menu_id;
set_theme_mod( 'nav_menu_locations', $deon_locations );

// --- Wipe existing items. ---
foreach ( wp_get_nav_menu_items( $deon_menu_id ) as $deon_old ) {
	wp_delete_post( $deon_old->ID, true );
}

/**
 * Add one menu item. Returns the new item's post ID, or 0 on skip.
 *
 * @param int   $menu_id Menu term ID.
 * @param array $item    Item spec (label + page|url).
 * @param int   $parent  Parent menu-item ID (0 for top level).
 */
function deon_add_menu_item( $menu_id, $item, $parent = 0 ) {
	$args = array(
		'menu-item-title'   => $item['label'],
		'menu-item-status'  => 'publish',
		'menu-item-parent-id' => $parent,
	);

	if ( ! empty( $item['page'] ) ) {
		$page = get_page_by_path( $item['page'] );
		if ( ! $page ) {
			WP_CLI::warning( "Skipped '{$item['label']}' — no page with slug '{$item['page']}'." );
			return 0;
		}
		$args['menu-item-object']    = 'page';
		$args['menu-item-object-id'] = $page->ID;
		$args['menu-item-type']      = 'post_type';
	} else {
		$args['menu-item-url']  = $item['url'];
		$args['menu-item-type'] = 'custom';
	}

	$id = wp_update_nav_menu_item( $menu_id, 0, $args );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "Failed '{$item['label']}': " . $id->get_error_message() );
		return 0;
	}
	return (int) $id;
}

// --- Build. ---
$deon_top = 0;
$deon_sub = 0;
foreach ( $deon_menu as $deon_item ) {
	$deon_parent_id = deon_add_menu_item( $deon_menu_id, $deon_item, 0 );
	if ( ! $deon_parent_id ) {
		continue;
	}
	$deon_top++;
	if ( empty( $deon_item['children'] ) ) {
		continue;
	}
	foreach ( $deon_item['children'] as $deon_child ) {
		if ( deon_add_menu_item( $deon_menu_id, $deon_child, $deon_parent_id ) ) {
			$deon_sub++;
		}
	}
}

WP_CLI::success( sprintf(
	'Primary menu "%s" rebuilt — %d top-level, %d dropdown items. Editable under Appearance → Menus.',
	$deon_menu_obj->name,
	$deon_top,
	$deon_sub
) );
