<?php
/**
 * Course list and the "new course" form.
 *
 * @var object[] $courses
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap qra-admin">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Courses', 'qr-attendance' ); ?></h1>
	<hr class="wp-header-end">

	<div class="qra-columns">
		<div class="qra-main">
			<?php if ( ! $courses ) : ?>
				<div class="qra-empty">
					<p><?php esc_html_e( 'No courses yet. Create your first course, add its students, then start a session to show the attendance QR code.', 'qr-attendance' ); ?></p>
				</div>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Course', 'qr-attendance' ); ?></th>
							<th><?php esc_html_e( 'Teacher', 'qr-attendance' ); ?></th>
							<th class="num"><?php esc_html_e( 'Students', 'qr-attendance' ); ?></th>
							<th class="num"><?php esc_html_e( 'Sessions', 'qr-attendance' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $courses as $course ) : ?>
						<?php
						$teacher  = get_userdata( (int) $course->teacher_id );
						$sessions = QRA_DB::get_sessions( (int) $course->id );
						$open     = array_filter(
							$sessions,
							function ( $s ) {
								return 'open' === $s->status;
							}
						);
						?>
						<tr>
							<td>
								<strong><a href="<?php echo esc_url( QRA_Admin::course_url( $course->id ) ); ?>"><?php echo esc_html( $course->name ); ?></a></strong>
								<?php if ( $course->code ) : ?>
									<br><span class="description"><?php echo esc_html( $course->code ); ?></span>
								<?php endif; ?>
							</td>
							<td><?php echo $teacher ? esc_html( $teacher->display_name ) : '—'; ?></td>
							<td class="num"><?php echo (int) QRA_DB::count_students( (int) $course->id ); ?></td>
							<td class="num"><?php echo count( $sessions ); ?></td>
							<td class="qra-actions">
								<?php if ( $open ) : ?>
									<a class="button button-primary" href="<?php echo esc_url( QRA_Admin::session_url( reset( $open )->id ) ); ?>"><?php esc_html_e( 'Session in progress', 'qr-attendance' ); ?></a>
								<?php else : ?>
									<?php QRA_Admin::form_open( 'start_session', array( 'course' => $course->id, 'duration' => QRA_Plugin::setting( 'default_duration' ) ), 'class="qra-inline"' ); ?>
										<button type="submit" class="button button-primary"><?php esc_html_e( 'Start session', 'qr-attendance' ); ?></button>
									</form>
								<?php endif; ?>
								<a class="button" href="<?php echo esc_url( QRA_Admin::course_url( $course->id, 'report' ) ); ?>"><?php esc_html_e( 'Report', 'qr-attendance' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<div class="qra-side">
			<div class="qra-box">
				<h2><?php esc_html_e( 'New course', 'qr-attendance' ); ?></h2>
				<?php QRA_Admin::form_open( 'save_course' ); ?>
					<?php QRA_Admin::course_fields(); ?>
					<?php submit_button( __( 'Create course', 'qr-attendance' ) ); ?>
				</form>
			</div>
			<div class="qra-box">
				<h2><?php esc_html_e( 'Student pages', 'qr-attendance' ); ?></h2>
				<p><?php esc_html_e( 'Students can simply open the projected QR code with their phone camera. You can also add these shortcodes to pages:', 'qr-attendance' ); ?></p>
				<ul class="qra-shortcodes">
					<li><code>[qra_scanner]</code> — <?php esc_html_e( 'scan the code from inside the site', 'qr-attendance' ); ?></li>
					<li><code>[qra_my_attendance]</code> — <?php esc_html_e( 'the student\'s attendance and absences', 'qr-attendance' ); ?></li>
					<li><code>[qra_my_card]</code> — <?php esc_html_e( 'personal QR card the teacher can scan', 'qr-attendance' ); ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
