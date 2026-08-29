<?php
/**
 * The green band under the masthead: portrait, titles, contact and clocks.
 *
 * It is skipped entirely when the site has nothing to put in it, so a fresh
 * install does not show an empty green rectangle.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_has_profile = mkadmi_option( 'profile_role' )
	|| mkadmi_option( 'profile_affiliations' )
	|| mkadmi_option( 'profile_emails' )
	|| mkadmi_option( 'profile_photo' )
	|| mkadmi_option( 'profile_website' );

if ( ! $mkadmi_has_profile ) {
	return;
}
?>
<div class="profile-band">
	<div class="wrap profile-band__inner">
		<div class="profile">
			<?php mkadmi_profile_photo(); ?>

			<div class="profile__text">
				<?php if ( mkadmi_option( 'profile_role' ) ) : ?>
					<p class="profile__role"><?php echo esc_html( mkadmi_option( 'profile_role' ) ); ?></p>
				<?php endif; ?>

				<?php mkadmi_profile_contacts(); ?>
			</div>
		</div>

		<?php mkadmi_clocks(); ?>
	</div>
</div>
