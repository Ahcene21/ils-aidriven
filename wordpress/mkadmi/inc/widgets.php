<?php
/**
 * The panels that make up the sidebar.
 *
 * Each one is a widget rather than hard-coded markup, so the column can be
 * reordered, trimmed or extended from Appearance → Widgets.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared helpers for the theme's widgets.
 */
abstract class Mkadmi_Widget extends WP_Widget {

	/**
	 * Print a textarea field.
	 *
	 * @param array  $instance Saved values.
	 * @param string $key      Field key.
	 * @param string $label    Field label.
	 * @param string $help     Help text.
	 * @param int    $rows     Textarea height.
	 */
	protected function textarea_field( $instance, $key, $label, $help = '', $rows = 5 ) {
		$value = isset( $instance[ $key ] ) ? $instance[ $key ] : '';

		printf(
			'<p><label for="%1$s">%2$s</label><textarea class="widefat" rows="%5$d" id="%1$s" name="%3$s">%4$s</textarea>%6$s</p>',
			esc_attr( $this->get_field_id( $key ) ),
			esc_html( $label ),
			esc_attr( $this->get_field_name( $key ) ),
			esc_textarea( $value ),
			(int) $rows,
			$help ? '<span class="description">' . esc_html( $help ) . '</span>' : ''
		);
	}

	/**
	 * Print a text field.
	 *
	 * @param array  $instance Saved values.
	 * @param string $key      Field key.
	 * @param string $label    Field label.
	 */
	protected function text_field( $instance, $key, $label ) {
		$value = isset( $instance[ $key ] ) ? $instance[ $key ] : '';

		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" type="text" id="%1$s" name="%3$s" value="%4$s" /></p>',
			esc_attr( $this->get_field_id( $key ) ),
			esc_html( $label ),
			esc_attr( $this->get_field_name( $key ) ),
			esc_attr( $value )
		);
	}

	/**
	 * Open the widget, printing its title.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved values.
	 */
	protected function open( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );

		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		if ( $title ) {
			echo wp_kses_post( $args['before_title'] . esc_html( $title ) . $args['after_title'] );
		}
	}
}

/**
 * Section navigation, rendered from a menu, one row per section.
 */
class Mkadmi_Nav_Widget extends Mkadmi_Widget {

	/**
	 * Register the widget.
	 */
	public function __construct() {
		parent::__construct(
			'mkadmi_nav',
			__( 'Mkadmi: section navigation', 'mkadmi' ),
			array( 'description' => __( 'A menu as a column of rows. An item’s description is used as its icon.', 'mkadmi' ) )
		);
	}

	/**
	 * Render the widget.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		$menu = isset( $instance['menu'] ) ? (int) $instance['menu'] : 0;

		if ( ! $menu ) {
			return;
		}

		$this->open( $args, $instance );

		wp_nav_menu(
			array(
				'menu'        => $menu,
				'container'   => 'nav',
				'container_class' => 'panel-nav',
				'container_aria_label' => esc_attr__( 'Sections', 'mkadmi' ),
				'menu_class'  => 'panel-nav__list',
				'depth'       => 1,
				'fallback_cb' => false,
				'walker'      => new Mkadmi_Icon_Walker(),
			)
		);

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * The widget form.
	 *
	 * @param array $instance Saved values.
	 */
	public function form( $instance ) {
		$this->text_field( $instance, 'title', __( 'Title:', 'mkadmi' ) );

		$menus    = wp_get_nav_menus();
		$selected = isset( $instance['menu'] ) ? (int) $instance['menu'] : 0;

		printf( '<p><label for="%1$s">%2$s</label>', esc_attr( $this->get_field_id( 'menu' ) ), esc_html__( 'Menu:', 'mkadmi' ) );
		printf( '<select class="widefat" id="%1$s" name="%2$s">', esc_attr( $this->get_field_id( 'menu' ) ), esc_attr( $this->get_field_name( 'menu' ) ) );
		printf( '<option value="0">%s</option>', esc_html__( '— Select —', 'mkadmi' ) );

		foreach ( $menus as $menu ) {
			printf(
				'<option value="%1$d"%3$s>%2$s</option>',
				(int) $menu->term_id,
				esc_html( $menu->name ),
				selected( $selected, (int) $menu->term_id, false )
			);
		}

		echo '</select></p>';
	}

