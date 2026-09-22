<?php
/**
 * Seeder for the two footer legal pages: Privacy Policy + Terms of Use.
 *
 * Run from the theme directory on the target install:
 *
 *     wp eval-file bin/seed-legal-pages.php
 *     wp eval-file bin/seed-legal-pages.php force   # overwrite existing bodies
 *
 * The footer legal row ( footer.php ) is deliberately slug-driven: each link is
 * printed only once a published page resolves, so the row stays honest instead
 * of shipping 404s. This creates those pages with the exact slugs the footer
 * looks for, publishes them with placeholder copy, and points
 * Settings → Privacy at the Privacy Policy page ( that option is what
 * get_privacy_policy_url() reads, and it is checked before the slug lookup ).
 *
 * Body copy is the approved text (PDF §13 Privacy Policy, §14 Terms of Use),
 * verbatim, as Gutenberg blocks. Both open with a "Last updated" line — a
 * literal date, never a dynamic one: it records when the text last actually
 * changed, so bump it by hand whenever the policy is edited.
 *
 * Idempotent: matched by slug, so re-running updates the same two pages instead
 * of creating duplicates. Existing content is never overwritten unless the
 * positional `force` argument is passed — use it to replace copy seeded by an
 * earlier version of this script (it will discard client edits, so check
 * first).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file bin/seed-legal-pages.php\n" );
	exit( 1 );
}

// Positional `force`, not `--force`: WP-CLI rejects unknown leading-dash flags
// before eval-file ever runs the script.
$deon_force = in_array( 'force', (array) ( $args ?? array() ), true );

$pages = array(
	array(
		'slug'    => 'privacy-policy',
		'title'   => 'Privacy Policy',
		'privacy' => true,
		'body'    => '<!-- wp:paragraph -->
<p><em>Last updated: 1 September 2026</em></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>1. Who We Are</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This Privacy Policy explains how Deon Energy Limited ("Deon Energy", "we", "us" or "our") collects, uses and protects personal information when you visit our website, contact us, request a proposal, apply for a job, or otherwise interact with us. Our registered office is at D-604, 605, 606 Westgate, Near YMCA Club, S.G. Highway, Makarba, Ahmedabad – 380051, Gujarat, India.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>2. Information We Collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We collect only the personal information we need to respond to your enquiry, deliver our services, or meet our legal obligations. Depending on how you interact with us, this may include:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li>Contact information — name, email address, phone number, and organisation</li><li>Enquiry details — your message, project details you choose to share, and any files you upload</li><li>Site information — where relevant to a solar assessment, details of your site, address, roof or land area, and electricity consumption</li><li>Career-related information — CV, professional history, references and eligibility to work</li><li>Technical information — IP address, browser type, device information, and pages visited via cookies and standard web analytics</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>3. How We Use Your Information</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We use personal information for the purposes for which you shared it, including:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li>Responding to enquiries and preparing proposals</li><li>Delivering, managing and supporting the projects and services we contract for</li><li>Considering job applications and communicating with candidates</li><li>Sending you updates you have subscribed to (you can unsubscribe at any time)</li><li>Improving our website, content and services</li><li>Meeting legal, regulatory, tax and accounting obligations</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>4. Legal Basis</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We process personal information on the basis of your consent (given when you submit a form, subscribe or apply), our legitimate business interests (running and improving our services), or where we are required to do so by applicable law.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>5. Cookies and Analytics</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Our website uses cookies and similar technologies to help the site function correctly and to help us understand how visitors use it. You can control cookies through your browser settings; blocking some cookies may affect how the site works for you.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>6. Sharing Your Information</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We do not sell personal information. We may share personal information with:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li>Trusted service providers who help us operate our business (for example, hosting, email delivery, analytics), under confidentiality obligations</li><li>Professional advisors — auditors, lawyers, consultants — where reasonably necessary</li><li>Government authorities, regulators or law-enforcement agencies where required by law</li><li>Successor entities in the event of a corporate transaction, subject to equivalent protection</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>7. Data Retention</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We retain personal information only for as long as we need it for the purpose it was collected, or as required by applicable law. Enquiry records are typically retained for a reasonable business period; contract and project records are retained for the duration of the engagement plus any period required for tax, audit or legal reasons.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>8. Your Rights</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Subject to applicable law (including the Digital Personal Data Protection Act, 2023 as it comes into force), you have the right to:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li>Access the personal information we hold about you</li><li>Request correction of inaccurate information</li><li>Request erasure of your personal information, where legally permissible</li><li>Withdraw consent for processing that was based on your consent</li><li>Raise a grievance about how your personal information has been handled</li></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>To exercise any of these rights, please write to us at <a href="mailto:info@deonenergy.in">info@deonenergy.in</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>9. Data Security</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We use reasonable technical and organisational measures to protect personal information against unauthorised access, alteration, disclosure or destruction. No system is completely secure; if you have reason to believe your interaction with us is no longer secure, please contact us immediately.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>10. Third-Party Links and Embedded Content</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Our website may contain links to, or embed content from, third-party websites (for example, video platforms, maps, social channels). These third parties operate under their own privacy policies. Once you follow a link or interact with embedded content, we are not responsible for how those third parties handle your data.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>11. Changes to This Policy</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We may update this Privacy Policy from time to time. The current version, together with the last-updated date, will always be available on this page.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>12. Contact Us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you have questions or concerns about this Privacy Policy or how we handle personal information, please write to:</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Deon Energy Limited<br>D-604, 605, 606 Westgate, Near YMCA Club, S.G. Highway, Makarba, Ahmedabad – 380051, Gujarat, India<br>Email: <a href="mailto:info@deonenergy.in">info@deonenergy.in</a></p>
<!-- /wp:paragraph -->',
	),
	array(
		'slug'    => 'terms-of-use',
		'title'   => 'Terms of Use',
		'privacy' => false,
		'body'    => '<!-- wp:paragraph -->
<p><em>Last updated: 1 September 2026</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>By accessing or using the Deon Energy Limited website ("this Site"), you agree to be bound by these Terms of Use. If you do not agree with any part of these terms, please do not use the Site. Deon Energy Limited may update these terms from time to time; the version published here is the version in force.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>1. Use of the Site</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This Site is provided for general information about Deon Energy Limited and our solar and renewable energy services. You agree to use the Site only for lawful purposes, and not in any way that could damage, disable, overburden, or impair the Site or interfere with any other party’s use of it. You may not attempt to gain unauthorised access to any part of the Site, our systems, or the systems of any user.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>2. Intellectual Property</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>All content on this Site — including text, images, project photography, graphics, videos, logos, marks, and design elements — belongs to Deon Energy Limited or its licensors and is protected by applicable intellectual property laws. You may not copy, reproduce, republish, distribute, or create derivative works from any content on this Site without our prior written permission. The Deon Energy name and logo are trademarks of Deon Energy Limited.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>3. Accuracy of Information</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We take reasonable care to keep information on this Site accurate and up to date. However, technical data, capacity figures, calculator outputs, generation estimates, savings estimates, and payback projections shown on this Site are indicative only. They are based on standard assumptions and do not form part of any contract or binding offer. A firm commercial proposal is issued only in writing after a site assessment and technical evaluation.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>4. No Financial or Investment Advice</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Any references on this Site to costs, savings, payback, tax benefits or commercial structures are for general information only and do not constitute financial, tax, legal, or investment advice. You should consult your own qualified advisors before making any financial or investment decision based on information on this Site.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>5. Third-Party Links</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This Site may contain links to third-party websites for your convenience. We do not control, endorse, or accept responsibility for the content, availability, or practices of any third-party sites. Access to third-party sites is at your own risk.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>6. User Submissions</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you submit information to us through a form on this Site — for example, an enquiry, a proposal request, or a job application — you confirm that the information is accurate and that you are authorised to share it. Your submission is handled in accordance with our Privacy Policy.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>7. Limitation of Liability</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>To the maximum extent permitted by applicable law, Deon Energy Limited and its directors, officers, employees and affiliates shall not be liable for any indirect, incidental, consequential, special or punitive loss or damage arising out of or in connection with your use of, or inability to use, this Site or reliance on any content on it. Nothing in these Terms limits or excludes any liability that cannot be limited or excluded under applicable law.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>8. Governing Law and Jurisdiction</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>These Terms of Use are governed by the laws of India. Any dispute arising out of or in connection with your use of this Site shall be subject to the exclusive jurisdiction of the competent courts at Ahmedabad, Gujarat.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>9. Contact</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you have any questions about these Terms of Use, please write to us at <a href="mailto:info@deonenergy.in">info@deonenergy.in</a>.</p>
<!-- /wp:paragraph -->',
	),
);



foreach ( $pages as $page ) {
	$existing = get_page_by_path( $page['slug'] );

	if ( $existing ) {
		$args = array( 'ID' => $existing->ID );

		// Republish a page the client trashed or left as a draft.
		if ( 'publish' !== $existing->post_status ) {
			$args['post_status'] = 'publish';
		}

		// Only refill a page that is genuinely empty — never clobber real copy
		// unless `force` was passed explicitly.
		if ( $deon_force || '' === trim( (string) $existing->post_content ) ) {
			$args['post_content'] = $page['body'];
		}

		if ( count( $args ) > 1 ) {
			wp_update_post( $args );
			WP_CLI::log( "Updated: {$page['title']} (/{$page['slug']}/)" );
		} else {
			WP_CLI::log( "Skipped (already published, has content): {$page['title']} (/{$page['slug']}/)" );
		}

		$page_id = $existing->ID;
	} else {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_name'    => $page['slug'],
				'post_title'   => $page['title'],
				'post_content' => $page['body'],
				'post_status'  => 'publish',
			),
			true
		);

		if ( is_wp_error( $page_id ) ) {
			WP_CLI::warning( "Failed: {$page['title']} — " . $page_id->get_error_message() );
			continue;
		}

		WP_CLI::log( "Created: {$page['title']} (/{$page['slug']}/)" );
	}

	// Settings → Privacy. WordPress reads this before the slug fallback, so
	// setting it keeps the footer link correct even if the slug is renamed.
	if ( $page['privacy'] && $page_id ) {
		update_option( 'wp_page_for_privacy_policy', (int) $page_id );
		WP_CLI::log( '  → set as Settings → Privacy policy page' );
	}
}

WP_CLI::success( 'Legal pages seeded. The footer Privacy Policy / Terms of Use links now resolve.' );
