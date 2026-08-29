<?php
/**
 * Palette generation.
 *
 * The two colours picked in the customizer are expanded into the handful of
 * shades the stylesheet asks for, so a site owner never has to choose a "dark
 * green" and a "green tint" that go together — the theme derives them.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Turn a hex colour into an array of red, green and blue.
 *
 * @param string $hex Hex colour, with or without the leading hash.
 * @return array{0:int,1:int,2:int}|null Null when the value is not a colour.
 */
function mkadmi_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return null;
	}

	return array(
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
	);
}

/**
 * Mix a colour towards black or white.
 *
 * @param string $hex    Base colour.
 * @param float  $amount Between -1 (black) and 1 (white).
 * @return string Hex colour.
 */
function mkadmi_shade( $hex, $amount ) {
	$rgb = mkadmi_hex_to_rgb( $hex );

	if ( ! $rgb ) {
		return $hex;
	}

	$target = $amount > 0 ? 255 : 0;
	$weight = min( 1, abs( (float) $amount ) );

	foreach ( $rgb as $index => $channel ) {
		$rgb[ $index ] = (int) round( $channel + ( $target - $channel ) * $weight );
	}

	return sprintf( '#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2] );
}

/**
 * The comma-separated channels of a colour, for use in rgba().
 *
 * @param string $hex Hex colour.
 * @return string
 */
function mkadmi_rgb_channels( $hex ) {
	$rgb = mkadmi_hex_to_rgb( $hex );

	return $rgb ? implode( ', ', $rgb ) : '0, 0, 0';
}

/**
 * Relative luminance, per WCAG 2.1.
 *
 * @param string $hex Hex colour.
 * @return float Between 0 and 1.
 */
function mkadmi_luminance( $hex ) {
	$rgb = mkadmi_hex_to_rgb( $hex );

	if ( ! $rgb ) {
		return 0.0;
	}

	$channels = array();

	foreach ( $rgb as $channel ) {
		$value      = $channel / 255;
		$channels[] = $value <= 0.03928 ? $value / 12.92 : pow( ( $value + 0.055 ) / 1.055, 2.4 );
	}

	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

/**
 * Black or white, whichever reads better on the given background.
 *
 * A site owner is free to pick a pale green; the text on it still has to be
 * legible, so the contrasting colour is computed rather than assumed.
 *
 * @param string $hex Background colour.
 * @return string
 */
function mkadmi_contrast_color( $hex ) {
	return mkadmi_luminance( $hex ) > 0.45 ? '#101413' : '#ffffff';
}

/**
 * The custom properties the stylesheet reads, built from the two settings.
 *
 * @return string CSS, ready to be inlined.
 */
function mkadmi_customizer_css() {
	$primary = mkadmi_option( 'color_primary' );
	$accent  = mkadmi_option( 'color_accent' );

	if ( ! mkadmi_hex_to_rgb( $primary ) ) {
		$primary = '#0e5540';
	}

	if ( ! mkadmi_hex_to_rgb( $accent ) ) {
		$accent = '#c8912b';
	}

	$vars = array(
		'--mk-primary'          => $primary,
		'--mk-primary-rgb'      => mkadmi_rgb_channels( $primary ),
		'--mk-primary-dark'     => mkadmi_shade( $primary, -0.32 ),
		'--mk-primary-darker'   => mkadmi_shade( $primary, -0.55 ),
		'--mk-primary-light'    => mkadmi_shade( $primary, 0.18 ),
		'--mk-primary-tint'     => mkadmi_shade( $primary, 0.92 ),
		'--mk-primary-tint-2'   => mkadmi_shade( $primary, 0.84 ),
		'--mk-on-primary'       => mkadmi_contrast_color( $primary ),
		'--mk-accent'           => $accent,
		'--mk-accent-rgb'       => mkadmi_rgb_channels( $accent ),
		'--mk-accent-dark'      => mkadmi_shade( $accent, -0.22 ),
		'--mk-accent-light'     => mkadmi_shade( $accent, 0.35 ),
		'--mk-accent-tint'      => mkadmi_shade( $accent, 0.88 ),
		'--mk-on-accent'        => mkadmi_contrast_color( $accent ),
	);

	$declarations = '';

	foreach ( $vars as $property => $value ) {
		$declarations .= sprintf( '%s:%s;', $property, $value );
	}

	return ':root{' . $declarations . '}';
}
