<?php
/**
 * Homepage: selected academic news, taken from the blog posts.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

if ( ! mkadmi_option( 'news_show' ) ) {
	return;
}

$mkadmi_news = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => max( 1, (int) mkadmi_option( 'news_count' ) ),
		'ignore_sticky_posts' => false,
		'no_found_rows'       => true,
	)
);

if ( ! $mkadmi_news->have_posts() ) {
	return;
}

mkadmi_section_open( mkadmi_option( 'news_title' ), 'news' );
?>
<ul class="news">
	<?php
	while ( $mkadmi_news->have_posts() ) :
		$mkadmi_news->the_post();

		$mkadmi_icon = mkadmi_field( 'icon' );
		?>
		<li class="news__item">
			<?php if ( $mkadmi_icon ) : ?>
				<span class="news__icon" aria-hidden="true"><?php echo esc_html( $mkadmi_icon ); ?></span>
			<?php endif; ?>

			<a class="news__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

			<?php if ( has_excerpt() ) : ?>
				<span class="news__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></span>
			<?php endif; ?>
		</li>
	<?php endwhile; ?>
</ul>
<?php
wp_reset_postdata();
mkadmi_section_close();
