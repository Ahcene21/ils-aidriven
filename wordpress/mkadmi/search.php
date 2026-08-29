<?php
/**
 * Search results.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<header class="archive-header">
	<h1 class="page-title">
		<?php
		printf(
			/* translators: %s: search query. */
			esc_html__( 'Search results for “%s”', 'mkadmi' ),
			esc_html( get_search_query() )
		);
		?>
	</h1>

	<?php get_search_form(); ?>
</header>

<?php
if ( have_posts() ) {
	echo '<div class="entries">';

	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/entry', get_post_type() );
	}

	echo '</div>';

	mkadmi_pagination();
} else {
	get_template_part( 'template-parts/content/none' );
}

get_footer();
