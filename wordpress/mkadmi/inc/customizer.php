<?php
/**
 * Customizer settings.
 *
 * Every piece of text on the masthead, the profile band and the homepage is a
 * setting, so the site can be run by its owner without opening a template.
 *
 * Settings are declared once, in mkadmi_settings(), and the panel is generated
 * from that declaration — a new setting needs one entry, not three.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Every theme setting: default value, sanitiser, and how it is edited.
 *
 * @return array<string, array<string, mixed>>
 */
function mkadmi_settings() {
	$settings = array(

		/* Masthead ----------------------------------------------------- */
		'brand_monogram'   => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'masthead',
			'label'    => __( 'Monogram', 'mkadmi' ),
			'help'     => __( 'One or two letters shown in the gold frame when no logo image is set.', 'mkadmi' ),
		),
		'brand_name'       => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'masthead',
			'label'    => __( 'Name in the masthead', 'mkadmi' ),
			'help'     => __( 'Leave empty to use the site title.', 'mkadmi' ),
		),

		/* Profile band ------------------------------------------------- */
		'profile_photo'    => array(
			'default'  => 0,
			'sanitize' => 'absint',
			'section'  => 'profile',
			'control'  => 'media',
			'label'    => __( 'Portrait', 'mkadmi' ),
		),
		'profile_role'     => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'profile',
			'label'    => __( 'Titles and position', 'mkadmi' ),
			'help'     => __( 'The gold line at the top of the band. Separate parts with “·”.', 'mkadmi' ),
		),
		'profile_affiliations' => array(
			'default'  => '',
			'sanitize' => 'mkadmi_sanitize_textarea',
			'section'  => 'profile',
			'control'  => 'textarea',
			'label'    => __( 'Affiliations', 'mkadmi' ),
			'help'     => __( 'One per line. Each gets a location pin.', 'mkadmi' ),
		),
		'profile_emails'   => array(
			'default'  => '',
			'sanitize' => 'mkadmi_sanitize_textarea',
			'section'  => 'profile',
			'control'  => 'textarea',
			'label'    => __( 'Email addresses', 'mkadmi' ),
			'help'     => __( 'One per line.', 'mkadmi' ),
		),
		'profile_website'  => array(
			'default'  => '',
			'sanitize' => 'esc_url_raw',
			'section'  => 'profile',
			'control'  => 'url',
			'label'    => __( 'Website shown in the band', 'mkadmi' ),
		),
		'profile_phones'   => array(
			'default'  => '',
			'sanitize' => 'mkadmi_sanitize_textarea',
			'section'  => 'profile',
			'control'  => 'textarea',
			'label'    => __( 'Telephone numbers', 'mkadmi' ),
			'help'     => __( 'One per line.', 'mkadmi' ),
		),
		'profile_cv'       => array(
			'default'  => '',
			'sanitize' => 'esc_url_raw',
			'section'  => 'profile',
			'control'  => 'url',
			'label'    => __( 'Curriculum vitae (PDF)', 'mkadmi' ),
		),

		/* Clocks ------------------------------------------------------- */
		'clocks_show'      => array(
			'default'  => true,
			'sanitize' => 'mkadmi_sanitize_checkbox',
			'section'  => 'clocks',
			'control'  => 'checkbox',
			'label'    => __( 'Show the date and clocks', 'mkadmi' ),
		),
		'clocks_list'      => array(
			'default'  => '',
			'sanitize' => 'mkadmi_sanitize_textarea',
			'section'  => 'clocks',
			'control'  => 'textarea',
			'label'    => __( 'Clocks', 'mkadmi' ),
			'help'     => __( 'One per line, as “City | Time zone”, e.g. Muscat | Asia/Muscat. Time zone names come from the IANA database.', 'mkadmi' ),
		),

		/* Figures strip ------------------------------------------------ */
		'stats_show'       => array(
			'default'  => true,
			'sanitize' => 'mkadmi_sanitize_checkbox',
			'section'  => 'stats',
			'control'  => 'checkbox',
			'label'    => __( 'Show the figures strip', 'mkadmi' ),
		),
		'stats_list'       => array(
			'default'  => '',
			'sanitize' => 'mkadmi_sanitize_textarea',
			'section'  => 'stats',
			'control'  => 'textarea',
			'label'    => __( 'Figures', 'mkadmi' ),
			'help'     => __( 'One per line, as “Figure | Label”, e.g. 93+ | scholarly publications.', 'mkadmi' ),
		),

		/* Colours ------------------------------------------------------ */
		'color_primary'    => array(
			'default'  => '#0e5540',
			'sanitize' => 'sanitize_hex_color',
			'section'  => 'colors',
			'control'  => 'color',
			'label'    => __( 'Green', 'mkadmi' ),
		),
		'color_accent'     => array(
			'default'  => '#c8912b',
			'sanitize' => 'sanitize_hex_color',
			'section'  => 'colors',
			'control'  => 'color',
			'label'    => __( 'Gold', 'mkadmi' ),
		),

		/* Homepage: research topics ------------------------------------ */
		'topics_show'      => array(
			'default'  => true,
			'sanitize' => 'mkadmi_sanitize_checkbox',
			'section'  => 'home',
			'control'  => 'checkbox',
			'label'    => __( 'Show “Current research topics”', 'mkadmi' ),
		),
		'topics_title'     => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'home',
			'label'    => __( 'Research topics: heading', 'mkadmi' ),
		),
		'topics_list'      => array(
			'default'  => '',
			'sanitize' => 'mkadmi_sanitize_textarea',
			'section'  => 'home',
			'control'  => 'textarea',
			'label'    => __( 'Research topics', 'mkadmi' ),
			'help'     => __( 'One per line, as “Emoji | Topic | optional link”.', 'mkadmi' ),
		),

		/* Homepage: news ----------------------------------------------- */
		'news_show'        => array(
			'default'  => true,
			'sanitize' => 'mkadmi_sanitize_checkbox',
			'section'  => 'home',
			'control'  => 'checkbox',
			'label'    => __( 'Show “Academic news”', 'mkadmi' ),
		),
		'news_title'       => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'home',
			'label'    => __( 'News: heading', 'mkadmi' ),
		),
		'news_count'       => array(
			'default'  => 3,
			'sanitize' => 'absint',
			'section'  => 'home',
			'control'  => 'number',
			'label'    => __( 'News: how many posts', 'mkadmi' ),
		),

		/* Homepage: conferences ---------------------------------------- */
		'events_show'      => array(
			'default'  => true,
			'sanitize' => 'mkadmi_sanitize_checkbox',
			'section'  => 'home',
			'control'  => 'checkbox',
			'label'    => __( 'Show “Conferences organised”', 'mkadmi' ),
		),
		'events_title'     => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'home',
			'label'    => __( 'Conferences: heading', 'mkadmi' ),
		),
		'events_count'     => array(
			'default'  => 4,
			'sanitize' => 'absint',
			'section'  => 'home',
			'control'  => 'number',
			'label'    => __( 'Conferences: how many', 'mkadmi' ),
		),

		/* Homepage: publications --------------------------------------- */
		'pubs_show'        => array(
			'default'  => true,
			'sanitize' => 'mkadmi_sanitize_checkbox',
			'section'  => 'home',
			'control'  => 'checkbox',
			'label'    => __( 'Show “Books and refereed work”', 'mkadmi' ),
		),
		'pubs_title'       => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'home',
			'label'    => __( 'Publications: heading', 'mkadmi' ),
		),
		'pubs_count'       => array(
			'default'  => 8,
			'sanitize' => 'absint',
			'section'  => 'home',
			'control'  => 'number',
			'label'    => __( 'Publications: how many', 'mkadmi' ),
		),
		'pubs_more'        => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'home',
			'label'    => __( 'Publications: “see all” text', 'mkadmi' ),
			'help'     => __( 'Shown under the list, e.g. See all publications (95+).', 'mkadmi' ),
		),

		/* Homepage: teaching and projects ------------------------------ */
		'courses_show'     => array(
			'default'  => false,
			'sanitize' => 'mkadmi_sanitize_checkbox',
			'section'  => 'home',
			'control'  => 'checkbox',
			'label'    => __( 'Show “Teaching”', 'mkadmi' ),
		),
		'courses_title'    => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'home',
			'label'    => __( 'Teaching: heading', 'mkadmi' ),
		),
		'courses_count'    => array(
			'default'  => 6,
			'sanitize' => 'absint',
			'section'  => 'home',
			'control'  => 'number',
			'label'    => __( 'Teaching: how many', 'mkadmi' ),
		),
		'projects_show'    => array(
			'default'  => false,
			'sanitize' => 'mkadmi_sanitize_checkbox',
			'section'  => 'home',
			'control'  => 'checkbox',
			'label'    => __( 'Show “Research projects”', 'mkadmi' ),
		),
		'projects_title'   => array(
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'section'  => 'home',
			'label'    => __( 'Projects: heading', 'mkadmi' ),
		),
		'projects_count'   => array(
			'default'  => 3,
			'sanitize' => 'absint',
			'section'  => 'home',
			'control'  => 'number',
			'label'    => __( 'Projects: how many', 'mkadmi' ),
		),

		/* Footer ------------------------------------------------------- */
		'footer_links'     => array(
			'default'  => '',
			'sanitize' => 'mkadmi_sanitize_textarea',
			'section'  => 'footer',
			'control'  => 'textarea',
			'label'    => __( 'Institution links', 'mkadmi' ),
			'help'     => __( 'One per line, as “Label | URL”.', 'mkadmi' ),
		),
		'footer_text'      => array(
			'default'  => '',
			'sanitize' => 'mkadmi_sanitize_textarea',
			'section'  => 'footer',
			'control'  => 'textarea',
			'label'    => __( 'Footer line', 'mkadmi' ),
			'help'     => __( 'Replaces the default copyright line.', 'mkadmi' ),
		),
	);

	/**
	 * Filter the theme's settings declaration.
	 *
	 * @param array $settings Settings, keyed by name.
	 */
	return apply_filters( 'mkadmi_settings', $settings );
}

