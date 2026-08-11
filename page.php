<?php
/**
 * Page template.
 *
 * Both pages of the microsite are one-off, so their content is authored as core
 * Gutenberg blocks and rendered here. See issue #2 for why this build does not
 * use custom blocks or a field group.
 *
 * @package crowdstrike-campaign
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	?>
	<div class="entry-content has-global-padding is-layout-constrained">
		<?php the_content(); ?>
	</div>
	<?php
}

get_footer();
