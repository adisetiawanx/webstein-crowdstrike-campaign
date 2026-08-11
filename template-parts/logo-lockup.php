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

$csc_logos = array(
	array(
		'file'   => 'crowdstrike-logo.webp',
		'alt'    => 'CrowdStrike',
		'width'  => 623,
		'height' => 112,
	),
	array(
		'file'   => 'mimecast-logo.webp',
		'alt'    => 'Mimecast',
		'width'  => 651,
		'height' => 112,
	),
);
?>
<div class="csc-lockup">
	<img
		class="csc-lockup__mark"
		src="<?php echo esc_url( CSC_URI . '/assets/images/' . $csc_logos[0]['file'] ); ?>"
		width="<?php echo esc_attr( (string) $csc_logos[0]['width'] ); ?>"
		height="<?php echo esc_attr( (string) $csc_logos[0]['height'] ); ?>"
		alt="<?php echo esc_attr( $csc_logos[0]['alt'] ); ?>"
		fetchpriority="high"
		decoding="async"
	>

	<span class="csc-lockup__rule" aria-hidden="true"></span>

	<img
		class="csc-lockup__mark"
		src="<?php echo esc_url( CSC_URI . '/assets/images/' . $csc_logos[1]['file'] ); ?>"
		width="<?php echo esc_attr( (string) $csc_logos[1]['width'] ); ?>"
		height="<?php echo esc_attr( (string) $csc_logos[1]['height'] ); ?>"
		alt="<?php echo esc_attr( $csc_logos[1]['alt'] ); ?>"
		fetchpriority="high"
		decoding="async"
	>
</div>
