<?php
/**
 * A course in an archive.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_meta = array_filter(
	array(
		mkadmi_field( 'code' ),
		mkadmi_field( 'institution' ),
		mkadmi_field( 'period' ),
	)
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
	<h2 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

	<?php if ( $mkadmi_meta ) : ?>
		<p class="card__meta"><?php echo esc_html( implode( ' · ', $mkadmi_meta ) ); ?></p>
	<?php endif; ?>

	<div class="card__note"><?php the_excerpt(); ?></div>

	<?php mkadmi_term_pills( 'mkadmi_level' ); ?>
</article>
