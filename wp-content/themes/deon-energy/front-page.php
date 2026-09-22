<?php
/**
 * Homepage template (Phase 1 deliverable).
 *
 * Composed from section partials in template-parts/home/.
 * A partial that does not yet exist is silently skipped.
 *
 * @package Deon_Energy
 */

get_header();

$deon_home_sections = array(
	'hero',
	'stats',
	'why-deon',
	'services',
	'projects',
	'about',
	'why-solar',
	'logos',
	'articles',
	'cta',
);

foreach ( $deon_home_sections as $deon_section ) {
	get_template_part( 'template-parts/home/' . $deon_section );
}

get_footer();
