<?php
/**
 * Archives: categories, tags, dates, research areas, courses and projects.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();

mkadmi_breadcrumbs();

if ( have_posts() ) {
	echo '<header class="archive-header">';
	the_archive_title( '<h1 class="page-title">', '</h1>' );
	the_archive_description( '<div class="archive-description">', '</div>' );
	echo '</header>';

	$mkadmi_is_cards = is_post_type_archive( array( 'mkadmi_course', 'mkadmi_project' ) )
		|| is_tax( array( 'mkadmi_level' ) );

	printf( '<div class="%s">', $mkadmi_is_cards ? 'entries entries--cards' : 'entries' );

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
