<?php
/**
 * The content model: publications, courses and research projects.
 *
 * The site this theme is modelled on keeps its scholarly record in three
 * lists — what was published, what is taught, and what is being researched — so
 * each one is a post type with real fields rather than a page of hand-written
 * HTML that has to be re-edited for every new entry.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the theme's post types.
 */
function mkadmi_register_post_types() {
	register_post_type(
		'mkadmi_publication',
		array(
			'labels'        => array(
				'name'               => __( 'Publications', 'mkadmi' ),
				'singular_name'      => __( 'Publication', 'mkadmi' ),
				'add_new_item'       => __( 'Add publication', 'mkadmi' ),
				'edit_item'          => __( 'Edit publication', 'mkadmi' ),
				'new_item'           => __( 'New publication', 'mkadmi' ),
				'view_item'          => __( 'View publication', 'mkadmi' ),
				'search_items'       => __( 'Search publications', 'mkadmi' ),
				'not_found'          => __( 'No publications yet.', 'mkadmi' ),
				'not_found_in_trash' => __( 'No publications in the trash.', 'mkadmi' ),
				'all_items'          => __( 'All publications', 'mkadmi' ),
				'menu_name'          => __( 'Publications', 'mkadmi' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-book-alt',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' ),
			'rewrite'       => array( 'slug' => 'publications', 'with_front' => false ),
			'show_in_rest'  => true,
			'taxonomies'    => array( 'mkadmi_pub_type', 'mkadmi_area' ),
		)
	);

	register_post_type(
		'mkadmi_course',
		array(
			'labels'        => array(
				'name'               => __( 'Courses', 'mkadmi' ),
				'singular_name'      => __( 'Course', 'mkadmi' ),
				'add_new_item'       => __( 'Add course', 'mkadmi' ),
				'edit_item'          => __( 'Edit course', 'mkadmi' ),
				'new_item'           => __( 'New course', 'mkadmi' ),
				'view_item'          => __( 'View course', 'mkadmi' ),
				'search_items'       => __( 'Search courses', 'mkadmi' ),
				'not_found'          => __( 'No courses yet.', 'mkadmi' ),
				'not_found_in_trash' => __( 'No courses in the trash.', 'mkadmi' ),
				'all_items'          => __( 'All courses', 'mkadmi' ),
				'menu_name'          => __( 'Teaching', 'mkadmi' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-welcome-learn-more',
			'menu_position' => 22,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'rewrite'       => array( 'slug' => 'teaching', 'with_front' => false ),
			'show_in_rest'  => true,
			'taxonomies'    => array( 'mkadmi_level', 'mkadmi_area' ),
		)
	);

	register_post_type(
		'mkadmi_project',
		array(
			'labels'        => array(
				'name'               => __( 'Research projects', 'mkadmi' ),
				'singular_name'      => __( 'Research project', 'mkadmi' ),
				'add_new_item'       => __( 'Add project', 'mkadmi' ),
				'edit_item'          => __( 'Edit project', 'mkadmi' ),
				'new_item'           => __( 'New project', 'mkadmi' ),
				'view_item'          => __( 'View project', 'mkadmi' ),
				'search_items'       => __( 'Search projects', 'mkadmi' ),
				'not_found'          => __( 'No projects yet.', 'mkadmi' ),
				'not_found_in_trash' => __( 'No projects in the trash.', 'mkadmi' ),
				'all_items'          => __( 'All projects', 'mkadmi' ),
				'menu_name'          => __( 'Research', 'mkadmi' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-analytics',
			'menu_position' => 23,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'rewrite'       => array( 'slug' => 'research', 'with_front' => false ),
			'show_in_rest'  => true,
			'taxonomies'    => array( 'mkadmi_area' ),
		)
	);

	register_post_type(
		'mkadmi_event',
		array(
			'labels'        => array(
				'name'               => __( 'Conferences', 'mkadmi' ),
				'singular_name'      => __( 'Conference', 'mkadmi' ),
				'add_new_item'       => __( 'Add conference', 'mkadmi' ),
				'edit_item'          => __( 'Edit conference', 'mkadmi' ),
				'new_item'           => __( 'New conference', 'mkadmi' ),
				'view_item'          => __( 'View conference', 'mkadmi' ),
				'search_items'       => __( 'Search conferences', 'mkadmi' ),
				'not_found'          => __( 'No conferences yet.', 'mkadmi' ),
				'not_found_in_trash' => __( 'No conferences in the trash.', 'mkadmi' ),
				'all_items'          => __( 'All conferences', 'mkadmi' ),
				'menu_name'          => __( 'Conferences', 'mkadmi' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-megaphone',
			'menu_position' => 24,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'rewrite'       => array( 'slug' => 'conferences', 'with_front' => false ),
			'show_in_rest'  => true,
			'taxonomies'    => array( 'mkadmi_area' ),
		)
	);
}
add_action( 'init', 'mkadmi_register_post_types' );

/**
 * Register the taxonomies that group the three lists.
 */
function mkadmi_register_taxonomies() {
	register_taxonomy(
		'mkadmi_pub_type',
		array( 'mkadmi_publication' ),
		array(
			'labels'            => array(
				'name'          => __( 'Publication types', 'mkadmi' ),
				'singular_name' => __( 'Publication type', 'mkadmi' ),
				'add_new_item'  => __( 'Add publication type', 'mkadmi' ),
				'all_items'     => __( 'All types', 'mkadmi' ),
				'menu_name'     => __( 'Types', 'mkadmi' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'publication-type', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'mkadmi_level',
		array( 'mkadmi_course' ),
		array(
			'labels'            => array(
				'name'          => __( 'Levels', 'mkadmi' ),
				'singular_name' => __( 'Level', 'mkadmi' ),
				'add_new_item'  => __( 'Add level', 'mkadmi' ),
				'all_items'     => __( 'All levels', 'mkadmi' ),
				'menu_name'     => __( 'Levels', 'mkadmi' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'level', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'mkadmi_area',
		array( 'mkadmi_publication', 'mkadmi_course', 'mkadmi_project', 'post' ),
		array(
			'labels'            => array(
				'name'          => __( 'Research areas', 'mkadmi' ),
				'singular_name' => __( 'Research area', 'mkadmi' ),
				'add_new_item'  => __( 'Add research area', 'mkadmi' ),
				'all_items'     => __( 'All research areas', 'mkadmi' ),
				'menu_name'     => __( 'Research areas', 'mkadmi' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'area', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'mkadmi_register_taxonomies', 5 );

/**
 * The custom fields each post type carries, with their sanitisers.
 *
 * Used to register post meta, to render the meta boxes, and to read values back
 * out in the templates, so the three never drift apart.
 *
 * @param string $post_type Post type name.
 * @return array<string, array<string, mixed>>
 */
function mkadmi_meta_fields( $post_type ) {
	$fields = array(
		'mkadmi_publication' => array(
			'authors'   => array( 'label' => __( 'Authors', 'mkadmi' ), 'type' => 'text', 'description' => __( 'As they should be cited, e.g. Mkadmi, A., Saleh, I.', 'mkadmi' ) ),
			'year'      => array( 'label' => __( 'Year', 'mkadmi' ), 'type' => 'text' ),
			'venue'     => array( 'label' => __( 'Journal, book or conference', 'mkadmi' ), 'type' => 'text' ),
			'publisher' => array( 'label' => __( 'Publisher', 'mkadmi' ), 'type' => 'text' ),
			'reference' => array( 'label' => __( 'Volume, issue, pages', 'mkadmi' ), 'type' => 'text' ),
			'doi'       => array( 'label' => __( 'DOI', 'mkadmi' ), 'type' => 'text', 'description' => __( 'Bare DOI, e.g. 10.1000/182.', 'mkadmi' ) ),
			'isbn'      => array( 'label' => __( 'ISBN / ISSN', 'mkadmi' ), 'type' => 'text' ),
			'url'       => array( 'label' => __( 'Link', 'mkadmi' ), 'type' => 'url' ),
			'pdf'       => array( 'label' => __( 'PDF', 'mkadmi' ), 'type' => 'url', 'description' => __( 'Paste the media file URL of the full text.', 'mkadmi' ) ),
			'links'     => array(
				'label'       => __( 'Further links', 'mkadmi' ),
				'type'        => 'textarea',
				'description' => __( 'One per line, as “Label | URL” — for the publisher page, HAL, Zenodo, a second chapter PDF, and so on.', 'mkadmi' ),
			),
		),
		'mkadmi_course'      => array(
			'code'        => array( 'label' => __( 'Course code', 'mkadmi' ), 'type' => 'text' ),
			'institution' => array( 'label' => __( 'Institution', 'mkadmi' ), 'type' => 'text' ),
			'period'      => array( 'label' => __( 'Academic year', 'mkadmi' ), 'type' => 'text', 'description' => __( 'e.g. 2024–2025, or “since 2004”.', 'mkadmi' ) ),
			'hours'       => array( 'label' => __( 'Hours', 'mkadmi' ), 'type' => 'text' ),
			'syllabus'    => array( 'label' => __( 'Syllabus or course material', 'mkadmi' ), 'type' => 'url' ),
		),
		'mkadmi_project'     => array(
			'role'     => array( 'label' => __( 'Role', 'mkadmi' ), 'type' => 'text', 'description' => __( 'e.g. Principal investigator, Member.', 'mkadmi' ) ),
			'funder'   => array( 'label' => __( 'Funder or programme', 'mkadmi' ), 'type' => 'text' ),
			'partners' => array( 'label' => __( 'Partners', 'mkadmi' ), 'type' => 'text' ),
			'period'   => array( 'label' => __( 'Period', 'mkadmi' ), 'type' => 'text' ),
			'status'   => array( 'label' => __( 'Status', 'mkadmi' ), 'type' => 'text', 'description' => __( 'e.g. Ongoing, Completed.', 'mkadmi' ) ),
			'url'      => array( 'label' => __( 'Project link', 'mkadmi' ), 'type' => 'url' ),
		),
		'mkadmi_event'       => array(
			'badge'    => array( 'label' => __( 'Badge', 'mkadmi' ), 'type' => 'text', 'description' => __( 'The short label on the card, e.g. CIBAHN-20.', 'mkadmi' ) ),
			'subtitle' => array( 'label' => __( 'Subtitle', 'mkadmi' ), 'type' => 'text', 'description' => __( 'The line under the title, e.g. Digital libraries · Digital humanities.', 'mkadmi' ) ),
			'place'    => array( 'label' => __( 'Place and proceedings', 'mkadmi' ), 'type' => 'text', 'description' => __( 'e.g. Paris · ACM ICPS 2018.', 'mkadmi' ) ),
			'url'      => array( 'label' => __( 'Link', 'mkadmi' ), 'type' => 'url' ),
		),
		'post'               => array(
			'icon' => array(
				'label'       => __( 'Icon', 'mkadmi' ),
				'type'        => 'text',
				'description' => __( 'A single emoji shown beside this item in the homepage news list, e.g. 📄.', 'mkadmi' ),
			),
		),
	);

	return isset( $fields[ $post_type ] ) ? $fields[ $post_type ] : array();
}

/**
 * The sanitiser a field type is saved through.
 *
 * @param string $type Field type: text, url or textarea.
 * @return string Callable name.
 */
function mkadmi_meta_sanitizer( $type ) {
	switch ( $type ) {
		case 'url':
			return 'esc_url_raw';
		case 'textarea':
			return 'sanitize_textarea_field';
		default:
			return 'sanitize_text_field';
	}
}

/**
 * Register the custom fields as post meta so the REST API and the block editor
 * can see them too.
 */
function mkadmi_register_meta() {
	foreach ( array( 'mkadmi_publication', 'mkadmi_course', 'mkadmi_project', 'mkadmi_event', 'post' ) as $post_type ) {
		foreach ( mkadmi_meta_fields( $post_type ) as $key => $field ) {
			register_post_meta(
				$post_type,
				'_mkadmi_' . $key,
				array(
					'type'              => 'string',
					'single'            => true,
					'default'           => '',
					'show_in_rest'      => false,
					'sanitize_callback' => mkadmi_meta_sanitizer( $field['type'] ),
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'mkadmi_register_meta' );

/**
 * Publications and courses read as lists, so show every entry and order them by
 * the year they carry rather than by the day they were typed in.
 *
 * @param WP_Query $query Query about to run.
 */
function mkadmi_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'mkadmi_publication' ) || $query->is_tax( array( 'mkadmi_pub_type' ) ) ) {
		$query->set( 'posts_per_page', 50 );

		// A publication with no year still belongs in the list, so the year is
		// joined in optionally and only used for sorting.
		$query->set(
			'meta_query',
			array(
				'relation'   => 'OR',
				'year_clause' => array(
					'key'     => '_mkadmi_year',
					'compare' => 'EXISTS',
					'type'    => 'NUMERIC',
				),
				array(
					'key'     => '_mkadmi_year',
					'compare' => 'NOT EXISTS',
				),
			)
		);
		$query->set( 'orderby', array( 'year_clause' => 'DESC', 'date' => 'DESC' ) );
	}

	if ( $query->is_post_type_archive( array( 'mkadmi_course', 'mkadmi_project', 'mkadmi_event' ) ) || $query->is_tax( array( 'mkadmi_level' ) ) ) {
		$query->set( 'posts_per_page', 50 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}
}
add_action( 'pre_get_posts', 'mkadmi_archive_query' );

/**
 * Flush rewrite rules once, after the theme is switched on, so the custom
 * archives resolve without a manual visit to Settings → Permalinks.
 */
function mkadmi_flush_rewrites() {
	mkadmi_register_taxonomies();
	mkadmi_register_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'mkadmi_flush_rewrites' );

/**
 * Leave the rewrite rules as we found them when the theme is switched away.
 */
add_action( 'switch_theme', 'flush_rewrite_rules' );
