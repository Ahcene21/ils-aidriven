<?php
/**
 * Signed codes carried by the QR images.
 *
 * Session code:  "<session id>-<time window>-<signature>". The window advances
 * every `rotation` seconds, so a photo of the projected code stops working
 * shortly after it is taken. The code of the previous window is still accepted
 * to cover the time between a scan and the request reaching the server.
 *
 * Student card:  "QRA-<user id>-<signature>". Static; shown by the student and
 * scanned by the teacher.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

class QRA_Token {

	const SIG_LENGTH = 16;

	public static function rotation() {
		return max( 0, (int) QRA_Plugin::setting( 'rotation' ) );
	}

	public static function current_window() {
		$rotation = self::rotation();
		return $rotation ? (int) floor( time() / $rotation ) : 0;
	}

	/** Seconds until the current session code is replaced (0 when static). */
	public static function seconds_left() {
		$rotation = self::rotation();
		return $rotation ? $rotation - ( time() % $rotation ) : 0;
	}

	private static function sign( $data, $secret ) {
		return substr( hash_hmac( 'sha256', $data, $secret . wp_salt( 'nonce' ) ), 0, self::SIG_LENGTH );
	}

	public static function session_code( $session, $window = null ) {
		$window = null === $window ? self::current_window() : (int) $window;
		$data   = (int) $session->id . '-' . $window;
		return $data . '-' . self::sign( $data, $session->secret );
	}

	/** URL placed in the QR: opening it with any camera app checks the student in. */
	public static function session_url( $session ) {
		return add_query_arg( 'qra', self::session_code( $session ), home_url( '/' ) );
	}

	/**
	 * Pull a session code out of whatever the scanner read (bare code or URL).
	 */
	public static function extract_session_code( $raw ) {
		$raw = trim( (string) $raw );
		if ( preg_match( '/[?&]qra=([0-9]+-[0-9]+-[0-9a-f]+)/i', $raw, $m ) ) {
			return strtolower( $m[1] );
		}
		if ( preg_match( '/^[0-9]+-[0-9]+-[0-9a-f]+$/i', $raw ) ) {
			return strtolower( $raw );
		}
		return '';
	}

	/**
	 * Validate a session code.
	 *
	 * @return object|WP_Error The session row.
	 */
	public static function verify_session_code( $code ) {
		$parts = explode( '-', (string) $code );
		if ( 3 !== count( $parts ) ) {
			return new WP_Error( 'qra_invalid_code', __( 'This QR code is not an attendance code.', 'qr-attendance' ) );
		}
		list( $session_id, $window, $sig ) = $parts;

		$session = QRA_DB::get_session( (int) $session_id );
		if ( ! $session || ! hash_equals( self::sign( (int) $session_id . '-' . (int) $window, $session->secret ), $sig ) ) {
			return new WP_Error( 'qra_invalid_code', __( 'This QR code is not an attendance code.', 'qr-attendance' ) );
		}

		$current = self::current_window();
		if ( self::rotation() && ( (int) $window > $current || (int) $window < $current - 1 ) ) {
			return new WP_Error( 'qra_expired_code', __( 'This QR code has expired. Scan the code currently on the screen.', 'qr-attendance' ) );
		}
		if ( ! self::rotation() && 0 !== (int) $window ) {
			return new WP_Error( 'qra_expired_code', __( 'This QR code has expired. Scan the code currently on the screen.', 'qr-attendance' ) );
		}

		return $session;
	}

	public static function card_code( $user_id ) {
		$data = 'QRA-' . (int) $user_id;
		return $data . '-' . self::sign( $data, 'card' );
	}

	/**
	 * @return int|WP_Error The student's user id.
	 */
	public static function verify_card_code( $code ) {
		if ( ! preg_match( '/^QRA-([0-9]+)-([0-9a-f]+)$/i', trim( (string) $code ), $m ) ) {
			return new WP_Error( 'qra_invalid_card', __( 'This QR code is not a student card.', 'qr-attendance' ) );
		}
		if ( ! hash_equals( self::card_code( (int) $m[1] ), 'QRA-' . (int) $m[1] . '-' . strtolower( $m[2] ) ) ) {
			return new WP_Error( 'qra_invalid_card', __( 'This QR code is not a student card.', 'qr-attendance' ) );
		}
		return (int) $m[1];
	}
}
