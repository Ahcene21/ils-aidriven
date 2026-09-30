<?php
/**
 * Schema and data access.
 *
 * All datetimes are stored in UTC.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

class QRA_DB {

	const VERSION = '1.0.0';

	public static function table( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'qra_' . $name;
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset     = $wpdb->get_charset_collate();
		$courses     = self::table( 'courses' );
		$enrollments = self::table( 'enrollments' );
		$sessions    = self::table( 'sessions' );
		$attendance  = self::table( 'attendance' );

		// dbDelta is picky: one column per line, two spaces after PRIMARY KEY.
		dbDelta(
			"CREATE TABLE $courses (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
name varchar(190) NOT NULL,
code varchar(64) NOT NULL DEFAULT '',
teacher_id bigint(20) unsigned NOT NULL DEFAULT 0,
created_at datetime NOT NULL,
PRIMARY KEY  (id),
KEY teacher_id (teacher_id)
) $charset;
CREATE TABLE $enrollments (
course_id bigint(20) unsigned NOT NULL,
user_id bigint(20) unsigned NOT NULL,
enrolled_at datetime NOT NULL,
PRIMARY KEY  (course_id,user_id),
KEY user_id (user_id)
) $charset;
CREATE TABLE $sessions (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
course_id bigint(20) unsigned NOT NULL,
title varchar(190) NOT NULL DEFAULT '',
starts_at datetime NOT NULL,
ends_at datetime DEFAULT NULL,
status varchar(20) NOT NULL DEFAULT 'open',
secret varchar(64) NOT NULL,
created_by bigint(20) unsigned NOT NULL DEFAULT 0,
created_at datetime NOT NULL,
PRIMARY KEY  (id),
KEY course_id (course_id),
KEY status (status)
) $charset;
CREATE TABLE $attendance (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
session_id bigint(20) unsigned NOT NULL,
user_id bigint(20) unsigned NOT NULL,
status varchar(20) NOT NULL,
method varchar(20) NOT NULL DEFAULT 'qr',
device_id varchar(64) NOT NULL DEFAULT '',
ip varchar(45) NOT NULL DEFAULT '',
note varchar(255) NOT NULL DEFAULT '',
recorded_by bigint(20) unsigned NOT NULL DEFAULT 0,
recorded_at datetime NOT NULL,
PRIMARY KEY  (id),
UNIQUE KEY session_user (session_id,user_id),
KEY user_id (user_id),
KEY device (session_id,device_id)
) $charset;"
		);

		update_option( 'qra_db_version', self::VERSION );
	}

	public static function maybe_upgrade() {
		if ( get_option( 'qra_db_version' ) !== self::VERSION ) {
			self::install();
		}
	}

	/* ------------------------------------------------------------------
	 * Courses
	 * ------------------------------------------------------------------ */

	public static function get_course( $id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . self::table( 'courses' ) . ' WHERE id = %d', $id ) ); // phpcs:ignore WordPress.DB
	}

	/** Courses the user may manage: all of them for administrators. */
	public static function get_managed_courses( $user_id ) {
		global $wpdb;
		$table = self::table( 'courses' );
		if ( user_can( $user_id, 'manage_options' ) ) {
			return $wpdb->get_results( "SELECT * FROM $table ORDER BY name" ); // phpcs:ignore WordPress.DB
		}
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE teacher_id = %d ORDER BY name", $user_id ) ); // phpcs:ignore WordPress.DB
	}

	public static function save_course( $data, $id = 0 ) {
		global $wpdb;
		$row = array(
			'name'       => $data['name'],
			'code'       => $data['code'],
			'teacher_id' => (int) $data['teacher_id'],
		);
		if ( $id ) {
			$wpdb->update( self::table( 'courses' ), $row, array( 'id' => $id ) );
			return $id;
		}
		$row['created_at'] = QRA_Plugin::now();
		$wpdb->insert( self::table( 'courses' ), $row );
		return (int) $wpdb->insert_id;
	}

	public static function delete_course( $id ) {
		global $wpdb;
		$session_ids = $wpdb->get_col( $wpdb->prepare( 'SELECT id FROM ' . self::table( 'sessions' ) . ' WHERE course_id = %d', $id ) ); // phpcs:ignore WordPress.DB
		foreach ( $session_ids as $session_id ) {
			self::delete_session( (int) $session_id );
		}
		$wpdb->delete( self::table( 'enrollments' ), array( 'course_id' => $id ) );
		$wpdb->delete( self::table( 'courses' ), array( 'id' => $id ) );
	}

	/* ------------------------------------------------------------------
	 * Enrolments
	 * ------------------------------------------------------------------ */

	public static function enroll( $course_id, $user_id ) {
		global $wpdb;
		return (bool) $wpdb->query( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'INSERT IGNORE INTO ' . self::table( 'enrollments' ) . ' (course_id, user_id, enrolled_at) VALUES (%d, %d, %s)', // phpcs:ignore WordPress.DB
				$course_id,
				$user_id,
				QRA_Plugin::now()
			)
		);
	}

	public static function unenroll( $course_id, $user_id ) {
		global $wpdb;
		$wpdb->delete( self::table( 'enrollments' ), array( 'course_id' => $course_id, 'user_id' => $user_id ) );
	}

	public static function is_enrolled( $course_id, $user_id ) {
		global $wpdb;
		return (bool) $wpdb->get_var( $wpdb->prepare( 'SELECT 1 FROM ' . self::table( 'enrollments' ) . ' WHERE course_id = %d AND user_id = %d', $course_id, $user_id ) ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Enrolled students, sorted by name, with their student number.
	 *
	 * @return object[] { ID, display_name, user_email, student_number, enrolled_at }
	 */
	public static function get_students( $course_id ) {
		global $wpdb;
		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				"SELECT u.ID, u.display_name, u.user_email, e.enrolled_at, COALESCE(m.meta_value, '') AS student_number
				FROM " . self::table( 'enrollments' ) . " e
				JOIN {$wpdb->users} u ON u.ID = e.user_id
				LEFT JOIN {$wpdb->usermeta} m ON m.user_id = u.ID AND m.meta_key = %s
				WHERE e.course_id = %d
				ORDER BY u.display_name",
				QRA_Plugin::META_NUM,
				$course_id
			)
		);
		return $rows ? $rows : array();
	}

	public static function count_students( $course_id ) {
		global $wpdb;
		return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . self::table( 'enrollments' ) . ' WHERE course_id = %d', $course_id ) ); // phpcs:ignore WordPress.DB
	}

	public static function get_student_courses( $user_id ) {
		global $wpdb;
		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'SELECT c.* FROM ' . self::table( 'courses' ) . ' c JOIN ' . self::table( 'enrollments' ) . ' e ON e.course_id = c.id WHERE e.user_id = %d ORDER BY c.name', // phpcs:ignore WordPress.DB
				$user_id
			)
		);
		return $rows ? $rows : array();
	}

	/* ------------------------------------------------------------------
	 * Sessions
	 * ------------------------------------------------------------------ */

	public static function get_session( $id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . self::table( 'sessions' ) . ' WHERE id = %d', $id ) ); // phpcs:ignore WordPress.DB
	}

	/** Sessions of a course, oldest first. */
	public static function get_sessions( $course_id ) {
		global $wpdb;
		$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . self::table( 'sessions' ) . ' WHERE course_id = %d ORDER BY starts_at, id', $course_id ) ); // phpcs:ignore WordPress.DB
		return $rows ? $rows : array();
	}

	public static function create_session( $course_id, $title, $starts_at, $ends_at ) {
		global $wpdb;
		$wpdb->insert(
			self::table( 'sessions' ),
			array(
				'course_id'  => $course_id,
				'title'      => $title,
				'starts_at'  => $starts_at,
				'ends_at'    => $ends_at,
				'status'     => 'open',
				'secret'     => wp_generate_password( 32, false ),
				'created_by' => get_current_user_id(),
				'created_at' => QRA_Plugin::now(),
			)
		);
		return (int) $wpdb->insert_id;
	}

	public static function update_session( $id, $data ) {
		global $wpdb;
		$wpdb->update( self::table( 'sessions' ), $data, array( 'id' => $id ) );
	}

	public static function delete_session( $id ) {
		global $wpdb;
		$wpdb->delete( self::table( 'attendance' ), array( 'session_id' => $id ) );
		$wpdb->delete( self::table( 'sessions' ), array( 'id' => $id ) );
	}

	/** Open sessions whose end time has passed. */
	public static function get_expired_open_sessions() {
		global $wpdb;
		return $wpdb->get_results( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'SELECT * FROM ' . self::table( 'sessions' ) . " WHERE status = 'open' AND ends_at IS NOT NULL AND ends_at < %s", // phpcs:ignore WordPress.DB
				QRA_Plugin::now()
			)
		);
	}

	/* ------------------------------------------------------------------
	 * Attendance records
	 * ------------------------------------------------------------------ */

	public static function get_record( $session_id, $user_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . self::table( 'attendance' ) . ' WHERE session_id = %d AND user_id = %d', $session_id, $user_id ) ); // phpcs:ignore WordPress.DB
	}

	/** Records of a session keyed by user id. */
	public static function get_session_records( $session_id ) {
		global $wpdb;
		$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . self::table( 'attendance' ) . ' WHERE session_id = %d', $session_id ) ); // phpcs:ignore WordPress.DB
		$out  = array();
		foreach ( (array) $rows as $row ) {
			$out[ (int) $row->user_id ] = $row;
		}
		return $out;
	}

	/** All records of a course as [session_id][user_id] => status. */
	public static function get_course_matrix( $course_id ) {
		global $wpdb;
		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'SELECT a.session_id, a.user_id, a.status FROM ' . self::table( 'attendance' ) . ' a JOIN ' . self::table( 'sessions' ) . ' s ON s.id = a.session_id WHERE s.course_id = %d', // phpcs:ignore WordPress.DB
				$course_id
			)
		);
		$out = array();
		foreach ( (array) $rows as $row ) {
			$out[ (int) $row->session_id ][ (int) $row->user_id ] = $row->status;
		}
		return $out;
	}

	/** Number of records per status for each session of a course: [session_id][status] => n. */
	public static function get_session_counts( $course_id ) {
		global $wpdb;
		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'SELECT a.session_id, a.status, COUNT(*) AS n FROM ' . self::table( 'attendance' ) . ' a JOIN ' . self::table( 'sessions' ) . ' s ON s.id = a.session_id WHERE s.course_id = %d GROUP BY a.session_id, a.status', // phpcs:ignore WordPress.DB
				$course_id
			)
		);
		$out = array();
		foreach ( (array) $rows as $row ) {
			$out[ (int) $row->session_id ][ $row->status ] = (int) $row->n;
		}
		return $out;
	}

	/** Whether another student already checked in from this device in the session. */
	public static function device_used_by_other( $session_id, $device_id, $user_id ) {
		global $wpdb;
		return (bool) $wpdb->get_var( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'SELECT 1 FROM ' . self::table( 'attendance' ) . ' WHERE session_id = %d AND device_id = %s AND user_id <> %d LIMIT 1', // phpcs:ignore WordPress.DB
				$session_id,
				$device_id,
				$user_id
			)
		);
	}

	/**
	 * Insert or replace a student's record for a session.
	 *
	 * @param array $extra method, device_id, ip, note, recorded_at.
	 */
	public static function set_record( $session_id, $user_id, $status, $extra = array() ) {
		global $wpdb;
		$row = wp_parse_args(
			$extra,
			array(
				'method'      => 'manual',
				'device_id'   => '',
				'ip'          => '',
				'note'        => '',
				'recorded_at' => QRA_Plugin::now(),
			)
		);

		$row['session_id']  = $session_id;
		$row['user_id']     = $user_id;
		$row['status']      = $status;
		$row['recorded_by'] = get_current_user_id();

		$existing = self::get_record( $session_id, $user_id );
		if ( $existing ) {
			$wpdb->update( self::table( 'attendance' ), $row, array( 'id' => $existing->id ) );
		} else {
			$wpdb->insert( self::table( 'attendance' ), $row );
		}
	}

	public static function delete_record( $session_id, $user_id ) {
		global $wpdb;
		$wpdb->delete( self::table( 'attendance' ), array( 'session_id' => $session_id, 'user_id' => $user_id ) );
	}

	/** Record "absent" for every enrolled student with no record yet. */
	public static function fill_absences( $session_id, $course_id ) {
		global $wpdb;
		$wpdb->query( // phpcs:ignore WordPress.DB
			$wpdb->prepare(
				'INSERT INTO ' . self::table( 'attendance' ) . " (session_id, user_id, status, method, recorded_at)
				SELECT %d, e.user_id, 'absent', 'auto', %s FROM " . self::table( 'enrollments' ) . ' e
				WHERE e.course_id = %d AND NOT EXISTS (
					SELECT 1 FROM ' . self::table( 'attendance' ) . ' a WHERE a.session_id = %d AND a.user_id = e.user_id
				)', // phpcs:ignore WordPress.DB
				$session_id,
				QRA_Plugin::now(),
				$course_id,
				$session_id
			)
		);
	}

	/** Remove the automatic absences written when a session was closed. */
	public static function clear_auto_absences( $session_id ) {
		global $wpdb;
		$wpdb->delete( self::table( 'attendance' ), array( 'session_id' => $session_id, 'method' => 'auto' ) );
	}

	public static function delete_user_data( $user_id ) {
		global $wpdb;
		$wpdb->delete( self::table( 'enrollments' ), array( 'user_id' => $user_id ) );
		$wpdb->delete( self::table( 'attendance' ), array( 'user_id' => $user_id ) );
	}
}
