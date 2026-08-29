<?php
/**
 * Homepage: the teaching list.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

if ( ! mkadmi_option( 'courses_show' ) ) {
	return;
}

$mkadmi_courses = new WP_Query(
	array(
		'post_type'      => 'mkadmi_course',
		'posts_per_page' => max( 1, (int) mkadmi_option( 'courses_count' ) ),
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'no_found_rows'  => true,
	)
);

if ( ! $mkadmi_courses->have_posts() ) {
	return;
}

mkadmi_section_open( mkadmi_option( 'courses_title' ), 'teaching' );
?>
<ul class="cards">
	<?php
	while ( $mkadmi_courses->have_posts() ) :
		$mkadmi_courses->the_post();

		$mkadmi_institution = mkadmi_field( 'institution' );
		$mkadmi_period      = mkadmi_field( 'period' );
		?>
		<li class="card">
			<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

			<?php if ( $mkadmi_institution || $mkadmi_period ) : ?>
				<p class="card__meta">
					<?php
					echo esc_html( trim( implode( ' · ', array_filter( array( $mkadmi_institution, $mkadmi_period ) ) ) ) );
					?>
				</p>
			<?php endif; ?>

			<?php if ( has_excerpt() ) : ?>
				<p class="card__note"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</li>
	<?php endwhile; ?>
</ul>
<?php
wp_reset_postdata();
mkadmi_section_close();
