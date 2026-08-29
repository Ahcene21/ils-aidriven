<?php
/**
 * The closing markup: institution links and the copyright line.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;
?>
		</main><!-- .content -->

		<?php get_sidebar(); ?>
	</div><!-- .site-body -->

	<footer class="site-footer" role="contentinfo">
		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="wrap site-footer__widgets">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div>
		<?php endif; ?>

		<div class="wrap site-footer__inner">
			<?php
			$mkadmi_footer_links = mkadmi_parse_lines( mkadmi_option( 'footer_links' ), 2 );

			if ( $mkadmi_footer_links ) :
				?>
				<nav class="footer-links" aria-label="<?php esc_attr_e( 'Institutions', 'mkadmi' ); ?>">
					<ul class="footer-links__list">
						<?php
						foreach ( $mkadmi_footer_links as $mkadmi_link ) :
							list( $mkadmi_label, $mkadmi_url ) = $mkadmi_link;

							if ( '' === $mkadmi_label ) {
								continue;
							}
							?>
							<li>
								<?php if ( $mkadmi_url ) : ?>
									<a href="<?php echo esc_url( $mkadmi_url ); ?>"<?php echo mkadmi_external_attrs( $mkadmi_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>><?php echo esc_html( $mkadmi_label ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $mkadmi_label ); ?>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'footer',
						'container'            => 'nav',
						'container_class'      => 'footer-menu',
						'container_aria_label' => esc_attr__( 'Footer', 'mkadmi' ),
						'menu_class'           => 'footer-menu__list',
						'depth'                => 1,
						'fallback_cb'          => false,
					)
				);
				?>
			<?php endif; ?>

			<p class="site-footer__colophon">
				<?php
				$mkadmi_footer_text = mkadmi_option( 'footer_text' );

				if ( $mkadmi_footer_text ) {
					echo esc_html( $mkadmi_footer_text );
				} else {
					printf(
						/* translators: 1: year, 2: site name. */
						esc_html__( '© %1$s %2$s', 'mkadmi' ),
						esc_html( wp_date( 'Y' ) ),
						esc_html( get_bloginfo( 'name', 'display' ) )
					);
				}
				?>
			</p>
		</div>
	</footer>
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
