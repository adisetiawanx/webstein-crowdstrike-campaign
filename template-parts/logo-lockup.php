<?php
/**
 * CrowdStrike and Mimecast co-brand lockup.
 *
 * Shared by both pages. Issue #3.
 *
 * MIMECAST is the official mark. Generated from the vector the client supplied
 * on 12 August 2026, `designs/Black.ai`. Pure #000000 as the mockup draws it,
 * and its ink ratio of 6.120 matches the mockup's 6.157 to within 0.6%.
 *
 * CROWDSTRIKE IS NOT. The client did not supply it. This file was sourced from
 * the internet and is a different version of the mark: its ink ratio was 5.56:1
 * where the mockup draws 7.32:1, because its swoosh is proportionally larger.
 * At a matched height that made the wordmark read noticeably smaller than the
 * design.
 *
 * WORKAROUND, at Adi's request on 12 August 2026: 32px of the swoosh's tail is
 * cropped from the bottom of the source, which brings the ink ratio to 7.304
 * against the mockup's 7.320, so the wordmark now reads at the right size. The
 * wordmark itself ends 26px above the cut and is untouched.
 *
 * This is a modification of someone else's trademark and it is deliberately
 * temporary. Replace it with the official file, uncropped, as soon as the
 * client supplies one. See issue #19.
 *
 * The two marks are aligned on height, which is how the mockup aligns them.
 *
 * @package crowdstrike-campaign
 */

defined( 'ABSPATH' ) || exit;

/*
 * Two widths each. Serving only the 2x file meant phones downloading roughly
 * three times the pixels they could display. Issue #15.
 *
 * `w1x` is the mark's width at the 56px render height, so it doubles as the
 * `sizes` value. The two marks are different widths, so `sizes` is computed per
 * mark rather than shared. It was previously hard coded to 311px, which stopped
 * being true for CrowdStrike the moment its file changed.
 */
$csc_logos = array(
	array(
		'slug'   => 'crowdstrike',
		'alt'    => 'CrowdStrike',
		'w1x'    => 409,
		'w2x'    => 818,
		'height' => 112,
	),
	array(
		'slug'   => 'mimecast',
		'alt'    => 'Mimecast',
		'w1x'    => 343,
		'w2x'    => 685,
		'height' => 112,
	),
);

/**
 * Render one mark of the lockup.
 *
 * Guarded because this file is a template part, and a template part can be
 * included more than once in a request without warning.
 *
 * @param array $logo Logo definition.
 */
if ( ! function_exists( 'csc_render_logo' ) ) :
	function csc_render_logo( array $logo ): void {
		$base = CSC_URI . '/assets/images/' . $logo['slug'] . '-logo';
		$file = CSC_DIR . '/assets/images/' . $logo['slug'] . '-logo.webp';

		/*
		 * Cache bust on the file's own modification time.
		 *
		 * Logo files get replaced under the same filename, so without this a
		 * browser that has seen the old one keeps showing it. That happened
		 * during the build: the file on disk was correct and the page still
		 * looked wrong. It would happen again the day the official CrowdStrike
		 * file arrives, and would be blamed on the swap rather than the cache.
		 */
		$ver  = file_exists( $file ) ? '?v=' . filemtime( $file ) : '';
		?>
	<img
		class="csc-lockup__mark csc-lockup__mark--<?php echo esc_attr( $logo['slug'] ); ?>"
		src="<?php echo esc_url( $base . '.webp' . $ver ); ?>"
		srcset="<?php echo esc_attr( $base . '-1x.webp' . $ver . ' ' . $logo['w1x'] . 'w, ' . $base . '.webp' . $ver . ' ' . $logo['w2x'] . 'w' ); ?>"
		sizes="<?php echo esc_attr( "(max-width: 30rem) " . round( $logo['w1x'] * 32 / 56 ) . "px, " . $logo['w1x'] . "px" ); ?>"
		width="<?php echo esc_attr( (string) $logo['w2x'] ); ?>"
		height="<?php echo esc_attr( (string) $logo['height'] ); ?>"
		alt="<?php echo esc_attr( $logo['alt'] ); ?>"
		fetchpriority="high"
		decoding="async"
	>
		<?php
	}
endif;
?>
<div class="csc-lockup">
	<?php csc_render_logo( $csc_logos[0] ); ?>

	<span class="csc-lockup__rule" aria-hidden="true"></span>

	<?php csc_render_logo( $csc_logos[1] ); ?>
</div>
