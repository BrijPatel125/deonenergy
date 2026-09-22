<?php
/**
 * Job detail — the built-in application form.
 *
 * Posts to admin-post.php → `deon_handle_application_submit()`
 * (inc/applications.php), which stores the submission under **Applications** in
 * wp-admin and emails the recipient set in Customize → Careers.
 *
 * Split out of template-parts/job/apply.php so that file stays a thin router
 * between the three intake modes (plugin shortcode / external ATS / this form).
 * Must run inside the loop — the job ID comes from the global post.
 *
 * Args: label_class, input_class (shared field styling from the parent),
 *       max_mb (résumé size cap, for the hint text).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_a      = is_array( $args ) ? $args : array();
$deon_label  = isset( $deon_a['label_class'] ) ? (string) $deon_a['label_class'] : '';
$deon_input  = isset( $deon_a['input_class'] ) ? (string) $deon_a['input_class'] : '';
$deon_max_mb = isset( $deon_a['max_mb'] ) ? (int) $deon_a['max_mb'] : 10;
?>

<form
	class="flex flex-col gap-6"
	method="post"
	enctype="multipart/form-data"
	action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
	data-apply-form
>
	<input type="hidden" name="action" value="deon_apply_submit">
	<input type="hidden" name="deon_job_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
	<?php wp_nonce_field( 'deon_apply_submit', 'deon_apply_nonce' ); ?>

	<?php // Honeypot — off-screen, never focusable; bots fill it, humans do not. ?>
	<div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
		<label for="deon-apply-website"><?php esc_html_e( 'Website', 'deon-energy' ); ?></label>
		<input type="text" id="deon-apply-website" name="deon_website" tabindex="-1" autocomplete="off">
	</div>

	<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
		<div class="flex flex-col gap-2">
			<label for="deon-apply-first" class="<?php echo esc_attr( $deon_label ); ?>"><?php esc_html_e( 'Legal First Name', 'deon-energy' ); ?></label>
			<input type="text" id="deon-apply-first" name="deon_first_name" required class="<?php echo esc_attr( $deon_input ); ?>" placeholder="<?php esc_attr_e( 'Alexander', 'deon-energy' ); ?>">
		</div>
		<div class="flex flex-col gap-2">
			<label for="deon-apply-last" class="<?php echo esc_attr( $deon_label ); ?>"><?php esc_html_e( 'Legal Last Name', 'deon-energy' ); ?></label>
			<input type="text" id="deon-apply-last" name="deon_last_name" required class="<?php echo esc_attr( $deon_input ); ?>" placeholder="<?php esc_attr_e( 'Von Neumann', 'deon-energy' ); ?>">
		</div>
	</div>

	<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
		<div class="flex flex-col gap-2">
			<label for="deon-apply-email" class="<?php echo esc_attr( $deon_label ); ?>"><?php esc_html_e( 'Corporate Email', 'deon-energy' ); ?></label>
			<input type="email" id="deon-apply-email" name="deon_email" required class="<?php echo esc_attr( $deon_input ); ?>" placeholder="<?php esc_attr_e( 'alexander@domain.com', 'deon-energy' ); ?>">
		</div>
		<div class="flex flex-col gap-2">
			<label for="deon-apply-phone" class="<?php echo esc_attr( $deon_label ); ?>"><?php esc_html_e( 'Phone', 'deon-energy' ); ?></label>
			<input type="tel" id="deon-apply-phone" name="deon_phone" class="<?php echo esc_attr( $deon_input ); ?>" placeholder="<?php esc_attr_e( '+91 98250 00000', 'deon-energy' ); ?>">
		</div>
	</div>

	<div class="flex flex-col gap-2">
		<span class="<?php echo esc_attr( $deon_label ); ?>"><?php esc_html_e( 'Curriculum Vitae (PDF/Word)', 'deon-energy' ); ?></span>

		<?php
		/*
		 * The designed drop panel IS the control: the file input sits on top at
		 * zero opacity so it keeps native click, keyboard and drag-drop behaviour
		 * while the panel supplies the visuals. main.js writes the chosen
		 * filename into [data-apply-file-name].
		 */
		?>
		<div class="relative flex flex-col items-center justify-center gap-2 border border-dashed border-deon-border py-10 text-center transition-colors focus-within:border-deon-accent" data-apply-drop>
			<input
				type="file"
				id="deon-apply-cv"
				name="deon_cv"
				required
				accept=".pdf,.doc,.docx,.rtf,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/rtf"
				class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
				aria-describedby="deon-apply-cv-hint"
			>
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-deon-accent" aria-hidden="true">
				<path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M5 21V5a2 2 0 0 1 2-2h8l5 5v13a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/><path d="M12 11v6"/><path d="M9.5 13.5 12 11l2.5 2.5"/>
			</svg>
			<label for="deon-apply-cv" class="font-sans font-bold text-deon-heading" data-apply-file-name>
				<?php esc_html_e( 'Drag and drop file', 'deon-energy' ); ?>
			</label>
			<span id="deon-apply-cv-hint" class="font-sans text-[11px] leading-[12px] tracking-[1px] uppercase text-deon-body opacity-70">
				<?php
				printf(
					/* translators: %d: size limit in megabytes. */
					esc_html__( 'Limit %dMB', 'deon-energy' ),
					absint( $deon_max_mb )
				);
				?>
			</span>
		</div>
	</div>

	<div class="flex flex-col gap-2">
		<label for="deon-apply-note" class="<?php echo esc_attr( $deon_label ); ?>"><?php esc_html_e( 'Executive Summary / Cover Note', 'deon-energy' ); ?></label>
		<textarea id="deon-apply-note" name="deon_cover_note" rows="4" class="<?php echo esc_attr( $deon_input ); ?>" placeholder="<?php esc_attr_e( 'Briefly detail your core project achievements…', 'deon-energy' ); ?>"></textarea>
	</div>

	<button type="submit" class="mt-4 inline-flex items-center gap-3 self-start bg-deon-dark py-4 px-10 font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white transition-opacity hover:opacity-90 disabled:opacity-60">
		<?php esc_html_e( 'Submit Candidacy', 'deon-energy' ); ?>
		<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
			<path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
	</button>
</form>
