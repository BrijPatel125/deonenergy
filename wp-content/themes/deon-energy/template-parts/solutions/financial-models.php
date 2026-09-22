<?php
/**
 * Solutions page — Section 04: Industrial Financial Models.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$rows = array(
	array( __( 'Upfront Investment', 'deon-energy' ),  __( '100% funded by the customer', 'deon-energy' ),        __( 'Zero upfront — investor funds the plant', 'deon-energy' ) ),
	array( __( 'Asset Ownership', 'deon-energy' ),     __( 'Customer owns the plant', 'deon-energy' ),            __( 'Investor / developer owns the plant', 'deon-energy' ) ),
	array( __( 'O&M Responsibility', 'deon-energy' ),  __( 'Contracted separately by customer', 'deon-energy' ),  __( 'Provider is responsible during the term', 'deon-energy' ) ),
	array( __( 'Long-Term Savings', 'deon-energy' ),   __( "Higher over the plant's life", 'deon-energy' ),       __( 'Steady per-unit discount vs grid tariff', 'deon-energy' ) ),
	array( __( 'Tax Position', 'deon-energy' ),        __( 'May qualify for accelerated depreciation', 'deon-energy' ), __( 'Treated as an operating expense', 'deon-energy' ) ),
);

?>
<section id="financial-models" class="sol-finmodels">
	<div class="sol-finmodels__section-num" aria-hidden="true">04</div>
	<div class="deon-container sol-finmodels__inner">
		<div class="sol-finmodels__header" data-anim>
			<h2 class="sol-finmodels__title"><?php esc_html_e( 'Commercial Structures: CAPEX vs OPEX', 'deon-energy' ); ?></h2>
			<p class="sol-finmodels__sub"><?php esc_html_e( 'Two common structures for financing solar. The right one depends on your balance-sheet appetite, tax position and cashflow preference.', 'deon-energy' ); ?></p>
		</div>
		<div class="sol-finmodels__table-wrap" data-anim>
			<table class="sol-finmodels__table">
				<thead>
					<tr class="sol-finmodels__thead-row">
						<th class="sol-finmodels__th sol-finmodels__th--metric"><?php esc_html_e( 'Comparison', 'deon-energy' ); ?></th>
						<th class="sol-finmodels__th"><?php esc_html_e( 'CAPEX (You Own)', 'deon-energy' ); ?></th>
						<th class="sol-finmodels__th"><?php esc_html_e( 'OPEX / PPA', 'deon-energy' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
					<tr class="sol-finmodels__row">
						<td class="sol-finmodels__td sol-finmodels__td--metric"><?php echo esc_html( $row[0] ); ?></td>
						<td class="sol-finmodels__td"><?php echo esc_html( $row[1] ); ?></td>
						<td class="sol-finmodels__td"><?php echo esc_html( $row[2] ); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
