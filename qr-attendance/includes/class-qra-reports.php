<?php
/**
 * Attendance totals and the CSV export.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

class QRA_Reports {

	/**
	 * Everything needed to draw a course's attendance grid.
	 *
	 * A cell is empty when the student has no record for that session — the
	 * session is still open, or they joined the course after it closed —
	 * and empty cells do not count towards the totals.
	 *
	 * @return array { sessions, students, matrix, totals }
	 */
	public static function course_report( $course_id ) {
		$sessions = QRA_DB::get_sessions( $course_id );
		$students = QRA_DB::get_students( $course_id );
		$matrix   = QRA_DB::get_course_matrix( $course_id );
		$totals   = array();

		foreach ( $students as $student ) {
			$totals[ $student->ID ] = self::empty_totals();
			foreach ( $sessions as $session ) {
				if ( isset( $matrix[ $session->id ][ $student->ID ] ) ) {
					self::add( $totals[ $student->ID ], $matrix[ $session->id ][ $student->ID ] );
				}
			}
			self::finish( $totals[ $student->ID ] );
		}

		return compact( 'sessions', 'students', 'matrix', 'totals' );
	}

	/**
	 * A student's history in one course.
	 *
	 * @return array { rows: [ { session, status } ], totals }
	 */
	public static function student_course_report( $course_id, $user_id ) {
		$totals = self::empty_totals();
		$rows   = array();
		foreach ( QRA_DB::get_sessions( $course_id ) as $session ) {
			$record = QRA_DB::get_record( $session->id, $user_id );
			$status = $record ? $record->status : '';
			if ( $status ) {
				self::add( $totals, $status );
			}
			$rows[] = array(
				'session' => $session,
				'status'  => $status,
			);
		}
		self::finish( $totals );
		return compact( 'rows', 'totals' );
	}

	private static function empty_totals() {
		return array(
			'present' => 0,
			'late'    => 0,
			'absent'  => 0,
			'excused' => 0,
			'counted' => 0,
			'rate'    => null,
		);
	}

	private static function add( &$totals, $status ) {
		if ( isset( $totals[ $status ] ) ) {
			$totals[ $status ]++;
			$totals['counted']++;
		}
	}

	/** Attendance rate: present or late, out of sessions not excused. */
	private static function finish( &$totals ) {
		$base           = $totals['counted'] - $totals['excused'];
		$totals['rate'] = $base > 0 ? round( 100 * ( $totals['present'] + $totals['late'] ) / $base ) : null;
	}

	public static function is_flagged( $totals ) {
		$threshold = (int) QRA_Plugin::setting( 'absence_warning' );
		return $threshold > 0 && $totals['absent'] >= $threshold;
	}

	public static function format_rate( $rate ) {
		return null === $rate ? '—' : $rate . '%';
	}

	/** Stream the course grid as a CSV file and stop. */
	public static function export_csv( $course ) {
		$report   = self::course_report( $course->id );
		$filename = sanitize_file_name( 'attendance-' . ( $course->code ? $course->code : $course->name ) . '-' . gmdate( 'Y-m-d' ) . '.csv' );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" ); // BOM so spreadsheet apps read Arabic names correctly.

		$header = array( __( 'Student number', 'qr-attendance' ), __( 'Name', 'qr-attendance' ), __( 'Email', 'qr-attendance' ) );
		foreach ( $report['sessions'] as $session ) {
			$header[] = QRA_Plugin::format_datetime( $session->starts_at, get_option( 'date_format' ) . ' H:i' ) . ( $session->title ? ' — ' . $session->title : '' );
		}
		array_push(
			$header,
			__( 'Present', 'qr-attendance' ),
			__( 'Late', 'qr-attendance' ),
			__( 'Absent', 'qr-attendance' ),
			__( 'Excused', 'qr-attendance' ),
			__( 'Attendance rate', 'qr-attendance' )
		);
		fputcsv( $out, $header );

		foreach ( $report['students'] as $student ) {
			$line = array( $student->student_number, $student->display_name, $student->user_email );
			foreach ( $report['sessions'] as $session ) {
				$status = isset( $report['matrix'][ $session->id ][ $student->ID ] ) ? $report['matrix'][ $session->id ][ $student->ID ] : '';
				$line[] = $status ? QRA_Plugin::status_label( $status ) : '';
			}
			$totals = $report['totals'][ $student->ID ];
			array_push( $line, $totals['present'], $totals['late'], $totals['absent'], $totals['excused'], self::format_rate( $totals['rate'] ) );
			fputcsv( $out, self::escape_formulas( $line ) );
		}

		fclose( $out );
		exit;
	}

	/** Keep spreadsheet apps from evaluating names such as "=HYPERLINK(...)". */
	private static function escape_formulas( $line ) {
		return array_map(
			function ( $value ) {
				$value = (string) $value;
				return ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) && ! is_numeric( $value ) ) ? "'" . $value : $value;
			},
			$line
		);
	}
}
