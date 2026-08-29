<?php
/**
 * The column of panels beside the content.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside class="sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Site panels', 'mkadmi' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
