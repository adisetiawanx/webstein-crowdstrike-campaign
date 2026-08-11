<?php
/**
 * Fallback template.
 *
 * The microsite is two pages, both built as block content, so this template
 * simply renders the block markup for whatever is requested.
 *
 * @package crowdstrike-campaign
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		?>
		<div class="entry-content has-global-padding is-layout-constrained">
			<?php the_content(); ?>
		</div>
		<?php
	}
} else {
	?>
	<div class="csc-notfound">
		<h1>Page not found</h1>
		<p>The page you are looking for is not available.</p>
	</div>
	<?php
}

get_footer();
