<?php get_header(); ?>
<div class="entry"><div class="container">
	<h1><?php echo is_home() ? esc_html( fh_opt( 'news_title' ) ) : esc_html( get_the_archive_title() ); ?></h1>
	<?php if ( is_search() ) { get_search_form(); } ?>
	<div class="page-grid cards-3">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); fh_post_card(); endwhile; else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'fihristech' ); ?></p>
	<?php endif; ?>
	</div>
	<?php the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); ?>
</div></div>
<?php get_footer();
