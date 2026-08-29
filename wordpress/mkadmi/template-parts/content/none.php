<?php
/**
 * Shown when a loop has nothing in it.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section section--empty">
	<h2 class="section__title"><?php esc_html_e( 'Nothing found', 'mkadmi' ); ?></h2>

	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'No entry matches that search. Try another wording, or fewer words.', 'mkadmi' ); ?></p>
	<?php else : ?>
		<p><?php esc_html_e( 'There is nothing here yet.', 'mkadmi' ); ?></p>
	<?php endif; ?>

	<?php get_search_form(); ?>
</section>
