<?php
/**
 * Page shown on the student's phone after opening a scanned attendance link.
 *
 * Variables: $type (success|info|error), $message, $detail, $link, $icons.
 *
 * @package QR_Attendance
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<title><?php echo esc_html( $message . ' — ' . get_bloginfo( 'name' ) ); ?></title>
	<style>
		:root { --bg: #f4f5f7; --card: #fff; --text: #1d2327; --muted: #646970; --success: #0a7d3b; --info: #2271b1; --error: #b32d2e; }
		@media (prefers-color-scheme: dark) {
			:root { --bg: #16181b; --card: #22262b; --text: #eceef0; --muted: #a7aaad; --success: #4cc27f; --info: #72aee6; --error: #f07b7c; }
		}
		* { box-sizing: border-box; }
		body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 16px;
			background: var(--bg); color: var(--text);
			font: 17px/1.5 -apple-system, BlinkMacSystemFont, "Segoe UI", Tahoma, "Noto Sans Arabic", Roboto, sans-serif; }
		main { width: 100%; max-width: 420px; background: var(--card); border-radius: 16px; padding: 32px 24px; text-align: center;
			box-shadow: 0 2px 16px rgba(0, 0, 0, .08); }
		.icon { width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center;
			font-size: 36px; color: #fff; background: var(--<?php echo esc_attr( $type ); ?>); }
		h1 { font-size: 1.25rem; margin: 0 0 8px; }
		p { margin: 0 0 8px; color: var(--muted); }
		.time { font-variant-numeric: tabular-nums; }
		a { display: inline-block; margin-top: 16px; color: var(--info); }
	</style>
</head>
<body>
	<main>
		<div class="icon" aria-hidden="true"><?php echo $icons[ $type ]; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed entities. ?></div>
		<h1><?php echo esc_html( $message ); ?></h1>
		<?php if ( $detail ) : ?>
			<p><?php echo esc_html( $detail ); ?></p>
		<?php endif; ?>
		<?php if ( 'error' !== $type ) : ?>
			<p class="time"><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' — ' . get_option( 'time_format' ) ) ); ?></p>
		<?php endif; ?>
		<?php if ( is_user_logged_in() ) : ?>
			<p><?php echo esc_html( wp_get_current_user()->display_name ); ?></p>
		<?php endif; ?>
		<?php if ( $link ) : ?>
			<a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View my attendance', 'qr-attendance' ); ?></a>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the site', 'qr-attendance' ); ?></a>
		<?php endif; ?>
	</main>
</body>
</html>
