<?php
/**
 * A single course: the details, the description, and the material.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_syllabus = mkadmi_field( 'syllabus' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>
	<header class="entry__header">
		<?php mkadmi_term_pills( 'mkadmi_level' ); ?>

		<h1 class="entry__title"><?php the_title(); ?></h1>

		<?php mkadmi_detail_list(); ?>
	</header>

	<div class="entry__content">
		<?php the_content(); ?>
	</div>

	<?php if ( $mkadmi_syllabus ) : ?>
		<p class="entry__links">
			<a class="button" href="<?php echo esc_url( $mkadmi_syllabus ); ?>"<?php echo mkadmi_external_attrs( $mkadmi_syllabus ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
				<?php esc_html_e( 'Course material', 'mkadmi' ); ?>
			</a>
		</p>
	<?php endif; ?>

	<footer class="entry__footer">
		<?php mkadmi_term_pills( 'mkadmi_area' ); ?>
	</footer>
</article>
