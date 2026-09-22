<?php
/**
 * Single Job — deon_job CPT detail view.
 *
 * Built from the approved reference "Careers_ Senior Solar Project Engineer"
 * (change log v1). Phase 2 (Tailwind v4). Shared inner-page hero + two-column
 * body (About the Role + Role Analytics sidebar) + Apply Now form section.
 *
 * The deon_job CPT is registered public with a `careers/role` rewrite so these
 * singles resolve. Meta rendered: department, location, type; the post editor
 * body carries the "About the Role" copy (client writes responsibilities /
 * requirements as sub-headings). Falls back to the short card description.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="bg-deon-bg">
	<?php
	while ( have_posts() ) :
		the_post();

		$deon_dept     = get_post_meta( get_the_ID(), '_deon_job_department', true );
		$deon_location = get_post_meta( get_the_ID(), '_deon_job_location', true );
		$deon_type     = get_post_meta( get_the_ID(), '_deon_job_type', true );

		// Inline fact row under the hero title: location · department · type.
		$deon_meta = array_values( array_filter( array( $deon_location, $deon_dept, $deon_type ) ) );

		get_template_part( 'template-parts/global/page-hero', null, array(
			'eyebrow' => $deon_dept
				? sprintf( '%1$s › %2$s', __( 'Careers', 'deon-energy' ), $deon_dept )
				: __( 'Careers', 'deon-energy' ),
			'title'   => get_the_title(),
			'meta'    => $deon_meta,
			'bg'      => 'offwhite',
		) );

		get_template_part( 'template-parts/job/detail' );
		get_template_part( 'template-parts/job/apply' );

	endwhile;
	?>
</main>

<?php
get_footer();
