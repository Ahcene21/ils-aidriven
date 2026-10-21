<?php get_header(); while ( have_posts() ) : the_post(); ?>
<article class="entry"><div class="container narrow">
	<time class="meta"><?php echo esc_html( get_the_date() ); ?></time>
	<h1><?php the_title(); ?></h1>
	<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } ?>
	<div class="content"><?php the_content(); ?></div>
	<nav class="post-nav"><?php previous_post_link( '%link', '← %title' ); next_post_link( '%link', '%title →' ); ?></nav>
</div></article>
<?php endwhile; get_footer();
