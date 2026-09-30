<?php
/**
 * Plugin settings.
 *
 * @var array $settings
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap qra-admin">
	<h1><?php esc_html_e( 'Attendance settings', 'qr-attendance' ); ?></h1>

	<?php QRA_Admin::form_open( 'save_settings' ); ?>
		<h2 class="title"><?php esc_html_e( 'Checking in', 'qr-attendance' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="qra-rotation"><?php esc_html_e( 'QR code refresh', 'qr-attendance' ); ?></label></th>
				<td>
					<input type="number" id="qra-rotation" name="rotation" min="0" max="600" class="small-text" value="<?php echo (int) $settings['rotation']; ?>"> <?php esc_html_e( 'seconds', 'qr-attendance' ); ?>
					<p class="description"><?php esc_html_e( 'The projected code changes this often, so a photo sent to an absent friend stops working almost at once. 0 keeps one code for the whole session (not recommended).', 'qr-attendance' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="qra-late"><?php esc_html_e( 'Late after', 'qr-attendance' ); ?></label></th>
				<td>
					<input type="number" id="qra-late" name="late_after" min="0" max="600" class="small-text" value="<?php echo (int) $settings['late_after']; ?>"> <?php esc_html_e( 'minutes', 'qr-attendance' ); ?>
					<p class="description"><?php esc_html_e( 'Check-ins this long after the session starts are recorded as late. 0 disables late marking.', 'qr-attendance' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="qra-duration"><?php esc_html_e( 'Default session length', 'qr-attendance' ); ?></label></th>
				<td>
					<input type="number" id="qra-duration" name="default_duration" min="0" max="1440" class="small-text" value="<?php echo (int) $settings['default_duration']; ?>"> <?php esc_html_e( 'minutes', 'qr-attendance' ); ?>
					<p class="description"><?php esc_html_e( 'Sessions close on their own after this time and absences are recorded.', 'qr-attendance' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Anti-cheating', 'qr-attendance' ); ?></th>
				<td>
					<label><input type="checkbox" name="one_device" value="1" <?php checked( $settings['one_device'] ); ?>> <?php esc_html_e( 'A phone can check in only one student per session', 'qr-attendance' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Enrolment', 'qr-attendance' ); ?></th>
				<td>
					<label><input type="checkbox" name="self_enroll" value="1" <?php checked( $settings['self_enroll'] ); ?>> <?php esc_html_e( 'Enrol students automatically the first time they scan a course\'s code', 'qr-attendance' ); ?></label>
					<p class="description"><?php esc_html_e( 'Leave unchecked to accept only students on the course list.', 'qr-attendance' ); ?></p>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Reports', 'qr-attendance' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="qra-warning"><?php esc_html_e( 'Absence warning', 'qr-attendance' ); ?></label></th>
				<td>
					<input type="number" id="qra-warning" name="absence_warning" min="0" max="100" class="small-text" value="<?php echo (int) $settings['absence_warning']; ?>"> <?php esc_html_e( 'absences', 'qr-attendance' ); ?>
					<p class="description"><?php esc_html_e( 'Students with this many unexcused absences are highlighted and warned on their attendance page. 0 disables it.', 'qr-attendance' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="qra-page"><?php esc_html_e( 'Student attendance page', 'qr-attendance' ); ?></label></th>
				<td>
					<?php
					wp_dropdown_pages(
						array(
							'name'              => 'attendance_page',
							'id'                => 'qra-page',
							'selected'          => (int) $settings['attendance_page'],
							'show_option_none'  => __( '— None —', 'qr-attendance' ),
							'option_none_value' => 0,
						)
					);
					?>
					<p class="description"><?php esc_html_e( 'The page containing [qra_my_attendance]; students are sent a link to it after checking in.', 'qr-attendance' ); ?></p>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Data', 'qr-attendance' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Uninstall', 'qr-attendance' ); ?></th>
				<td>
					<label><input type="checkbox" name="remove_data" value="1" <?php checked( $settings['remove_data'] ); ?>> <?php esc_html_e( 'Delete all courses and attendance records when the plugin is deleted', 'qr-attendance' ); ?></label>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