	/**
	 * Sanitise on save.
	 *
	 * @param array $new_instance Submitted values.
	 * @param array $old_instance Saved values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array(
			'title' => sanitize_text_field( isset( $new_instance['title'] ) ? $new_instance['title'] : '' ),
			'menu'  => absint( isset( $new_instance['menu'] ) ? $new_instance['menu'] : 0 ),
		);
	}
}

/**
 * Menu walker that promotes an item's description to an icon.
 */
class Mkadmi_Icon_Walker extends Walker_Nav_Menu {

	/**
	 * Start one element.
	 *
	 * @param string   $output Accumulated markup, by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Menu arguments.
	 * @param int      $id     Item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'panel-nav__item';

		$class_attr = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
		$icon       = trim( (string) $item->description );
		$current    = in_array( 'current-menu-item', $classes, true ) ? ' aria-current="page"' : '';

		$output .= sprintf(
			'<li class="%1$s"><a class="panel-nav__link" href="%2$s"%3$s>%4$s<span class="panel-nav__label">%5$s</span></a>',
			esc_attr( $class_attr ),
			esc_url( $item->url ),
			$current,
			$icon ? '<span class="panel-nav__icon" aria-hidden="true">' . esc_html( $icon ) . '</span>' : '',
			esc_html( $item->title )
		);
	}
}

/**
 * Institutions the site's owner belongs to, each with its emblem.
 */
class Mkadmi_Affiliations_Widget extends Mkadmi_Widget {

	/**
	 * Register the widget.
	 */
	public function __construct() {
		parent::__construct(
			'mkadmi_affiliations',
			__( 'Mkadmi: affiliations', 'mkadmi' ),
			array( 'description' => __( 'Institutions with their emblems.', 'mkadmi' ) )
		);
	}

	/**
	 * Render the widget.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		$rows = mkadmi_parse_lines( isset( $instance['items'] ) ? $instance['items'] : '', 4 );

		if ( ! $rows ) {
			return;
		}

		$this->open( $args, $instance );

		echo '<ul class="affiliations">';

		foreach ( $rows as $row ) {
			list( $name, $subtitle, $image, $url ) = $row;

			if ( '' === $name ) {
				continue;
			}

			$logo = $image
				? sprintf( '<img src="%1$s" alt="" loading="lazy" decoding="async" />', esc_url( $image ) )
				: sprintf( '<span class="affiliation__initials" aria-hidden="true">%s</span>', esc_html( mb_substr( $name, 0, 2 ) ) );

			$text = sprintf(
				'<span class="affiliation__text"><span class="affiliation__name">%1$s</span>%2$s</span>',
				esc_html( $name ),
				$subtitle ? '<span class="affiliation__subtitle">' . esc_html( $subtitle ) . '</span>' : ''
			);

			$inner = $url
				? sprintf( '<a href="%1$s"%3$s>%2$s</a>', esc_url( $url ), $text, mkadmi_external_attrs( $url ) )
				: $text;

			printf(
				'<li class="affiliation"><span class="affiliation__logo">%1$s</span>%2$s</li>',
				$logo, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts above.
				$inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts above.
			);
		}

		echo '</ul>';

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * The widget form.
	 *
	 * @param array $instance Saved values.
	 */
	public function form( $instance ) {
		$this->text_field( $instance, 'title', __( 'Title:', 'mkadmi' ) );
		$this->textarea_field(
			$instance,
			'items',
			__( 'Institutions:', 'mkadmi' ),
			__( 'One per line, as “Name | Department | Emblem URL | Link”. Leave a part empty to skip it.', 'mkadmi' ),
			6
		);
	}

