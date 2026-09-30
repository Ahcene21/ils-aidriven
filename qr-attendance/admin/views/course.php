<?php
/**
 * A course: sessions, students, report and details tabs.
 *
 * @var object $course
 * @var array  $tabs
 * @var string $tab
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

$course_id = (int) $course->id;
?>
<div class="wrap qra-admin">
	<p class="qra-breadcrumb"><a href="<?php echo esc_url( QRA_Admin::url() ); ?>">&larr; <?php esc_html_e( 'Courses', 'qr-attendance' ); ?></a></p>
	<h1><?php echo esc_html( $course->name ); ?>
		<?php if ( $course->code ) : ?>
			<span class="qra-code"><?php echo esc_html( $course->code ); ?></span>
		<?php endif; ?>
	</h1>

	<nav class="nav-tab-wrapper">
		<?php foreach ( $tabs as $key => $label ) : ?>
			<a href="<?php echo esc_url( QRA_Admin::course_url( $course_id, $key ) ); ?>" class="nav-tab<?php echo $key === $tab ? ' nav-tab-active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</nav>

	<?php
	/* ==================================================================
	 * Sessions
	 * ================================================================== */
	if ( 'sessions' === $tab ) :
		$sessions = array_reverse( QRA_DB::get_sessions( $course_id ) );
		$counts   = QRA_DB::get_session_counts( $course_id );
		$students = QRA_DB::count_students( $course_id );
		?>
		<div class="qra-box qra-start">
			<h2><?php esc_html_e( 'Start a session', 'qr-attendance' ); ?></h2>
			<?php if ( ! $students ) : ?>
				<p class="description"><?php esc_html_e( 'Tip: add students first, otherwise nobody can check in.', 'qr-attendance' ); ?></p>
			<?php endif; ?>
			<?php QRA_Admin::form_open( 'start_session', array( 'course' => $course_id ), 'class="qra-start-form"' ); ?>
				<label>
					<span><?php esc_html_e( 'Title (optional)', 'qr-attendance' ); ?></span>
					<input type="text" name="title" placeholder="<?php esc_attr_e( 'e.g. Lecture 5 — Recursion', 'qr-attendance' ); ?>">
				</label>
				<label>
					<span><?php esc_html_e( 'Open for (minutes)', 'qr-attendance' ); ?></span>
					<input type="number" name="duration" min="0" max="1440" value="<?php echo (int) QRA_Plugin::setting( 'default_duration' ); ?>" class="small-text">
				</label>
				<button type="submit" class="button button-primary button-hero"><?php esc_html_e( 'Start and show QR code', 'qr-attendance' ); ?></button>
			</form>
			<p class="description"><?php esc_html_e( 'When the session closes, every enrolled student who did not check in is recorded as absent. Use 0 minutes to keep it open until you close it.', 'qr-attendance' ); ?></p>
		</div>

		<?php if ( $sessions ) : ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'qr-attendance' ); ?></th>
						<th><?php esc_html_e( 'Title', 'qr-attendance' ); ?></th>
						<th><?php esc_html_e( 'Status', 'qr-attendance' ); ?></th>
						<?php foreach ( QRA_Plugin::statuses() as $label ) : ?>
							<th class="num"><?php echo esc_html( $label ); ?></th>
						<?php endforeach; ?>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $sessions as $session ) : ?>
					<tr>
						<td><a href="<?php echo esc_url( QRA_Admin::session_url( $session->id ) ); ?>"><?php echo esc_html( QRA_Plugin::format_datetime( $session->starts_at ) ); ?></a></td>
						<td><?php echo esc_html( $session->title ); ?></td>
						<td>
							<?php if ( 'open' === $session->status ) : ?>
								<span class="qra-badge is-open"><?php esc_html_e( 'Open', 'qr-attendance' ); ?></span>
							<?php else : ?>
								<span class="qra-badge"><?php esc_html_e( 'Closed', 'qr-attendance' ); ?></span>
							<?php endif; ?>
						</td>
						<?php foreach ( array_keys( QRA_Plugin::statuses() ) as $status ) : ?>
							<td class="num"><?php echo isset( $counts[ $session->id ][ $status ] ) ? (int) $counts[ $session->id ][ $status ] : 0; ?></td>
						<?php endforeach; ?>
						<td class="qra-actions">
							<a class="button" href="<?php echo esc_url( QRA_Admin::session_url( $session->id ) ); ?>"><?php echo 'open' === $session->status ? esc_html__( 'Show QR code', 'qr-attendance' ) : esc_html__( 'Details', 'qr-attendance' ); ?></a>
							<?php if ( 'open' === $session->status ) : ?>
								<a class="button" href="<?php echo esc_url( QRA_Admin::action_url( 'close_session', array( 'session' => $session->id, 'return' => 'course' ) ) ); ?>"><?php esc_html_e( 'Close', 'qr-attendance' ); ?></a>
							<?php endif; ?>
							<a class="qra-delete" href="<?php echo esc_url( QRA_Admin::action_url( 'delete_session', array( 'session' => $session->id ) ) ); ?>" data-confirm="<?php esc_attr_e( 'Delete this session and all its attendance records?', 'qr-attendance' ); ?>"><?php esc_html_e( 'Delete', 'qr-attendance' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

	<?php
	/* ==================================================================
	 * Students
	 * ================================================================== */
	elseif ( 'students' === $tab ) :
		$students = QRA_DB::get_students( $course_id );
		$report   = QRA_Reports::course_report( $course_id );
		?>
		<div class="qra-columns">
			<div class="qra-main">
				<h2>
					<?php
					/* translators: %d: number of students. */
					echo esc_html( sprintf( _n( '%d student', '%d students', count( $students ), 'qr-attendance' ), count( $students ) ) );
					?>
				</h2>
				<?php if ( $students ) : ?>
					<table class="widefat striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Student number', 'qr-attendance' ); ?></th>
								<th><?php esc_html_e( 'Name', 'qr-attendance' ); ?></th>
								<th><?php esc_html_e( 'Email', 'qr-attendance' ); ?></th>
								<th class="num"><?php esc_html_e( 'Absences', 'qr-attendance' ); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $students as $student ) : ?>
							<?php $totals = $report['totals'][ $student->ID ]; ?>
							<tr<?php echo QRA_Reports::is_flagged( $totals ) ? ' class="is-flagged"' : ''; ?>>
								<td><?php echo esc_html( $student->student_number ); ?></td>
								<td>
									<?php if ( current_user_can( 'edit_user', $student->ID ) ) : ?>
										<a href="<?php echo esc_url( get_edit_user_link( $student->ID ) ); ?>"><?php echo esc_html( $student->display_name ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $student->display_name ); ?>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( $student->user_email ); ?></td>
								<td class="num"><?php echo (int) $totals['absent']; ?></td>
								<td class="qra-actions">
									<a class="qra-delete" href="<?php echo esc_url( QRA_Admin::action_url( 'unenroll', array( 'course' => $course_id, 'user' => $student->ID ) ) ); ?>" data-confirm="<?php esc_attr_e( 'Remove this student from the course?', 'qr-attendance' ); ?>"><?php esc_html_e( 'Remove', 'qr-attendance' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<div class="qra-empty"><p><?php esc_html_e( 'No students yet. Add existing accounts or create new ones with the forms on the side.', 'qr-attendance' ); ?></p></div>
				<?php endif; ?>
			</div>

			<div class="qra-side">
				<div class="qra-box">
					<h2><?php esc_html_e( 'Add existing accounts', 'qr-attendance' ); ?></h2>
					<?php QRA_Admin::form_open( 'enroll', array( 'course' => $course_id ) ); ?>
						<p><label for="qra-identifiers"><?php esc_html_e( 'Emails, usernames or student numbers — one per line.', 'qr-attendance' ); ?></label></p>
						<textarea id="qra-identifiers" name="identifiers" rows="6" class="large-text code" dir="ltr"></textarea>
						<?php submit_button( __( 'Enrol', 'qr-attendance' ), 'secondary' ); ?>
					</form>
				</div>

				<?php if ( current_user_can( 'create_users' ) ) : ?>
				<div class="qra-box">
					<h2><?php esc_html_e( 'Create student accounts', 'qr-attendance' ); ?></h2>
					<?php QRA_Admin::form_open( 'create_students', array( 'course' => $course_id ) ); ?>
						<p><label for="qra-students">
							<?php esc_html_e( 'One student per line: student number, full name, email. You can paste three columns straight from a spreadsheet.', 'qr-attendance' ); ?>
						</label></p>
						<textarea id="qra-students" name="students" rows="8" class="large-text code" placeholder="2024001, Amina Benali, amina@example.com"></textarea>
						<p><label><input type="checkbox" name="notify" value="1" checked> <?php esc_html_e( 'Email each new student a link to set their password', 'qr-attendance' ); ?></label></p>
						<p class="description"><?php esc_html_e( 'Existing accounts (same email or student number) are enrolled instead of duplicated.', 'qr-attendance' ); ?></p>
						<?php submit_button( __( 'Create and enrol', 'qr-attendance' ), 'secondary' ); ?>
					</form>
				</div>
				<?php endif; ?>
			</div>
		</div>

	<?php
	/* ==================================================================
	 * Report
	 * ================================================================== */
	elseif ( 'report' === $tab ) :
		$report = QRA_Reports::course_report( $course_id );
		?>
		<div class="qra-report-head">
			<p class="description">
				<?php
				$threshold = (int) QRA_Plugin::setting( 'absence_warning' );
				if ( $threshold ) {
					/* translators: %d: number of absences. */
					echo esc_html( sprintf( __( 'Highlighted rows have %d or more absences.', 'qr-attendance' ), $threshold ) ) . ' ';
				}
				esc_html_e( 'An empty cell means no record: the session is still open, or the student joined the course later.', 'qr-attendance' );
				?>
			</p>
			<a class="button" href="<?php echo esc_url( QRA_Admin::action_url( 'export_csv', array( 'course' => $course_id ) ) ); ?>"><span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Export CSV', 'qr-attendance' ); ?></a>
		</div>

		<?php if ( ! $report['students'] || ! $report['sessions'] ) : ?>
			<div class="qra-empty"><p><?php esc_html_e( 'The report fills in once the course has students and sessions.', 'qr-attendance' ); ?></p></div>
		<?php else : ?>
			<div class="qra-grid-wrap">
				<table class="widefat qra-grid">
					<thead>
						<tr>
							<th class="qra-sticky"><?php esc_html_e( 'Student', 'qr-attendance' ); ?></th>
							<?php foreach ( $report['sessions'] as $session ) : ?>
								<th class="qra-cell" title="<?php echo esc_attr( $session->title ); ?>">
									<a href="<?php echo esc_url( QRA_Admin::session_url( $session->id ) ); ?>"><?php echo esc_html( QRA_Plugin::format_datetime( $session->starts_at, 'd/m' ) ); ?></a>
								</th>
							<?php endforeach; ?>
							<?php foreach ( QRA_Plugin::statuses() as $status => $label ) : ?>
								<th class="num" title="<?php echo esc_attr( $label ); ?>"><span class="qra-dot is-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( QRA_Plugin::status_short( $status ) ); ?></span></th>
							<?php endforeach; ?>
							<th class="num"><?php esc_html_e( 'Rate', 'qr-attendance' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $report['students'] as $student ) : ?>
						<?php $totals = $report['totals'][ $student->ID ]; ?>
						<tr<?php echo QRA_Reports::is_flagged( $totals ) ? ' class="is-flagged"' : ''; ?>>
							<th class="qra-sticky" scope="row">
								<?php echo esc_html( $student->display_name ); ?>
								<?php if ( $student->student_number ) : ?>
									<br><span class="description"><?php echo esc_html( $student->student_number ); ?></span>
								<?php endif; ?>
							</th>
							<?php foreach ( $report['sessions'] as $session ) : ?>
								<?php $status = isset( $report['matrix'][ $session->id ][ $student->ID ] ) ? $report['matrix'][ $session->id ][ $student->ID ] : ''; ?>
								<td class="qra-cell">
									<?php if ( $status ) : ?>
										<span class="qra-dot is-<?php echo esc_attr( $status ); ?>" title="<?php echo esc_attr( QRA_Plugin::status_label( $status ) ); ?>"><?php echo esc_html( QRA_Plugin::status_short( $status ) ); ?></span>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
							<?php foreach ( array_keys( QRA_Plugin::statuses() ) as $status ) : ?>
								<td class="num"><?php echo (int) $totals[ $status ]; ?></td>
							<?php endforeach; ?>
							<td class="num"><strong><?php echo esc_html( QRA_Reports::format_rate( $totals['rate'] ) ); ?></strong></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>

	<?php
	/* ==================================================================
	 * Course details
	 * ================================================================== */
	else :
		?>
		<div class="qra-box">
			<?php QRA_Admin::form_open( 'save_course', array( 'course' => $course_id ) ); ?>
				<?php QRA_Admin::course_fields( $course ); ?>
				<?php submit_button( __( 'Save course', 'qr-attendance' ) ); ?>
			</form>
		</div>
		<div class="qra-box qra-danger">
			<h2><?php esc_html_e( 'Delete course', 'qr-attendance' ); ?></h2>
			<p><?php esc_html_e( 'Deletes the course with all its sessions and attendance records. Student accounts are kept.', 'qr-attendance' ); ?></p>
			<a class="button qra-delete" href="<?php echo esc_url( QRA_Admin::action_url( 'delete_course', array( 'course' => $course_id ) ) ); ?>" data-confirm="<?php esc_attr_e( 'Delete this course and all its attendance records? This cannot be undone.', 'qr-attendance' ); ?>"><?php esc_html_e( 'Delete course', 'qr-attendance' ); ?></a>
		</div>
	<?php endif; ?>
</div>
<script>
document.addEventListener( 'click', function ( e ) {
	var link = e.target.closest( '[data-confirm]' );
	if ( link && ! window.confirm( link.getAttribute( 'data-confirm' ) ) ) {
		e.preventDefault();
	}
} );
</script>
