<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_nopriv_fh_contact', 'fh_handle_contact' );
add_action( 'admin_post_fh_contact', 'fh_handle_contact' );

function fh_contact_redirect( $status ) {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'fh_contact', $status, remove_query_arg( 'fh_contact', $back ) ) . '#contact' );
	exit;
}

function fh_handle_contact() {
	if ( ! isset( $_POST['fh_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fh_nonce'] ) ), 'fh_contact' ) ) {
		fh_contact_redirect( 'error' );
	}
	// Honeypot and minimum fill time to deflect bots.
	$ts = isset( $_POST['fh_ts'] ) ? (int) $_POST['fh_ts'] : 0;
	if ( ! empty( $_POST['fh_website'] ) || ( $ts && time() - $ts < 3 ) ) {
		fh_contact_redirect( 'sent' );
	}
	// Simple per-IP throttle: 5 messages per hour.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	$key = 'fh_rl_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 5 ) {
		fh_contact_redirect( 'error' );
	}
	set_transient( $key, $n + 1, HOUR_IN_SECONDS );

	$name  = isset( $_POST['fh_name'] ) ? sanitize_text_field( wp_unslash( $_POST['fh_name'] ) ) : '';
	$email = isset( $_POST['fh_email'] ) ? sanitize_email( wp_unslash( $_POST['fh_email'] ) ) : '';
	$msg   = isset( $_POST['fh_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['fh_message'] ) ) : '';
	if ( '' === $name || ! is_email( $email ) || '' === $msg ) {
		fh_contact_redirect( 'invalid' );
	}

	wp_insert_post( array(
		'post_type'    => 'fh_message',
		'post_status'  => 'private',
		/* translators: %s: sender name */
		'post_title'   => sprintf( __( 'Message from %s', 'fihristech' ), $name ),
		'post_content' => $msg . "\n\n" . $name . ' <' . $email . '>',
	) );

	$to = fh_opt( 'contact_email' );
	$to = is_email( $to ) ? $to : get_option( 'admin_email' );
	wp_mail(
		$to,
		/* translators: %s: sender name */
		sprintf( __( '[%1$s] New message from %2$s', 'fihristech' ), wp_specialchars_decode( get_bloginfo( 'name' ) ), $name ),
		$msg . "\n\n" . $name . ' <' . $email . '>',
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);
	fh_contact_redirect( 'sent' );
}
