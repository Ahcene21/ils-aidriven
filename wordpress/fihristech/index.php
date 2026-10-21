<?php get_header(); ?>
<div class="entry"><div class="container">
	<h1><?php echo is_home() && ! is_front_page() ? esc_html( single_post_title( '', false ) ) : esc_html( get_the_archive_title() ); ?></h1>
	<div class="page-grid">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
		get_template_part( 'template-parts/card', null, array( 'title' => get_the_title(), 'text' => get_the_excerpt(), 'url' => get_permalink() ) );
	endwhile; else : ?><p><?php esc_html_e( 'Nothing found.', 'fihristech' ); ?></p><?php endif; ?>
	</div>
	<?php the_posts_pagination(); ?>
</div></div>
<?php get_footer();
