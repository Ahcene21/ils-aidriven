<?php
/**
 * The masthead and the profile band.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'mkadmi' ); ?></a>

<div class="site">

	<header class="masthead" role="banner">
		<div class="masthead__inner wrap">
			<div class="masthead__start">
				<?php mkadmi_language_switcher( 'chips' ); ?>

				<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">
					<span class="menu-toggle__bars" aria-hidden="true"></span>
					<span class="menu-toggle__label"><?php esc_html_e( 'Menu', 'mkadmi' ); ?></span>
				</button>

				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location'       => 'primary',
							'container'            => 'nav',
							'container_class'      => 'primary-nav',
							'container_id'         => 'primary-menu',
							'container_aria_label' => esc_attr__( 'Primary', 'mkadmi' ),
							'menu_class'           => 'primary-nav__list',
							'depth'                => 2,
							'fallback_cb'          => false,
						)
					);
				}
				?>
			</div>

			<?php mkadmi_brand(); ?>
		</div>
	</header>

	<?php get_template_part( 'template-parts/profile-band' ); ?>

	<?php mkadmi_stats(); ?>

	<div class="site-body wrap">
		<main id="content" class="content" role="main">
