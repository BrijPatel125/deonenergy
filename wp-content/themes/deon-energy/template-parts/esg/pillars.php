<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_img = get_template_directory_uri() . '/assets/img/';

$deon_esg_pillars = array(
	array(
		'icon'    => 'esg-icon-clean-energy.svg',
		'iw'      => '30', 'ih' => '30',
		'title'   => __( 'Clean Energy Delivery', 'deon-energy' ),
		'desc'    => __( 'Every plant we build and operate replaces grid electricity with on-site solar generation. This is the primary mechanism by which our work reduces emissions — not marketing language, but measured units of energy produced and reported.', 'deon-energy' ),
		'img'     => 'esg-pillar-1.png',
		'setting' => 'deon_img_esg_pillar_1',
	),
	array(
		'icon'    => 'esg-icon-circular.svg',
		'iw'      => '32', 'ih' => '32',
		'title'   => __( 'Responsible Site Practices', 'deon-energy' ),
		'desc'    => __( 'Structured HSE protocols, use of local skilled labour where possible, and site-cleanup standards at handover. The environmental footprint of building a plant is real; we work to keep it small.', 'deon-energy' ),
		'img'     => 'esg-pillar-2.png',
		'setting' => 'deon_img_esg_pillar_2',
	),
	array(
		'icon'    => 'esg-icon-carbon.svg',
		'iw'      => '28', 'ih' => '14',
		'title'   => __( 'End-of-Life Awareness', 'deon-energy' ),
		'desc'    => __( 'Solar modules and inverters are long-life assets, but they will eventually need decommissioning and recycling. We follow evolving MNRE and central pollution board guidance for module handling and end-of-life disposition.', 'deon-energy' ),
		'img'     => 'esg-pillar-3.png',
		'setting' => 'deon_img_esg_pillar_3',
	),
);

?>

<section class="w-full bg-white">
	<div class="max-w-[1280px] mx-auto px-4 py-[80px] flex flex-col gap-[48px]
	            md:px-16 md:py-(--section-pad) md:gap-12">

		<!-- Header row -->
		<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between md:gap-8" data-anim>
			<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.32px] text-deon-heading
			           md:whitespace-nowrap">
				<?php esc_html_e( 'Strategic Pillars', 'deon-energy' ); ?>
			</h2>
			<div class="hidden md:block flex-1 h-px bg-deon-border"></div>
		</div>

		<!-- Cards -->
		<div class="grid grid-cols-1 gap-6 md:grid-cols-3" data-anim-stagger>
			<?php foreach ( $deon_esg_pillars as $card ) : ?>
				<div class="bg-white border border-deon-border p-[33px] flex flex-col gap-4">
					<img src="<?php echo esc_url( $deon_img . $card['icon'] ); ?>" alt=""
					     width="<?php echo esc_attr( $card['iw'] ); ?>" height="<?php echo esc_attr( $card['ih'] ); ?>"
					     class="block" style="width:<?php echo esc_attr( $card['iw'] ); ?>px;height:auto">
					<h3 class="font-sans font-normal text-h3 leading-snug text-deon-heading pt-2">
						<?php echo esc_html( $card['title'] ); ?>
					</h3>
					<p class="font-sans font-normal leading-relaxed text-deon-body">
						<?php echo esc_html( $card['desc'] ); ?>
					</p>
					<div class="mt-auto pt-4">
						<img src="<?php echo esc_url( deon_site_image_url( $card['setting'], $card['img'] ) ); ?>" alt=""
						     class="w-full h-[176px] object-cover" width="291" height="176">
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>