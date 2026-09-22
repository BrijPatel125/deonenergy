<?php
/**
 * Contact — Main section: inquiry form (left) + office cards (right).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$deon_subjects = function_exists( 'deon_inquiry_subjects' ) ? deon_inquiry_subjects() : array();
$deon_status   = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';

// Offices — client-managed CPT, ordered by menu_order. Falls back to design
// placeholders so the page renders correctly before any office is added.
$deon_offices = get_posts( array(
	'post_type'      => 'deon_office',
	'posts_per_page' => -1,
	'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	'no_found_rows'  => true,
) );

$deon_icons = function_exists( 'deon_office_icons' ) ? deon_office_icons() : array();

if ( empty( $deon_offices ) ) {
	/*
	 * Deon Energy's three real offices (scraped from deonenergy.in — see
	 * bin/data/company.json). The previous fallback invented Zurich / London /
	 * Singapore addresses for a Gujarat-only company; that shipped on any
	 * environment where deon_office posts had not been created yet.
	 */
	$deon_office_cards = array(
		array(
			'name'    => __( 'Ahmedabad Office', 'deon-energy' ),
			'address' => "D-604, 605, 606 Westgate, Near YMCA Club\nS.G. Highway, Makarba, Ahmedabad-380051, Gujarat, India.",
			'map'     => 'https://maps.app.goo.gl/1hhSsNiHuFgMFwnv7',
			'icon'    => 'pin',
		),
		array(
			'name'    => __( 'Rajkot Office', 'deon-energy' ),
			'address' => "401, R K Prime, Near Nana Mava Circle\n150 Feet Ring Road, Rajkot-360 005, Gujarat, India.",
			'map'     => 'https://maps.app.goo.gl/o2xmtotpHkRSdQnP9',
			'icon'    => 'building',
		),
		array(
			'name'    => __( 'Morbi Office', 'deon-energy' ),
			'address' => "715, 716 & 717 Siromany-142, B/h. Eden Ceramic City\nNH-8A, Lalpar-363 642 Morbi, Gujarat, India.",
			'map'     => 'https://maps.app.goo.gl/u1djubd4sfLfpetM9',
			'icon'    => 'globe',
		),
	);
} else {
	$deon_office_cards = array();
	foreach ( $deon_offices as $office ) {
		$deon_office_cards[] = array(
			'name'    => get_the_title( $office ),
			'address' => (string) get_post_meta( $office->ID, '_deon_office_address', true ),
			'map'     => (string) get_post_meta( $office->ID, '_deon_office_map_url', true ),
			'icon'    => (string) get_post_meta( $office->ID, '_deon_office_icon', true ),
		);
	}
}
?>

