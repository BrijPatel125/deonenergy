<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_img = get_template_directory_uri() . '/assets/img/';

$deon_esg_impact = array(
	array(
		'n'     => '01',
		'title' => __( 'Local Employment', 'deon-energy' ),
		'desc'  => __( 'Site work is delivered largely with local skilled workers, supporting income in the regions where our plants are built.', 'deon-energy' ),
	),
	array(
		'n'     => '02',
		'title' => __( 'Skill Development', 'deon-energy' ),
		'desc'  => __( 'Structured on-site safety and technical training for the technicians who install, commission and maintain our plants.', 'deon-energy' ),
	),
	array(
		'n'     => '03',
		'title' => __( 'Long-Term Presence', 'deon-energy' ),
		'desc'  => __( 'O&M contracts mean we stay involved in the regions we build in — not just during construction, but for years afterwards.', 'deon-energy' ),
	),
);

?>

<section class="w-full bg-deon-warm">
	<div class="max-w-[1280px] mx-auto px-4 py-[80px] flex flex-col gap-[48px]
	            md:px-16 md:py-(--section-pad) md:grid md:grid-cols-12 md:gap-6 md:items-center">

		<!-- Left: copy + numbered list -->
		<div class="flex flex-col gap-8 md:col-span-5" data-anim>
			<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
				<?php esc_html_e( 'Community & People', 'deon-energy' ); ?>
			</h2>
			<p class="font-sans font-normal text-lead leading-relaxed text-deon-body">
				<?php esc_html_e( 'Beyond generation, we contribute where our operations touch communities.', 'deon-energy' ); ?>
			</p>
			<div class="flex flex-col gap-8 pt-4">
				<?php foreach ( $deon_esg_impact as $item ) : ?>
					<div class="flex gap-6 items-start">
						<div class="bg-deon-dark text-white font-sans font-bold text-[18px] leading-[18px] p-[12px] shrink-0">
							<?php echo esc_html( $item['n'] ); ?>
						</div>
						<div class="flex flex-col gap-2">
							<h4 class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-heading">
								<?php echo esc_html( $item['title'] ); ?>
							</h4>
							<p class="font-sans font-normal leading-relaxed text-deon-body">
								<?php echo esc_html( $item['desc'] ); ?>
							</p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Right: staggered image pair -->
		<div class="grid grid-cols-2 gap-4 items-start md:col-span-7" data-anim>
			<div class="bg-[#e4e2de] border border-deon-border p-px overflow-hidden">
				<img src="<?php echo esc_url( deon_site_image_url( 'deon_img_esg_community_1', 'esg-community-1.png' ) ); ?>" alt=""
				     class="w-full h-[240px] object-cover grayscale md:h-[320px]" width="311" height="320">
			</div>
			<div class="bg-[#e4e2de] border border-deon-border p-px overflow-hidden mt-12">
				<img src="<?php echo esc_url( deon_site_image_url( 'deon_img_esg_community_2', 'esg-community-2.png' ) ); ?>" alt=""
				     class="w-full h-[240px] object-cover grayscale md:h-[320px]" width="311" height="320">
			</div>
		</div>

	</div>
</section>