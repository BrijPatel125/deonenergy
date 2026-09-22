<?php
/**
 * Leadership — hero. Delegates to the shared inner-page hero (redesign wave 1).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/page-hero', null, array(
	'eyebrow'   => __( 'Governance & People', 'deon-energy' ),
	'title'     => __( 'Leadership', 'deon-energy' ),
	'lead'      => __( 'Deon Energy is led by a team that combines hands-on solar delivery with strong governance and financial discipline. Our founders remain closely involved in project decisions, and our board provides the independent oversight expected of a growing infrastructure company.', 'deon-energy' ),
	'image'     => get_template_directory_uri() . '/assets/img/careers-life.jpg',
	'image_alt' => __( 'Deon Energy leadership workspace overlooking the city', 'deon-energy' ),
	'bg'        => 'offwhite',
) );
