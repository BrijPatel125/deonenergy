<?php
/**
 * Leadership — Board of Directors grid.
 *
 * deon_leader posts with display type "board", ordered by menu_order, each via
 * the shared leader-card (3D flip: primary photo front, second photo — or the
 * same photo — on the back) — the same card as Core Team. Falls back to
 * designed placeholders until the client adds real directors.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_board = array();

$deon_board_q = new WP_Query( array(
	'post_type'      => 'deon_leader',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'no_found_rows'  => true,
	'meta_query'     => array(
		array(
			'key'   => '_deon_leader_type',
			'value' => 'board',
		),
	),
) );

if ( $deon_board_q->have_posts() ) {
	while ( $deon_board_q->have_posts() ) {
		$deon_board_q->the_post();
		$deon_id = get_the_ID();

		$deon_board[] = array(
			'name'     => get_the_title(),
			'role'     => get_post_meta( $deon_id, '_deon_leader_role', true ),
			'bio'      => get_post_meta( $deon_id, '_deon_leader_bio', true ),
			'photo'    => get_the_post_thumbnail_url( $deon_id, 'medium_large' ),
			'casual'   => function_exists( 'deon_leader_casual_photo' )
				? deon_leader_casual_photo( $deon_id )
				: (string) get_post_meta( $deon_id, '_deon_leader_casual', true ),
			'size'     => 'sm',
		);
	}
	wp_reset_postdata();
} else {
	// Design placeholders — replaced the moment real board members are added.
	for ( $deon_i = 1; $deon_i <= 4; $deon_i++ ) {
		$deon_board[] = array(
			'name'     => sprintf( /* translators: %d: placeholder index. */ __( 'Board Member %d', 'deon-energy' ), $deon_i ),
			'role'     => __( 'Board of Directors', 'deon-energy' ),
			'bio'      => '',
			'photo'    => DEON_URI . '/assets/img/leadership-team-' . $deon_i . '.png',
			'casual'   => '',
			'size'     => 'sm',
		);
	}
}
?>

<section class="w-full bg-deon-warm border-t border-deon-divider">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)
	            flex flex-col gap-8 md:gap-10">

		<div class="flex flex-col gap-3 max-w-[680px]" data-anim>
			<h2 class="font-display font-bold text-h2 leading-tight text-deon-heading">
				<?php esc_html_e( 'Board of Directors', 'deon-energy' ); ?>
			</h2>
			<p class="font-sans text-lead leading-relaxed text-deon-body">
				<?php esc_html_e( 'Independent oversight and strategic guidance for Deon Energy Limited.', 'deon-energy' ); ?>
			</p>
		</div>

		<div data-leader-paginate data-page-size="10" class="flex flex-col gap-8">
			<div class="grid grid-cols-2 gap-6 md:grid-cols-3 md:gap-8 lg:grid-cols-5" data-anim-stagger data-leader-track>
				<?php foreach ( $deon_board as $deon_m ) : ?>
					<?php get_template_part( 'template-parts/leadership/leader-card', null, $deon_m ); ?>
				<?php endforeach; ?>
			</div>
			<?php get_template_part( 'template-parts/leadership/leader-pagination' ); ?>
		</div>

	</div>
</section>