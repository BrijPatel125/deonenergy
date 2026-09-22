<?php
/**
 * Job detail — Apply Now. Warm full-width band with the designed application
 * form (name / email / phone / CV upload / cover note).
 *
 * The form is functional: it posts to admin-post.php, where
 * `deon_handle_application_submit()` (inc/applications.php) validates it, stores
 * the résumé outside the Media Library and files the submission under
 * **Applications** in wp-admin. Outcome comes back as `?applied=success|error`
 * and is reported through the shared dialog below.
 *
 * A form-plugin shortcode set in Customize → Careers still wins, for clients who
 * would rather run intake through their own form/ATS tooling.
 *
 * Must run inside the loop (single-deon_job.php) — relies on the global post.
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_apply     = get_post_meta( get_the_ID(), '_deon_job_apply', true );
$deon_form_code = trim( (string) get_theme_mod( 'deon_apply_form_shortcode', '' ) );

// An external apply target (ATS link) replaces the built-in form entirely —
// otherwise the applicant would be offered two conflicting routes.
$deon_external = ( '' === $deon_form_code ) && ( '' !== trim( (string) $deon_apply ) ) && ! is_email( trim( (string) $deon_apply ) );
$deon_rules    = function_exists( 'deon_application_upload_rules' ) ? deon_application_upload_rules() : array( 'max' => 10 * MB_IN_BYTES );
$deon_max_mb   = (int) round( $deon_rules['max'] / MB_IN_BYTES );

// Prefer the client-set apply target; else email intake for this role.
$deon_mailto = 'mailto:info@deonenergy.in?subject=' . rawurlencode(
	sprintf(
		/* translators: %s: job title. */
		__( 'Application: %s', 'deon-energy' ),
		get_the_title()
	)
);
$deon_apply_href = ( '' !== trim( (string) $deon_apply ) )
	? deon_job_apply_href( $deon_apply )
	: $deon_mailto;

$deon_field_label = 'font-sans font-bold text-[11px] leading-[12px] tracking-[1px] uppercase text-deon-heading';
// Reference design: underline-only fields — no boxes, a single rule per row.
$deon_field_input = 'w-full bg-transparent border-0 border-b border-deon-border px-0 py-3 font-sans text-deon-body placeholder:text-deon-body/50 focus:outline-none focus:border-deon-accent';
?>

