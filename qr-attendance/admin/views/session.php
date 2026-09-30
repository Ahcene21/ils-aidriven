<?php
/**
 * Live session screen: the projected QR code and the roll call.
 *
 * @var object $session
 * @var object $course
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;

$is_open = 'open' === $session->status;
?>
<div class="wrap qra-admin qra-session<?php echo $is_open ? ' is-open' : ' is-closed'; ?>" id="qra-session">
	<p class="qra-breadcrumb"><a href="<?php echo esc_url( QRA_Admin::course_url( $course->id ) ); ?>">&larr; <?php echo esc_html( $course->name ); ?></a></p>
	<h1>
		<?php echo esc_html( $session->title ? $session->title : __( 'Session', 'qr-attendance' ) ); ?>
		<span class="qra-code"><?php echo esc_html( QRA_Plugin::format_datetime( $session->starts_at ) ); ?></span>
	</h1>

	<div class="qra-live">
		<section class="qra-projector" id="qra-projector">
			<div class="qra-projector-head">
				<strong><?php echo esc_html( $course->name ); ?></strong>
				<?php if ( $course->code ) : ?>
					<span><?php echo esc_html( $course->code ); ?></span>
				<?php endif; ?>
			</div>

			<div class="qra-qr" data-qra-qr aria-label="<?php esc_attr_e( 'Attendance QR code', 'qr-attendance' ); ?>"></div>
			<p class="qra-closed-msg"<?php echo $is_open ? ' hidden' : ''; ?>><?php esc_html_e( 'This session is closed.', 'qr-attendance' ); ?></p>

			<div class="qra-projector-foot">
				<p class="qra-instructions"><?php esc_html_e( 'Scan with your phone camera to check in', 'qr-attendance' ); ?></p>
				<div class="qra-timer"><span class="qra-timer-bar" data-qra-bar></span></div>
				<p class="qra-meta"><span data-qra-countdown></span> <span data-qra-ends></span></p>
				<p class="qra-counter"><strong data-qra-checked>0</strong> / <span data-qra-total>0</span> <?php esc_html_e( 'checked in', 'qr-attendance' ); ?></p>
			</div>

			<button type="button" class="button qra-fullscreen" data-qra-fullscreen>
				<span class="dashicons dashicons-fullscreen-alt"></span> <?php esc_html_e( 'Full screen', 'qr-attendance' ); ?>
			</button>
		</section>

		<section class="qra-roll">
			<div class="qra-counts">
				<?php foreach ( QRA_Plugin::statuses() as $status => $label ) : ?>
					<div class="qra-count is-<?php echo esc_attr( $status ); ?>"><strong data-qra-count="<?php echo esc_attr( $status ); ?>">0</strong><span><?php echo esc_html( $label ); ?></span></div>
				<?php endforeach; ?>
			</div>

			<div class="qra-toolbar">
				<?php if ( $is_open ) : ?>
					<button type="button" class="button" data-qra-cards><span class="dashicons dashicons-camera"></span> <span data-qra-cards-label><?php esc_html_e( 'Scan student cards', 'qr-attendance' ); ?></span></button>
					<?php QRA_Admin::form_open( 'close_session', array( 'session' => $session->id ), 'class="qra-inline" data-confirm-submit="' . esc_attr__( 'Close the session? Students who have not checked in will be marked absent.', 'qr-attendance' ) . '"' ); ?>
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Close session', 'qr-attendance' ); ?></button>
					</form>
				<?php else : ?>
					<?php QRA_Admin::form_open( 'reopen_session', array( 'session' => $session->id ), 'class="qra-inline"' ); ?>
						<label><?php esc_html_e( 'Reopen for', 'qr-attendance' ); ?> <input type="number" name="duration" value="15" min="0" max="1440" class="small-text"> <?php esc_html_e( 'minutes', 'qr-attendance' ); ?></label>
						<button type="submit" class="button"><?php esc_html_e( 'Reopen', 'qr-attendance' ); ?></button>
					</form>
				<?php endif; ?>
			</div>

			<div class="qra-card-scanner" data-qra-card-scanner hidden>
				<video playsinline muted></video>
				<p class="qra-card-result" role="status" aria-live="polite"></p>
			</div>

			<input type="search" class="qra-filter" data-qra-filter placeholder="<?php esc_attr_e( 'Search students…', 'qr-attendance' ); ?>">
			<p class="qra-offline" data-qra-offline hidden></p>

			<table class="widefat striped qra-roll-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Student', 'qr-attendance' ); ?></th>
						<th><?php esc_html_e( 'Time', 'qr-attendance' ); ?></th>
						<th><?php esc_html_e( 'Status', 'qr-attendance' ); ?></th>
					</tr>
				</thead>
				<tbody data-qra-roll>
					<tr><td colspan="3"><span class="spinner is-active"></span></td></tr>
				</tbody>
			</table>
		</section>
	</div>
</div>
<script>
document.addEventListener( 'submit', function ( e ) {
	var msg = e.target.getAttribute( 'data-confirm-submit' );
	if ( msg && ! window.confirm( msg ) ) {
		e.preventDefault();
	}
} );
</script>
