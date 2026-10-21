<?php get_header(); ?>
<div class="entry"><div class="container">
	<h1><?php the_archive_title(); ?></h1>
	<div class="page-grid">
	<?php while ( have_posts() ) : the_post();
		get_template_part( 'template-parts/card', null, array(
			'icon' => get_post_meta( get_the_ID(), 'fh_icon', true ), 'title' => get_the_title(), 'text' => get_the_excerpt(),
			'bullets' => fh_lines( get_post_meta( get_the_ID(), 'fh_bullets', true ) ), 'url' => get_permalink(),
		) );
	endwhile; ?>
	</div>
	<?php the_posts_pagination(); ?>
</div></div>
<?php get_footer();
