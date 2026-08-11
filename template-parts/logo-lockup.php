<?php
/**
 * CrowdStrike and Mimecast co-brand lockup.
 *
 * Shared by both pages. Issue #3.
 *
 * PROVENANCE WARNING. These logo files were sourced from the internet, not from
 * a brand kit supplied by the client. Two measured differences from the approved
 * mockup are recorded in BUILD-NOTES.md and flagged in REPORT.md:
 *
 *   1. The Mimecast mark here is navy #00003F. The approved mockup draws it in
 *      black #000000.
 *   2. The CrowdStrike mark here has an ink ratio of 5.56:1. The mockup draws it
 *      at 7.32:1, so it is not the same version of the mark.
 *
 * Both are aligned on height, which is how the mockup aligns them. Replace both
 * files with the official assets as soon as Excelerate supplies them.
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
		'w1x'    => 326,
		'w2x'    => 651,
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
