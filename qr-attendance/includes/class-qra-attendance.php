<?php
/**
 * Attendance rules: checking in, marking, opening and closing sessions.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

class QRA_Attendance {

	const DEVICE_COOKIE = 'qra_device';
	const CLAIM_TTL     = 600;

	/* ------------------------------------------------------------------
	 * Sessions
	 * ------------------------------------------------------------------ */

	/**
	 * Start a session now.
	 *
	 * @param int $duration Minutes it stays open; 0 = until closed by hand.
	 */
	public static function start_session( $course_id, $title, $duration ) {
		$now  = time();
		$ends = $duration > 0 ? gmdate( 'Y-m-d H:i:s', $now + $duration * MINUTE_IN_SECONDS ) : null;
		return QRA_DB::create_session( $course_id, $title, gmdate( 'Y-m-d H:i:s', $now ), $ends );
	}

	/** Close a session and mark everyone who did not check in as absent. */
	public static function close_session( $session ) {
		if ( 'closed' === $session->status ) {
			return;
		}
		$data = array( 'status' => 'closed' );
		if ( empty( $session->ends_at ) || strtotime( $session->ends_at . ' UTC' ) > time() ) {
			$data['ends_at'] = QRA_Plugin::now();
		}
		QRA_DB::update_session( $session->id, $data );
		QRA_DB::fill_absences( (int) $session->id, (int) $session->course_id );

		do_action( 'qra_session_closed', $session );
	}

	/**
	 * Re-open a closed session for further check-ins.
	 *
	 * @param int $duration Minutes from now; 0 = until closed by hand.
	 */
	public static function reopen_session( $session, $duration ) {
		QRA_DB::clear_auto_absences( (int) $session->id );
		QRA_DB::update_session(
			$session->id,
			array(
				'status'  => 'open',
				'ends_at' => $duration > 0 ? gmdate( 'Y-m-d H:i:s', time() + $duration * MINUTE_IN_SECONDS ) : null,
			)
		);
	}

	public static function is_expired( $session ) {
		return ! empty( $session->ends_at ) && strtotime( $session->ends_at . ' UTC' ) < time();
	}

	/**
	 * Close the session if its time is up; returns the fresh row.
	 */
	public static function refresh_session( $session ) {
		if ( 'open' === $session->status && self::is_expired( $session ) ) {
			self::close_session( $session );
			$session = QRA_DB::get_session( $session->id );
		}
		return $session;
	}

	public static function close_expired_sessions() {
		foreach ( (array) QRA_DB::get_expired_open_sessions() as $session ) {
			self::close_session( $session );
		}
	}

	/** Present, or late when the check-in comes after the grace period. */
	public static function status_for_time( $session, $timestamp ) {
		$late_after = (int) QRA_Plugin::setting( 'late_after' );
		if ( $late_after > 0 && $timestamp > strtotime( $session->starts_at . ' UTC' ) + $late_after * MINUTE_IN_SECONDS ) {
			return 'late';
		}
		return 'present';
	}

	/* ------------------------------------------------------------------
	 * Student check-in
	 * ------------------------------------------------------------------ */

	/**
	 * Check a student into a session.
	 *
	 * @param int $scanned_at When the code was scanned (differs from now when
	 *                        the student had to log in first).
	 * @return array|WP_Error { status, already, session, course }
	 */
	public static function check_in( $session, $user_id, $device_id, $scanned_at = 0 ) {
		$scanned_at = $scanned_at ? $scanned_at : time();
		$session    = self::refresh_session( $session );
		$course     = QRA_DB::get_course( (int) $session->course_id );

		if ( ! $course ) {
			return new WP_Error( 'qra_invalid_code', __( 'This QR code is not an attendance code.', 'qr-attendance' ) );
		}
		if ( 'open' !== $session->status ) {
			return new WP_Error( 'qra_session_closed', __( 'Attendance for this session is closed.', 'qr-attendance' ) );
		}

		if ( ! QRA_DB::is_enrolled( $course->id, $user_id ) ) {
			if ( ! QRA_Plugin::setting( 'self_enroll' ) ) {
				return new WP_Error( 'qra_not_enrolled', __( 'You are not on the student list of this course. Please contact your teacher.', 'qr-attendance' ) );
			}
			QRA_DB::enroll( $course->id, $user_id );
		}

		$existing = QRA_DB::get_record( $session->id, $user_id );
		if ( $existing && 'absent' !== $existing->status ) {
			return array(
				'status'  => $existing->status,
				'already' => true,
				'session' => $session,
				'course'  => $course,
			);
		}

		if ( QRA_Plugin::setting( 'one_device' ) && $device_id && QRA_DB::device_used_by_other( $session->id, $device_id, $user_id ) ) {
			return new WP_Error( 'qra_device_used', __( 'This device has already been used to check in another student for this session.', 'qr-attendance' ) );
		}

		$status = self::status_for_time( $session, $scanned_at );
		QRA_DB::set_record(
			(int) $session->id,
			$user_id,
			$status,
			array(
				'method'      => 'qr',
				'device_id'   => $device_id,
				'ip'          => self::client_ip(),
				'recorded_at' => gmdate( 'Y-m-d H:i:s', $scanned_at ),
			)
		);

		do_action( 'qra_checked_in', $session, $user_id, $status );

		return array(
			'status'  => $status,
			'already' => false,
			'session' => $session,
			'course'  => $course,
		);
	}

	/**
	 * Teacher scans a student's personal card.
	 *
	 * @return array|WP_Error { status, already, user }
	 */
	public static function check_in_card( $session, $card_code ) {
		$user_id = QRA_Token::verify_card_code( $card_code );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return new WP_Error( 'qra_invalid_card', __( 'This QR code is not a student card.', 'qr-attendance' ) );
		}
		if ( ! QRA_DB::is_enrolled( (int) $session->course_id, $user_id ) ) {
			return new WP_Error(
				'qra_not_enrolled',
				/* translators: %s: student name. */
				sprintf( __( '%s is not enrolled in this course.', 'qr-attendance' ), $user->display_name )
			);
		}

		$existing = QRA_DB::get_record( $session->id, $user_id );
		if ( $existing && 'absent' !== $existing->status ) {
			return array( 'status' => $existing->status, 'already' => true, 'user' => $user );
		}

		$status = self::status_for_time( $session, time() );
		QRA_DB::set_record( (int) $session->id, $user_id, $status, array( 'method' => 'card' ) );

		do_action( 'qra_checked_in', $session, $user_id, $status );

		return array( 'status' => $status, 'already' => false, 'user' => $user );
	}

	/* ------------------------------------------------------------------
	 * Pending check-ins across a login
	 * ------------------------------------------------------------------
	 * A projected code lives only a few seconds, but a student who is not
	 * logged in needs longer than that to sign in. The scan is therefore
	 * validated immediately and remembered as a one-time claim bound to the
	 * device, which is redeemed once the student is back from the login form.
	 */

	public static function create_claim( $session, $device_id ) {
		$id = wp_generate_password( 24, false );
		set_transient(
			'qra_claim_' . $id,
			array(
				'session_id' => (int) $session->id,
				'device_id'  => $device_id,
				'scanned_at' => time(),
			),
			self::CLAIM_TTL
		);
		return $id;
	}

	/**
	 * @return array|WP_Error Claim data, consumed.
	 */
	public static function redeem_claim( $id, $device_id ) {
		$key   = 'qra_claim_' . preg_replace( '/[^A-Za-z0-9]/', '', (string) $id );
		$claim = get_transient( $key );
		if ( ! is_array( $claim ) || ! hash_equals( (string) $claim['device_id'], (string) $device_id ) ) {
			return new WP_Error( 'qra_expired_code', __( 'This QR code has expired. Scan the code currently on the screen.', 'qr-attendance' ) );
		}
		delete_transient( $key );
		return $claim;
	}

	/* ------------------------------------------------------------------
	 * Request helpers
	 * ------------------------------------------------------------------ */

	/**
	 * A random id stored in a long-lived cookie, used to spot one phone
	 * checking in several students. Created on first use.
	 */
	public static function device_id() {
		static $id = null;
		if ( null !== $id ) {
			return $id;
		}
		$cookie = isset( $_COOKIE[ self::DEVICE_COOKIE ] ) ? preg_replace( '/[^A-Za-z0-9]/', '', wp_unslash( $_COOKIE[ self::DEVICE_COOKIE ] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( strlen( $cookie ) >= 20 ) {
			$id = substr( $cookie, 0, 64 );
			return $id;
		}
		$id = wp_generate_password( 32, false );
		if ( ! headers_sent() ) {
			setcookie(
				self::DEVICE_COOKIE,
				$id,
				array(
					'expires'  => time() + 5 * YEAR_IN_SECONDS,
					'path'     => COOKIEPATH ? COOKIEPATH : '/',
					'domain'   => COOKIE_DOMAIN,
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		}
		$_COOKIE[ self::DEVICE_COOKIE ] = $id;
		return $id;
	}

	public static function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
	}

	/** Human-readable outcome of a successful check-in. */
	public static function success_message( $result ) {
		if ( $result['already'] ) {
			/* translators: %s: attendance status such as "Present". */
			return sprintf( __( 'You are already checked in for this session (%s).', 'qr-attendance' ), QRA_Plugin::status_label( $result['status'] ) );
		}
		if ( 'late' === $result['status'] ) {
			return __( 'Checked in — recorded as late.', 'qr-attendance' );
		}
		return __( 'Checked in — you are marked present.', 'qr-attendance' );
	}
}
