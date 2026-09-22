<?php
/**
 * Homepage — Hero section (full-bleed background media + overlaid content).
 *
 * @package Deon_Energy
 */

$hero_type = function_exists( 'get_field' ) ? ( get_field( 'hero_type' ) ?: 'image' ) : 'image';

/**
 * Editable hero copy (ACF "Hero Section" → Content tab). Every field falls back
 * to the designed default, so the section renders unchanged until the client
 * types something in wp-admin.
 */
$hero_field = static function ( $name ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	$value = get_field( $name );
	return is_string( $value ) ? trim( $value ) : '';
};

$hero_eyebrow    = $hero_field( 'hero_eyebrow' );
$hero_heading    = $hero_field( 'hero_heading' );
$hero_btn1_label = $hero_field( 'hero_btn1_label' );
$hero_btn1_url   = $hero_field( 'hero_btn1_url' );
$hero_btn2_label = $hero_field( 'hero_btn2_label' );
$hero_btn2_url   = $hero_field( 'hero_btn2_url' );

$hero_eyebrow    = $hero_eyebrow ?: __( "Gujarat's Solar EPC Partner", 'deon-energy' );
$hero_btn1_label = $hero_btn1_label ?: __( 'Explore Our Projects', 'deon-energy' );
$hero_btn1_url   = $hero_btn1_url ?: home_url( '/projects/' );
$hero_btn2_label = $hero_btn2_label ?: __( 'Our Story', 'deon-energy' );
$hero_btn2_url   = $hero_btn2_url ?: home_url( '/about/' );

// Editor line breaks become <br>; the default already ships with them.
$hero_heading_html = $hero_heading
	? nl2br( esc_html( $hero_heading ), false )
	: wp_kses_post( __( "Building India's Renewable<br>Energy Future, One<br>Project at a Time", 'deon-energy' ) );
?>
<section class="hero hero--full" aria-label="<?php esc_attr_e( 'Introduction', 'deon-energy' ); ?>">

	<div class="hero__media">

		<?php if ( 'slider' === $hero_type ) : ?>

			<?php
			$slides = array();
			for ( $i = 1; $i <= 6; $i++ ) {
				$slide_img = get_field( 'hero_slide_' . $i );
				if ( $slide_img ) {
					$slides[] = $slide_img;
				}
			}
			?>
			<?php if ( $slides ) : ?>
			<div class="swiper hero__swiper" aria-label="<?php esc_attr_e( 'Hero image slider', 'deon-energy' ); ?>">
				<div class="swiper-wrapper">
					<?php foreach ( $slides as $img ) : ?>
					<div class="swiper-slide">
						<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy">
					</div>
					<?php endforeach; ?>
				</div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'deon-energy' ); ?>"></div>
				<div class="swiper-button-next" aria-label="<?php esc_attr_e( 'Next slide', 'deon-energy' ); ?>"></div>
			</div>
			<?php else : ?>
			<img class="hero__img"
				src="<?php echo esc_url( DEON_URI . '/assets/img/project-3.jpg' ); ?>"
				alt="<?php esc_attr_e( 'Deon Energy solar installation', 'deon-energy' ); ?>"
				loading="eager">
			<?php endif; ?>

		<?php elseif ( 'video' === $hero_type ) : ?>

			<?php
			$video_source = get_field( 'hero_video_source' ) ?: 'upload';
			$poster       = get_field( 'hero_video_poster' );
			?>

			<?php if ( 'upload' === $video_source ) :
				$video_url = get_field( 'hero_video_file' );
				if ( $video_url ) : ?>
			<video class="hero__video"
				autoplay muted loop playsinline
				<?php if ( $poster ) : ?>poster="<?php echo esc_url( $poster ); ?>"<?php endif; ?>>
				<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
			</video>
			<?php endif; ?>

			<?php else :
				$embed_url  = get_field( 'hero_video_embed' );
				$iframe_src = deon_hero_embed_url( $embed_url );
				if ( $iframe_src ) : ?>
			<iframe class="hero__video hero__video--embed"
				src="<?php echo esc_url( $iframe_src ); ?>"
				frameborder="0"
				allow="autoplay; encrypted-media"
				allowfullscreen
				loading="lazy"
				title="<?php esc_attr_e( 'Hero video', 'deon-energy' ); ?>"
			></iframe>
			<div class="hero__poster" aria-hidden="true"
				<?php if ( $poster ) : ?>style="background-image:url(<?php echo esc_url( $poster ); ?>)"<?php endif; ?>></div>
			<?php endif; ?>
			<?php endif; ?>

		<?php else : // image (default) ?>

			<?php $img = function_exists( 'get_field' ) ? get_field( 'hero_image' ) : null; ?>
			<?php if ( $img ) : ?>
			<img class="hero__img"
				src="<?php echo esc_url( $img['url'] ); ?>"
				alt="<?php echo esc_attr( $img['alt'] ); ?>"
				loading="eager">
			<?php else : ?>
			<img class="hero__img"
				src="<?php echo esc_url( DEON_URI . '/assets/img/project-3.jpg' ); ?>"
				alt="<?php esc_attr_e( 'Deon Energy solar installation', 'deon-energy' ); ?>"
				loading="eager">
			<?php endif; ?>

		<?php endif; ?>

	</div><!-- /.hero__media -->

	<div class="hero__scrim" aria-hidden="true"></div>

	<div class="hero__inner deon-container">
		<div class="hero__content">
			<div class="hero__intro">
				<p class="deon-eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
				<h1 class="hero__title"><?php echo $hero_heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></h1>
				<span class="deon-rule" aria-hidden="true"></span>
			</div>
			<div class="hero__actions">
				<a class="deon-btn deon-btn--dark" href="<?php echo esc_url( $hero_btn1_url ); ?>"><?php echo esc_html( $hero_btn1_label ); ?></a>
				<a class="deon-link hero__link" href="<?php echo esc_url( $hero_btn2_url ); ?>"><?php echo esc_html( $hero_btn2_label ); ?></a>
			</div>
		</div>
	</div>
</section>
