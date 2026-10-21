<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'fihristech' ); ?></a>
<header class="site-header" id="top">
	<?php echo fh_logo(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
	<button class="menu-btn" aria-expanded="false" aria-controls="nav"><?php esc_html_e( 'MENU', 'fihristech' ); ?> <span class="bars" aria-hidden="true"></span></button>
	<nav id="nav" class="nav" aria-label="<?php esc_attr_e( 'Primary', 'fihristech' ); ?>">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'depth' => 1 ) );
		} else {
			printf( '<a href="%s">%s</a><a href="%s">%s</a><a href="%s">%s</a><a href="%s">%s</a><a href="%s">%s</a>',
				esc_url( fh_anchor( 'solutions' ) ), esc_html__( 'Solutions', 'fihristech' ),
				esc_url( fh_anchor( 'products' ) ), esc_html__( 'Products', 'fihristech' ),
				esc_url( fh_anchor( 'news' ) ), esc_html__( 'News', 'fihristech' ),
				esc_url( fh_anchor( 'about' ) ), esc_html__( 'About', 'fihristech' ),
				esc_url( fh_anchor( 'contact' ) ), esc_html__( 'Contact', 'fihristech' )
			);
		}
		?>
	</nav>
</header>
<main id="main">