	/**
	 * Sanitise on save.
	 *
	 * @param array $new_instance Submitted values.
	 * @param array $old_instance Saved values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array(
			'title' => sanitize_text_field( isset( $new_instance['title'] ) ? $new_instance['title'] : '' ),
			'items' => sanitize_textarea_field( isset( $new_instance['items'] ) ? $new_instance['items'] : '' ),
		);
	}
}

/**
 * Scholarly profiles — ORCID, Scopus, ResearchGate and the rest — as a grid of
 * round buttons, with identifiers spelled out underneath.
 */
class Mkadmi_Profiles_Widget extends Mkadmi_Widget {

	/**
	 * Register the widget.
	 */
	public function __construct() {
		parent::__construct(
			'mkadmi_profiles',
			__( 'Mkadmi: academic profiles', 'mkadmi' ),
			array( 'description' => __( 'ORCID, Scopus, ResearchGate, Google Scholar and other profile links.', 'mkadmi' ) )
		);
	}

	/**
	 * Render the widget.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		$profiles = mkadmi_parse_lines( isset( $instance['items'] ) ? $instance['items'] : '', 3 );
		$ids      = mkadmi_parse_lines( isset( $instance['ids'] ) ? $instance['ids'] : '', 2 );

		if ( ! $profiles && ! $ids ) {
			return;
		}

		$this->open( $args, $instance );

		if ( $profiles ) {
			echo '<ul class="profiles">';

			foreach ( $profiles as $profile ) {
				list( $name, $url, $short ) = $profile;

				if ( '' === $name || '' === $url ) {
					continue;
				}

				printf(
					'<li class="profiles__item"><a class="profiles__link" href="%1$s"%4$s><span class="profiles__mark" aria-hidden="true">%2$s</span><span class="screen-reader-text">%3$s</span></a></li>',
					esc_url( $url ),
					esc_html( $short ? $short : mb_substr( $name, 0, 2 ) ),
					esc_html( $name ),
					mkadmi_external_attrs( $url )
				);
			}

			echo '</ul>';
		}

		if ( $ids ) {
			echo '<dl class="identifiers">';

			foreach ( $ids as $identifier ) {
				list( $label, $value ) = $identifier;

				if ( '' === $label ) {
					continue;
				}

				printf( '<dt>%s</dt><dd>%s</dd>', esc_html( $label ), esc_html( $value ) );
			}

			echo '</dl>';
		}

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * The widget form.
	 *
	 * @param array $instance Saved values.
	 */
	public function form( $instance ) {
		$this->text_field( $instance, 'title', __( 'Title:', 'mkadmi' ) );
		$this->textarea_field(
			$instance,
			'items',
			__( 'Profiles:', 'mkadmi' ),
			__( 'One per line, as “Name | URL | Short label”, e.g. ORCID | https://orcid.org/… | iD.', 'mkadmi' ),
			6
		);
		$this->textarea_field(
			$instance,
			'ids',
			__( 'Identifiers:', 'mkadmi' ),
			__( 'One per line, as “Label | Value”, e.g. ORCID | 0000-0002-5621-2235.', 'mkadmi' ),
			3
		);
	}

	/**
	 * Sanitise on save.
	 *
	 * @param array $new_instance Submitted values.
	 * @param array $old_instance Saved values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array(
			'title' => sanitize_text_field( isset( $new_instance['title'] ) ? $new_instance['title'] : '' ),
			'items' => sanitize_textarea_field( isset( $new_instance['items'] ) ? $new_instance['items'] : '' ),
			'ids'   => sanitize_textarea_field( isset( $new_instance['ids'] ) ? $new_instance['ids'] : '' ),
		);
	}
}

/**
 * Quick links: the CV, a reading list, anything worth one click.
 */
class Mkadmi_Links_Widget extends Mkadmi_Widget {

