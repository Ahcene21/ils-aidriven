<?php
/**
 * A blog post in a list.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<?php mkadmi_post_thumbnail(); ?>

	<div class="entry__body">
		<h2 class="entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<?php mkadmi_entry_meta(); ?>

		<div class="entry__summary"><?php the_excerpt(); ?></div>
	</div>
</article>
