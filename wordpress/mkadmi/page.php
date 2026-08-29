<?php
/**
 * A page.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();

mkadmi_breadcrumbs();

while ( have_posts() ) {
	the_post();

	get_template_part( 'template-parts/content/page' );

	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
