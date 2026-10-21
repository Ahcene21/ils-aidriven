<?php get_header(); while ( have_posts() ) : the_post(); $bullets = fh_lines( get_post_meta( get_the_ID(), 'fh_bullets', true ) ); $icon = get_post_meta( get_the_ID(), 'fh_icon', true ); ?>
<section class="hero hero-small"><div class="container narrow">
	<a class="link" href="<?php echo esc_url( get_post_type_archive_link( get_post_type() ) ); ?>">← <?php echo esc_html( get_post_type_object( get_post_type() )->labels->name ); ?></a>
	<?php if ( $icon ) { echo '<div class="icon" aria-hidden="true">' . esc_html( $icon ) . '</div>'; } ?>
	<h1><?php the_title(); ?></h1>
	<?php if ( has_excerpt() ) { echo '<p class="lead">' . esc_html( get_the_excerpt() ) . '</p>'; } ?>
</div></section>
<article class="entry"><div class="container narrow">
	<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } ?>
	<div class="content"><?php the_content(); ?></div>
	<?php if ( $bullets ) : ?>
		<h2><?php esc_html_e( 'What is included', 'fihristech' ); ?></h2>
		<ul class="dots"><?php foreach ( $bullets as $b ) { echo '<li>' . esc_html( $b ) . '</li>'; } ?></ul>
	<?php endif; ?>
	<p><a class="btn btn-primary" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>"><?php esc_html_e( 'Discuss your project', 'fihristech' ); ?></a></p>
	<?php
	$more = new WP_Query( array( 'post_type' => get_post_type(), 'post__not_in' => array( get_the_ID() ), 'posts_per_page' => 3, 'orderby' => 'menu_order date', 'order' => 'ASC', 'no_found_rows' => true ) );
	if ( $more->have_posts() ) : ?>
		<h2><?php esc_html_e( 'Related', 'fihristech' ); ?></h2>
		<div class="cards">
		<?php while ( $more->have_posts() ) { $more->the_post(); get_template_part( 'template-parts/card', null, array( 'icon' => get_post_meta( get_the_ID(), 'fh_icon', true ), 'title' => get_the_title(), 'text' => get_the_excerpt(), 'url' => get_permalink() ) ); } wp_reset_postdata(); ?>
		</div>
	<?php endif; ?>
</div></article>
<?php endwhile; get_footer();