/**
 * Read a theme setting, falling back to its declared default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function mkadmi_option( $key ) {
	$settings = mkadmi_settings();
	$default  = isset( $settings[ $key ] ) ? $settings[ $key ]['default'] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Sanitiser for multi-line text.
 *
 * @param string $value Raw value.
 * @return string
 */
function mkadmi_sanitize_textarea( $value ) {
	return sanitize_textarea_field( $value );
}

/**
 * Sanitiser for checkboxes.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function mkadmi_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * The customizer sections, in the order they appear.
 *
 * @return array<string, array<string, string>>
 */
function mkadmi_customizer_sections() {
	return array(
		'masthead' => array(
			'title'       => __( 'Masthead', 'mkadmi' ),
			'description' => __( 'The green bar at the top: the menu, the language switcher and the name.', 'mkadmi' ),
		),
		'profile'  => array(
			'title'       => __( 'Profile band', 'mkadmi' ),
			'description' => __( 'The portrait, titles, affiliations and contact details under the masthead.', 'mkadmi' ),
		),
		'clocks'   => array(
			'title'       => __( 'Date and clocks', 'mkadmi' ),
			'description' => __( 'Local time in the cities the site spans, shown beside the profile.', 'mkadmi' ),
		),
		'stats'    => array(
			'title'       => __( 'Figures', 'mkadmi' ),
			'description' => __( 'The strip of counts under the profile band.', 'mkadmi' ),
		),
		'colors'   => array(
			'title'       => __( 'Colours', 'mkadmi' ),
			'description' => __( 'Two colours drive the whole palette.', 'mkadmi' ),
		),
		'home'     => array(
			'title'       => __( 'Homepage sections', 'mkadmi' ),
			'description' => __( 'Shown on a static front page. Each section can be turned off, and every heading is yours to write.', 'mkadmi' ),
		),
		'footer'   => array(
			'title' => __( 'Footer', 'mkadmi' ),
		),
	);
}

