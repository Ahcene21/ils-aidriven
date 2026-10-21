<?php
/**
 * Solution / product card. Args: icon, title, text, bullets[], url (optional).
 */
$a = wp_parse_args( $args, array( 'icon' => '', 'title' => '', 'text' => '', 'bullets' => array(), 'url' => '', 'image' => '', 'date' => '' ) );
?>
<article class="card">
	<?php if ( $a['image'] ) : ?><a href="<?php echo esc_url( $a['url'] ); ?>" tabindex="-1" aria-hidden="true"><?php echo wp_kses_post( $a['image'] ); ?></a><?php endif; ?>
	<?php if ( $a['date'] ) : ?><time class="meta"><?php echo esc_html( $a['date'] ); ?></time><?php endif; ?>
	<?php if ( $a['icon'] ) : ?><div class="icon" aria-hidden="true"><?php echo esc_html( $a['icon'] ); ?></div><?php endif; ?>
	<h3><?php echo esc_html( $a['title'] ); ?></h3>
	<p><?php echo esc_html( $a['text'] ); ?></p>
	<?php if ( $a['bullets'] ) : ?>
		<ul><?php foreach ( $a['bullets'] as $b ) { echo '<li>' . esc_html( $b ) . '</li>'; } ?></ul>
	<?php endif; ?>
	<?php if ( $a['url'] ) : ?><a class="more" href="<?php echo esc_url( $a['url'] ); ?>"><?php esc_html_e( 'Learn more', 'fihristech' ); ?> →</a><?php endif; ?>
</article>
