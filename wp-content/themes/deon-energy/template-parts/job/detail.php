<?php
/**
 * Job detail — body. Two-column: left "About the Role" (post editor body, with
 * `.deon-prose` styling; client writes Core Responsibilities / Professional
 * Requirements as sub-headings) + "The Deon Workspace" brand block; right a
 * "Role Analytics" sidebar card (department / type / location from meta) with
 * an INITIATE APPLICATION button that jumps to the Apply Now form.
 *
 * Must run inside the loop (single-deon_job.php) — relies on the global post.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_uri       = get_template_directory_uri();
$deon_type      = get_post_meta( get_the_ID(), '_deon_job_type', true );
$deon_seniority = get_post_meta( get_the_ID(), '_deon_job_seniority', true );
$deon_travel    = get_post_meta( get_the_ID(), '_deon_job_travel', true );
$deon_desc      = get_post_meta( get_the_ID(), '_deon_job_desc', true );

// Role Analytics sidebar rows (PDF: Seniority / Type / Travel) — skip blanks.
$deon_analytics = array();
if ( $deon_seniority ) {
	$deon_analytics[] = array( 'label' => __( 'Seniority', 'deon-energy' ), 'value' => $deon_seniority );
}
if ( $deon_type ) {
	$deon_analytics[] = array( 'label' => __( 'Type', 'deon-energy' ), 'value' => $deon_type );
}
if ( $deon_travel ) {
	$deon_analytics[] = array( 'label' => __( 'Travel', 'deon-energy' ), 'value' => $deon_travel );
}

// Core Responsibilities: one bullet per non-empty line.
$deon_respons = array_values( array_filter( array_map(
	'trim',
	preg_split( '/\r\n|\r|\n/', (string) get_post_meta( get_the_ID(), '_deon_job_responsibilities', true ) )
) ) );

// Professional Requirements: each line "LABEL | body" → labeled card.
$deon_requires = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) get_post_meta( get_the_ID(), '_deon_job_requirements', true ) ) as $deon_line ) {
	$deon_line = trim( $deon_line );
	if ( '' === $deon_line ) {
		continue;
	}
	$deon_parts = array_map( 'trim', explode( '|', $deon_line, 2 ) );
	$deon_requires[] = array(
		'label' => $deon_parts[0],
		'body'  => isset( $deon_parts[1] ) ? $deon_parts[1] : '',
	);
}

$deon_benefits = array(
	__( 'Carbon-Neutral HQ', 'deon-energy' ),
	__( 'Professional Development Fund', 'deon-energy' ),
	__( 'Wellness Program', 'deon-energy' ),
);

$deon_has_body = '' !== trim( (string) get_the_content() );
?>

<section class="w-full bg-white border-y border-deon-border">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-16 md:py-(--section-pad)">
		<div class="md:grid md:grid-cols-12 md:gap-x-12 md:items-start">

			<!-- Main column -->
			<div class="md:col-span-8 flex flex-col gap-14" data-anim>

				<!-- About the Role -->
				<div>
					<h2 class="flex items-center gap-3 font-sans font-extrabold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
						<span aria-hidden="true" class="inline-block w-1 h-7 bg-deon-accent"></span>
						<?php esc_html_e( 'About the Role', 'deon-energy' ); ?>
					</h2>
					<div class="deon-prose mt-6 max-w-[720px]">
						<?php
						if ( $deon_has_body ) {
							the_content();
						} elseif ( $deon_desc ) {
							echo '<p>' . esc_html( $deon_desc ) . '</p>';
						} else {
							echo '<p>' . esc_html__( 'A detailed role description is being finalised. Reach out to our talent team to learn more about this opportunity.', 'deon-energy' ) . '</p>';
						}
						?>
					</div>
				</div>

				<?php if ( $deon_respons ) : ?>
					<!-- Core Responsibilities -->
					<div>
						<h2 class="font-sans font-extrabold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
							<?php esc_html_e( 'Core Responsibilities', 'deon-energy' ); ?>
						</h2>
						<ul class="list-none m-0 p-0 mt-6 flex flex-col gap-4 max-w-[720px]">
							<?php foreach ( $deon_respons as $deon_item ) : ?>
								<li class="flex items-start gap-3 font-sans leading-relaxed text-deon-body">
									<span aria-hidden="true" class="mt-1.5 inline-block w-2 h-2 shrink-0 bg-deon-accent"></span>
									<span><?php echo esc_html( $deon_item ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( $deon_requires ) : ?>
					<!-- Professional Requirements -->
					<div>
						<h2 class="font-sans font-extrabold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
							<?php esc_html_e( 'Professional Requirements', 'deon-energy' ); ?>
						</h2>
						<div class="mt-6 flex flex-col gap-4 max-w-[720px]">
							<?php foreach ( $deon_requires as $deon_req ) : ?>
								<div class="bg-deon-bg border border-deon-border p-6">
									<h3 class="font-sans font-bold text-[11px] leading-[12px] tracking-[1px] uppercase text-deon-accent">
										<?php echo esc_html( $deon_req['label'] ); ?>
									</h3>
									<?php if ( '' !== $deon_req['body'] ) : ?>
										<p class="mt-3 font-sans leading-relaxed text-deon-body">
											<?php echo esc_html( $deon_req['body'] ); ?>
										</p>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- The Deon Workspace -->
				<div class="flex flex-col gap-6">
					<h2 class="font-sans font-extrabold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
						<?php esc_html_e( 'The Deon Workspace', 'deon-energy' ); ?>
					</h2>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-8 items-center">
						<div class="border border-deon-border overflow-hidden">
							<img src="<?php echo esc_url( $deon_uri . '/assets/img/careers-life.jpg' ); ?>"
							     alt="<?php esc_attr_e( 'Inside a Deon Energy workspace', 'deon-energy' ); ?>"
							     class="block w-full h-[240px] object-cover" loading="lazy">
						</div>
						<div class="flex flex-col items-start gap-4">
							<h3 class="font-sans font-normal! text-h3 leading-snug text-deon-heading">
								<?php esc_html_e( 'Institutional Excellence', 'deon-energy' ); ?>
							</h3>
							<p class="font-sans leading-relaxed text-deon-body">
								<?php esc_html_e( 'We foster an environment of "Quiet Intensity." Our team members enjoy private, focus-optimized workspaces, access to premier global industry summits, and a flat hierarchy that values intellectual merit over tenure.', 'deon-energy' ); ?>
							</p>
							<ul class="list-none m-0 p-0 flex flex-wrap gap-2 pt-2">
								<?php foreach ( $deon_benefits as $deon_benefit ) : ?>
									<li class="bg-deon-bg border border-deon-border px-3 py-2 font-sans font-bold text-[10px] leading-[12px] tracking-[1px] uppercase text-deon-body">
										<?php echo esc_html( $deon_benefit ); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>

			</div>

			<!-- Sidebar -->
			<aside class="md:col-span-4 md:col-start-9 mt-10 md:mt-0 md:sticky md:top-28 flex flex-col gap-4" data-anim>
				<div class="bg-deon-accent/10 p-8 flex flex-col gap-5">
					<h3 class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-heading">
						<?php esc_html_e( 'Role Analytics', 'deon-energy' ); ?>
					</h3>
					<span aria-hidden="true" class="block h-px bg-deon-accent/30"></span>
					<?php foreach ( $deon_analytics as $deon_row ) : ?>
						<div class="flex items-start justify-between gap-4">
							<span class="font-sans text-[11px] leading-[16px] tracking-[1px] uppercase text-deon-body">
								<?php echo esc_html( $deon_row['label'] ); ?>
							</span>
							<span class="font-sans font-bold leading-snug text-deon-heading text-right">
								<?php echo esc_html( $deon_row['value'] ); ?>
							</span>
						</div>
					<?php endforeach; ?>
					<a href="#apply" class="mt-2 w-full inline-flex items-center justify-center bg-deon-dark py-4 px-8 font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white! transition-opacity hover:opacity-90">
						<?php esc_html_e( 'Initiate Application', 'deon-energy' ); ?>
					</a>
				</div>
				<p class="bg-deon-bg border border-deon-border p-6 font-sans text-[10px] leading-[18px] tracking-[0.5px] uppercase text-deon-body opacity-60">
					<?php esc_html_e( 'Deon Energy Limited is an equal opportunity employer committed to structural transparency and intellectual diversity.', 'deon-energy' ); ?>
				</p>
			</aside>

		</div>
	</div>
</section>
