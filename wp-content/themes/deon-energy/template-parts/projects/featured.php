<?php
/**
 * Project Gallery — featured asset.
 *
 * Renders the first published deon_project (menu_order) with its real meta.
 * Falls back to the designed placeholder copy only while no projects exist —
 * same pattern as the leadership partials. Never present the placeholder
 * figures as client data.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_featured = get_posts(
	array(
		'post_type'      => 'deon_project',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
$deon_featured = ! empty( $deon_featured ) ? $deon_featured[0] : null;

$deon_placeholder_img = get_template_directory_uri() . '/assets/img/project-featured-main.jpg';
$deon_images          = array();
$deon_rows            = array();
$deon_link            = '';

if ( $deon_featured ) {
	$deon_id    = $deon_featured->ID;
	$deon_title = get_the_title( $deon_id );
	$deon_desc  = trim( wp_strip_all_tags( get_the_excerpt( $deon_id ) ) );
	$deon_link  = get_permalink( $deon_id );

	$deon_fields = function_exists( 'deon_project_spec_fields' ) ? deon_project_spec_fields() : array();
	foreach ( $deon_fields as $deon_key => $deon_field ) {
		$deon_val = get_post_meta( $deon_id, $deon_key, true );
		if ( '' !== trim( (string) $deon_val ) ) {
			$deon_rows[] = array( $deon_field[0], $deon_val );
		}
	}

	$deon_main = get_the_post_thumbnail_url( $deon_id, 'large' );
	if ( $deon_main ) {
		$deon_images[] = $deon_main;
	}
	$deon_gallery = function_exists( 'deon_project_gallery_ids' ) ? deon_project_gallery_ids( $deon_id ) : array();
	foreach ( $deon_gallery as $deon_gid ) {
		$deon_url = wp_get_attachment_image_url( $deon_gid, 'large' );
		if ( $deon_url && ! in_array( $deon_url, $deon_images, true ) ) {
			$deon_images[] = $deon_url;
		}
		if ( count( $deon_images ) >= 3 ) {
			break;
		}
	}
} else {
	/* Designed placeholder — shown only until real projects are published. */
	$deon_title = __( 'The Meridian Utility Complex', 'deon-energy' );
	$deon_desc  = __( 'Placeholder layout preview. Publish a project to feature its real capacity, location and photography here.', 'deon-energy' );
	$deon_images = array(
		$deon_placeholder_img,
		get_template_directory_uri() . '/assets/img/project-featured-detail-1.jpg',
		get_template_directory_uri() . '/assets/img/project-featured-detail-2.jpg',
	);
}

if ( empty( $deon_images ) ) {
	$deon_images[] = $deon_placeholder_img;
}
?>

<section class="w-full bg-white border-t border-deon-divider">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad) flex flex-col gap-10">

		<!-- Section label -->
		<div class="flex items-center gap-4" data-anim>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-accent shrink-0">
				<?php esc_html_e( 'Featured Asset', 'deon-energy' ); ?>
			</span>
			<div class="bg-deon-border h-px flex-1 min-w-0"></div>
		</div>

		<!-- Featured content grid -->
		<div class="flex flex-col gap-8 md:grid md:grid-cols-12 md:gap-8">

			<!-- Left — project details -->
			<div class="md:col-span-4 flex flex-col" data-anim>

				<h2 class="font-display font-extrabold text-h2 leading-[1.2] tracking-[-0.32px] text-deon-heading">
					<?php echo esc_html( $deon_title ); ?>
				</h2>

				<?php if ( $deon_desc ) : ?>
				<p class="font-sans font-normal text-lead leading-[1.7] text-deon-body pt-6 pb-8">
					<?php echo esc_html( $deon_desc ); ?>
				</p>
				<?php else : ?>
				<div class="pt-6"></div>
				<?php endif; ?>

				<?php foreach ( $deon_rows as $deon_i => $deon_row ) : ?>
				<div class="border-t border-deon-divider flex items-start justify-between gap-4 pt-[17px] pb-4
				            <?php echo ( count( $deon_rows ) - 1 === $deon_i ) ? 'border-b' : ''; ?>">
					<span class="font-sans font-bold text-[12px] leading-[16px] tracking-[1.2px] uppercase text-deon-body">
						<?php echo esc_html( $deon_row[0] ); ?>
					</span>
					<span class="font-sans font-bold text-[15px] leading-[24px] text-deon-heading text-right">
						<?php echo esc_html( $deon_row[1] ); ?>
					</span>
				</div>
				<?php endforeach; ?>

				<?php if ( $deon_link ) : ?>
				<a href="<?php echo esc_url( $deon_link ); ?>"
				   class="border border-deon-heading flex items-center justify-center px-1 py-[17px] mt-10
				          font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-heading text-center
				          transition-colors hover:bg-deon-dark hover:text-white!">
					<?php esc_html_e( 'View Project', 'deon-energy' ); ?>
				</a>
				<?php endif; ?>

			</div>

			<!-- Right — images -->
			<div class="md:col-span-8 flex flex-col gap-6" data-anim>

				<div class="bg-deon-warm relative overflow-hidden h-[220px] md:h-[316px] shrink-0 w-full">
					<img
						src="<?php echo esc_url( $deon_images[0] ); ?>"
						alt="<?php echo esc_attr( sprintf( /* translators: %s: project name. */ __( '%s — aerial view', 'deon-energy' ), $deon_title ) ); ?>"
						class="absolute inset-0 w-full h-full object-cover"
						loading="lazy"
					/>
				</div>

				<?php if ( count( $deon_images ) > 1 ) : ?>
				<div class="grid grid-cols-2 gap-6">
					<?php foreach ( array_slice( $deon_images, 1, 2 ) as $deon_sub ) : ?>
					<div class="bg-deon-warm relative overflow-hidden h-[200px] md:h-[300px]">
						<img
							src="<?php echo esc_url( $deon_sub ); ?>"
							alt="<?php echo esc_attr( sprintf( /* translators: %s: project name. */ __( '%s — detail view', 'deon-energy' ), $deon_title ) ); ?>"
							class="absolute inset-0 w-full h-full object-cover"
							loading="lazy"
						/>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

			</div>

		</div>
	</div>
</section>
