<?php
/**
 * FihrisTech theme bootstrap.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FH_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/defaults.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/contact.php';
require_once get_template_directory() . '/inc/seo.php';

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'fihristech', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 400, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	register_nav_menus( array( 'primary' => __( 'Primary menu', 'fihristech' ), 'footer' => __( 'Footer menu', 'fihristech' ) ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$fonts = is_rtl()
		? 'https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap'
		: 'https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@500;600;700;800&display=swap';
	wp_enqueue_style( 'fh-fonts', $fonts, array(), null );
	wp_enqueue_style( 'fh-style', get_stylesheet_uri(), array( 'fh-fonts' ), FH_VERSION );
	if ( is_rtl() ) {
		wp_enqueue_style( 'fh-rtl', get_template_directory_uri() . '/assets/css/rtl.css', array( 'fh-style' ), FH_VERSION );
	}
	wp_enqueue_script( 'fh-main', get_template_directory_uri() . '/assets/js/main.js', array(), FH_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
} );

/** Trim head bloat and harden a few defaults. */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'xmlrpc_enabled', '__return_false' );

/** Read a theme option with its default. */
function fh_opt( $key ) {
	$defaults = fh_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/** Split a textarea option into trimmed non-empty lines. */
function fh_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $text ) ) ) );
}

function fh_logo() {
	if ( has_custom_logo() ) {
		return get_custom_logo();
	}
	return sprintf(
		'<a class="logo" href="%s" aria-label="%s"><img src="%s" alt="%s" width="2034" height="663"></a>',
		esc_url( home_url( '/' ) ),
		esc_attr( get_bloginfo( 'name' ) ),
		esc_url( get_template_directory_uri() . '/assets/img/logo.png' ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}

/** Anchor targets on the front page, real URLs elsewhere. */
function fh_anchor( $id ) {
	return is_front_page() ? '#' . $id : home_url( '/#' . $id );
}
