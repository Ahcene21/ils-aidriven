<?php
/**
 * A single post or conference.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>
	<header class="entry__header">
		<h1 class="entry__title"><?php the_title(); ?></h1>

		<?php
		if ( 'post' === get_post_type() ) {
			mkadmi_entry_meta();
		} else {
			mkadmi_detail_list();
		}
		?>
	</header>

	<?php mkadmi_post_thumbnail( 'large' ); ?>

	<div class="entry__content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'mkadmi' ) . '">',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<?php
	$mkadmi_url = mkadmi_field( 'url' );

	if ( $mkadmi_url ) :
		?>
		<p class="entry__links">
			<a class="button" href="<?php echo esc_url( $mkadmi_url ); ?>"<?php echo mkadmi_external_attrs( $mkadmi_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
				<?php esc_html_e( 'Visit the site', 'mkadmi' ); ?>
			</a>
		</p>
	<?php endif; ?>

	<footer class="entry__footer">
		<?php
		mkadmi_term_pills( 'mkadmi_area' );

		if ( 'post' === get_post_type() ) {
			the_tags( '<p class="pills">', '', '</p>' );
		}
		?>
	</footer>
</article>
