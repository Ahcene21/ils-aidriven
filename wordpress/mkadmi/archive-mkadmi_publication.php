<?php
/**
 * The publications archive, grouped under a heading per year.
 *
 * A publication list is read by year, so the year is a heading rather than a
 * column: it gives the page landmarks to scroll between.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();

mkadmi_breadcrumbs();

echo '<header class="archive-header">';
the_archive_title( '<h1 class="page-title">', '</h1>' );
the_archive_description( '<div class="archive-description">', '</div>' );
echo '</header>';

if ( have_posts() ) {
	$mkadmi_current_year = null;
	$mkadmi_open         = false;

	while ( have_posts() ) {
		the_post();

		$mkadmi_year = mkadmi_field( 'year' );
		$mkadmi_year = $mkadmi_year ? $mkadmi_year : __( 'Undated', 'mkadmi' );

		if ( $mkadmi_year !== $mkadmi_current_year ) {
			if ( $mkadmi_open ) {
				echo '</ul>';
			}

			printf( '<h2 class="year-heading">%s</h2>', esc_html( $mkadmi_year ) );
			echo '<ul class="publications">';

			$mkadmi_current_year = $mkadmi_year;
			$mkadmi_open         = true;
		}

		get_template_part( 'template-parts/content/publication' );
	}

	if ( $mkadmi_open ) {
		echo '</ul>';
	}

	mkadmi_pagination();
} else {
	get_template_part( 'template-parts/content/none' );
}

get_footer();
