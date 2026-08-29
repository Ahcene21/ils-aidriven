<?php
/**
 * Template helpers shared across the theme.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read one of the theme's custom fields.
 *
 * @param string   $key     Field key without the _mkadmi_ prefix.
 * @param int|null $post_id Post to read from. Defaults to the current post.
 * @return string
 */
function mkadmi_field( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	return (string) get_post_meta( $post_id, '_mkadmi_' . $key, true );
}

/**
 * The citation line under a publication title: authors, venue, publisher and
 * reference, with only the parts that were filled in.
 *
 * @param int|null $post_id Publication ID.
 * @return string Escaped, ready to print.
 */
function mkadmi_citation( $post_id = null ) {
	$parts = array();

	foreach ( array( 'authors', 'venue', 'publisher', 'reference', 'isbn' ) as $key ) {
		$value = mkadmi_field( $key, $post_id );

		if ( '' !== $value ) {
			$parts[] = esc_html( $value );
		}
	}

	return implode( '<span class="sep" aria-hidden="true"> · </span>', $parts );
}

/**
 * The links a publication offers: DOI, full text, and whatever else was added
 * to the "Further links" field — publisher page, HAL, Zenodo, a chapter PDF.
 *
 * @param int|null $post_id Publication ID.
 */
function mkadmi_publication_links( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$links   = array();

	$doi = mkadmi_field( 'doi', $post_id );

	if ( $doi ) {
		$links[] = array(
			'label' => __( 'DOI', 'mkadmi' ),
			'url'   => 0 === strpos( $doi, 'http' ) ? $doi : 'https://doi.org/' . ltrim( $doi, '/' ),
		);
	}

	$pdf = mkadmi_field( 'pdf', $post_id );

	if ( $pdf ) {
		$links[] = array(
			'label' => __( 'PDF', 'mkadmi' ),
			'url'   => $pdf,
		);
	}

	$url = mkadmi_field( 'url', $post_id );

	if ( $url ) {
		$links[] = array(
			'label' => __( 'Link', 'mkadmi' ),
			'url'   => $url,
		);
	}

	foreach ( mkadmi_parse_lines( mkadmi_field( 'links', $post_id ), 2 ) as $line ) {
		if ( '' === $line[0] || '' === $line[1] ) {
			continue;
		}

		$links[] = array(
			'label' => $line[0],
			'url'   => $line[1],
		);
	}

	if ( ! $links ) {
		return;
	}

	$title = get_the_title( $post_id );

	echo '<p class="entry__links">';

	foreach ( $links as $link ) {
		printf(
			'<a class="entry__link" href="%1$s"%3$s>%2$s<span class="screen-reader-text"> — %4$s</span></a>',
			esc_url( $link['url'] ),
			esc_html( $link['label'] ),
			mkadmi_external_attrs( $link['url'] ),
			esc_html( $title )
		);
	}

	echo '</p>';
}

/**
 * rel/target attributes for a link that leaves the site.
 *
 * @param string $url Destination.
 * @return string Attribute string, already escaped.
 */
function mkadmi_external_attrs( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	$home = wp_parse_url( home_url(), PHP_URL_HOST );

	if ( ! $host || $host === $home ) {
		return '';
	}

	return ' target="_blank" rel="noopener noreferrer"';
}

/**
 * A definition list of the fields a course or project carries.
 *
 * @param int|null $post_id Post ID.
 */
function mkadmi_detail_list( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$fields  = mkadmi_meta_fields( get_post_type( $post_id ) );
	$rows    = array();

	foreach ( $fields as $key => $field ) {
		if ( in_array( $key, array( 'syllabus', 'url', 'pdf', 'doi' ), true ) ) {
			continue;
		}

		$value = mkadmi_field( $key, $post_id );

		if ( '' !== $value ) {
			$rows[ $field['label'] ] = $value;
		}
	}

	if ( ! $rows ) {
		return;
	}

	echo '<dl class="detail-list">';

	foreach ( $rows as $label => $value ) {
		printf( '<dt>%s</dt><dd>%s</dd>', esc_html( $label ), esc_html( $value ) );
	}

	echo '</dl>';
}

/**
 * Date, author and categories under a blog post title.
 */