	/**
	 * Register the widget.
	 */
	public function __construct() {
		parent::__construct(
			'mkadmi_links',
			__( 'Mkadmi: quick links', 'mkadmi' ),
			array( 'description' => __( 'A short list of links, each with an icon.', 'mkadmi' ) )
		);
	}

	/**
	 * Render the widget.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		$rows = mkadmi_parse_lines( isset( $instance['items'] ) ? $instance['items'] : '', 3 );

		if ( ! $rows ) {
			return;
		}

		$this->open( $args, $instance );

		echo '<ul class="quick-links">';

		foreach ( $rows as $row ) {
			list( $icon, $label, $url ) = $row;

			if ( '' === $label || '' === $url ) {
				continue;
			}

			printf(
				'<li><a class="quick-link" href="%1$s"%4$s>%2$s<span>%3$s</span></a></li>',
				esc_url( $url ),
				$icon ? '<span class="quick-link__icon" aria-hidden="true">' . esc_html( $icon ) . '</span>' : '',
				esc_html( $label ),
				mkadmi_external_attrs( $url )
			);
		}

		echo '</ul>';

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * The widget form.
	 *
	 * @param array $instance Saved values.
	 */
	public function form( $instance ) {
		$this->text_field( $instance, 'title', __( 'Title:', 'mkadmi' ) );
		$this->textarea_field(
			$instance,
			'items',
			__( 'Links:', 'mkadmi' ),
			__( 'One per line, as “Icon | Label | URL”.', 'mkadmi' ),
			5
		);
	}

	/**
	 * Sanitise on save.
	 *
	 * @param array $new_instance Submitted values.
	 * @param array $old_instance Saved values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array(
			'title' => sanitize_text_field( isset( $new_instance['title'] ) ? $new_instance['title'] : '' ),
			'items' => sanitize_textarea_field( isset( $new_instance['items'] ) ? $new_instance['items'] : '' ),
		);
	}
}

/**
 * The language switcher, as a stack of buttons.
 */
class Mkadmi_Language_Widget extends Mkadmi_Widget {

	/**
	 * Register the widget.
	 */
	public function __construct() {
		parent::__construct(
			'mkadmi_language',
			__( 'Mkadmi: language', 'mkadmi' ),
			array( 'description' => __( 'The languages the site is published in, taken from the language menu.', 'mkadmi' ) )
		);
	}

	/**
	 * Render the widget.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		if ( ! mkadmi_has_languages() ) {
			return;
		}

		$this->open( $args, $instance );
		mkadmi_language_switcher( 'stack' );
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * The widget form.
	 *
	 * @param array $instance Saved values.
	 */
	public function form( $instance ) {
		$this->text_field( $instance, 'title', __( 'Title:', 'mkadmi' ) );

		printf(
			'<p class="description">%s</p>',
			esc_html__( 'The languages themselves are the items of the “Language switcher” menu, under Appearance → Menus.', 'mkadmi' )
		);
	}

	/**
	 * Sanitise on save.
	 *
	 * @param array $new_instance Submitted values.
	 * @param array $old_instance Saved values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array( 'title' => sanitize_text_field( isset( $new_instance['title'] ) ? $new_instance['title'] : '' ) );
	}
}

/**
 * A visit counter.
 *
 * It keeps two integers and a date. Nothing about the reader is recorded: no
 * address, no identifier, no cookie — so the panel can be shown without asking
 * anyone for consent.
 */
class Mkadmi_Visitors_Widget extends Mkadmi_Widget {

	/**
	 * Register the widget.
	 */
	public function __construct() {
		parent::__construct(
			'mkadmi_visitors',
			__( 'Mkadmi: visits', 'mkadmi' ),
			array( 'description' => __( 'Page views in total and today. No personal data is stored.', 'mkadmi' ) )
		);
	}

