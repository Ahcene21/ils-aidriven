=== QR Attendance ===
Tags: attendance, qr code, students, school, university
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Student attendance and absences by QR code: the teacher projects a rotating code, students scan it with their phone.

== Description ==

* Courses with a teacher, enrolled students and sessions.
* Live session screen with a full-screen QR code that changes every few seconds, so a photo of it cannot be shared.
* Students scan with their phone's camera app; if they are not logged in they log in and are checked in afterwards.
* Students who did not check in are marked absent when the session closes (manually or automatically).
* Late marking, excused absences, manual corrections, one-phone-one-student rule.
* Teachers can also scan students' personal QR cards.
* Course report grid with totals, attendance rate, absence warnings and CSV export.
* Shortcodes: `[qra_scanner]`, `[qra_my_attendance]`, `[qra_my_card]`.
* Fully translated into Arabic, RTL ready.

== Installation ==

1. Upload the `qr-attendance` folder to `/wp-content/plugins/` or upload the zip from Plugins → Add New.
2. Activate the plugin.
3. Create a page with `[qra_my_attendance][qra_my_card]` and pick it under Attendance → Settings.
4. Give teachers the "Teacher" role, then create courses under Attendance → Courses.

The in-browser camera scanner needs HTTPS; scanning with the phone's own camera app works regardless.

== Changelog ==

= 1.0.0 =
* First release.
