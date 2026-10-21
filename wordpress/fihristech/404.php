<?php get_header(); ?>
<div class="entry"><div class="container narrow center">
	<h1>404</h1>
	<p class="sub"><?php esc_html_e( 'This page could not be found.', 'fihristech' ); ?></p>
	<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'fihristech' ); ?></a>
</div></div>
<?php get_footer();
