<?php
/**
 * CrowdStrike Campaign theme setup.
 *
 * Design tokens live in theme.json. Front end styles are in assets/css/theme.css.
 * See BUILD-NOTES.md for the decisions behind this structure.
 *
 * @package crowdstrike-campaign
 */

defined( 'ABSPATH' ) || exit;

define( 'CSC_VERSION', '0.1.0' );
define( 'CSC_DIR', get_template_directory() );
define( 'CSC_URI', get_template_directory_uri() );

/**
 * GA4 measurement ID.
 *
 * Deliberately empty. Analytics is on hold pending a decision on whose property
 * to use, client or Webstein. See issue #11. Setting this constant is the only
 * change needed to switch tracking on, no template edits.
 */
define( 'CSC_GA4_MEASUREMENT_ID', '' );

/**
 * Theme supports.
 */
function csc_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	// The microsite has no navigation in the approved design, so none is registered.
	add_editor_style( 'assets/css/theme.css' );
}
add_action( 'after_setup_theme', 'csc_setup' );

/**
 * Front end assets.
 */
function csc_assets(): void {
	wp_enqueue_style(
		'csc-theme',
		CSC_URI . '/assets/css/theme.css',
		array(),
		CSC_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'csc_assets' );

/**
 * Preload the self-hosted variable font.
 *
 * The font is the only render blocking asset that matters here, so it is
 * preloaded rather than discovered late in the CSS.
 */
function csc_preload_font(): void {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( CSC_URI . '/assets/fonts/outfit-variable.woff2' )
	);
}
add_action( 'wp_head', 'csc_preload_font', 1 );

/**
 * Trim front end weight this microsite does not use.
 *
 * No performance plugin is installed. A correctly built lightweight theme does
 * not need one, and plugins of that kind create their own support burden.
 */
function csc_trim_head(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}
add_action( 'init', 'csc_trim_head' );

/**
 * Drop the core block library duotone filters and global styles this build never uses.
 */
function csc_dequeue_unused(): void {
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'csc_dequeue_unused', 20 );

/**
 * Australian English document language.
 *
 * Set here as well as in Settings so the value is correct regardless of how the
 * site is installed or migrated to GridPane.
 */
function csc_language_attributes( string $output ): string {
	return 'lang="en-AU"';
}
add_filter( 'language_attributes', 'csc_language_attributes' );

/**
 * This is a two page campaign microsite. Comments are not part of it.
 */
function csc_disable_comments(): bool {
	return false;
}
add_filter( 'comments_open', 'csc_disable_comments' );
add_filter( 'pings_open', 'csc_disable_comments' );
