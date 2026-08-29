<?php
/**
 * The search form.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

$mkadmi_search_id = wp_unique_id( 'search-field-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $mkadmi_search_id ); ?>">
		<?php esc_html_e( 'Search this site', 'mkadmi' ); ?>
	</label>

	<input
		type="search"
		id="<?php echo esc_attr( $mkadmi_search_id ); ?>"
		class="search-form__field"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Search…', 'mkadmi' ); ?>"
	/>

	<button type="submit" class="button search-form__submit"><?php esc_html_e( 'Search', 'mkadmi' ); ?></button>
</form>
