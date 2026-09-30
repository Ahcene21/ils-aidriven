<?php
/**
 * Runs when the plugin is deleted from the Plugins screen.
 *
 * Attendance records are only removed when the "delete data" setting is on,
 * so deleting the plugin by mistake does not wipe a semester of records.
 *
 * @package QR_Attendance
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

$qra_settings = get_option( 'qra_settings', array() );
if ( empty( $qra_settings['remove_data'] ) ) {
	return;
}

global $wpdb;
foreach ( array( 'attendance', 'sessions', 'enrollments', 'courses' ) as $qra_table ) {
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'qra_' . $qra_table ); // phpcs:ignore WordPress.DB
}
$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key = 'qra_student_number'" ); // phpcs:ignore WordPress.DB

delete_option( 'qra_settings' );
delete_option( 'qra_db_version' );
remove_role( 'qra_teacher' );
remove_role( 'qra_student' );

$qra_admin = get_role( 'administrator' );
if ( $qra_admin ) {
	$qra_admin->remove_cap( 'qra_manage_attendance' );
}
