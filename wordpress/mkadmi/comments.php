<?php
/**
 * Comments.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments__title">
			<?php
			$mkadmi_count = (int) get_comments_number();

			printf(
				esc_html(
					/* translators: %s: number of comments. */
					_n( '%s comment', '%s comments', $mkadmi_count, 'mkadmi' )
				),
				esc_html( number_format_i18n( $mkadmi_count ) )
			);
			?>
		</h2>

		<ol class="comments__list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => esc_html__( 'Previous', 'mkadmi' ),
				'next_text' => esc_html__( 'Next', 'mkadmi' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="comments__closed"><?php esc_html_e( 'Comments are closed.', 'mkadmi' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_submit' => 'button',
			'title_reply'  => esc_html__( 'Leave a comment', 'mkadmi' ),
		)
	);
	?>
</section>