<section id="apply" class="w-full scroll-mt-24">
	<div class="max-w-[1280px] mx-auto px-4 py-16 md:px-16 md:py-(--section-pad)">
		<div class="max-w-[720px] mx-auto flex flex-col gap-8" data-anim>

			<!-- Header -->
			<div class="flex flex-col items-center gap-4 text-center">
				<h2 class="font-display font-bold! text-h2 leading-tight tracking-[-0.32px] text-deon-heading">
					<?php esc_html_e( 'Apply Now', 'deon-energy' ); ?>
				</h2>
				<p class="font-sans text-lead leading-relaxed text-deon-body">
					<?php esc_html_e( 'Submit your credentials for institutional review. We process all applications within 48 business hours.', 'deon-energy' ); ?>
				</p>
			</div>

			<?php if ( '' !== $deon_form_code ) : ?>

				<!-- Live form plugin (e.g. Contact Form 7) — overrides the built-in
				     form when the client runs intake through their own tooling. -->
				<div class="deon-apply-form flex flex-col gap-6">
					<?php echo do_shortcode( $deon_form_code ); ?>
				</div>

			<?php elseif ( $deon_external ) : ?>

				<!-- The job points at an external ATS; send the applicant there. -->
				<p class="text-center font-sans text-deon-body">
					<?php esc_html_e( 'Applications for this role are handled on our recruitment portal.', 'deon-energy' ); ?>
				</p>
				<a href="<?php echo esc_url( deon_job_apply_href( $deon_apply ) ); ?>" class="mx-auto inline-flex items-center gap-3 bg-deon-dark py-4 px-10 font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white! transition-opacity hover:opacity-90">
					<?php esc_html_e( 'Apply on our portal', 'deon-energy' ); ?>
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
						<path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>

			<?php else : ?>

				<?php get_template_part( 'template-parts/job/apply-form', null, array(
					'label_class' => $deon_field_label,
					'input_class' => $deon_field_input,
					'max_mb'      => $deon_max_mb,
				) ); ?>

			<?php endif; ?>

			<?php if ( ! $deon_external ) : ?>

				<?php
				/*
				 * Submit confirmation, shared by both live paths. The built-in form
				 * round-trips through admin-post.php and comes back as
				 * ?applied=success|error, while Contact Form 7 reports inline at the
				 * foot of the form where applicants miss it — main.js raises this
				 * dialog for either. Copy lives on the wrapper as data-* so the JS
				 * stays translation-free.
				 */
				?>
				<div
					class="deon-apply-modal hidden fixed inset-0 z-[1200] items-center justify-center px-4"
					data-apply-modal
					data-title-success="<?php esc_attr_e( 'Application submitted', 'deon-energy' ); ?>"
					data-message-success="<?php esc_attr_e( 'Thank you — your application has been received. Our team reviews all submissions within 48 business hours and will be in touch.', 'deon-energy' ); ?>"
					data-title-error="<?php esc_attr_e( 'Submission failed', 'deon-energy' ); ?>"
					data-message-error="<?php esc_attr_e( 'Your application could not be sent. Please try again, or email your CV to info@deonenergy.in.', 'deon-energy' ); ?>"
					<?php /* Reason-specific copy for the built-in form, keyed by the ?reason= value. */ ?>
					data-message-fields="<?php esc_attr_e( 'Please fill in your first name, last name and a valid email address.', 'deon-energy' ); ?>"
					data-message-nocv="<?php esc_attr_e( 'Please attach your résumé — we cannot review an application without one.', 'deon-energy' ); ?>"
					data-message-toobig="<?php echo esc_attr( sprintf( /* translators: %d: size limit in megabytes. */ __( 'That file is too large. Please attach a résumé under %dMB.', 'deon-energy' ), $deon_max_mb ) ); ?>"
					data-message-filetype="<?php esc_attr_e( 'Unsupported file type. Please attach a PDF, DOC, DOCX or RTF.', 'deon-energy' ); ?>"
					role="dialog"
					aria-modal="true"
					aria-labelledby="deon-apply-modal-title"
					aria-describedby="deon-apply-modal-message"
				>
					<div class="absolute inset-0 bg-deon-dark/60" data-apply-modal-close></div>

					<div class="relative w-full max-w-[460px] bg-white px-8 py-10 text-center shadow-[0_24px_60px_rgba(29,27,32,0.28)]">
						<button
							type="button"
							class="absolute right-4 top-4 p-2 text-deon-body transition-opacity hover:opacity-60"
							data-apply-modal-close
							aria-label="<?php esc_attr_e( 'Close', 'deon-energy' ); ?>"
						>
							<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
								<path d="M4 4l12 12M16 4L4 16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
							</svg>
						</button>

						<span class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-deon-accent/12 text-deon-accent" data-apply-modal-icon="success">
							<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M20 6 9 17l-5-5"/>
							</svg>
						</span>
						<span class="mx-auto mb-6 hidden h-14 w-14 items-center justify-center rounded-full bg-deon-dark/8 text-deon-dark" data-apply-modal-icon="error">
							<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M12 8v5"/><path d="M12 16.5h.01"/><circle cx="12" cy="12" r="9"/>
							</svg>
						</span>

						<h3 id="deon-apply-modal-title" class="font-display font-bold! text-h3 leading-tight text-deon-heading" data-apply-modal-title></h3>
						<p id="deon-apply-modal-message" class="mt-3 font-sans text-deon-body leading-relaxed" data-apply-modal-message></p>

						<button
							type="button"
							class="mt-8 inline-flex items-center justify-center bg-deon-dark py-4 px-10 font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white transition-opacity hover:opacity-90"
							data-apply-modal-close
						>
							<?php esc_html_e( 'Close', 'deon-energy' ); ?>
						</button>
					</div>
				</div>

			<?php endif; ?>

		</div>
	</div>
</section>