/**
 * Build the panel from the settings declaration.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function mkadmi_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'mkadmi_panel',
		array(
			'title'       => __( 'Academic site', 'mkadmi' ),
			'description' => __( 'The profile, the homepage sections and the contact details.', 'mkadmi' ),
			'priority'    => 20,
		)
	);

	foreach ( mkadmi_customizer_sections() as $id => $section ) {
		$wp_customize->add_section(
			'mkadmi_' . $id,
			array(
				'title'       => $section['title'],
				'description' => isset( $section['description'] ) ? $section['description'] : '',
				'panel'       => 'mkadmi_panel',
			)
		);
	}

	foreach ( mkadmi_settings() as $key => $config ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => $config['sanitize'],
				'transport'         => 'refresh',
			)
		);

		$control = isset( $config['control'] ) ? $config['control'] : 'text';
		$args    = array(
			'label'       => isset( $config['label'] ) ? $config['label'] : $key,
			'description' => isset( $config['help'] ) ? $config['help'] : '',
			'section'     => 'mkadmi_' . $config['section'],
		);

		if ( 'media' === $control ) {
			$args['mime_type'] = 'image';
			$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $key, $args ) );
			continue;
		}

		if ( 'color' === $control ) {
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, $args ) );
			continue;
		}

		$args['type'] = $control;

		if ( 'number' === $control ) {
			$args['input_attrs'] = array( 'min' => 1, 'max' => 24, 'step' => 1 );
		}

		$wp_customize->add_control( $key, $args );
	}

	// Live-update the two core strings that appear in the masthead.
	foreach ( array( 'blogname', 'blogdescription' ) as $core_setting ) {
		$setting = $wp_customize->get_setting( $core_setting );

		if ( $setting ) {
			$setting->transport = 'postMessage';
		}
	}

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.brand__name',
				'render_callback' => function () {
					return get_bloginfo( 'name', 'display' );
				},
			)
		);
	}
}
add_action( 'customize_register', 'mkadmi_customize_register' );

/**
 * Script that applies the live-preview settings without a reload.
 */
function mkadmi_customize_preview_js() {
	wp_enqueue_script(
		'mkadmi-customizer-preview',
		MKADMI_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		MKADMI_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'mkadmi_customize_preview_js' );
