<?php
/**
 * Admin screens and their form handlers.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

class QRA_Admin {

	const SLUG = 'qra';

	/** Form handlers, each posted to admin-post.php as action=qra_<name>. */
	const ACTIONS = array(
		'save_course',
		'delete_course',
		'enroll',
		'create_students',
		'unenroll',
		'start_session',
		'close_session',
		'reopen_session',
		'delete_session',
		'export_csv',
		'save_settings',
	);

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'admin_notices', array( __CLASS__, 'show_flash' ) );
		foreach ( self::ACTIONS as $action ) {
			add_action( 'admin_post_qra_' . $action, array( __CLASS__, 'handle_' . $action ) );
		}
		add_filter( 'plugin_action_links_' . plugin_basename( QRA_FILE ), array( __CLASS__, 'action_links' ) );
	}

	public static function menu() {
		$hook = add_menu_page(
			__( 'Attendance', 'qr-attendance' ),
			__( 'Attendance', 'qr-attendance' ),
			QRA_Plugin::CAP,
			self::SLUG,
			array( __CLASS__, 'render_main' ),
			'dashicons-yes-alt',
			26
		);
		add_submenu_page( self::SLUG, __( 'Courses', 'qr-attendance' ), __( 'Courses', 'qr-attendance' ), QRA_Plugin::CAP, self::SLUG, array( __CLASS__, 'render_main' ) );
		// Refuse foreign courses before any output, so the response carries a 403.
		add_action( 'load-' . $hook, array( __CLASS__, 'guard_main' ) );
		add_submenu_page( self::SLUG, __( 'Attendance settings', 'qr-attendance' ), __( 'Settings', 'qr-attendance' ), 'manage_options', 'qra-settings', array( __CLASS__, 'render_settings' ) );
	}

	public static function guard_main() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_GET['session'] ) && ! self::requested_session() ) {
			wp_die( esc_html__( 'You are not allowed to manage this course.', 'qr-attendance' ), 403 );
		}
		if ( ! isset( $_GET['session'] ) && isset( $_GET['course'] ) && ! QRA_Plugin::can_manage_course( absint( $_GET['course'] ) ) ) {
			wp_die( esc_html__( 'You are not allowed to manage this course.', 'qr-attendance' ), 403 );
		}
		// phpcs:enable
	}

	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=qra-settings' ) ) . '">' . esc_html__( 'Settings', 'qr-attendance' ) . '</a>' );
		return $links;
	}

	public static function assets( $hook ) {
		if ( false === strpos( $hook, self::SLUG ) ) {
			return;
		}
		wp_enqueue_style( 'qra-admin', QRA_URL . 'assets/css/admin.css', array(), QRA_VERSION );

		$session = self::requested_session();
		if ( ! $session ) {
			return;
		}
		wp_enqueue_script( 'qra-session', QRA_URL . 'assets/js/session.js', array( 'qra-qrcode', 'qra-camera' ), QRA_VERSION, true );
		wp_localize_script(
			'qra-session',
			'qraSession',
			array(
				'base'     => esc_url_raw( rest_url( QRA_REST::NS . '/sessions/' . $session->id ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'statuses' => QRA_Plugin::statuses(),
				'i18n'     => array(
					'notRecorded'   => __( 'Not recorded', 'qr-attendance' ),
					'clear'         => __( '— Clear —', 'qr-attendance' ),
					'closed'        => __( 'This session is closed.', 'qr-attendance' ),
					/* translators: %d: seconds until the QR code changes. */
					'newCodeIn'     => __( 'New code in %ds', 'qr-attendance' ),
					/* translators: %s: remaining time such as 12:05. */
					'endsIn'        => __( 'Closes automatically in %s', 'qr-attendance' ),
					'offline'       => __( 'Connection lost — retrying…', 'qr-attendance' ),
					'noStudents'    => __( 'No students are enrolled in this course yet.', 'qr-attendance' ),
					'methodQr'      => __( 'QR', 'qr-attendance' ),
					'methodCard'    => __( 'Card', 'qr-attendance' ),
					'methodManual'  => __( 'Manual', 'qr-attendance' ),
					'methodAuto'    => __( 'Auto', 'qr-attendance' ),
					'scanCards'     => __( 'Scan student cards', 'qr-attendance' ),
					'stopScanning'  => __( 'Stop scanning', 'qr-attendance' ),
					'failed'        => __( 'Could not reach the server.', 'qr-attendance' ),
				),
			)
		);
	}

	/** Session requested on the live screen, if the user may manage it. */
	private static function requested_session() {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( ! isset( $_GET['page'], $_GET['session'] ) || self::SLUG !== $_GET['page'] ) {
			return null;
		}
		$session = QRA_DB::get_session( absint( $_GET['session'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		return ( $session && QRA_Plugin::can_manage_course( (int) $session->course_id ) ) ? $session : null;
	}

	/* ------------------------------------------------------------------
	 * URLs and notices
	 * ------------------------------------------------------------------ */

	public static function url( $args = array() ) {
		return add_query_arg( array_merge( array( 'page' => self::SLUG ), $args ), admin_url( 'admin.php' ) );
	}

	public static function course_url( $course_id, $tab = 'sessions' ) {
		return self::url( array( 'course' => $course_id, 'tab' => $tab ) );
	}

	public static function session_url( $session_id ) {
		return self::url( array( 'session' => $session_id ) );
	}

	/** URL of a nonce-protected admin-post action. */
	public static function action_url( $action, $args = array() ) {
		return wp_nonce_url( add_query_arg( array_merge( array( 'action' => 'qra_' . $action ), $args ), admin_url( 'admin-post.php' ) ), 'qra_' . $action );
	}

	/** Opening tag and hidden fields of a form posting to an admin-post action. */
	public static function form_open( $action, $fields = array(), $attrs = '' ) {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" ' . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput -- static attributes.
		echo '<input type="hidden" name="action" value="qra_' . esc_attr( $action ) . '">';
		foreach ( $fields as $name => $value ) {
			echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
		}
		wp_nonce_field( 'qra_' . $action );
	}

	private static function flash( $type, $message ) {
		$key   = 'qra_flash_' . get_current_user_id();
		$queue = get_transient( $key );
		$queue = is_array( $queue ) ? $queue : array();

		$queue[] = array( $type, $message );
		set_transient( $key, $queue, 60 );
	}

	public static function show_flash() {
		$key   = 'qra_flash_' . get_current_user_id();
		$queue = get_transient( $key );
		if ( ! is_array( $queue ) ) {
			return;
		}
		delete_transient( $key );
		foreach ( $queue as $item ) {
			printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr( $item[0] ), wp_kses_post( $item[1] ) );
		}
	}

	private static function done( $url ) {
		wp_safe_redirect( $url );
		exit;
	}

	/* ------------------------------------------------------------------
	 * Request guards
	 * ------------------------------------------------------------------ */

	private static function verify( $action ) {
		if ( ! current_user_can( QRA_Plugin::CAP ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'qr-attendance' ), 403 );
		}
		check_admin_referer( 'qra_' . $action );
	}

	// phpcs:disable WordPress.Security.NonceVerification -- every caller runs verify() first.
	private static function input( $key, $default = '' ) {
		return isset( $_REQUEST[ $key ] ) ? wp_unslash( $_REQUEST[ $key ] ) : $default; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}
	// phpcs:enable

	/** The course named in the request, or die. */
	private static function course_from_request() {
		$course = QRA_DB::get_course( absint( self::input( 'course' ) ) );
		if ( ! $course || ! QRA_Plugin::can_manage_course( $course ) ) {
			wp_die( esc_html__( 'You are not allowed to manage this course.', 'qr-attendance' ), 403 );
		}
		return $course;
	}

	/** The session named in the request, or die. */
	private static function session_from_request() {
		$session = QRA_DB::get_session( absint( self::input( 'session' ) ) );
		if ( ! $session || ! QRA_Plugin::can_manage_course( (int) $session->course_id ) ) {
			wp_die( esc_html__( 'You are not allowed to manage this course.', 'qr-attendance' ), 403 );
		}
		return $session;
	}

	/* ------------------------------------------------------------------
	 * Pages
	 * ------------------------------------------------------------------ */

	public static function render_main() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_GET['session'] ) ) {
			$session = self::requested_session();
			if ( ! $session ) {
				wp_die( esc_html__( 'You are not allowed to manage this course.', 'qr-attendance' ), 403 );
			}
			$session = QRA_Attendance::refresh_session( $session );
			$course  = QRA_DB::get_course( (int) $session->course_id );
			include QRA_DIR . 'admin/views/session.php';
			return;
		}

		if ( isset( $_GET['course'] ) ) {
			$course = QRA_DB::get_course( absint( $_GET['course'] ) );
			if ( ! $course || ! QRA_Plugin::can_manage_course( $course ) ) {
				wp_die( esc_html__( 'You are not allowed to manage this course.', 'qr-attendance' ), 403 );
			}
			$tabs = array(
				'sessions' => __( 'Sessions', 'qr-attendance' ),
				'students' => __( 'Students', 'qr-attendance' ),
				'report'   => __( 'Report', 'qr-attendance' ),
				'edit'     => __( 'Course details', 'qr-attendance' ),
			);
			$tab  = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'sessions';
			$tab  = isset( $tabs[ $tab ] ) ? $tab : 'sessions';
			include QRA_DIR . 'admin/views/course.php';
			return;
		}
		// phpcs:enable

		$courses = QRA_DB::get_managed_courses( get_current_user_id() );
		include QRA_DIR . 'admin/views/courses.php';
	}

	public static function render_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = QRA_Plugin::settings();
		include QRA_DIR . 'admin/views/settings.php';
	}

	/** Users who can be put in charge of a course. */
	public static function teachers() {
		return get_users(
			array(
				'capability' => QRA_Plugin::CAP,
				'orderby'    => 'display_name',
				'fields'     => array( 'ID', 'display_name' ),
			)
		);
	}

	/** Fields shared by the "new course" and "course details" forms. */
	public static function course_fields( $course = null ) {
		$teacher_id = $course ? (int) $course->teacher_id : get_current_user_id();
		?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="qra-name"><?php esc_html_e( 'Course name', 'qr-attendance' ); ?></label></th>
				<td><input type="text" id="qra-name" name="name" class="regular-text" required value="<?php echo esc_attr( $course ? $course->name : '' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="qra-code"><?php esc_html_e( 'Code / group', 'qr-attendance' ); ?></label></th>
				<td><input type="text" id="qra-code" name="code" class="regular-text" value="<?php echo esc_attr( $course ? $course->code : '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. CS101 — Group A', 'qr-attendance' ); ?>"></td>
			</tr>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
			<tr>
				<th scope="row"><label for="qra-teacher"><?php esc_html_e( 'Teacher', 'qr-attendance' ); ?></label></th>
				<td>
					<select id="qra-teacher" name="teacher_id">
						<?php foreach ( self::teachers() as $teacher ) : ?>
							<option value="<?php echo (int) $teacher->ID; ?>" <?php selected( $teacher_id, (int) $teacher->ID ); ?>><?php echo esc_html( $teacher->display_name ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Give a user the "Teacher" role to list them here. Teachers only see their own courses.', 'qr-attendance' ); ?></p>
				</td>
			</tr>
			<?php endif; ?>
		</table>
		<?php
	}

	/* ------------------------------------------------------------------
	 * Handlers: courses
	 * ------------------------------------------------------------------ */

	public static function handle_save_course() {
		self::verify( 'save_course' );

		$id     = absint( self::input( 'course', 0 ) );
		$course = $id ? self::course_from_request() : null;
		$name   = sanitize_text_field( self::input( 'name' ) );
		if ( '' === $name ) {
			self::flash( 'error', __( 'The course name is required.', 'qr-attendance' ) );
			self::done( $id ? self::course_url( $id, 'edit' ) : self::url() );
		}

		$teacher_id = $course ? (int) $course->teacher_id : get_current_user_id();
		if ( current_user_can( 'manage_options' ) && self::input( 'teacher_id' ) ) {
			$candidate = absint( self::input( 'teacher_id' ) );
			if ( user_can( $candidate, QRA_Plugin::CAP ) ) {
				$teacher_id = $candidate;
			}
		}

		$id = QRA_DB::save_course(
			array(
				'name'       => $name,
				'code'       => sanitize_text_field( self::input( 'code' ) ),
				'teacher_id' => $teacher_id,
			),
			$id
		);

		self::flash( 'success', $course ? __( 'Course updated.', 'qr-attendance' ) : __( 'Course created. Now add its students.', 'qr-attendance' ) );
		self::done( self::course_url( $id, $course ? 'edit' : 'students' ) );
	}

	public static function handle_delete_course() {
		self::verify( 'delete_course' );
		$course = self::course_from_request();
		QRA_DB::delete_course( (int) $course->id );
		/* translators: %s: course name. */
		self::flash( 'success', sprintf( __( 'Course "%s" and its attendance records were deleted.', 'qr-attendance' ), esc_html( $course->name ) ) );
		self::done( self::url() );
	}

	/* ------------------------------------------------------------------
	 * Handlers: students
	 * ------------------------------------------------------------------ */

	/** Find a user by email, username or student number. */
	private static function find_user( $identifier ) {
		$identifier = trim( $identifier );
		if ( '' === $identifier ) {
			return null;
		}
		if ( is_email( $identifier ) ) {
			$user = get_user_by( 'email', $identifier );
			if ( $user ) {
				return $user;
			}
		}
		$user = get_user_by( 'login', $identifier );
		if ( $user ) {
			return $user;
		}
		$users = get_users(
			array(
				'meta_key'   => QRA_Plugin::META_NUM, // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value' => $identifier, // phpcs:ignore WordPress.DB.SlowDBQuery
				'number'     => 1,
			)
		);
		return $users ? $users[0] : null;
	}

	/** Split a pasted line on commas, semicolons or tabs (spreadsheet copy). */
	private static function split_line( $line ) {
		return array_map( 'trim', preg_split( '/[,;\t]/', $line ) );
	}

	private static function lines( $text ) {
		return array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' );
	}

	public static function handle_enroll() {
		self::verify( 'enroll' );
		$course = self::course_from_request();

		$added   = 0;
		$missing = array();
		foreach ( self::lines( self::input( 'identifiers' ) ) as $line ) {
			foreach ( self::split_line( $line ) as $identifier ) {
				if ( '' === $identifier ) {
					continue;
				}
				$user = self::find_user( $identifier );
				if ( ! $user ) {
					$missing[] = $identifier;
					continue;
				}
				if ( QRA_DB::enroll( (int) $course->id, $user->ID ) ) {
					$added++;
				}
			}
		}

		/* translators: %d: number of students. */
		self::flash( 'success', sprintf( _n( '%d student enrolled.', '%d students enrolled.', $added, 'qr-attendance' ), $added ) );
		if ( $missing ) {
			/* translators: %s: list of emails, usernames or numbers. */
			self::flash( 'warning', sprintf( __( 'No account was found for: %s', 'qr-attendance' ), esc_html( implode( ', ', $missing ) ) ) );
		}
		self::done( self::course_url( $course->id, 'students' ) );
	}

	/** Create accounts from "number, name, email" lines and enrol them. */
	public static function handle_create_students() {
		self::verify( 'create_students' );
		if ( ! current_user_can( 'create_users' ) ) {
			wp_die( esc_html__( 'You are not allowed to create user accounts.', 'qr-attendance' ), 403 );
		}
		$course = self::course_from_request();
		$notify = (bool) self::input( 'notify' );

		$created = 0;
		$linked  = 0;
		$errors  = array();

		foreach ( self::lines( self::input( 'students' ) ) as $line ) {
			$cols   = self::split_line( $line ) + array( '', '', '' );
			$number = sanitize_text_field( $cols[0] );
			$name   = sanitize_text_field( $cols[1] );
			$email  = sanitize_email( $cols[2] );

			$user = ( $email ? get_user_by( 'email', $email ) : null );
			if ( ! $user && $number ) {
				$user = self::find_user( $number );
			}

			if ( $user ) {
				$linked++;
			} else {
				if ( ! $email || '' === $name ) {
					/* translators: %s: the line as typed. */
					$errors[] = sprintf( __( '"%s": a name and a valid email are needed to create an account.', 'qr-attendance' ), esc_html( $line ) );
					continue;
				}
				$login = sanitize_user( $number ? $number : strstr( $email, '@', true ), true );
				$login = $login ? $login : 'student';
				$base  = $login;
				for ( $i = 2; username_exists( $login ); $i++ ) {
					$login = $base . $i;
				}
				$user_id = wp_insert_user(
					array(
						'user_login'   => $login,
						'user_email'   => $email,
						'user_pass'    => wp_generate_password( 20 ),
						'display_name' => $name,
						'first_name'   => $name,
						'role'         => 'qra_student',
					)
				);
				if ( is_wp_error( $user_id ) ) {
					$errors[] = esc_html( $line . ': ' . $user_id->get_error_message() );
					continue;
				}
				if ( $notify ) {
					wp_new_user_notification( $user_id, null, 'user' );
				}
				$user = get_userdata( $user_id );
				$created++;
			}

			if ( $number && ! QRA_Plugin::student_number( $user->ID ) ) {
				update_user_meta( $user->ID, QRA_Plugin::META_NUM, $number );
			}
			QRA_DB::enroll( (int) $course->id, $user->ID );
		}

		self::flash(
			'success',
			sprintf(
				/* translators: 1: accounts created, 2: existing accounts enrolled. */
				__( '%1$d accounts created, %2$d existing accounts enrolled.', 'qr-attendance' ),
				$created,
				$linked
			)
		);
		if ( $errors ) {
			self::flash( 'warning', implode( '<br>', $errors ) );
		}
		self::done( self::course_url( $course->id, 'students' ) );
	}

	public static function handle_unenroll() {
		self::verify( 'unenroll' );
		$course = self::course_from_request();
		QRA_DB::unenroll( (int) $course->id, absint( self::input( 'user' ) ) );
		self::flash( 'success', __( 'Student removed from the course. Their past records are kept.', 'qr-attendance' ) );
		self::done( self::course_url( $course->id, 'students' ) );
	}

	/* ------------------------------------------------------------------
	 * Handlers: sessions
	 * ------------------------------------------------------------------ */

	public static function handle_start_session() {
		self::verify( 'start_session' );
		$course   = self::course_from_request();
		$duration = min( 24 * 60, absint( self::input( 'duration', QRA_Plugin::setting( 'default_duration' ) ) ) );
		$title    = sanitize_text_field( self::input( 'title' ) );

		$id = QRA_Attendance::start_session( (int) $course->id, $title, $duration );
		self::done( self::session_url( $id ) );
	}

	public static function handle_close_session() {
		self::verify( 'close_session' );
		$session = self::session_from_request();
		QRA_Attendance::close_session( $session );
		self::flash( 'success', __( 'Session closed. Students who did not check in are marked absent.', 'qr-attendance' ) );
		self::done( self::input( 'return' ) === 'course' ? self::course_url( $session->course_id ) : self::session_url( $session->id ) );
	}

	public static function handle_reopen_session() {
		self::verify( 'reopen_session' );
		$session  = self::session_from_request();
		$duration = min( 24 * 60, absint( self::input( 'duration', 15 ) ) );
		QRA_Attendance::reopen_session( $session, $duration );
		self::flash( 'success', __( 'Session reopened.', 'qr-attendance' ) );
		self::done( self::session_url( $session->id ) );
	}

	public static function handle_delete_session() {
		self::verify( 'delete_session' );
		$session = self::session_from_request();
		QRA_DB::delete_session( (int) $session->id );
		self::flash( 'success', __( 'Session deleted.', 'qr-attendance' ) );
		self::done( self::course_url( $session->course_id ) );
	}

	public static function handle_export_csv() {
		self::verify( 'export_csv' );
		QRA_Reports::export_csv( self::course_from_request() );
	}

	/* ------------------------------------------------------------------
	 * Handler: settings
	 * ------------------------------------------------------------------ */

	public static function handle_save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'qr-attendance' ), 403 );
		}
		check_admin_referer( 'qra_save_settings' );

		$settings = array(
			'rotation'         => min( 600, absint( self::input( 'rotation' ) ) ),
			'late_after'       => min( 600, absint( self::input( 'late_after' ) ) ),
			'default_duration' => min( 24 * 60, absint( self::input( 'default_duration' ) ) ),
			'one_device'       => self::input( 'one_device' ) ? 1 : 0,
			'self_enroll'      => self::input( 'self_enroll' ) ? 1 : 0,
			'absence_warning'  => min( 100, absint( self::input( 'absence_warning' ) ) ),
			'attendance_page'  => absint( self::input( 'attendance_page' ) ),
			'remove_data'      => self::input( 'remove_data' ) ? 1 : 0,
		);
		// Codes that change every few seconds are unreadable before they are rendered.
		if ( $settings['rotation'] > 0 && $settings['rotation'] < 5 ) {
			$settings['rotation'] = 5;
		}

		update_option( QRA_Plugin::OPTION, $settings );
		self::flash( 'success', __( 'Settings saved.', 'qr-attendance' ) );
		self::done( admin_url( 'admin.php?page=qra-settings' ) );
	}
}
