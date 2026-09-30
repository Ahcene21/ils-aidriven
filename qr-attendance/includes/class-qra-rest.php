<?php
/**
 * REST routes used by the scanner and the live session screen.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

class QRA_REST {

	const NS = 'qra/v1';

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	public static function register_routes() {
		register_rest_route(
			self::NS,
			'/checkin',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'checkin' ),
				'permission_callback' => 'is_user_logged_in',
				'args'                => array(
					'code' => array(
						'type'     => 'string',
						'required' => true,
					),
				),
			)
		);

		$session_args = array(
			'id' => array(
				'type'     => 'integer',
				'required' => true,
			),
		);

		register_rest_route(
			self::NS,
			'/sessions/(?P<id>\d+)/live',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'live' ),
				'permission_callback' => array( __CLASS__, 'can_manage_session' ),
				'args'                => $session_args,
			)
		);

		register_rest_route(
			self::NS,
			'/sessions/(?P<id>\d+)/card',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'card' ),
				'permission_callback' => array( __CLASS__, 'can_manage_session' ),
				'args'                => $session_args + array(
					'code' => array(
						'type'     => 'string',
						'required' => true,
					),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/sessions/(?P<id>\d+)/mark',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'mark' ),
				'permission_callback' => array( __CLASS__, 'can_manage_session' ),
				'args'                => $session_args + array(
					'user_id' => array(
						'type'     => 'integer',
						'required' => true,
					),
					'status'  => array(
						'type'     => 'string',
						'required' => true,
						'enum'     => array_merge( array_keys( QRA_Plugin::statuses() ), array( 'none' ) ),
					),
				),
			)
		);
	}

	public static function can_manage_session( WP_REST_Request $request ) {
		$session = QRA_DB::get_session( (int) $request['id'] );
		return $session && QRA_Plugin::can_manage_course( (int) $session->course_id );
	}

	/** A student submits the code read by the in-page scanner. */
	public static function checkin( WP_REST_Request $request ) {
		$code = QRA_Token::extract_session_code( $request['code'] );
		if ( ! $code ) {
			if ( ! is_wp_error( QRA_Token::verify_card_code( $request['code'] ) ) ) {
				return self::error( new WP_Error( 'qra_invalid_code', __( 'That is a student card. Scan the code shown by your teacher.', 'qr-attendance' ) ) );
			}
			return self::error( new WP_Error( 'qra_invalid_code', __( 'This QR code is not an attendance code.', 'qr-attendance' ) ) );
		}

		$session = QRA_Token::verify_session_code( $code );
		if ( is_wp_error( $session ) ) {
			return self::error( $session );
		}

		$result = QRA_Attendance::check_in( $session, get_current_user_id(), QRA_Attendance::device_id() );
		if ( is_wp_error( $result ) ) {
			return self::error( $result );
		}

		return array(
			'ok'      => true,
			'status'  => $result['status'],
			'already' => $result['already'],
			'message' => QRA_Attendance::success_message( $result ),
			'course'  => $result['course']->name,
			'session' => $result['session']->title,
		);
	}

	/** State of a session for the teacher's screen: current code and roll call. */
	public static function live( WP_REST_Request $request ) {
		$session = QRA_Attendance::refresh_session( QRA_DB::get_session( (int) $request['id'] ) );
		return self::live_payload( $session );
	}

	public static function live_payload( $session ) {
		$records  = QRA_DB::get_session_records( $session->id );
		$counts   = array_fill_keys( array_keys( QRA_Plugin::statuses() ), 0 );
		$students = array();

		foreach ( QRA_DB::get_students( (int) $session->course_id ) as $student ) {
			$record = isset( $records[ $student->ID ] ) ? $records[ $student->ID ] : null;
			if ( $record && isset( $counts[ $record->status ] ) ) {
				$counts[ $record->status ]++;
			}
			$students[] = array(
				'id'     => (int) $student->ID,
				'name'   => $student->display_name,
				'number' => $student->student_number,
				'status' => $record ? $record->status : '',
				'method' => $record ? $record->method : '',
				'time'   => $record ? QRA_Plugin::format_datetime( $record->recorded_at, get_option( 'time_format' ) ) : '',
				'ts'     => $record ? strtotime( $record->recorded_at . ' UTC' ) : 0,
			);
		}

		$open = 'open' === $session->status;

		return array(
			'id'           => (int) $session->id,
			'status'       => $session->status,
			'open'         => $open,
			'url'          => $open ? QRA_Token::session_url( $session ) : '',
			'seconds_left' => $open ? QRA_Token::seconds_left() : 0,
			'rotation'     => QRA_Token::rotation(),
			'ends_in'      => ( $open && $session->ends_at ) ? max( 0, strtotime( $session->ends_at . ' UTC' ) - time() ) : null,
			'counts'       => $counts,
			'total'        => count( $students ),
			'students'     => $students,
		);
	}

	/** Teacher scanned a student card. */
	public static function card( WP_REST_Request $request ) {
		$session = QRA_Attendance::refresh_session( QRA_DB::get_session( (int) $request['id'] ) );
		if ( 'open' !== $session->status ) {
			return self::error( new WP_Error( 'qra_session_closed', __( 'Attendance for this session is closed.', 'qr-attendance' ) ) );
		}

		$result = QRA_Attendance::check_in_card( $session, $request['code'] );
		if ( is_wp_error( $result ) ) {
			return self::error( $result );
		}

		$message = $result['already']
			/* translators: 1: student name, 2: attendance status. */
			? sprintf( __( '%1$s is already recorded (%2$s).', 'qr-attendance' ), $result['user']->display_name, QRA_Plugin::status_label( $result['status'] ) )
			/* translators: 1: student name, 2: attendance status. */
			: sprintf( __( '%1$s: %2$s', 'qr-attendance' ), $result['user']->display_name, QRA_Plugin::status_label( $result['status'] ) );

		return array(
			'ok'      => true,
			'status'  => $result['status'],
			'already' => $result['already'],
			'message' => $message,
		);
	}

	/** Teacher sets a student's status by hand; "none" clears it. */
	public static function mark( WP_REST_Request $request ) {
		$session = QRA_DB::get_session( (int) $request['id'] );
		$user_id = (int) $request['user_id'];

		if ( ! QRA_DB::is_enrolled( (int) $session->course_id, $user_id ) ) {
			return self::error( new WP_Error( 'qra_not_enrolled', __( 'This student is not enrolled in the course.', 'qr-attendance' ) ) );
		}

		if ( 'none' === $request['status'] ) {
			QRA_DB::delete_record( (int) $session->id, $user_id );
		} else {
			$existing = QRA_DB::get_record( $session->id, $user_id );
			$extra    = array( 'method' => 'manual' );
			if ( $existing && in_array( $existing->method, array( 'qr', 'card' ), true ) ) {
				// Keep the evidence of a scan when the teacher only corrects the status.
				$extra = array(
					'method'      => $existing->method,
					'device_id'   => $existing->device_id,
					'ip'          => $existing->ip,
					'recorded_at' => $existing->recorded_at,
				);
			}
			QRA_DB::set_record( (int) $session->id, $user_id, $request['status'], $extra );
		}

		return self::live_payload( QRA_DB::get_session( (int) $session->id ) );
	}

	private static function error( WP_Error $error ) {
		$status = array(
			'qra_not_enrolled'   => 403,
			'qra_device_used'    => 403,
			'qra_session_closed' => 410,
			'qra_expired_code'   => 410,
		);
		$code = $error->get_error_code();
		return new WP_Error( $code, $error->get_error_message(), array( 'status' => isset( $status[ $code ] ) ? $status[ $code ] : 400 ) );
	}
}