function mkadmi_entry_meta() {
	printf(
		'<p class="entry__meta"><time class="entry__date" datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);

	$categories = get_the_category_list( ', ' );

	if ( $categories ) {
		printf(
			'<span class="sep" aria-hidden="true"> · </span><span class="entry__terms">%s</span>',
			wp_kses_post( $categories )
		);
	}

	echo '</p>';
}

/**
 * The terms of one taxonomy as a row of pills.
 *
 * @param string   $taxonomy Taxonomy name.
 * @param int|null $post_id  Post ID.
 */
function mkadmi_term_pills( $taxonomy, $post_id = null ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return;
	}

	echo '<p class="pills">';

	foreach ( $terms as $term ) {
		printf(
			'<a class="pill" href="%1$s">%2$s</a>',
			esc_url( get_term_link( $term ) ),
			esc_html( $term->name )
		);
	}

	echo '</p>';
}

/**
 * The featured image, linked on archives and plain on single views.
 *
 * @param string $size Image size.
 */
function mkadmi_post_thumbnail( $size = 'mkadmi-card' ) {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) {
		echo '<figure class="entry__media">';
		the_post_thumbnail( $size, array( 'loading' => 'eager' ) );
		echo '</figure>';

		return;
	}

	printf(
		'<a class="entry__media entry__media--link" href="%s" aria-hidden="true" tabindex="-1">',
		esc_url( get_permalink() )
	);
	the_post_thumbnail( $size );
	echo '</a>';
}

/**
 * Pagination for archives, styled with the theme's button.
 */
function mkadmi_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => esc_html__( 'Previous', 'mkadmi' ),
			'next_text'          => esc_html__( 'Next', 'mkadmi' ),
			'screen_reader_text' => esc_html__( 'Page navigation', 'mkadmi' ),
			'aria_label'         => esc_html__( 'Pages', 'mkadmi' ),
		)
	);
}

/**
 * Where the reader is, for pages below the top level.
 */
function mkadmi_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'mkadmi' ) . '</a>' );

	if ( is_singular( array( 'mkadmi_publication', 'mkadmi_course', 'mkadmi_project' ) ) ) {
		$object  = get_post_type_object( get_post_type() );
		$archive = get_post_type_archive_link( get_post_type() );

		if ( $object && $archive ) {
			$items[] = '<a href="' . esc_url( $archive ) . '">' . esc_html( $object->labels->name ) . '</a>';
		}
	} elseif ( is_singular( 'post' ) ) {
		$posts_page = get_option( 'page_for_posts' );

		if ( $posts_page ) {
			$items[] = '<a href="' . esc_url( get_permalink( $posts_page ) ) . '">' . esc_html( get_the_title( $posts_page ) ) . '</a>';
		}
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$items[] = '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a>';
		}
	}

	if ( is_singular() ) {
		$items[] = '<span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( ! is_home() ) {
		$items[] = '<span aria-current="page">' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</span>';
	}

	if ( count( $items ) < 2 ) {
		return;
	}

	printf(
		'<nav class="breadcrumbs" aria-label="%1$s">%2$s</nav>',
		esc_attr__( 'Breadcrumb', 'mkadmi' ),
		wp_kses_post( implode( '<span class="sep" aria-hidden="true">/</span>', $items ) )
	);
}

/**
 * Split a textarea setting into trimmed, non-empty lines.
 *
 * @param string $value Raw setting value.
 * @return string[]
 */
function mkadmi_lines( $value ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $value );

	if ( ! $lines ) {
		return array();
	}

	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Split a textarea setting into lines, and each line into pipe-separated parts.
 *
 * Short lines are padded so a template can read $line[1] without checking that
 * the editor filled every column in.
 *
 * @param string $value Raw setting value.
 * @param int    $parts How many columns each line has.
 * @return array<int, string[]>
 */
function mkadmi_parse_lines( $value, $parts = 2 ) {
	$rows = array();

	foreach ( mkadmi_lines( $value ) as $line ) {
		$columns = array_map( 'trim', explode( '|', $line ) );
		$columns = array_slice( array_pad( $columns, $parts, '' ), 0, $parts );

		$rows[] = $columns;
	}

	return $rows;
}

/**
 * The figures strip: a count and what it counts.
 */
function mkadmi_stats() {
	if ( ! mkadmi_option( 'stats_show' ) ) {
		return;
	}

	$stats = mkadmi_parse_lines( mkadmi_option( 'stats_list' ), 2 );

	if ( ! $stats ) {
		return;
	}

	echo '<div class="stats"><div class="wrap stats__inner">';

	foreach ( $stats as $stat ) {
		printf(
			'<div class="stat"><span class="stat__figure">%1$s</span><span class="stat__label">%2$s</span></div>',
			esc_html( $stat[0] ),
			esc_html( $stat[1] )
		);
	}

	echo '</div></div>';
}

