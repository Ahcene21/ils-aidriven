<?php
/**
 * Student-facing side: the scan URL, the in-page scanner and shortcodes.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

class QRA_Frontend {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_assets' ) );
		add_action( 'template_redirect', array( __CLASS__, 'handle_scan_url' ), 1 );

		add_shortcode( 'qra_scanner', array( __CLASS__, 'shortcode_scanner' ) );
		add_shortcode( 'qra_my_attendance', array( __CLASS__, 'shortcode_my_attendance' ) );
		add_shortcode( 'qra_my_card', array( __CLASS__, 'shortcode_my_card' ) );
	}

	public static function register_assets() {
		wp_register_style( 'qra-front', QRA_URL . 'assets/css/front.css', array(), QRA_VERSION );
		wp_register_script( 'qra-jsqr', QRA_URL . 'assets/vendor/jsQR.js', array(), '1.4.0', true );
		wp_register_script( 'qra-qrcode', QRA_URL . 'assets/vendor/qrcode.js', array(), '2.0.4', true );
		wp_register_script( 'qra-camera', QRA_URL . 'assets/js/camera.js', array( 'qra-jsqr' ), QRA_VERSION, true );
		wp_register_script( 'qra-scanner', QRA_URL . 'assets/js/scanner.js', array( 'qra-camera' ), QRA_VERSION, true );
		wp_register_script( 'qra-card', QRA_URL . 'assets/js/card.js', array( 'qra-qrcode' ), QRA_VERSION, true );

		wp_localize_script(
			'qra-camera',
			'qraCameraL10n',
			array(
				'noCamera'    => __( 'No camera is available. Open this page on a phone, or use your phone\'s camera app to scan the code.', 'qr-attendance' ),
				'denied'      => __( 'Camera access was refused. Allow the camera for this site in your browser settings and try again.', 'qr-attendance' ),
				'insecure'    => __( 'The camera only works on a secure (https) connection.', 'qr-attendance' ),
				'startCamera' => __( 'Start camera', 'qr-attendance' ),
				'stopCamera'  => __( 'Stop camera', 'qr-attendance' ),
			)
		);
	}

	/* ------------------------------------------------------------------
	 * Opening the QR link with a phone camera: /?qra=<code>
	 * ------------------------------------------------------------------ */

	public static function handle_scan_url() {
		// phpcs:disable WordPress.Security.NonceVerification -- the signed code is the credential.
		if ( isset( $_GET['qra_claim'] ) ) {
			self::redeem_claim( sanitize_text_field( wp_unslash( $_GET['qra_claim'] ) ) );
		}
		if ( ! isset( $_GET['qra'] ) ) {
			return;
		}
		$code = QRA_Token::extract_session_code( '?qra=' . sanitize_text_field( wp_unslash( $_GET['qra'] ) ) );
		// phpcs:enable

		$session = QRA_Token::verify_session_code( $code );
		if ( is_wp_error( $session ) ) {
			self::result_page( 'error', $session->get_error_message() );
		}

		$device_id = QRA_Attendance::device_id();

		if ( ! is_user_logged_in() ) {
			// Validate now, check in after the login form (see create_claim()).
			$claim = QRA_Attendance::create_claim( $session, $device_id );
			wp_safe_redirect( wp_login_url( add_query_arg( 'qra_claim', $claim, home_url( '/' ) ) ) );
			exit;
		}

		self::finish_check_in( $session, $device_id, time() );
	}

	private static function redeem_claim( $claim_id ) {
		if ( ! is_user_logged_in() ) {
			auth_redirect();
		}
		$device_id = QRA_Attendance::device_id();
		$claim     = QRA_Attendance::redeem_claim( $claim_id, $device_id );
		if ( is_wp_error( $claim ) ) {
			self::result_page( 'error', $claim->get_error_message() );
		}
		$session = QRA_DB::get_session( (int) $claim['session_id'] );
		if ( ! $session ) {
			self::result_page( 'error', __( 'This QR code is not an attendance code.', 'qr-attendance' ) );
		}
		self::finish_check_in( $session, $device_id, (int) $claim['scanned_at'] );
	}

	private static function finish_check_in( $session, $device_id, $scanned_at ) {
		$result = QRA_Attendance::check_in( $session, get_current_user_id(), $device_id, $scanned_at );
		if ( is_wp_error( $result ) ) {
			self::result_page( 'error', $result->get_error_message() );
		}

		$detail = $result['course']->name;
		if ( $result['session']->title ) {
			$detail .= ' — ' . $result['session']->title;
		}
		self::result_page( $result['already'] ? 'info' : 'success', QRA_Attendance::success_message( $result ), $detail );
	}

	/**
	 * A small standalone page shown on the student's phone, then exit.
	 *
	 * @param string $type success|info|error.
	 */
	private static function result_page( $type, $message, $detail = '' ) {
		nocache_headers();
		status_header( 'error' === $type ? 400 : 200 );

		$page_id = (int) QRA_Plugin::setting( 'attendance_page' );
		$link    = $page_id ? get_permalink( $page_id ) : '';
		$icons   = array(
			'success' => '&#10004;',
			'info'    => '&#8505;',
			'error'   => '&#10006;',
		);

		include QRA_DIR . 'templates/result.php';
		exit;
	}

	/* ------------------------------------------------------------------
	 * Shortcodes
	 * ------------------------------------------------------------------ */

	private static function login_prompt() {
		return '<div class="qra qra-notice">' . sprintf(
			/* translators: %s: login URL. */
			wp_kses_post( __( 'Please <a href="%s">log in</a> to continue.', 'qr-attendance' ) ),
			esc_url( wp_login_url( get_permalink() ) )
		) . '</div>';
	}

	/** [qra_scanner] — scan the teacher's code from inside the site. */
	public static function shortcode_scanner() {
		if ( ! is_user_logged_in() ) {
			return self::login_prompt();
		}
		wp_enqueue_style( 'qra-front' );
		wp_enqueue_script( 'qra-scanner' );
		wp_localize_script(
			'qra-scanner',
			'qraScanner',
			array(
				'endpoint' => esc_url_raw( rest_url( QRA_REST::NS . '/checkin' ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'working'  => __( 'Checking in…', 'qr-attendance' ),
				'again'    => __( 'Scan again', 'qr-attendance' ),
				'failed'   => __( 'Could not reach the server. Check your connection and try again.', 'qr-attendance' ),
			)
		);

		ob_start();
		?>
		<div class="qra qra-scanner" data-qra-scanner>
			<p class="qra-help"><?php esc_html_e( 'Point your camera at the QR code shown by your teacher.', 'qr-attendance' ); ?></p>
			<div class="qra-video-wrap" hidden>
				<video playsinline muted></video>
				<span class="qra-frame" aria-hidden="true"></span>
			</div>
			<div class="qra-result" role="status" aria-live="polite"></div>
			<p><button type="button" class="qra-button" data-qra-toggle><?php esc_html_e( 'Start camera', 'qr-attendance' ); ?></button></p>
		</div>
		<?php
		return ob_get_clean();
	}

	/** [qra_my_attendance] — the student's record in each course. */
	public static function shortcode_my_attendance() {
		if ( ! is_user_logged_in() ) {
			return self::login_prompt();
		}
		wp_enqueue_style( 'qra-front' );

		$user_id = get_current_user_id();
		$courses = QRA_DB::get_student_courses( $user_id );

		ob_start();
		echo '<div class="qra qra-my-attendance">';
		if ( ! $courses ) {
			echo '<p class="qra-notice">' . esc_html__( 'You are not enrolled in any course yet.', 'qr-attendance' ) . '</p>';
		}
		foreach ( $courses as $course ) {
			$report = QRA_Reports::student_course_report( $course->id, $user_id );
			$totals = $report['totals'];
			?>
			<section class="qra-course<?php echo QRA_Reports::is_flagged( $totals ) ? ' is-flagged' : ''; ?>">
				<h3><?php echo esc_html( $course->name ); ?><?php echo $course->code ? ' <small>(' . esc_html( $course->code ) . ')</small>' : ''; ?></h3>
				<ul class="qra-stats">
					<li class="is-present"><strong><?php echo (int) $totals['present']; ?></strong> <?php esc_html_e( 'Present', 'qr-attendance' ); ?></li>
					<li class="is-late"><strong><?php echo (int) $totals['late']; ?></strong> <?php esc_html_e( 'Late', 'qr-attendance' ); ?></li>
					<li class="is-absent"><strong><?php echo (int) $totals['absent']; ?></strong> <?php esc_html_e( 'Absent', 'qr-attendance' ); ?></li>
					<li class="is-excused"><strong><?php echo (int) $totals['excused']; ?></strong> <?php esc_html_e( 'Excused', 'qr-attendance' ); ?></li>
					<li><strong><?php echo esc_html( QRA_Reports::format_rate( $totals['rate'] ) ); ?></strong> <?php esc_html_e( 'Attendance rate', 'qr-attendance' ); ?></li>
				</ul>
				<?php if ( QRA_Reports::is_flagged( $totals ) ) : ?>
					<p class="qra-warning"><?php esc_html_e( 'Warning: you have reached the absence limit for this course.', 'qr-attendance' ); ?></p>
				<?php endif; ?>
				<?php if ( $report['rows'] ) : ?>
					<table class="qra-table">
						<thead><tr><th><?php esc_html_e( 'Session', 'qr-attendance' ); ?></th><th><?php esc_html_e( 'Status', 'qr-attendance' ); ?></th></tr></thead>
						<tbody>
						<?php foreach ( array_reverse( $report['rows'] ) as $row ) : ?>
							<tr>
								<td>
									<?php echo esc_html( QRA_Plugin::format_datetime( $row['session']->starts_at ) ); ?>
									<?php echo $row['session']->title ? '<br><small>' . esc_html( $row['session']->title ) . '</small>' : ''; ?>
								</td>
								<td>
									<?php if ( $row['status'] ) : ?>
										<span class="qra-badge is-<?php echo esc_attr( $row['status'] ); ?>"><?php echo esc_html( QRA_Plugin::status_label( $row['status'] ) ); ?></span>
									<?php elseif ( 'open' === $row['session']->status ) : ?>
										<span class="qra-badge"><?php esc_html_e( 'In progress', 'qr-attendance' ); ?></span>
									<?php else : ?>
										—
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</section>
			<?php
		}
		echo '</div>';
		return ob_get_clean();
	}

	/** [qra_my_card] — personal QR code the teacher can scan. */
	public static function shortcode_my_card() {
		if ( ! is_user_logged_in() ) {
			return self::login_prompt();
		}
		wp_enqueue_style( 'qra-front' );
		wp_enqueue_script( 'qra-card' );

		$user = wp_get_current_user();
		$num  = QRA_Plugin::student_number( $user->ID );

		ob_start();
		?>
		<div class="qra qra-card">
			<div class="qra-card-code" data-qra-code="<?php echo esc_attr( QRA_Token::card_code( $user->ID ) ); ?>"></div>
			<p class="qra-card-name"><strong><?php echo esc_html( $user->display_name ); ?></strong></p>
			<?php if ( $num ) : ?>
				<p class="qra-card-number"><?php echo esc_html( $num ); ?></p>
			<?php endif; ?>
			<p class="qra-help"><?php esc_html_e( 'Show this code to your teacher to be checked in.', 'qr-attendance' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}
}
