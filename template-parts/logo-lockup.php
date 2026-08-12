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
 * the internet and is a different version of the mark: its ink ratio is 5.56:1
 * where the mockup draws 7.32:1, which means the swoosh is proportionally
 * larger, so at a matched height the wordmark reads smaller than the design.
 * There is no scale that fixes both. Still outstanding, see issue #19.
 *
 * The two marks are aligned on height, which is how the mockup aligns them.
 *
 * @package crowdstrike-campaign
 */

defined( 'ABSPATH' ) || exit;

/*
 * Two widths each. The mark renders between 32px and 56px tall, so its widest
 * rendered width is about 311px. Serving only the 2x file meant phones
 * downloading roughly three times the pixels they could display. Issue #15.
 */
$csc_logos = array(
	array(
		'slug'   => 'crowdstrike',
		'alt'    => 'CrowdStrike',
		'w1x'    => 311,
		'w2x'    => 623,
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
	?>
	<img
		class="csc-lockup__mark"
		src="<?php echo esc_url( $base . '.webp' ); ?>"
		srcset="<?php echo esc_attr( $base . '-1x.webp ' . $logo['w1x'] . 'w, ' . $base . '.webp ' . $logo['w2x'] . 'w' ); ?>"
		sizes="(max-width: 30rem) 178px, 311px"
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
