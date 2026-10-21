</main>
<footer class="footer">
	<div class="container">
		<b><?php bloginfo( 'name' ); ?></b> · <?php bloginfo( 'description' ); ?>
		<?php if ( has_nav_menu( 'footer' ) ) { wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ) ); } ?>
		<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'All rights reserved.', 'fihristech' ); ?></span>
	</div>
</footer>
<a class="to-top" href="#top" aria-label="<?php esc_attr_e( 'Back to top', 'fihristech' ); ?>">⌃</a>
<?php wp_footer(); ?>
</body>
</html>