<section id="contact-form" class="w-full bg-white border-t border-white">
	<div class="max-w-[1280px] mx-auto px-4 pt-[48px] pb-[80px] flex flex-col gap-[64px]
	            md:px-16 md:py-(--section-pad) md:flex-row md:items-stretch md:gap-[80px]">

		<!-- Left: Inquiry Form -->
		<div class="w-full md:flex-1" data-anim>

			<?php if ( 'success' === $deon_status ) : ?>
				<div class="mb-8 border border-deon-accent bg-[#fdf6ea] px-5 py-4 font-sans text-[15px] text-deon-heading" role="status">
					<?php esc_html_e( 'Thank you — your inquiry has been received. Our team will be in touch shortly.', 'deon-energy' ); ?>
				</div>
			<?php elseif ( 'error' === $deon_status ) : ?>
				<div class="mb-8 border border-[#c0392b] bg-[#fdecea] px-5 py-4 font-sans text-[15px] text-[#c0392b]" role="alert">
					<?php esc_html_e( 'Something went wrong. Please check the required fields and try again.', 'deon-energy' ); ?>
				</div>
			<?php endif; ?>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="flex flex-col gap-[48px]">
				<input type="hidden" name="action" value="deon_contact_submit">
				<?php wp_nonce_field( 'deon_contact_submit', 'deon_contact_nonce' ); ?>
				<!-- Honeypot: visually hidden, off-screen; bots fill it, humans don't. -->
				<div class="absolute -left-[9999px]" aria-hidden="true">
					<label>Website<input type="text" name="deon_website" tabindex="-1" autocomplete="off"></label>
				</div>

				<div class="grid grid-cols-1 gap-[48px] sm:grid-cols-2">
					<div class="flex flex-col gap-[8px]">
						<label for="deon-name" class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-body">
							<?php esc_html_e( 'Full Name', 'deon-energy' ); ?>
						</label>
						<input type="text" id="deon-name" name="deon_name" required placeholder="<?php esc_attr_e( 'Johnathan Doe', 'deon-energy' ); ?>"
							class="w-full bg-white border-0 border-b border-solid border-deon-border pl-0 pr-[12px] pt-[18px] pb-[19px] font-sans text-[16px] text-deon-heading placeholder:text-[#6b7280] focus:outline-none focus:border-deon-accent">
					</div>
					<div class="flex flex-col gap-[8px]">
						<label for="deon-email" class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-body">
							<?php esc_html_e( 'Email Address', 'deon-energy' ); ?>
						</label>
						<input type="email" id="deon-email" name="deon_email" required placeholder="<?php esc_attr_e( 'j.doe@corporate.com', 'deon-energy' ); ?>"
							class="w-full bg-white border-0 border-b border-solid border-deon-border pl-0 pr-[12px] pt-[18px] pb-[19px] font-sans text-[16px] text-deon-heading placeholder:text-[#6b7280] focus:outline-none focus:border-deon-accent">
					</div>
				</div>

				<div class="grid grid-cols-1 gap-[48px] sm:grid-cols-2">
					<div class="flex flex-col gap-[8px]">
						<label for="deon-org" class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-body">
							<?php esc_html_e( 'Organization', 'deon-energy' ); ?>
						</label>
						<input type="text" id="deon-org" name="deon_org" placeholder="<?php esc_attr_e( 'Global Infrastructure Partners', 'deon-energy' ); ?>"
							class="w-full bg-white border-0 border-b border-solid border-deon-border pl-0 pr-[12px] pt-[18px] pb-[19px] font-sans text-[16px] text-deon-heading placeholder:text-[#6b7280] focus:outline-none focus:border-deon-accent">
					</div>
					<div class="flex flex-col gap-[8px]">
						<label for="deon-subject" class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-body">
							<?php esc_html_e( 'Subject', 'deon-energy' ); ?>
						</label>
						<select id="deon-subject" name="deon_subject"
							class="w-full bg-white border-0 border-b border-solid border-deon-border pl-0 pr-[12px] pt-[16px] pb-[17px] font-sans text-[16px] text-deon-heading focus:outline-none focus:border-deon-accent appearance-none bg-[length:14px] bg-[right_4px_center] bg-no-repeat"
							style="background-image:url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226 9 12 15 18 9%22/></svg>');">
							<option value="" disabled selected><?php esc_html_e( 'Select Project Type', 'deon-energy' ); ?></option>
							<?php foreach ( $deon_subjects as $deon_key => $deon_label ) : ?>
								<option value="<?php echo esc_attr( $deon_key ); ?>"><?php echo esc_html( $deon_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="flex flex-col gap-[8px]">
					<label for="deon-message" class="font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-body">
						<?php esc_html_e( 'Message Detail', 'deon-energy' ); ?>
					</label>
					<textarea id="deon-message" name="deon_message" required rows="4" placeholder="<?php esc_attr_e( 'Briefly describe the nature of your inquiry...', 'deon-energy' ); ?>"
						class="w-full bg-white border-0 border-b border-solid border-deon-border pl-0 pr-[12px] pt-[16px] pb-[16px] font-sans text-[16px] leading-[24px] text-deon-heading placeholder:text-[#6b7280] focus:outline-none focus:border-deon-accent resize-y min-h-[120px]"></textarea>
				</div>

				<button type="submit" class="self-start inline-flex items-center gap-[16px] bg-deon-dark px-[48px] py-[20px] font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-white transition-colors hover:bg-deon-accent">
					<?php esc_html_e( 'Send Inquiry', 'deon-energy' ); ?>
					<svg class="w-[12px] h-[12px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
				</button>
			</form>
		</div>

		<!-- Right: Office Cards -->
		<div class="w-full md:w-[400px] md:shrink-0 flex flex-col gap-[20px]" data-anim-stagger>
			<?php foreach ( $deon_office_cards as $deon_card ) :
				$deon_icon_key = isset( $deon_icons[ $deon_card['icon'] ] ) ? $deon_card['icon'] : 'pin';
				$deon_icon_svg = isset( $deon_icons[ $deon_icon_key ] ) ? $deon_icons[ $deon_icon_key ] : '';
			?>
				<div class="md:flex-1 bg-white border border-deon-border p-[20px] flex flex-col justify-center gap-[4px] shadow-[0px_2px_10px_rgba(0,0,0,0.06)]">
					<span class="block w-[18px] h-[18px] text-deon-accent">
						<?php echo $deon_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG constant. ?>
					</span>
					<h3 class="font-sans font-normal! text-[19px] leading-snug text-deon-heading pt-[4px]">
						<?php echo esc_html( $deon_card['name'] ); ?>
					</h3>
					<p class="font-sans text-[14px] leading-relaxed text-deon-body">
						<?php echo nl2br( esc_html( $deon_card['address'] ) ); ?>
					</p>
					<?php if ( ! empty( $deon_card['map'] ) ) : ?>
						<a href="<?php echo esc_url( $deon_card['map'] ); ?>" target="_blank" rel="noopener noreferrer"
							class="inline-flex items-center gap-[8px] pt-[12px] font-sans font-bold text-[12px] leading-[12px] tracking-[1.2px] uppercase text-deon-accent hover:opacity-70">
							<?php esc_html_e( 'View on Map', 'deon-energy' ); ?>
							<svg class="w-[12px] h-[12px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
