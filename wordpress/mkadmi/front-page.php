<?php
/**
 * The homepage.
 *
 * Each section is a template part that renders nothing when it has nothing to
 * say, so the page is built from whatever the site actually has.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();

// A static front page may carry an introduction of its own.
if ( is_page() && have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$mkadmi_intro = get_the_content();

		if ( '' !== trim( wp_strip_all_tags( $mkadmi_intro ) ) ) {
			echo '<section class="section section--intro"><div class="entry__content">';
			the_content();
			echo '</div></section>';
		}
	}

	rewind_posts();
}

get_template_part( 'template-parts/home/topics' );
get_template_part( 'template-parts/home/news' );
get_template_part( 'template-parts/home/events' );
get_template_part( 'template-parts/home/publications' );
get_template_part( 'template-parts/home/courses' );
get_template_part( 'template-parts/home/projects' );

get_footer();
