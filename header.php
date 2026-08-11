<?php
/**
 * Document head and the co-brand logo lockup.
 *
 * @package crowdstrike-campaign
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="csc-skip-link" href="#csc-main">Skip to content</a>

<header class="csc-masthead">
	<?php get_template_part( 'template-parts/logo-lockup' ); ?>
</header>

<main id="csc-main" class="csc-main">
