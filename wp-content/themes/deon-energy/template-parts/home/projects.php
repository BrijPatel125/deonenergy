<?php
/**
 * Homepage — Project Gallery preview (2×2 grid).
 *
 * Tiles come from the `deon_project` CPT via deon_featured_projects(); the text
 * column is editable in the ACF "Selected Work Section" box. Both fall back to
 * the designed defaults, so the section renders unchanged on a bare install.
 *
 * @package Deon_Energy
 */

$deon_field = static function ( $name ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	$value = get_field( $name );
	return is_string( $value ) ? trim( $value ) : '';
};

$deon_eyebrow   = $deon_field( 'home_projects_eyebrow' ) ?: __( 'Recent Projects', 'deon-energy' );
$deon_text      = $deon_field( 'home_projects_text' ) ?: __( 'A snapshot of installations Deon Energy has delivered for industrial and commercial customers in Gujarat.', 'deon-energy' );
$deon_btn_label = $deon_field( 'home_projects_btn_label' ) ?: __( 'View All Projects', 'deon-energy' );
$deon_btn_url   = $deon_field( 'home_projects_btn_url' ) ?: home_url( '/projects/' );

$deon_heading      = $deon_field( 'home_projects_heading' );
$deon_heading_html = $deon_heading
	? nl2br( esc_html( $deon_heading ), false )
	: wp_kses_post( __( 'Real projects.<br>Real sites.<br>Real generation.', 'deon-energy' ) );

// Real projects when they exist; the designed placeholders until then.
$deon_projects   = function_exists( 'deon_featured_projects' ) ? deon_featured_projects( 4 ) : array();
$deon_fallbacks  = array(
	array( 'label' => __( 'Industrial Hub, Ahmedabad', 'deon-energy' ),   'img' => 'project-1.jpg' ),
	array( 'label' => __( 'Solar-Wind Hybrid, Kutch', 'deon-energy' ),    'img' => 'project-2.jpg' ),
	array( 'label' => __( 'Manufacturing Plant, Surat', 'deon-energy' ),  'img' => 'project-3.jpg' ),
	array( 'label' => __( 'Utility Substation, Vadodara', 'deon-energy' ),'img' => 'project-4.jpg' ),
);
?>
<section class="projects deon-section" aria-label="<?php esc_attr_e( 'Selected projects', 'deon-energy' ); ?>">
	<div class="projects__inner deon-container">
		<div class="projects__lead">
			<p class="deon-eyebrow"><?php echo esc_html( $deon_eyebrow ); ?></p>
			<h2 class="projects__heading"><?php echo $deon_heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></h2>
			<p class="projects__text"><?php echo esc_html( $deon_text ); ?></p>
			<a class="deon-btn deon-btn--outline" href="<?php echo esc_url( $deon_btn_url ); ?>"><?php echo esc_html( $deon_btn_label ); ?></a>
		</div>

		<div class="projects__grid">
			<?php if ( $deon_projects ) : ?>
				<?php foreach ( $deon_projects as $deon_project ) :
					$deon_label = deon_project_tile_label( $deon_project->ID );
					$deon_capacity = trim( (string) get_post_meta( $deon_project->ID, '_deon_capacity', true ) );
					?>
					<figure class="project-tile">
						<a class="project-tile__link" href="<?php echo esc_url( get_permalink( $deon_project ) ); ?>">
							<?php
							if ( has_post_thumbnail( $deon_project ) ) {
								echo get_the_post_thumbnail(
									$deon_project,
									'large',
									array(
										'class'   => 'project-tile__img',
										'alt'     => esc_attr( $deon_label ),
										'loading' => 'lazy',
									)
								);
							} else {
								$deon_index = 1 + ( absint( $deon_project->ID ) % 4 );
								?>
								<img
									class="project-tile__img"
									src="<?php echo esc_url( DEON_URI . '/assets/img/project-' . $deon_index . '.jpg' ); ?>"
									alt="<?php echo esc_attr( $deon_label ); ?>"
									loading="lazy"
								>
								<?php
							}
							?>
							<span class="project-tile__overlay">
								<span class="project-tile__content">
									<?php if ( '' !== $deon_capacity ) : ?>
										<span class="project-tile__capacity"><?php echo esc_html( $deon_capacity ); ?></span>
									<?php endif; ?>
									<span class="project-tile__label"><?php echo esc_html( $deon_label ); ?></span>
									<span class="project-tile__cta">
										<?php esc_html_e( 'View project', 'deon-energy' ); ?>
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
									</span>
								</span>
							</span>
						</a>
					</figure>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ( $deon_fallbacks as $deon_placeholder ) : ?>
					<figure class="project-tile">
						<img
							class="project-tile__img"
							src="<?php echo esc_url( DEON_URI . '/assets/img/' . $deon_placeholder['img'] ); ?>"
							alt="<?php echo esc_attr( $deon_placeholder['label'] ); ?>"
						>
						<div class="project-tile__overlay">
							<span class="project-tile__label"><?php echo esc_html( $deon_placeholder['label'] ); ?></span>
						</div>
					</figure>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>