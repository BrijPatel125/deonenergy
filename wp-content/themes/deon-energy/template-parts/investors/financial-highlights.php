<?php
/**
 * Investor Relations — Portfolio Highlights strip (dark).
 *
 * Previously shipped four designed placeholders presented as fact on an
 * investor-facing page: "$1.2B Annual Revenue", "24% EBITDA Margin",
 * "8.4GW Net Capacity" and a "15+ Project Pipeline". None of them are
 * supported by any client-verified source, and 8.4GW overstates the real
 * commissioned portfolio by roughly 54x. All four have been removed.
 *
 * The strip now reports only figures derived from the published deon_project
 * portfolio via deon_project_portfolio_stats() ( inc/projects.php ) —
 * commissioned plants only, no forecasts. Audited financials must come from
 * the client before any revenue / margin / pipeline claim is reinstated.
 *
 * Delegates to the shared dark band so ESG / Project Gallery / Investor
 * Relations stay identical. The per-tile icons (capacity / pipeline / EBITDA /
 * revenue SVGs) were dropped in the same pass — the other two bands have none.
 * Values + labels are managed in Appearance → Customize → Site Statistics →
 * Investor Relations ( inc/site-stats.php ).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_template_part( 'template-parts/global/stat-band', null, array( 'band' => 'investor' ) );
