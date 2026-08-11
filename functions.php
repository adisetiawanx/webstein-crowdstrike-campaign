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

	// Only loaded where the form actually is. Issue #10.
	if ( ! is_page( 'thank-you' ) ) {
		wp_enqueue_script(
			'csc-utm',
			CSC_URI . '/assets/js/utm.js',
			array(),
			CSC_VERSION,
			true
		);

		wp_localize_script(
			'csc-utm',
			'cscUtm',
			array( 'fields' => csc_utm_field_map() )
		);
	}
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
 * Map UTM parameter names to their Gravity Forms field ids.
 *
 * Read from the form itself rather than hard coded, so that rebuilding or
 * reordering the form cannot silently break campaign tracking. Returns an empty
 * array if Gravity Forms is not active, which is the correct behaviour: no
 * form, nothing to populate.
 *
 * @return array<string, string>
 */
function csc_utm_field_map(): array {
	if ( ! class_exists( 'GFAPI' ) ) {
		return array();
	}

	$form = GFAPI::get_form( 1 );
	if ( ! $form || empty( $form['fields'] ) ) {
		return array();
	}

	$map = array();
	foreach ( $form['fields'] as $field ) {
		if ( 'hidden' === $field->type && ! empty( $field->inputName )
			&& str_starts_with( $field->inputName, 'utm_' ) ) {
			$map[ $field->inputName ] = (string) $field->id;
		}
	}

	return $map;
}

/**
 * Add a slug based body class.
 *
 * WordPress does not add one for pages, only `page-id-N`, and an id is a poor
 * thing to hang styles on because it changes between environments. The Thank
 * You page needs its own background and masthead spacing, so it needs a stable
 * hook: `page-thank-you`.
 */
function csc_body_class( array $classes ): array {
	if ( is_page() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post && $post->post_name ) {
			$classes[] = 'page-' . sanitize_html_class( $post->post_name );
		}
	}

	return $classes;
}
add_filter( 'body_class', 'csc_body_class' );

/**
 * Ship none of Gravity Forms' own CSS.
 *
 * The form is styled from scratch in assets/css/theme.css to match the approved
 * mockup. Loading the plugin's stylesheet as well would mean overriding it
 * selector by selector, and would put a stylesheet on the page that the design
 * never uses. This keeps the front end to the theme's own CSS.
 *
 * The trade off is that every state has to be written by hand, including
 * validation errors. That is done, see section 9 of theme.css.
 */
add_filter( 'gform_disable_css', '__return_true' );

/**
 * This is a two page campaign microsite. Comments are not part of it.
 */
function csc_disable_comments(): bool {
	return false;
}
add_filter( 'comments_open', 'csc_disable_comments' );
add_filter( 'pings_open', 'csc_disable_comments' );
