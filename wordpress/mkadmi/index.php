<?php
/**
 * The fallback template: the blog, and anything without a template of its own.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() && ! is_front_page() ) {
	$mkadmi_posts_page = get_option( 'page_for_posts' );

	if ( $mkadmi_posts_page ) {
		printf( '<h1 class="page-title">%s</h1>', esc_html( get_the_title( $mkadmi_posts_page ) ) );
	}
}

if ( have_posts() ) {
	echo '<div class="entries">';

	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/post' );
	}

	echo '</div>';

	mkadmi_pagination();
} else {
	get_template_part( 'template-parts/content/none' );
}

get_footer();
