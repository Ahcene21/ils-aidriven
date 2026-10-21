<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', function ( $wp ) {
	$defaults = fh_defaults();

	$sections = array(
		'fh_hero'    => array( __( 'FihrisTech: Hero', 'fihristech' ), array(
			'hero_eyebrow' => array( __( 'Badge text', 'fihristech' ), 'text' ),
			'hero_title'   => array( __( 'Headline', 'fihristech' ), 'text' ),
			'hero_accent'  => array( __( 'Headline accent (coloured)', 'fihristech' ), 'text' ),
			'hero_lead'    => array( __( 'Intro paragraph', 'fihristech' ), 'textarea' ),
			'hero_cta1'    => array( __( 'Primary button label', 'fihristech' ), 'text' ),
			'hero_cta1_url'=> array( __( 'Primary button link', 'fihristech' ), 'url_or_anchor' ),
			'hero_cta2'    => array( __( 'Secondary button label', 'fihristech' ), 'text' ),
			'hero_cta2_url'=> array( __( 'Secondary button link', 'fihristech' ), 'url_or_anchor' ),
			'pillars'      => array( __( 'Pillars (one per line)', 'fihristech' ), 'textarea' ),
			'steps'        => array( __( 'Steps (one per line)', 'fihristech' ), 'textarea' ),
		) ),
		'fh_stats'   => array( __( 'FihrisTech: Stats', 'fihristech' ), array(
			'strip' => array( __( 'Strip message', 'fihristech' ), 'text' ),
			'stats' => array( __( 'Stats: one per line as value|label', 'fihristech' ), 'textarea' ),
		) ),
		'fh_content' => array( __( 'FihrisTech: Sections', 'fihristech' ), array(
			'sol_eyebrow'   => array( __( 'Solutions: eyebrow', 'fihristech' ), 'text' ),
			'sol_title'     => array( __( 'Solutions: title', 'fihristech' ), 'text' ),
			'sol_lead'      => array( __( 'Solutions: intro', 'fihristech' ), 'textarea' ),
			'about_title'   => array( __( 'About: title', 'fihristech' ), 'text' ),
			'about_text'    => array( __( 'About: text', 'fihristech' ), 'textarea' ),
		) ),
		'fh_contact' => array( __( 'FihrisTech: Contact', 'fihristech' ), array(
			'contact_title' => array( __( 'Heading', 'fihristech' ), 'text' ),
			'contact_lead'  => array( __( 'Intro', 'fihristech' ), 'text' ),
			'contact_email' => array( __( 'Email (also receives form messages)', 'fihristech' ), 'email' ),
			'contact_phone' => array( __( 'Phone', 'fihristech' ), 'text' ),
			'contact_addr'  => array( __( 'Address', 'fihristech' ), 'text' ),
		) ),
	);

	$priority = 30;
	foreach ( $sections as $sid => $section ) {
		$wp->add_section( $sid, array( 'title' => $section[0], 'priority' => $priority++ ) );
		foreach ( $section[1] as $key => $field ) {
			$kind = $field[1];
			$san  = 'sanitize_text_field';
			if ( 'textarea' === $kind ) {
				$san = 'sanitize_textarea_field';
			} elseif ( 'email' === $kind ) {
				$san = 'sanitize_email';
			} elseif ( 'url_or_anchor' === $kind ) {
				$san = function ( $v ) {
					return 0 === strpos( $v, '#' ) ? sanitize_text_field( $v ) : esc_url_raw( $v );
				};
			}
			$wp->add_setting( $key, array( 'default' => $defaults[ $key ], 'sanitize_callback' => $san, 'transport' => 'refresh' ) );
			$wp->add_control( $key, array( 'label' => $field[0], 'section' => $sid, 'type' => 'url_or_anchor' === $kind ? 'text' : $kind ) );
		}
	}
} );
