<?php
/**
 * A single publication: the record, then the abstract.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_citation = mkadmi_citation();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single entry--publication' ); ?>>
	<header class="entry__header">
		<?php mkadmi_term_pills( 'mkadmi_pub_type' ); ?>

		<h1 class="entry__title"><?php the_title(); ?></h1>

		<?php if ( $mkadmi_citation ) : ?>
			<p class="publication__citation"><?php echo wp_kses_post( $mkadmi_citation ); ?></p>
		<?php endif; ?>

		<?php mkadmi_publication_links(); ?>
	</header>

	<div class="publication__record">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="publication__cover publication__cover--single">
				<?php the_post_thumbnail( 'medium' ); ?>
			</figure>
		<?php endif; ?>

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
	</div>

	<footer class="entry__footer">
		<?php mkadmi_term_pills( 'mkadmi_area' ); ?>
	</footer>
</article>
