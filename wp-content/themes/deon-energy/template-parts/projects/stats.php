<?php
/**
 * Project Gallery — portfolio stat band (change log v1 §5).
 *
 * The one dark #1D1B20 band allowed on this page (spec: section rhythm).
 * Delegates to the shared dark band so ESG / Project Gallery / Investor
 * Relations stay identical. Tiles are managed in Appearance → Customize →
 * Site Statistics → Projects ( inc/site-stats.php ); each ships a default
 * derived from the published deon_project portfolio. Empty tiles are dropped.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/stat-band', null, array( 'band' => 'projects' ) );
