<?php
/**
 * A single post, publication, course, project or conference.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();

mkadmi_breadcrumbs();

while ( have_posts() ) {
	the_post();

	get_template_part( 'template-parts/content/single', get_post_type() );

	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

	the_post_navigation(
		array(
			'prev_text' => '<span class="nav__direction">' . esc_html__( 'Previous', 'mkadmi' ) . '</span><span class="nav__title">%title</span>',
			'next_text' => '<span class="nav__direction">' . esc_html__( 'Next', 'mkadmi' ) . '</span><span class="nav__title">%title</span>',
			'class'     => 'post-navigation',
		)
	);
}

get_footer();
