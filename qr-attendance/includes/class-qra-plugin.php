<?php
/**
 * Bootstrap: activation, roles, settings and small shared helpers.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

class QRA_Plugin {

	/** Capability that lets a user run courses and take attendance. */
	const CAP = 'qra_manage_attendance';

	const OPTION   = 'qra_settings';
	const CRON     = 'qra_close_expired_sessions';
	const META_NUM = 'qra_student_number';

	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( 'QRA_DB', 'maybe_upgrade' ) );
		add_action( self::CRON, array( 'QRA_Attendance', 'close_expired_sessions' ) );
		add_action( 'deleted_user', array( 'QRA_DB', 'delete_user_data' ) );

		// Student number on the user profile screens.
		add_action( 'show_user_profile', array( __CLASS__, 'profile_fields' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'profile_fields' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'save_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'save_profile_fields' ) );

		QRA_REST::init();
		QRA_Frontend::init();
		if ( is_admin() ) {
			QRA_Admin::init();
		}
	}

	public static function activate() {
		QRA_DB::install();

		add_role( 'qra_teacher', __( 'Teacher', 'qr-attendance' ), array( 'read' => true, self::CAP => true ) );
		add_role( 'qra_student', __( 'Student', 'qr-attendance' ), array( 'read' => true ) );

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( self::CAP );
		}

		if ( false === get_option( self::OPTION ) ) {
			add_option( self::OPTION, self::default_settings() );
		}

		if ( ! wp_next_scheduled( self::CRON ) ) {
			wp_schedule_event( time() + 300, 'hourly', self::CRON );
		}
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( self::CRON );
	}

	public static function load_textdomain() {
		load_plugin_textdomain( 'qr-attendance', false, dirname( plugin_basename( QRA_FILE ) ) . '/languages' );
	}

	/* ------------------------------------------------------------------
	 * Settings
	 * ------------------------------------------------------------------ */

	public static function default_settings() {
		return array(
			'rotation'         => 20,  // Seconds a projected QR code stays valid; 0 = static.
			'late_after'       => 15,  // Minutes after start that a check-in counts as late; 0 = never.
			'default_duration' => 90,  // Minutes a new session stays open.
			'one_device'       => 1,   // One phone may check in only one student per session.
			'self_enroll'      => 0,   // Scanning a course's code enrols a student who is not on the list.
			'absence_warning'  => 3,   // Absences at which a student is flagged in reports.
			'attendance_page'  => 0,   // Page holding [qra_my_attendance], linked after a check-in.
			'remove_data'      => 0,   // Drop tables and settings on uninstall.
		);
	}

	public static function settings() {
		$saved = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), self::default_settings() );
	}

	public static function setting( $key ) {
		$settings = self::settings();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
	}

	/* ------------------------------------------------------------------
	 * Permissions
	 * ------------------------------------------------------------------ */

	/**
	 * Administrators manage every course; teachers only the ones they own.
	 *
	 * @param object|int $course Course row or id.
	 */
	public static function can_manage_course( $course, $user_id = 0 ) {
		$user_id = $user_id ? $user_id : get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		if ( ! user_can( $user_id, self::CAP ) ) {
			return false;
		}
		if ( ! is_object( $course ) ) {
			$course = QRA_DB::get_course( (int) $course );
		}
		return $course && (int) $course->teacher_id === (int) $user_id;
	}

	/* ------------------------------------------------------------------
	 * Formatting helpers
	 * ------------------------------------------------------------------ */

	/** Current time as a UTC MySQL datetime. */
	public static function now() {
		return current_time( 'mysql', true );
	}

	/** Format a stored UTC datetime in the site's timezone. */
	public static function format_datetime( $gmt, $format = '' ) {
		if ( empty( $gmt ) ) {
			return '';
		}
		if ( ! $format ) {
			$format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		}
		return wp_date( $format, strtotime( $gmt . ' UTC' ) );
	}

	public static function statuses() {
		return array(
			'present' => __( 'Present', 'qr-attendance' ),
			'late'    => __( 'Late', 'qr-attendance' ),
			'absent'  => __( 'Absent', 'qr-attendance' ),
			'excused' => __( 'Excused', 'qr-attendance' ),
		);
	}

	public static function status_label( $status ) {
		$statuses = self::statuses();
		return isset( $statuses[ $status ] ) ? $statuses[ $status ] : '—';
	}

	/** One-letter code used in the report grid. */
	public static function status_short( $status ) {
		$short = array(
			/* translators: one-letter abbreviation of "Present" used in the attendance grid. */
			'present' => _x( 'P', 'present', 'qr-attendance' ),
			/* translators: one-letter abbreviation of "Late". */
			'late'    => _x( 'L', 'late', 'qr-attendance' ),
			/* translators: one-letter abbreviation of "Absent". */
			'absent'  => _x( 'A', 'absent', 'qr-attendance' ),
			/* translators: one-letter abbreviation of "Excused". */
			'excused' => _x( 'E', 'excused', 'qr-attendance' ),
		);
		return isset( $short[ $status ] ) ? $short[ $status ] : '—';
	}

	public static function student_number( $user_id ) {
		return (string) get_user_meta( $user_id, self::META_NUM, true );
	}

	/* ------------------------------------------------------------------
	 * Profile field
	 * ------------------------------------------------------------------ */

	public static function profile_fields( $user ) {
		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}
		?>
		<h2><?php esc_html_e( 'Attendance', 'qr-attendance' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th><label for="qra_student_number"><?php esc_html_e( 'Student number', 'qr-attendance' ); ?></label></th>
				<td>
					<input type="text" name="qra_student_number" id="qra_student_number" class="regular-text"
						value="<?php echo esc_attr( self::student_number( $user->ID ) ); ?>"
						<?php disabled( ! current_user_can( self::CAP ) ); ?> />
				</td>
			</tr>
		</table>
		<?php
	}

	public static function save_profile_fields( $user_id ) {
		// Students may see their number but only staff may change it.
		if ( ! current_user_can( 'edit_user', $user_id ) || ! current_user_can( self::CAP ) ) {
			return;
		}
		if ( isset( $_POST['qra_student_number'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- core verifies the profile nonce.
			update_user_meta( $user_id, self::META_NUM, sanitize_text_field( wp_unslash( $_POST['qra_student_number'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
	}
}
