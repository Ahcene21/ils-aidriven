<?php get_header(); while ( have_posts() ) : the_post(); ?>
<article class="entry"><div class="container narrow">
	<h1><?php the_title(); ?></h1>
	<div class="content"><?php the_content(); ?></div>
</div></article>
<?php endwhile; get_footer();