	/**
	 * Render the widget.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		$counts = mkadmi_visit_counts();

		$this->open( $args, $instance );

		echo '<table class="visits"><tbody>';
		printf(
			'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
			esc_html__( 'Total visits', 'mkadmi' ),
			esc_html( number_format_i18n( $counts['total'] ) )
		);
		printf(
			'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
			esc_html__( 'Today', 'mkadmi' ),
			esc_html( number_format_i18n( $counts['today'] ) )
		);
		echo '</tbody></table>';

		if ( $counts['since'] ) {
			printf(
				'<p class="visits__since">%s</p>',
				esc_html(
					sprintf(
						/* translators: %s: month and year counting started, e.g. June 2026. */
						__( 'Since %s', 'mkadmi' ),
						wp_date( _x( 'F Y', 'counting since', 'mkadmi' ), $counts['since'] )
					)
				)
			);
		}

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * The widget form.
	 *
	 * @param array $instance Saved values.
	 */
	public function form( $instance ) {
		$this->text_field( $instance, 'title', __( 'Title:', 'mkadmi' ) );
	}

	/**
	 * Sanitise on save.
	 *
	 * @param array $new_instance Submitted values.
	 * @param array $old_instance Saved values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array( 'title' => sanitize_text_field( isset( $new_instance['title'] ) ? $new_instance['title'] : '' ) );
	}
}

/**
 * Register the theme's widgets.
 */
function mkadmi_register_widgets() {
	register_widget( 'Mkadmi_Nav_Widget' );
	register_widget( 'Mkadmi_Affiliations_Widget' );
	register_widget( 'Mkadmi_Profiles_Widget' );
	register_widget( 'Mkadmi_Links_Widget' );
	register_widget( 'Mkadmi_Language_Widget' );
	register_widget( 'Mkadmi_Visitors_Widget' );
}
add_action( 'widgets_init', 'mkadmi_register_widgets' );

/**
 * The stored visit counts, rolled over at midnight in the site's time zone.
 *
 * @return array{total:int,today:int,since:int}
 */
function mkadmi_visit_counts() {
	$counts = get_option(
		'mkadmi_visits',
		array(
			'total' => 0,
			'today' => 0,
			'day'   => '',
			'since' => 0,
		)
	);

	$counts = wp_parse_args( (array) $counts, array( 'total' => 0, 'today' => 0, 'day' => '', 'since' => 0 ) );

	if ( wp_date( 'Y-m-d' ) !== $counts['day'] ) {
		$counts['today'] = 0;
	}

	return array(
		'total' => (int) $counts['total'],
		'today' => (int) $counts['today'],
		'since' => (int) $counts['since'],
	);
}

/**
 * Count one page view.
 *
 * Only front-end views by logged-out readers count, and only while the counter
 * is actually on display — an unseen counter is not worth a write on every
 * request.
 */
function mkadmi_count_visit() {
	if ( is_admin() || is_user_logged_in() || wp_doing_ajax() || is_robots() || is_feed() || is_trackback() || is_preview() ) {
		return;
	}

	if ( ! is_active_widget( false, false, 'mkadmi_visitors', true ) ) {
		return;
	}

	$stored = get_option( 'mkadmi_visits', array() );
	$stored = wp_parse_args( (array) $stored, array( 'total' => 0, 'today' => 0, 'day' => '', 'since' => 0 ) );
	$today  = wp_date( 'Y-m-d' );

	if ( $stored['day'] !== $today ) {
		$stored['day']   = $today;
		$stored['today'] = 0;
	}

	if ( ! $stored['since'] ) {
		$stored['since'] = time();
	}

	++$stored['total'];
	++$stored['today'];

	update_option( 'mkadmi_visits', $stored, false );
}
add_action( 'template_redirect', 'mkadmi_count_visit' );
