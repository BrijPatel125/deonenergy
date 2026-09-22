<?php
/**
 * Template Name: Leadership
 *
 * Page template — Leadership.
 *
 * Slug: leadership. Phase 2 (Tailwind v4). Founders + Core Team pull from the
 * deon_leader CPT; header strip and values section are static.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-white">
	<?php
	get_template_part( 'template-parts/leadership/header' );
	get_template_part( 'template-parts/leadership/founders' );
	get_template_part( 'template-parts/leadership/board' );
	get_template_part( 'template-parts/leadership/team' );
	get_template_part( 'template-parts/leadership/values' );
	?>
</main>

<?php get_footer(); ?>
