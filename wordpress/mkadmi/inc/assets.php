<?php
/**
 * Front-end and admin asset loading.
 *
 * Everything the theme needs is served from the theme folder: no web fonts, no
 * CDNs, no third-party scripts. An academic site should not leak its readers to
 * anyone, and it should keep working offline on a campus network.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end styles and scripts.
 */
function mkadmi_enqueue_assets() {
	// The theme header lives in style.css, so WordPress expects it registered.
	wp_enqueue_style( 'mkadmi-style', get_stylesheet_uri(), array(), MKADMI_VERSION );

	wp_enqueue_style(
		'mkadmi-main',
		MKADMI_URI . '/assets/css/main.css',
		array( 'mkadmi-style' ),
		MKADMI_VERSION
	);

	wp_add_inline_style( 'mkadmi-main', mkadmi_customizer_css() );

	wp_enqueue_script(
		'mkadmi-theme',
		MKADMI_URI . '/assets/js/theme.js',
		array(),
		MKADMI_VERSION,
		true
	);

	wp_localize_script(
		'mkadmi-theme',
		'mkadmiL10n',
		array(
			'openMenu'  => __( 'Open menu', 'mkadmi' ),
			'closeMenu' => __( 'Close menu', 'mkadmi' ),
			'copied'    => __( 'Copied', 'mkadmi' ),
			'copy'      => __( 'Copy', 'mkadmi' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mkadmi_enqueue_assets' );

/**
 * Match the editor canvas to the front end.
 */
function mkadmi_enqueue_editor_assets() {
	wp_add_inline_style( 'wp-block-library', mkadmi_customizer_css() );
}
add_action( 'enqueue_block_editor_assets', 'mkadmi_enqueue_editor_assets' );

/**
 * Styles for the publication and course meta boxes.
 *
 * @param string $hook Current admin page.
 */
function mkadmi_enqueue_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	wp_enqueue_style(
		'mkadmi-admin',
		MKADMI_URI . '/assets/css/admin.css',
		array(),
		MKADMI_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'mkadmi_enqueue_admin_assets' );
