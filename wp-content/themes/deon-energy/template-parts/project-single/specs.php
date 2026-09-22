<?php
/**
 * Project detail — spec list. Renders every filled spec field, minus the three
 * the hero fact row already shows (location, capacity, commissioned year) so
 * nothing is stated twice on the page. Project Status left the hero on
 * 2026-08-09 and now lands here.
 *
 * Restored 2026-08-09 (client): without it, Grid Voltage / Annual Output /
 * Technology / EPC Partner / Client-Owner were editable in wp-admin but
 * published on no project page at all.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_all    = function_exists( 'deon_project_spec_fields' ) ? deon_project_spec_fields() : array();
$deon_skip   = array( '_deon_location', '_deon_capacity', '_deon_commissioned' );
$deon_rows   = array();
foreach ( $deon_all as $deon_key => $deon_field ) {
	if ( in_array( $deon_key, $deon_skip, true ) ) {
		continue;
	}
	$deon_val = get_post_meta( get_the_ID(), $deon_key, true );
	if ( '' !== trim( (string) $deon_val ) ) {
		$deon_rows[] = array( $deon_field[0], $deon_val );
	}
}

if ( empty( $deon_rows ) ) {
	return;
}
?>

<?php
/*
 * Margin, not padding: the warm band needs white space above it, and the
 * overview section that precedes it closes on a tight `pb-2`.
 */
?>
<section class="w-full bg-deon-bg mt-[clamp(28px,3.5vw,52px)]">
	<?php /* Same container rhythm as the sections around it (overview / gallery). */ ?>
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">
		<div class="flex items-center gap-[16px] mb-[32px] md:mb-[40px]" data-anim>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-accent shrink-0">
				<?php esc_html_e( 'Project Specifications', 'deon-energy' ); ?>
			</span>
			<div class="bg-deon-border h-px flex-1 min-w-0"></div>
		</div>

		<?php
		/*
		 * Stacked label-over-value blocks, no row rules. The old layout drew a
		 * border on every row of a 2-column grid, so an odd number of specs left
		 * the columns ending on different lines and the lines read as a table
		 * grid. The section's own hairline above is the only rule needed.
		 */
		?>
		<dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-[clamp(32px,5vw,72px)] gap-y-[clamp(28px,3vw,40px)] m-0" data-anim-stagger>
			<?php foreach ( $deon_rows as $deon_row ) : ?>
			<div class="flex flex-col gap-2 min-w-0">
				<dt class="font-sans font-bold text-[11px] leading-[12px] tracking-[1.6px] uppercase text-deon-body/70">
					<?php echo esc_html( $deon_row[0] ); ?>
				</dt>
				<dd class="m-0 font-sans font-bold text-[clamp(17px,1.4vw,20px)] leading-[1.35] text-deon-heading">
					<?php echo esc_html( $deon_row[1] ); ?>
				</dd>
			</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>
