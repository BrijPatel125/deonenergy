<?php
/**
 * Global header — shared across all pages (SOW: global top navigation).
 *
 * @package Deon_Energy
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
/*
 * Page loading UI. Both pieces stay display:none until deon_js_flag() marks
 * <html> with `deon-js`, so a JS-less browser never gets stuck behind a
 * curtain it can never dismiss.
 *
 *  1. Preloader  — full-screen brand curtain on first paint, faded out by
 *     main.js on window load (hard 6s failsafe).
 *  2. Progress bar — thin accent bar at the top of the viewport shown while
 *     the browser fetches the next page (link click / form submit).
 */
$deon_logo_src = DEON_URI . '/assets/img/deon-logo.png';
?>
<div class="deon-preloader" data-preloader role="status">
	<span class="deon-visually-hidden"><?php esc_html_e( 'Loading…', 'deon-energy' ); ?></span>
	<div class="deon-preloader__inner" aria-hidden="true">
		<img class="deon-preloader__logo" src="<?php echo esc_url( $deon_logo_src ); ?>" alt="" width="149" height="35">
		<span class="deon-preloader__track"><span class="deon-preloader__fill"></span></span>
	</div>
</div>

<div class="deon-progress" data-nav-progress aria-hidden="true"><span class="deon-progress__bar"></span></div>

<header class="site-header" data-site-header>
	<div class="site-header__inner">
		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				printf(
					'<img src="%s" alt="%s" width="149" height="35">',
					esc_url( DEON_URI . '/assets/img/deon-logo.png' ),
					esc_attr( get_bloginfo( 'name' ) )
				);
			}
			?>
		</a>

		<button class="nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Toggle menu', 'deon-energy' ); ?>" aria-expanded="false" aria-controls="site-primary-nav" data-nav-toggle>
			<span></span><span></span><span></span>
		</button>

		<?php
		/*
		 * Nav + actions are wrapped so the mobile breakpoint can slide them in
		 * together as one off-canvas drawer (client request: match solex.in).
		 * On desktop the wrapper is `display: contents`, so both children stay
		 * direct flex items of .site-header__inner and the layout is unchanged.
		 */
		?>
		<div class="site-header__panel" data-nav-panel>

			<div class="nav-drawer__head">
				<img class="nav-drawer__logo"
				     src="<?php echo esc_url( DEON_URI . '/assets/img/deon-logo-white.png' ); ?>"
				     alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="2374" height="563">
				<button class="nav-drawer__close" type="button" data-nav-close
				        aria-label="<?php esc_attr_e( 'Close menu', 'deon-energy' ); ?>">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
						<path d="M4 4l12 12M16 4L4 16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
					</svg>
				</button>
			</div>

			<nav id="site-primary-nav" class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'deon-energy' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'site-nav',
						'fallback_cb'    => 'deon_primary_menu_fallback',
						'depth'          => 2,
						'walker'         => new Deon_Nav_Walker(),
						'items_wrap'     => '<ul class="site-nav">%3$s</ul>',
					)
				);
				?>
			</nav>

			<div class="site-header__actions">
				<a class="btn-pill-outline" href="<?php echo esc_url( home_url( '/calculator/' ) ); ?>"><?php esc_html_e( 'Solar Calculator', 'deon-energy' ); ?></a>
				<a class="btn-pill" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Get a Quote', 'deon-energy' ); ?></a>
			</div>

		</div>
	</div>
	<div class="nav-drawer__scrim" data-nav-scrim></div>
</header>

<main id="content" class="site-main">
