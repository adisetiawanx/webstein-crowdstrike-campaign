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

define( 'CSC_VERSION', '0.2.6' );
define( 'CSC_DIR', get_template_directory() );
define( 'CSC_URI', get_template_directory_uri() );

/**
 * GA4 measurement ID.
 *
 * Still empty, but no longer undecided. Mika settled it on 13 August 2026: GA4
 * goes on at launch using Webstein's own GA account, not the client's. That
 * answers the question issue #11 was open on.
 *
 * All that is missing now is the measurement ID itself. Setting this constant is
 * the only change needed to switch tracking on, no template edits.
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
			'csc-form',
			CSC_URI . '/assets/js/form.js',
			array(),
			CSC_VERSION,
			true
		);

		wp_localize_script(
			'csc-form',
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
 * Fallback favicon.
 *
 * The site had no icon at all, so browsers drew their own default. Mika flagged
 * it on 13 August 2026.
 *
 * The proper home for this is Settings > Site Identity, and this function stands
 * aside the moment a Site Icon is set there, so it cannot end up fighting with
 * one or printing a second icon tag. It exists because the theme can be deployed
 * by file copy while the database cannot, so shipping the icon in the theme is
 * the only way to guarantee both environments have one.
 *
 * Drawn from the official CrowdStrike export rather than invented: the swoosh
 * with the C, which is the part of the mark that survives being shrunk.
 */
function csc_fallback_favicon(): void {
	if ( has_site_icon() ) {
		return;
	}

	$icon = CSC_URI . '/assets/images/favicon-512.png';

	printf(
		'<link rel="icon" href="%1$s" sizes="512x512">' . "\n" .
		'<link rel="apple-touch-icon" href="%1$s">' . "\n",
		esc_url( $icon )
	);
}
add_action( 'wp_head', 'csc_fallback_favicon', 2 );

/**
 * Meta description.
 *
 * No SEO plugin is installed on a two page microsite, and Lighthouse correctly
 * flagged the pages as having no description.
 *
 * The wording is NOT written here. It is the client's own approved sentence,
 * taken verbatim from the Canva mockup for each page, so nothing has been
 * invented. Excelerate should still review it, since a meta description is
 * public facing copy. Recorded in REPORT.md.
 */
function csc_meta_description(): void {
	if ( is_page( 'thank-you' ) ) {
		$description = 'Your request has been successfully submitted. A CrowdStrike and Mimecast security specialist will review your enquiry and contact you.';
	} elseif ( is_front_page() ) {
		$description = 'See how CrowdStrike and Mimecast work together to help organisations strengthen cyber resilience, improve threat visibility and respond faster across email, endpoint and the wider attack surface.';
	} else {
		return;
	}

	printf(
		'<meta name="description" content="%s">' . "\n",
		esc_attr( $description )
	);
}
add_action( 'wp_head', 'csc_meta_description', 2 );

/**
 * Keep the Thank You page out of search results.
 *
 * It is a form confirmation. It has no value as a landing page, and if it ranks
 * then people arrive at a "thank you" for something they never submitted.
 *
 * This goes through the `wp_robots` filter rather than printing a tag in
 * wp_head. WordPress already emits its own robots meta, so printing a second
 * one produces two conflicting tags and search engines are free to pick either.
 * The first version of this did exactly that and the verification pass caught
 * it.
 *
 * @param array<string, mixed> $robots Robots directives.
 * @return array<string, mixed>
 */
function csc_noindex_thank_you( array $robots ): array {
	if ( is_page( 'thank-you' ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'csc_noindex_thank_you' );

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
 * Drop the Gravity Forms JavaScript this form does not use.
 *
 * ONLY `gform_placeholder` is removed. It is a polyfill that gives IE9 support
 * for the placeholder attribute, which every browser has supported natively for
 * over a decade.
 *
 * DO NOT extend this list without testing. Removing the Gravity Forms theme
 * framework scripts was tried first, because the plugin's CSS is disabled and
 * they looked unused. It saved 59KB and it broke the form: submissions stopped
 * redirecting to the Thank You page and the console threw
 * "Cannot read properties of undefined (reading 'trigger')". They were put
 * back. The saving is not worth a form that silently stops working.
 *
 * If you touch this, re-run the end to end submission test afterwards. A broken
 * form here does not look broken, it just quietly stops producing leads.
 */
function csc_trim_gravityforms_js(): void {
	if ( is_admin() ) {
		return;
	}

	wp_dequeue_script( 'gform_placeholder' );
}

/**
 * Move jQuery out of the document head.
 *
 * Lighthouse measured jQuery and jQuery Migrate as the largest render blocking
 * cost on the page, roughly 600ms on its simulated connection, purely because
 * WordPress prints them in the head.
 *
 * Nothing on this site needs jQuery before first paint. Gravity Forms prints
 * its own inline scripts in the footer, after this.
 *
 * Verified by re-running the end to end form test after the change, because the
 * last attempt at trimming Gravity Forms' JavaScript broke submissions
 * silently. If you change this, test the form again.
 */
function csc_jquery_to_footer(): void {
	if ( is_admin() ) {
		return;
	}

	foreach ( array( 'jquery', 'jquery-core', 'jquery-migrate' ) as $handle ) {
		$script = wp_scripts()->query( $handle, 'registered' );
		if ( $script ) {
			wp_scripts()->add_data( $handle, 'group', 1 );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'csc_jquery_to_footer', 100 );
add_action( 'wp_print_scripts', 'csc_trim_gravityforms_js', 100 );
add_action( 'wp_print_footer_scripts', 'csc_trim_gravityforms_js', 1 );

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

	/*
	 * Both pages carry the dark top section the client asked for on 14 August
	 * 2026, so the styling keys off one class rather than naming both pages in
	 * every selector. Section 3B of theme.css is the whole of it.
	 *
	 * A class rather than a page test in the CSS because the two pages arrived at
	 * it separately, hours apart, and a third could follow.
	 */
	if ( is_front_page() || is_page( 'thank-you' ) ) {
		$classes[] = 'csc-dark-top';
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
