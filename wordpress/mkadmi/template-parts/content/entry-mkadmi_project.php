<?php
/**
 * A research project in an archive.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_meta = array_filter(
	array(
		mkadmi_field( 'role' ),
		mkadmi_field( 'period' ),
		mkadmi_field( 'status' ),
	)
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
	<h2 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

	<?php if ( $mkadmi_meta ) : ?>
		<p class="card__meta"><?php echo esc_html( implode( ' · ', $mkadmi_meta ) ); ?></p>
	<?php endif; ?>

	<div class="card__note"><?php the_excerpt(); ?></div>

	<?php mkadmi_term_pills( 'mkadmi_area' ); ?>
</article>
