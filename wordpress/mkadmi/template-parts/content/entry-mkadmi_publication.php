<?php
/**
 * A publication in an archive or a search result.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_citation = mkadmi_citation();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--publication' ); ?>>
	<?php mkadmi_post_thumbnail( 'medium' ); ?>

	<div class="entry__body">
		<h2 class="entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<?php if ( $mkadmi_citation ) : ?>
			<p class="publication__citation"><?php echo wp_kses_post( $mkadmi_citation ); ?></p>
		<?php endif; ?>

		<div class="entry__summary"><?php the_excerpt(); ?></div>

		<?php mkadmi_publication_links(); ?>
	</div>
</article>
