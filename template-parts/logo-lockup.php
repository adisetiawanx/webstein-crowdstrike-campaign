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
 * CROWDSTRIKE is now official too. Exported from the Canva design on 13 August
 * 2026 at 1334x186 with real transparency, ink ratio 7.279:1 against the
 * mockup's 7.333:1, a 0.8% match.
 *
 * That retired two workarounds. The previous file was sourced from the internet,
 * was a different version of the mark at 5.56:1, and had 32px of its swoosh tail
 * cropped off to force the ratio, which meant shipping a modified trademark. It
 * also had to be rendered 60px tall against Mimecast's 52px so its wordmark read
 * at the right size, and carried a 4px nudge to recentre it. All of that is gone.
 *
 * The two marks are aligned on height, which is how the mockup aligns them: it
 * draws them 616x84 and 511x83.
 *
 * Do not take a "cleaner" export from the PNG mockups. Measured, the lockup in
 * `designs/Landing page.png` gives CrowdStrike only 616px wide, well below the
 * 1332px the Canva export gives. Export from Canva, not from the PNGs.
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
		'w1x'    => 408,
		'w2x'    => 815,
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