/**
 * Today's date and a clock per city.
 *
 * The times are rendered server-side so the strip is complete before any script
 * runs; theme.js then keeps them ticking. Each clock carries its time zone in a
 * data attribute rather than a pre-computed offset, so daylight saving is the
 * browser's problem, not ours.
 */
function mkadmi_clocks() {
	if ( ! mkadmi_option( 'clocks_show' ) ) {
		return;
	}

	$clocks = mkadmi_parse_lines( mkadmi_option( 'clocks_list' ), 2 );

	if ( ! $clocks ) {
		return;
	}

	echo '<div class="clocks">';

	printf(
		'<p class="clocks__date"><span class="clocks__weekday">%1$s</span><span class="clocks__day">%2$s</span></p>',
		esc_html( wp_date( _x( 'l', 'weekday format', 'mkadmi' ) ) ),
		esc_html( wp_date( _x( 'j F Y', 'date format', 'mkadmi' ) ) )
	);

	echo '<ul class="clocks__list">';

	foreach ( $clocks as $clock ) {
		list( $city, $zone ) = $clock;

		if ( '' === $city ) {
			continue;
		}

		$time   = '';
		$offset = '';

		try {
			$timezone = new DateTimeZone( $zone ? $zone : 'UTC' );
			$now      = new DateTime( 'now', $timezone );
			$time     = $now->format( 'H:i:s' );
			$offset   = 'GMT' . $now->format( 'P' );
			$offset   = str_replace( array( ':00', '+0', '-0' ), array( '', '+', '-' ), $offset );
		} catch ( Exception $e ) {
			// An unknown time zone should cost the city its clock, not the page.
			continue;
		}

		printf(
			'<li class="clock"><span class="clock__time" data-timezone="%1$s">%2$s</span><span class="clock__city">%3$s <span class="clock__offset">(%4$s)</span></span></li>',
			esc_attr( $zone ),
			esc_html( $time ),
			esc_html( $city ),
			esc_html( $offset )
		);
	}

	echo '</ul></div>';
}

/**
 * The contact lines in the profile band: affiliations, email, site and phones.
 */
function mkadmi_profile_contacts() {
	$affiliations = mkadmi_lines( mkadmi_option( 'profile_affiliations' ) );
	$emails       = mkadmi_lines( mkadmi_option( 'profile_emails' ) );
	$phones       = mkadmi_lines( mkadmi_option( 'profile_phones' ) );
	$website      = mkadmi_option( 'profile_website' );

	foreach ( $affiliations as $affiliation ) {
		printf(
			'<p class="profile__line"><span class="profile__icon" aria-hidden="true">%1$s</span>%2$s</p>',
			'&#128205;',
			esc_html( $affiliation )
		);
	}

	if ( $emails ) {
		echo '<p class="profile__line"><span class="profile__icon" aria-hidden="true">&#9993;</span>';
		printf( '<span class="screen-reader-text">%s </span>', esc_html__( 'Email:', 'mkadmi' ) );

		foreach ( $emails as $index => $email ) {
			if ( $index ) {
				echo '<span class="sep" aria-hidden="true"> · </span>';
			}

			printf( '<a href="%1$s">%2$s</a>', esc_url( 'mailto:' . $email ), esc_html( $email ) );
		}

		echo '</p>';
	}

	if ( $website || $phones ) {
		echo '<p class="profile__line">';

		if ( $website ) {
			printf(
				'<span class="profile__icon" aria-hidden="true">&#127760;</span><a href="%1$s">%2$s</a>',
				esc_url( $website ),
				esc_html( preg_replace( '#^https?://#', '', untrailingslashit( $website ) ) )
			);
		}

		foreach ( $phones as $phone ) {
			printf(
				'<span class="sep" aria-hidden="true"> · </span><span class="profile__icon" aria-hidden="true">&#9742;</span><a href="%1$s"><span class="screen-reader-text">%3$s </span>%2$s</a>',
				esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ),
				esc_html( $phone ),
				esc_html__( 'Telephone:', 'mkadmi' )
			);
		}

		echo '</p>';
	}
}

/**
 * Whether a language menu has been set up.
 *
 * @return bool
 */
