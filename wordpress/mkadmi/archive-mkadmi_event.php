<?php
/**
 * The conferences archive.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();

mkadmi_breadcrumbs();

echo '<header class="archive-header">';
the_archive_title( '<h1 class="page-title">', '</h1>' );
the_archive_description( '<div class="archive-description">', '</div>' );
echo '</header>';

if ( have_posts() ) {
	echo '<ul class="event-cards">';

	while ( have_posts() ) {
		the_post();

		$mkadmi_badge    = mkadmi_field( 'badge' );
		$mkadmi_subtitle = mkadmi_field( 'subtitle' );
		$mkadmi_place    = mkadmi_field( 'place' );
		?>
		<li class="event-card">
			<a class="event-card__link" href="<?php the_permalink(); ?>">
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
		<?php
	}

	echo '</ul>';

	mkadmi_pagination();
} else {
	get_template_part( 'template-parts/content/none' );
}

get_footer();
