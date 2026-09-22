<?php
/**
 * Careers — Open Positions. Loops the deon_job CPT into designed position
 * cards. Jobs with a short description become expandable. Empty state points
 * applicants at the Contact page.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_uri  = get_template_directory_uri();
$deon_jobs = new WP_Query( array(
	'post_type'      => 'deon_job',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
$deon_count = (int) $deon_jobs->found_posts;

$deon_pin   = $deon_uri . '/assets/img/careers-icon-location.svg';
$deon_clock = $deon_uri . '/assets/img/careers-icon-clock.svg';
?>

<section class="w-full bg-white border-y border-deon-border">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-16 md:py-(--section-pad)">
		<div class="flex flex-col gap-10 md:gap-12">

			<!-- Header -->
			<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4" data-anim>
				<div class="flex flex-col items-start gap-4 max-w-[576px]">
					<h2 class="font-sans font-extrabold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
						<?php esc_html_e( 'Open Positions', 'deon-energy' ); ?>
					</h2>
					<p class="font-sans text-lead leading-relaxed text-deon-body">
						<?php esc_html_e( 'Roles across engineering, projects, O&M and finance — on the ground in Gujarat.', 'deon-energy' ); ?>
					</p>
				</div>
				<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-body md:pb-2">
					<?php
					/* translators: %d: number of open roles. */
					printf( esc_html( _n( '%d role available', '%d roles available', $deon_count, 'deon-energy' ) ), esc_html( $deon_count ) );
					?>
				</span>
			</div>

			<!-- Cards -->
			<div class="flex flex-col gap-4" data-anim-stagger>
				<?php if ( $deon_jobs->have_posts() ) : ?>
					<?php
					while ( $deon_jobs->have_posts() ) :
						$deon_jobs->the_post();
						$dept      = get_post_meta( get_the_ID(), '_deon_job_department', true );
						$location  = get_post_meta( get_the_ID(), '_deon_job_location', true );
						$type      = get_post_meta( get_the_ID(), '_deon_job_type', true );
						$permalink = get_permalink();
						?>
						<div class="careers-job-card bg-white border border-deon-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-[33px]">
							<div class="flex flex-col items-start gap-1">
								<?php if ( $dept ) : ?>
									<span class="font-sans text-[10px] leading-[15px] tracking-[1px] uppercase text-deon-accent">
										<?php echo esc_html( $dept ); ?>
									</span>
								<?php endif; ?>
								<h3 class="font-sans font-normal! text-h3 leading-snug text-deon-heading">
									<a href="<?php echo esc_url( $permalink ); ?>" class="transition-colors hover:text-deon-accent">
										<?php the_title(); ?>
									</a>
								</h3>
								<?php if ( $location || $type ) : ?>
									<div class="flex flex-wrap items-center gap-x-6 gap-y-1 pt-2">
										<?php if ( $location ) : ?>
											<span class="flex items-center gap-2">
												<img src="<?php echo esc_url( $deon_pin ); ?>" alt="" aria-hidden="true" style="width:10px;height:12px;">
												<span class="font-sans text-[12px] leading-[16px] text-deon-body"><?php echo esc_html( $location ); ?></span>
											</span>
										<?php endif; ?>
										<?php if ( $type ) : ?>
											<span class="flex items-center gap-2">
												<img src="<?php echo esc_url( $deon_clock ); ?>" alt="" aria-hidden="true" style="width:12px;height:12px;">
												<span class="font-sans text-[12px] leading-[16px] text-deon-body"><?php echo esc_html( $type ); ?></span>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
							<a href="<?php echo esc_url( $permalink ); ?>" class="shrink-0 inline-flex items-center justify-center bg-deon-accent py-3 px-8 font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white! transition-opacity hover:opacity-90">
								<?php esc_html_e( 'View Role & Apply', 'deon-energy' ); ?>
							</a>
						</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>

				<?php else : ?>
					<!-- Empty state -->
					<div class="bg-white border border-deon-border p-[33px] md:p-12 text-center">
						<p class="font-sans leading-relaxed text-deon-body">
							<?php esc_html_e( 'No current openings.', 'deon-energy' ); ?>
							<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="text-deon-accent! font-bold hover:underline">
								<?php esc_html_e( 'Email us', 'deon-energy' ); ?></a>
							<?php esc_html_e( "and we'll reach out when a matching role opens.", 'deon-energy' ); ?>
						</p>
					</div>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>