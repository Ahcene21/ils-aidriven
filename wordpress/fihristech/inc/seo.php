<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Meta description, Open Graph and Organization schema (skipped when a SEO plugin is active). */
add_action( 'wp_head', function () {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}
	$desc = is_singular() && has_excerpt() ? get_the_excerpt() : wp_strip_all_tags( fh_opt( 'hero_lead' ) );
	$desc = wp_trim_words( $desc, 30, '…' );
	$img  = get_template_directory_uri() . '/assets/img/logo.png';
	if ( is_singular() && has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( null, 'large' );
	}
	printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $desc ) );
	printf( "<meta property=\"og:title\" content=\"%s\">\n<meta property=\"og:description\" content=\"%s\">\n<meta property=\"og:image\" content=\"%s\">\n<meta property=\"og:type\" content=\"website\">\n<meta name=\"theme-color\" content=\"#14375e\">\n", esc_attr( wp_get_document_title() ), esc_attr( $desc ), esc_url( $img ) );
	if ( is_front_page() ) {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'name'     => get_bloginfo( 'name' ),
			'url'      => home_url( '/' ),
			'logo'     => get_template_directory_uri() . '/assets/img/logo.png',
		);
		if ( fh_opt( 'contact_email' ) ) {
			$schema['email'] = fh_opt( 'contact_email' );
		}
		if ( fh_opt( 'contact_phone' ) ) {
			$schema['telephone'] = fh_opt( 'contact_phone' );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
}, 5 );
