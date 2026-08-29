<?php
/**
 * Homepage: the research projects.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

if ( ! mkadmi_option( 'projects_show' ) ) {
	return;
}

$mkadmi_projects = new WP_Query(
	array(
		'post_type'      => 'mkadmi_project',
		'posts_per_page' => max( 1, (int) mkadmi_option( 'projects_count' ) ),
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);

if ( ! $mkadmi_projects->have_posts() ) {
	return;
}

mkadmi_section_open( mkadmi_option( 'projects_title' ), 'research' );
?>
<ul class="cards">
	<?php
	while ( $mkadmi_projects->have_posts() ) :
		$mkadmi_projects->the_post();

		$mkadmi_role   = mkadmi_field( 'role' );
		$mkadmi_period = mkadmi_field( 'period' );
		$mkadmi_status = mkadmi_field( 'status' );
		$mkadmi_meta   = array_filter( array( $mkadmi_role, $mkadmi_period, $mkadmi_status ) );
		?>
		<li class="card">
			<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

			<?php if ( $mkadmi_meta ) : ?>
				<p class="card__meta"><?php echo esc_html( implode( ' · ', $mkadmi_meta ) ); ?></p>
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
