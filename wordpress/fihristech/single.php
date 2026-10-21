<?php get_header(); while ( have_posts() ) : the_post(); ?>
<article class="entry"><div class="container narrow">
	<h1><?php the_title(); ?></h1>
	<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } ?>
	<div class="content"><?php the_content(); ?></div>
	<?php $b = fh_lines( get_post_meta( get_the_ID(), 'fh_bullets', true ) );
	if ( $b ) { echo '<ul class="dots">'; foreach ( $b as $i ) { echo '<li>' . esc_html( $i ) . '</li>'; } echo '</ul>'; } ?>
	<p><a class="btn btn-primary" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>"><?php esc_html_e( 'Discuss your project', 'fihristech' ); ?></a></p>
</div></article>
<?php endwhile; get_footer();
