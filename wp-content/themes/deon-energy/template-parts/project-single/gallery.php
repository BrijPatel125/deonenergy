<?php
/**
 * Project detail — bento photo gallery. Simplified bento: image 1 is the big
 * hero tile (2x2), image 2 is a tall tile beside it (1x2, matches the hero's
 * height) — everything from image 3 onward is a uniform small tile (1x1),
 * regardless of how many images exist. No repeating pattern, so there's no
 * risk of a later image landing on an oversized/stretched span again. Wired
 * to the lightbox ( assets/js/project-gallery.js ). Hidden when no images set.
 *
 * Only the first 5 images render by default. If more than 5 exist, a
 * "View All Photos" button reveals the rest in place — kept in the DOM for
 * the lightbox, just visually hidden until expanded.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$deon_ids = function_exists( 'deon_project_gallery_ids' ) ? deon_project_gallery_ids( get_the_ID() ) : array();
if ( empty( $deon_ids ) ) {
	return;
}
$deon_count    = count( $deon_ids );
$deon_visible  = 5;
$deon_has_more = $deon_count > $deon_visible;

// Fixed spans: position 0 = hero, position 1 = tall, everything else = small.
$deon_bento_hero  = 'md:col-span-2 md:row-span-2';
$deon_bento_tall  = 'md:col-span-1 md:row-span-2';
$deon_bento_small = 'md:col-span-1 md:row-span-1';
?>

<section class="w-full bg-white">
	<div class="max-w-(--container-max) mx-auto px-[clamp(20px,5vw,64px)] py-(--section-pad)">
		<div class="flex items-center gap-4 mb-8 md:mb-10" data-anim>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-accent shrink-0">
				<?php esc_html_e( 'Project Perspectives', 'deon-energy' ); ?>
			</span>
			<div class="bg-deon-border h-px flex-1 min-w-0"></div>
			<span class="font-sans font-bold text-[12px] leading-[12px] tracking-[2.4px] uppercase text-deon-body shrink-0" data-deon-gallery-count>
				<?php
				echo esc_html(
					$deon_has_more
						? sprintf( /* translators: 1: visible count, 2: total count */ __( 'Images 1–%1$d of %2$d', 'deon-energy' ), $deon_visible, $deon_count )
						: sprintf( /* translators: %d: total number of gallery images */ __( 'Images 1–%d', 'deon-energy' ), $deon_count )
				);
				?>
			</span>
		</div>

		<div class="grid grid-cols-2 gap-[12px] md:grid-cols-3 md:gap-[16px]
		            md:auto-rows-[200px] md:grid-flow-dense"
		     data-deon-gallery data-anim-stagger>
			<?php foreach ( $deon_ids as $deon_i => $deon_id ) :
				$deon_thumb = wp_get_attachment_image_url( $deon_id, 'large' );
				$deon_full  = wp_get_attachment_image_url( $deon_id, 'full' );
				$deon_alt   = trim( (string) get_post_meta( $deon_id, '_wp_attachment_image_alt', true ) );
				if ( ! $deon_thumb ) {
					continue;
				}

				if ( 0 === $deon_i ) {
					$deon_span = $deon_bento_hero;
				} elseif ( 1 === $deon_i ) {
					$deon_span = $deon_bento_tall;
				} else {
					$deon_span = $deon_bento_small;
				}

				$deon_is_extra = $deon_i >= $deon_visible;
				?>
				<button type="button"
				        class="deon-gallery-item group relative overflow-hidden bg-deon-warm w-full h-full min-h-[160px] cursor-zoom-in <?php echo esc_attr( $deon_span ); ?><?php echo $deon_is_extra ? ' hidden' : ''; ?>"
				        data-full="<?php echo esc_url( $deon_full ); ?>"
				        data-index="<?php echo esc_attr( $deon_i ); ?>"
				        <?php echo $deon_is_extra ? 'data-deon-gallery-extra' : ''; ?>
				        aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'deon-energy' ), $deon_i + 1 ) ); ?>">
					<img src="<?php echo esc_url( $deon_thumb ); ?>"
					     alt="<?php echo esc_attr( $deon_alt ? $deon_alt : get_the_title() ); ?>"
					     class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.05]"
					     loading="lazy">
				</button>
			<?php endforeach; ?>
		</div>

		<?php if ( $deon_has_more ) : ?>
		<div class="flex justify-center mt-8 md:mt-10">
			<button type="button"
			        data-deon-gallery-toggle
			        aria-expanded="false"
			        data-label-more="<?php echo esc_attr( sprintf( /* translators: %d: total number of gallery images */ __( 'View All %d Photos', 'deon-energy' ), $deon_count ) ); ?>"
			        data-label-less="<?php esc_attr_e( 'Show Fewer Photos', 'deon-energy' ); ?>"
			        data-count-more="<?php echo esc_attr( sprintf( /* translators: 1: visible count, 2: total count */ __( 'Images 1–%1$d of %2$d', 'deon-energy' ), $deon_visible, $deon_count ) ); ?>"
			        data-count-less="<?php echo esc_attr( sprintf( /* translators: %d: total number of gallery images */ __( 'Images 1–%d', 'deon-energy' ), $deon_count ) ); ?>"
			        class="inline-flex items-center gap-2 px-6 py-3 border border-deon-border text-deon-heading font-sans font-bold text-[13px] tracking-[0.5px] uppercase transition-colors hover:border-deon-accent hover:text-deon-accent">
				<span data-deon-gallery-toggle-label><?php echo esc_html( sprintf( /* translators: %d: total number of gallery images */ __( 'View All %d Photos', 'deon-energy' ), $deon_count ) ); ?></span>
			</button>
		</div>
		<?php endif; ?>

	</div>
</section>

<?php if ( $deon_has_more ) : ?>
<script>
(function () {
	document.querySelectorAll('[data-deon-gallery-toggle]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var section    = btn.closest('section');
			var extras     = section.querySelectorAll('[data-deon-gallery-extra]');
			var label      = btn.querySelector('[data-deon-gallery-toggle-label]');
			var countLabel = section.querySelector('[data-deon-gallery-count]');
			var expanded   = btn.getAttribute('aria-expanded') === 'true';

			extras.forEach(function (el) {
				el.classList.toggle('hidden', expanded);
			});

			btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			if (label)      { label.textContent = expanded ? btn.getAttribute('data-label-more') : btn.getAttribute('data-label-less'); }
			if (countLabel) { countLabel.textContent = expanded ? btn.getAttribute('data-count-more') : btn.getAttribute('data-count-less'); }

			if (expanded) {
				section.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		});
	});
})();
</script>
<?php endif; ?>