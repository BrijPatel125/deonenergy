<?php
/**
 * Leadership — Founder feature blocks.
 *
 * deon_leader posts with display type "founder", ordered by menu_order. Each is
 * a large alternating feature row: full-color portrait on one side, and on the
 * other the name (with LinkedIn icon inline, if set), role eyebrow, headline
 * quote (orange rule), bio, and an optional custom button. Photo side flips
 * every other row. Falls back to two designed placeholders until real founders
 * are added.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_founders = array();

$deon_founder_q = new WP_Query( array(
	'post_type'      => 'deon_leader',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'no_found_rows'  => true,
	'meta_query'     => array(
		array(
			'key'   => '_deon_leader_type',
			'value' => 'founder',
		),
	),
) );

if ( $deon_founder_q->have_posts() ) {
	while ( $deon_founder_q->have_posts() ) {
		$deon_founder_q->the_post();
		$deon_id = get_the_ID();

		$deon_founders[] = array(
			'name'       => get_the_title(),
			'role'       => get_post_meta( $deon_id, '_deon_leader_role', true ),
			'quote'      => get_post_meta( $deon_id, '_deon_leader_quote', true ),
			'bio'        => get_post_meta( $deon_id, '_deon_leader_bio', true ),
			'linkedin'   => get_post_meta( $deon_id, '_deon_leader_linkedin', true ),
			'x'          => get_post_meta( $deon_id, '_deon_leader_x', true ),
			'link_label' => get_post_meta( $deon_id, '_deon_leader_link_label', true ),
			'link_url'   => get_post_meta( $deon_id, '_deon_leader_link_url', true ),
			'profile'    => get_post_meta( $deon_id, '_deon_leader_profile', true ),
			'img'        => get_the_post_thumbnail_url( $deon_id, 'large' ),
		);
	}
	wp_reset_postdata();
} else {
	// Design placeholders — replaced the moment real founders are added.
	$deon_founders = array(
		array(
			'name'  => __( 'Elias Thorne', 'deon-energy' ),
			'role'  => __( 'Co-Founder & CEO', 'deon-energy' ),
			'quote' => __( "The future of energy isn't just renewable; it's relentlessly efficient and systematically engineered.", 'deon-energy' ),
			'bio'   => __( 'Elias brings over two decades of engineering expertise to Deon Energy. His vision for scalable, high-yield renewable infrastructure has fundamentally shaped our approach to global energy challenges.', 'deon-energy' ),
			'linkedin' => '', 'x' => '', 'link_label' => '', 'link_url' => '', 'profile' => '',
			'img'   => DEON_URI . '/assets/img/leadership-founder-1.png',
		),
		array(
			'name'  => __( 'Marcus Chen', 'deon-energy' ),
			'role'  => __( 'Co-Founder & CTO', 'deon-energy' ),
			'quote' => __( 'Innovation is only valuable when applied with uncompromising precision.', 'deon-energy' ),
			'bio'   => __( 'Marcus leads our technological advancements, ensuring that every solar array and grid-management system we deploy operates at absolute peak performance through rigorous data analysis and system design.', 'deon-energy' ),
			'linkedin' => '', 'x' => '', 'link_label' => '', 'link_url' => '', 'profile' => '',
			'img'   => DEON_URI . '/assets/img/leadership-founder-2.png',
		),
	);
}
?>

<section class="w-full bg-white" aria-label="<?php esc_attr_e( 'Founders', 'deon-energy' ); ?>">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)
	            flex flex-col gap-16 md:gap-24">

		<?php foreach ( $deon_founders as $deon_i => $deon_f ) :
			// Alternate the portrait side: 1st row photo right, 2nd left, and so on.
			$deon_photo_right = 0 === ( $deon_i % 2 );
			?>
			<article class="grid grid-cols-1 gap-8 md:grid-cols-2 md:gap-[clamp(40px,5vw,80px)] md:items-center" data-anim>

				<!-- Portrait (card hugs the photo — no fixed aspect, so no empty space below) -->
				<div class="<?php echo $deon_photo_right ? 'md:order-2' : 'md:order-1'; ?>
				            rounded-[8px] overflow-hidden border border-deon-border bg-deon-warm">
					<?php if ( $deon_f['img'] ) : ?>
					<img src="<?php echo esc_url( $deon_f['img'] ); ?>"
					     alt="<?php echo esc_attr( $deon_f['name'] ); ?>"
					     class="block w-full h-auto object-cover"
					     loading="lazy" decoding="async">
					<?php endif; ?>
				</div>

				<!-- Text -->
				<div class="<?php echo $deon_photo_right ? 'md:order-1' : 'md:order-2'; ?> flex flex-col gap-5">
					<div class="flex flex-col gap-1.5">
						<div class="flex items-center gap-2.5">
							<h3 class="font-display font-bold text-h2 leading-tight tracking-[-0.5px] text-deon-heading">
								<?php echo esc_html( $deon_f['name'] ); ?>
							</h3>
							<?php if ( $deon_f['linkedin'] ) : ?>
							<a href="<?php echo esc_url( $deon_f['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer"
							   aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person's name. */ __( '%s on LinkedIn', 'deon-energy' ), $deon_f['name'] ) ); ?>"
							   class="shrink-0 text-deon-heading hover:text-deon-accent transition-colors">
								<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5A2.5 2.5 0 1 1 0 3.5a2.5 2.5 0 0 1 4.98 0zM.25 8.25h4.5V24h-4.5V8.25zM8.25 8.25h4.31v2.15h.06c.6-1.14 2.07-2.34 4.26-2.34 4.56 0 5.4 3 5.4 6.9V24h-4.5v-6.15c0-1.47-.03-3.36-2.05-3.36-2.05 0-2.36 1.6-2.36 3.25V24h-4.5V8.25z"/></svg>
							</a>
							<?php endif; ?>
						</div>
						<?php if ( $deon_f['role'] ) : ?>
						<p class="font-sans font-semibold text-eyebrow uppercase tracking-[0.12em] text-deon-accent">
							<?php echo esc_html( $deon_f['role'] ); ?>
						</p>
						<?php endif; ?>
					</div>

					<?php if ( $deon_f['quote'] ) : ?>
					<blockquote class="border-l-2 border-deon-accent pl-5 font-display text-h3 leading-snug text-deon-heading">
						&ldquo;<?php echo esc_html( $deon_f['quote'] ); ?>&rdquo;
					</blockquote>
					<?php endif; ?>

					<?php if ( $deon_f['bio'] ) : ?>
					<p class="font-sans text-lead leading-relaxed text-deon-body max-w-[640px]">
						<?php echo esc_html( $deon_f['bio'] ); ?>
					</p>
					<?php endif; ?>

					<?php if ( $deon_f['x'] || $deon_f['link_url'] || $deon_f['profile'] ) : ?>
					<div class="flex flex-wrap items-center gap-x-6 gap-y-3 pt-1">
						<?php if ( $deon_f['link_url'] ) : ?>
						<a href="<?php echo esc_url( $deon_f['link_url'] ); ?>"
						   class="inline-flex items-center gap-2 font-sans font-semibold text-eyebrow uppercase tracking-[0.1em] text-deon-heading hover:text-deon-accent transition-colors">
							<?php echo esc_html( $deon_f['link_label'] ? $deon_f['link_label'] : __( 'Read Profile', 'deon-energy' ) ); ?>
							<span aria-hidden="true">&rarr;</span>
						</a>
						<?php endif; ?>

						<?php if ( $deon_f['x'] ) : ?>
						<a href="<?php echo esc_url( $deon_f['x'] ); ?>" target="_blank" rel="noopener noreferrer"
						   aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person's name. */ __( '%s on X', 'deon-energy' ), $deon_f['name'] ) ); ?>"
						   class="text-deon-heading hover:text-deon-accent transition-colors">
							<svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.66l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
						</a>
						<?php endif; ?>

						<?php if ( $deon_f['profile'] ) : ?>
						<a href="<?php echo esc_url( $deon_f['profile'] ); ?>" target="_blank" rel="noopener noreferrer"
						   class="inline-flex items-center gap-2 font-sans font-semibold text-eyebrow uppercase tracking-[0.1em] text-deon-heading hover:text-deon-accent transition-colors">
							<?php esc_html_e( 'Download Profile', 'deon-energy' ); ?>
							<span aria-hidden="true">&darr;</span>
						</a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>

			</article>
		<?php endforeach; ?>

	</div>
</section>