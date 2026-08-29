<?php
/**
 * A page.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single entry--page' ); ?>>
	<header class="entry__header">
		<h1 class="entry__title"><?php the_title(); ?></h1>
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

		edit_post_link(
			__( 'Edit this page', 'mkadmi' ),
			'<p class="entry__edit">',
			'</p>'
		);
		?>
	</div>
</article>
