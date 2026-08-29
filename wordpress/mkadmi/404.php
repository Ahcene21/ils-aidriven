<?php
/**
 * The page shown when nothing matches the address.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="section section--404">
	<h1 class="page-title"><?php esc_html_e( 'This page could not be found', 'mkadmi' ); ?></h1>

	<p><?php esc_html_e( 'The address may have changed, or the page may have been removed. Try a search, or start from the homepage.', 'mkadmi' ); ?></p>

	<?php get_search_form(); ?>

	<p>
		<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Go to the homepage', 'mkadmi' ); ?>
		</a>
	</p>
</section>
<?php
get_footer();
