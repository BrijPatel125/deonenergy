<?php
/**
 * Fallback template (required by WordPress).
 *
 * @package Deon_Energy
 */

get_header();
?>

<section class="content-fallback">
	<div class="content-fallback__inner">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_title( '<h1 class="entry-title">', '</h1>' );
				the_content();
			}
		} else {
			esc_html_e( 'Nothing found.', 'deon-energy' );
		}
		?>
	</div>
</section>

<?php
get_footer();
