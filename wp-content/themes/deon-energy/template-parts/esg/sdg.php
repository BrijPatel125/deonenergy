<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_esg_goals = array(
	array( 'n' => '7',  'color' => '#fdb713', 'title' => __( 'Affordable & Clean Energy', 'deon-energy' ),        'desc' => __( 'Direct contribution through commercial, industrial and utility-scale solar generation.', 'deon-energy' ) ),
	array( 'n' => '9',  'color' => '#f36d25', 'title' => __( 'Industry, Innovation & Infrastructure', 'deon-energy' ), 'desc' => __( 'Building resilient distributed energy infrastructure for Indian industry.', 'deon-energy' ) ),
	array( 'n' => '11', 'color' => '#f99d26', 'title' => __( 'Sustainable Cities & Communities', 'deon-energy' ), 'desc' => __( 'Enabling urban and industrial facilities to reduce their emissions footprint.', 'deon-energy' ) ),
	array( 'n' => '12', 'color' => '#bf8b2e', 'title' => __( 'Responsible Consumption & Production', 'deon-energy' ), 'desc' => __( 'Responsible sourcing, safe site practices and evolving end-of-life protocols for equipment.', 'deon-energy' ) ),
	array( 'n' => '13', 'color' => '#3f7e44', 'title' => __( 'Climate Action', 'deon-energy' ),                   'desc' => __( 'Displacing grid emissions through renewable generation, one project at a time.', 'deon-energy' ) ),
);

?>

<section class="w-full bg-white">
	<div class="max-w-[1280px] mx-auto px-4 py-[80px] flex flex-col gap-[48px]
	            md:px-16 md:py-(--section-pad) md:gap-12">

		<!-- Heading -->
		<div class="flex flex-col items-center gap-4 text-center" data-anim>
			<h2 class="font-sans font-extrabold text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
				<?php esc_html_e( 'Alignment with the UN Sustainable Development Goals', 'deon-energy' ); ?>
			</h2>
			<p class="font-sans font-normal text-lead leading-relaxed text-deon-body max-w-[576px]">
				<?php esc_html_e( 'Our work contributes primarily to the following UN SDGs.', 'deon-energy' ); ?>
			</p>
		</div>

		<!-- Goal cards -->
		<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5 md:gap-6" data-anim-stagger>
			<?php foreach ( $deon_esg_goals as $goal ) : ?>
				<div class="bg-deon-bg border border-deon-border p-[33px] flex flex-col items-center text-center">
					<div class="size-[64px] flex items-center justify-center font-sans font-bold text-[24px] leading-[32px] text-white mb-6"
					     style="background-color:<?php echo esc_attr( $goal['color'] ); ?>">
						<?php echo esc_html( $goal['n'] ); ?>
					</div>
					<h4 class="font-sans font-normal text-[11px] leading-[16.5px] tracking-[1.65px] uppercase text-deon-heading">
						<?php echo esc_html( $goal['title'] ); ?>
					</h4>
					<div class="w-[32px] h-px bg-deon-accent my-4"></div>
					<p class="font-sans font-normal text-[12px] leading-[19.5px] text-deon-body">
						<?php echo esc_html( $goal['desc'] ); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
