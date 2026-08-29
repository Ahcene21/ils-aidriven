<?php
/**
 * One publication: cover, title, citation, note and links.
 *
 * Used by the homepage list and by the publications archive, so both stay in
 * step.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_citation = mkadmi_citation();
$mkadmi_classes  = has_post_thumbnail() ? 'publication' : 'publication publication--no-cover';
?>
<li id="publication-<?php the_ID(); ?>" <?php post_class( $mkadmi_classes ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="publication__cover" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
		</a>
	<?php endif; ?>

	<div class="publication__body">
		<h3 class="publication__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php if ( $mkadmi_citation ) : ?>
			<p class="publication__citation"><?php echo wp_kses_post( $mkadmi_citation ); ?></p>
		<?php endif; ?>

		<?php if ( has_excerpt() ) : ?>
			<p class="publication__note"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>

		<?php mkadmi_publication_links(); ?>
	</div>
</li>
