<?php
/**
 * Plugin Name:       QR Attendance
 * Description:       Student attendance and absence tracking. Teachers open a session and project a rotating QR code; students scan it with their phone to check in. Absences, late arrivals and excuses are tracked per course, with reports and CSV export.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            ILS AI-Driven
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       qr-attendance
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'QRA_VERSION', '1.0.0' );
define( 'QRA_FILE', __FILE__ );
define( 'QRA_DIR', plugin_dir_path( __FILE__ ) );
define( 'QRA_URL', plugin_dir_url( __FILE__ ) );

require_once QRA_DIR . 'includes/class-qra-plugin.php';
require_once QRA_DIR . 'includes/class-qra-db.php';
require_once QRA_DIR . 'includes/class-qra-token.php';
require_once QRA_DIR . 'includes/class-qra-attendance.php';
require_once QRA_DIR . 'includes/class-qra-reports.php';
require_once QRA_DIR . 'includes/class-qra-rest.php';
require_once QRA_DIR . 'includes/class-qra-frontend.php';
require_once QRA_DIR . 'includes/class-qra-admin.php';

register_activation_hook( __FILE__, array( 'QRA_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'QRA_Plugin', 'deactivate' ) );

QRA_Plugin::init();
