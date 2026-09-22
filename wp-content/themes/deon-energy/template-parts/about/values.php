<?php
/**
 * About page — Section 03: Core Values.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_values = array(
	array(
		'icon' => 'about-icon-sustainable.svg',
		'title' => __( 'Long-Term Thinking', 'deon-energy' ),
		'text'  => __( "We optimise every design decision for the plant’s full lifecycle, prioritising sustained performance over short-term output.", 'deon-energy' ),
	),
	array(
		'icon' => 'about-icon-people.svg',
		'title' => __( 'People &amp; Site Safety', 'deon-energy' ),
		'text'  => __( 'Clear safety protocols, PPE compliance, and skill development keep our teams protected and sites disciplined.', 'deon-energy' ),
	),
	array(
		'icon' => 'about-icon-excellence.svg',
		'title' => __( 'Straight Answers', 'deon-energy' ),
		'text'  => __( "We state the assumptions behind every quotation and flag uncertainties upfront, helping customers make informed decisions.", 'deon-energy' ),
	),
);
?>
<section class="about-values">
	<div class="deon-container">
		<div class="about-values__head">
			<h2 class="about-values__h2"><?php esc_html_e( 'Core Values', 'deon-energy' ); ?></h2>
			<span class="about-values__rule" aria-hidden="true"></span>
		</div>
		<div class="about-values__grid">
			<?php foreach ( $deon_values as $deon_value ) : ?>
			<div class="about-values__card">
				<img
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/' . $deon_value['icon'] ); ?>"
					alt=""
					class="about-values__icon"
					aria-hidden="true"
				/>
				<h3 class="about-values__title"><?php echo wp_kses_post( $deon_value['title'] ); ?></h3>
				<p class="about-values__text"><?php echo esc_html( $deon_value['text'] ); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
