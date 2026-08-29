<?php
/**
 * The default entry in an archive or a search result.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_type = get_post_type_object( get_post_type() );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<?php mkadmi_post_thumbnail(); ?>

	<div class="entry__body">
		<h2 class="entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<?php if ( is_search() && $mkadmi_type ) : ?>
			<p class="entry__kind"><?php echo esc_html( $mkadmi_type->labels->singular_name ); ?></p>
		<?php endif; ?>

		<?php if ( 'post' === get_post_type() ) : ?>
			<?php mkadmi_entry_meta(); ?>
		<?php endif; ?>

		<div class="entry__summary"><?php the_excerpt(); ?></div>
	</div>
</article>