function mkadmi_has_languages() {
	return has_nav_menu( 'language' );
}

/**
 * The language switcher.
 *
 * The languages themselves are menu items, so the theme makes no assumption
 * about how the site is translated: WordPress multisite, a translation plugin,
 * or plain links to a separate build all work the same way. An item's
 * description carries its short code (EN, AR); adding the CSS class "is-active"
 * to an item marks the language currently being read.
 *
 * @param string $variant "chips" for the masthead, "stack" for the sidebar.
 */
function mkadmi_language_switcher( $variant = 'chips' ) {
	if ( ! mkadmi_has_languages() ) {
		return;
	}

	wp_nav_menu(
		array(
			'theme_location'       => 'language',
			'container'            => 'nav',
			'container_class'      => 'languages languages--' . sanitize_html_class( $variant ),
			'container_aria_label' => esc_attr__( 'Language', 'mkadmi' ),
			'menu_class'           => 'languages__list',
			'depth'                => 1,
			'fallback_cb'          => false,
			'walker'               => new Mkadmi_Language_Walker(),
		)
	);
}

/**
 * Renders each language as a code and a name.
 */
class Mkadmi_Language_Walker extends Walker_Nav_Menu {

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
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$active  = in_array( 'is-active', $classes, true ) || in_array( 'current-menu-item', $classes, true );
		$code    = trim( (string) $item->description );

		$output .= sprintf(
			'<li class="languages__item"><a class="language%1$s" href="%2$s"%3$s>%4$s<span class="language__name">%5$s</span></a>',
			$active ? ' is-active' : '',
			esc_url( $item->url ),
			$active ? ' aria-current="true"' : '',
			$code ? '<span class="language__code">' . esc_html( $code ) . '</span>' : '',
			esc_html( $item->title )
		);
	}
}

/**
 * The masthead brand: the logo or monogram, and the name.
 */
function mkadmi_brand() {
	$name = mkadmi_option( 'brand_name' );
	$name = $name ? $name : get_bloginfo( 'name', 'display' );
	$mark = mkadmi_option( 'brand_monogram' );

	echo '<div class="brand">';

	printf( '<a class="brand__link" href="%s" rel="home">', esc_url( home_url( '/' ) ) );
	printf( '<span class="brand__name">%s</span>', esc_html( $name ) );

	if ( has_custom_logo() ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		$logo    = wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'brand__logo', 'alt' => '' ) );

		if ( $logo ) {
			printf( '<span class="brand__mark">%s</span>', $logo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by wp_get_attachment_image().
		}
	} elseif ( $mark ) {
		printf( '<span class="brand__mark" aria-hidden="true"><span class="brand__monogram">%s</span></span>', esc_html( $mark ) );
	}

	echo '</a></div>';
}

/**
 * The portrait in the profile band, from the theme setting or the header image.
 */
function mkadmi_profile_photo() {
	$photo_id = (int) mkadmi_option( 'profile_photo' );

	if ( $photo_id ) {
		$image = wp_get_attachment_image(
			$photo_id,
			'mkadmi-portrait',
			false,
			array(
				'class'    => 'profile__photo',
				'alt'      => '',
				'loading'  => 'eager',
				'decoding' => 'async',
			)
		);

		if ( $image ) {
			printf( '<div class="profile__frame">%s</div>', $image ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by wp_get_attachment_image().

			return;
		}
	}

	if ( get_header_image() ) {
		printf(
			'<div class="profile__frame"><img class="profile__photo" src="%1$s" width="%2$d" height="%3$d" alt="" /></div>',
			esc_url( get_header_image() ),
			(int) get_custom_header()->width,
			(int) get_custom_header()->height
		);
	}
}

/**
 * A section wrapper used by the homepage: heading, then the caller's markup.
 *
 * @param string $title Section heading.
 * @param string $id    Optional anchor.
 */
function mkadmi_section_open( $title, $id = '' ) {
	printf(
		'<section class="section%1$s"%2$s>',
		$id ? ' section--' . sanitize_html_class( $id ) : '',
		$id ? ' id="' . esc_attr( $id ) . '"' : ''
	);

	if ( '' !== $title ) {
		printf( '<h2 class="section__title">%s</h2>', esc_html( $title ) );
	}
}

/**
 * Close a section opened by mkadmi_section_open().
 */
function mkadmi_section_close() {
	echo '</section>';
}
