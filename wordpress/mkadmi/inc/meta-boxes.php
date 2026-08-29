<?php
/**
 * Editing UI for the publication, course and project fields.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add one meta box per post type, built from mkadmi_meta_fields().
 */
function mkadmi_add_meta_boxes() {
	$titles = array(
		'mkadmi_publication' => __( 'Publication details', 'mkadmi' ),
		'mkadmi_course'      => __( 'Course details', 'mkadmi' ),
		'mkadmi_project'     => __( 'Project details', 'mkadmi' ),
		'mkadmi_event'       => __( 'Conference details', 'mkadmi' ),
		'post'               => __( 'Homepage news list', 'mkadmi' ),
	);

	foreach ( $titles as $post_type => $title ) {
		add_meta_box(
			'mkadmi-details',
			$title,
			'mkadmi_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'mkadmi_add_meta_boxes' );

/**
 * Render the fields for the post being edited.
 *
 * @param WP_Post $post Post being edited.
 */
function mkadmi_render_meta_box( $post ) {
	$fields = mkadmi_meta_fields( $post->post_type );

	if ( ! $fields ) {
		return;
	}

	wp_nonce_field( 'mkadmi_save_meta', 'mkadmi_meta_nonce' );

	echo '<div class="mkadmi-fields">';

	foreach ( $fields as $key => $field ) {
		$id    = 'mkadmi-field-' . $key;
		$value = get_post_meta( $post->ID, '_mkadmi_' . $key, true );

		echo '<p class="mkadmi-field">';
		printf( '<label for="%1$s"><strong>%2$s</strong></label>', esc_attr( $id ), esc_html( $field['label'] ) );

		if ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea id="%1$s" name="%2$s" rows="4" class="widefat">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( 'mkadmi_meta[' . $key . ']' ),
				esc_textarea( $value )
			);
		} else {
			printf(
				'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="widefat" />',
				'url' === $field['type'] ? 'url' : 'text',
				esc_attr( $id ),
				esc_attr( 'mkadmi_meta[' . $key . ']' ),
				esc_attr( $value )
			);
		}

		if ( ! empty( $field['description'] ) ) {
			printf( '<span class="description">%s</span>', esc_html( $field['description'] ) );
		}

		echo '</p>';
	}

	echo '</div>';
}

/**
 * Persist the meta box values.
 *
 * @param int     $post_id Post being saved.
 * @param WP_Post $post    Post object.
 */
function mkadmi_save_meta( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['mkadmi_meta_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['mkadmi_meta_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'mkadmi_save_meta' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = mkadmi_meta_fields( $post->post_type );

	if ( ! $fields ) {
		return;
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each value is sanitised per field below.
	$submitted = isset( $_POST['mkadmi_meta'] ) ? wp_unslash( (array) $_POST['mkadmi_meta'] ) : array();

	foreach ( $fields as $key => $field ) {
		$raw = isset( $submitted[ $key ] ) ? $submitted[ $key ] : '';

		$sanitize = mkadmi_meta_sanitizer( $field['type'] );
		$value    = call_user_func( $sanitize, trim( (string) $raw ) );

		// The row is kept even when empty: publication archives sort on the
		// year, and a missing row would push the post out of that ordering.
		update_post_meta( $post_id, '_mkadmi_' . $key, $value );
	}
}
add_action( 'save_post', 'mkadmi_save_meta', 10, 2 );

/**
 * Show the year and type in the publications list table — the two columns an
 * editor actually scans when the list runs to hundreds of rows.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function mkadmi_publication_columns( $columns ) {
	$date = isset( $columns['date'] ) ? $columns['date'] : null;
	unset( $columns['date'] );

	$columns['mkadmi_year'] = __( 'Year', 'mkadmi' );

	if ( $date ) {
		$columns['date'] = $date;
	}

	return $columns;
}
add_filter( 'manage_mkadmi_publication_posts_columns', 'mkadmi_publication_columns' );

/**
 * Fill the year column.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function mkadmi_publication_column_content( $column, $post_id ) {
	if ( 'mkadmi_year' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_mkadmi_year', true ) );
	}
}
add_action( 'manage_mkadmi_publication_posts_custom_column', 'mkadmi_publication_column_content', 10, 2 );

/**
 * Let the year column sort the list table.
 *
 * @param array $columns Sortable columns.
 * @return array
 */
function mkadmi_publication_sortable_columns( $columns ) {
	$columns['mkadmi_year'] = 'mkadmi_year';

	return $columns;
}
add_filter( 'manage_edit-mkadmi_publication_sortable_columns', 'mkadmi_publication_sortable_columns' );

/**
 * Apply the year sort in the admin list table.
 *
 * @param WP_Query $query Admin query.
 */
function mkadmi_publication_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( 'mkadmi_year' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', '_mkadmi_year' );
		$query->set( 'orderby', 'meta_value_num' );
	}
}
add_action( 'pre_get_posts', 'mkadmi_publication_admin_order' );
