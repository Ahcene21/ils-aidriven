<?php
/**
 * Mkadmi theme bootstrap.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

define( 'MKADMI_VERSION', '1.0.0' );
define( 'MKADMI_DIR', get_template_directory() );
define( 'MKADMI_URI', get_template_directory_uri() );

require MKADMI_DIR . '/inc/setup.php';
require MKADMI_DIR . '/inc/assets.php';
require MKADMI_DIR . '/inc/post-types.php';
require MKADMI_DIR . '/inc/meta-boxes.php';
require MKADMI_DIR . '/inc/template-tags.php';
require MKADMI_DIR . '/inc/widgets.php';
require MKADMI_DIR . '/inc/customizer.php';
require MKADMI_DIR . '/inc/customizer-css.php';
require MKADMI_DIR . '/inc/starter-content.php';
