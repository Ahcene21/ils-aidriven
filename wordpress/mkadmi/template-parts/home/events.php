<?php
/**
 * Homepage: the conferences and symposia the site's owner organised.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

if ( ! mkadmi_option( 'events_show' ) ) {
	return;
}

$mkadmi_events = new WP_Query(
	array(
		'post_type'      => 'mkadmi_event',
		'posts_per_page' => max( 1, (int) mkadmi_option( 'events_count' ) ),
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);

if ( ! $mkadmi_events->have_posts() ) {
	return;
}

mkadmi_section_open( mkadmi_option( 'events_title' ), 'events' );
?>
<ul class="event-cards">
	<?php
	while ( $mkadmi_events->have_posts() ) :
		$mkadmi_events->the_post();

		$mkadmi_badge    = mkadmi_field( 'badge' );
		$mkadmi_subtitle = mkadmi_field( 'subtitle' );
		$mkadmi_place    = mkadmi_field( 'place' );
		$mkadmi_url      = mkadmi_field( 'url' );
		$mkadmi_href     = $mkadmi_url ? $mkadmi_url : get_permalink();
		?>
		<li class="event-card">
			<a class="event-card__link" href="<?php echo esc_url( $mkadmi_href ); ?>"<?php echo mkadmi_external_attrs( $mkadmi_href ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
				<?php if ( $mkadmi_badge ) : ?>
					<span class="event-card__badge"><?php echo esc_html( $mkadmi_badge ); ?></span>
				<?php endif; ?>

				<span class="event-card__body">
					<span class="event-card__title"><?php the_title(); ?></span>

					<?php if ( $mkadmi_subtitle ) : ?>
						<span class="event-card__subtitle"><?php echo esc_html( $mkadmi_subtitle ); ?></span>
					<?php endif; ?>

					<?php if ( $mkadmi_place ) : ?>
						<span class="event-card__place"><?php echo esc_html( $mkadmi_place ); ?></span>
					<?php endif; ?>
				</span>
			</a>
		</li>
	<?php endwhile; ?>
</ul>
<?php
wp_reset_postdata();
mkadmi_section_close();
