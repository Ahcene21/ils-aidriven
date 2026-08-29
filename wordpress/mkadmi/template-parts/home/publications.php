<?php
/**
 * Homepage: books and refereed work, with their covers.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

if ( ! mkadmi_option( 'pubs_show' ) ) {
	return;
}

$mkadmi_pubs = new WP_Query(
	array(
		'post_type'      => 'mkadmi_publication',
		'posts_per_page' => max( 1, (int) mkadmi_option( 'pubs_count' ) ),
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);

if ( ! $mkadmi_pubs->have_posts() ) {
	return;
}

mkadmi_section_open( mkadmi_option( 'pubs_title' ), 'publications' );
?>
<ul class="publications">
	<?php
	while ( $mkadmi_pubs->have_posts() ) :
		$mkadmi_pubs->the_post();
		get_template_part( 'template-parts/content/publication' );
	endwhile;
	?>
</ul>

<?php
$mkadmi_more = mkadmi_option( 'pubs_more' );
$mkadmi_all  = get_post_type_archive_link( 'mkadmi_publication' );

if ( $mkadmi_more && $mkadmi_all ) :
	?>
	<p class="section__more">
		<a href="<?php echo esc_url( $mkadmi_all ); ?>">
			<span aria-hidden="true">&larr;</span> <?php echo esc_html( $mkadmi_more ); ?>
		</a>
	</p>
	<?php
endif;

wp_reset_postdata();
mkadmi_section_close();
