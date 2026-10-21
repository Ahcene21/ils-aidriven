<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {
	$common = array( 'public' => true, 'show_in_rest' => true, 'has_archive' => true, 'menu_position' => 21, 'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ) );

	register_post_type( 'fh_solution', array_merge( $common, array(
		'labels'     => array( 'name' => __( 'Solutions', 'fihristech' ), 'singular_name' => __( 'Solution', 'fihristech' ), 'add_new_item' => __( 'Add new solution', 'fihristech' ) ),
		'menu_icon'  => 'dashicons-book-alt',
		'rewrite'    => array( 'slug' => 'solutions' ),
	) ) );

	register_post_type( 'fh_product', array_merge( $common, array(
		'labels'     => array( 'name' => __( 'Products', 'fihristech' ), 'singular_name' => __( 'Product', 'fihristech' ), 'add_new_item' => __( 'Add new product', 'fihristech' ) ),
		'menu_icon'  => 'dashicons-screenoptions',
		'rewrite'    => array( 'slug' => 'products' ),
	) ) );

	// Contact messages: visible only in the dashboard.
	register_post_type( 'fh_message', array(
		'labels'       => array( 'name' => __( 'Messages', 'fihristech' ), 'singular_name' => __( 'Message', 'fihristech' ) ),
		'public'       => false,
		'show_ui'      => true,
		'menu_icon'    => 'dashicons-email-alt',
		'menu_position'=> 22,
		'supports'     => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities' => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap' => true,
	) );

	foreach ( array( 'fh_solution' => 'icon', 'fh_product' => 'icon' ) as $type => $unused ) {
		register_post_meta( $type, 'fh_bullets', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_textarea_field', 'auth_callback' => function () { return current_user_can( 'edit_posts' ); } ) );
		register_post_meta( $type, 'fh_icon', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_text_field', 'auth_callback' => function () { return current_user_can( 'edit_posts' ); } ) );
	}
} );

add_action( 'add_meta_boxes', function () {
	foreach ( array( 'fh_solution', 'fh_product' ) as $type ) {
		add_meta_box( 'fh_details', __( 'Card details', 'fihristech' ), 'fh_details_box', $type, 'normal', 'high' );
	}
} );

function fh_details_box( $post ) {
	wp_nonce_field( 'fh_details', 'fh_details_nonce' );
	printf( '<p><label><strong>%s</strong><br><input type="text" class="widefat" name="fh_icon" value="%s" placeholder="📖"></label></p>', esc_html__( 'Icon (emoji or short text)', 'fihristech' ), esc_attr( get_post_meta( $post->ID, 'fh_icon', true ) ) );
	printf( '<p><label><strong>%s</strong><br><textarea class="widefat" rows="5" name="fh_bullets">%s</textarea></label></p>', esc_html__( 'Bullet points (one per line)', 'fihristech' ), esc_textarea( get_post_meta( $post->ID, 'fh_bullets', true ) ) );
	echo '<p class="description">' . esc_html__( 'The excerpt is used as the card description.', 'fihristech' ) . '</p>';
}

add_action( 'save_post', function ( $post_id ) {
	if ( ! isset( $_POST['fh_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fh_details_nonce'] ) ), 'fh_details' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['fh_icon'] ) ) {
		update_post_meta( $post_id, 'fh_icon', sanitize_text_field( wp_unslash( $_POST['fh_icon'] ) ) );
	}
	if ( isset( $_POST['fh_bullets'] ) ) {
		update_post_meta( $post_id, 'fh_bullets', sanitize_textarea_field( wp_unslash( $_POST['fh_bullets'] ) ) );
	}
} );

add_action( 'after_switch_theme', function () {
	flush_rewrite_rules();
} );
