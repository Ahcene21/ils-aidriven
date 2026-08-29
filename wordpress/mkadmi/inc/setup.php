<?php
/**
 * Theme supports, menus, sidebars and editor integration.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the features the theme relies on.
 */
function mkadmi_setup() {
	load_theme_textdomain( 'mkadmi', MKADMI_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 96,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'custom-background',
		array( 'default-color' => 'ffffff' )
	);

	// The portrait that leads the homepage doubles as the site's header image.
	add_theme_support(
		'custom-header',
		array(
			'width'         => 900,
			'height'        => 1100,
			'flex-height'   => true,
			'flex-width'    => true,
			'header-text'   => false,
			'video'         => false,
		)
	);

	add_image_size( 'mkadmi-portrait', 720, 880, true );
	add_image_size( 'mkadmi-card', 720, 420, true );

	register_nav_menus(
		array(
			'primary'  => __( 'Primary menu', 'mkadmi' ),
			'language' => __( 'Language switcher', 'mkadmi' ),
			'footer'   => __( 'Footer menu', 'mkadmi' ),
		)
	);

	// Content width used by oEmbed and wide images.
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 768;
	}
}
add_action( 'after_setup_theme', 'mkadmi_setup' );

/**
 * Widget areas.
 */
function mkadmi_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar panels', 'mkadmi' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'The column of panels beside the content: navigation, affiliations, profiles, languages, visits, calendar.', 'mkadmi' ),
			'before_widget' => '<section id="%1$s" class="widget panel %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="panel__title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer', 'mkadmi' ),
			'id'            => 'footer-1',
			'description'   => __( 'Optional widgets above the footer links, side by side.', 'mkadmi' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'mkadmi_widgets_init' );

/**
 * Body classes that the stylesheet keys off.
 *
 * @param array $classes Existing classes.
 * @return array
 */
function mkadmi_body_classes( $classes ) {
	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'templates/full-width.php' ) ) {
		$classes[] = 'no-sidebar';
	}

	if ( is_singular() && has_post_thumbnail() ) {
		$classes[] = 'has-featured-image';
	}

	return $classes;
}
add_filter( 'body_class', 'mkadmi_body_classes' );

/**
 * Length of the automatic excerpt, in words.
 *
 * @param int $length Default length.
 * @return int
 */
function mkadmi_excerpt_length( $length ) {
	return is_admin() ? $length : 34;
}
add_filter( 'excerpt_length', 'mkadmi_excerpt_length' );

/**
 * Ellipsis used when an excerpt is trimmed.
 *
 * @return string
 */
function mkadmi_excerpt_more() {
	return is_admin() ? '[&hellip;]' : '&hellip;';
}
add_filter( 'excerpt_more', 'mkadmi_excerpt_more' );

/**
 * Give the "read more" link an accessible name that says what is being read.
 *
 * @param string $link Default link markup.
 * @return string
 */
function mkadmi_content_more_link( $link ) {
	$screen_reader = sprintf(
		/* translators: %s: post title. */
		__( 'Continue reading %s', 'mkadmi' ),
		get_the_title()
	);

	return str_replace(
		'</a>',
		'<span class="screen-reader-text"> ' . esc_html( $screen_reader ) . '</span></a>',
		$link
	);
}
add_filter( 'the_content_more_link', 'mkadmi_content_more_link' );
