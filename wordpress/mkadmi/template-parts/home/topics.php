<?php
/**
 * Homepage: the research topics, as a row of pills.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

if ( ! mkadmi_option( 'topics_show' ) ) {
	return;
}

$mkadmi_topics = mkadmi_parse_lines( mkadmi_option( 'topics_list' ), 3 );

if ( ! $mkadmi_topics ) {
	return;
}

mkadmi_section_open( mkadmi_option( 'topics_title' ), 'topics' );
?>
<ul class="topics">
	<?php
	foreach ( $mkadmi_topics as $mkadmi_topic ) :
		list( $mkadmi_icon, $mkadmi_label, $mkadmi_url ) = $mkadmi_topic;

		if ( '' === $mkadmi_label ) {
			continue;
		}

		$mkadmi_icon_markup = $mkadmi_icon
			? '<span class="topic__icon" aria-hidden="true">' . esc_html( $mkadmi_icon ) . '</span>'
			: '';
		?>
		<li class="topic">
			<?php if ( $mkadmi_url ) : ?>
				<a class="topic__link" href="<?php echo esc_url( $mkadmi_url ); ?>">
					<?php echo $mkadmi_icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
					<?php echo esc_html( $mkadmi_label ); ?>
				</a>
			<?php else : ?>
				<span class="topic__link">
					<?php echo $mkadmi_icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
					<?php echo esc_html( $mkadmi_label ); ?>
				</span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
<?php
mkadmi_section_close();
