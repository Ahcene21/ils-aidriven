<?php
/**
 * Starter content.
 *
 * On a brand-new site, WordPress offers this as the customizer's starting
 * point: the menus, the pages, the sidebar panels and the homepage sections,
 * already wired together. Nothing is written to the database until the site
 * owner presses Publish.
 *
 * The professional details below are the ones the site publishes about itself.
 * Personal contact details — the email addresses and telephone numbers that
 * appear in the profile band — are deliberately left blank: they belong to the
 * site, not to the theme, and are filled in under Customize → Academic site →
 * Profile band.
 *
 * @package Mkadmi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Declare the starter content.
 */
function mkadmi_starter_content() {
	$content = array(
		'posts'       => array(
			'about'     => array(
				'post_type'  => 'page',
				'post_title' => __( 'Who am I?', 'mkadmi' ),
				'post_content' => __( 'A short biography: education, career, and the questions that drive the work.', 'mkadmi' ),
			),
			'cv'        => array(
				'post_type'  => 'page',
				'post_title' => __( 'Curriculum vitae', 'mkadmi' ),
				'post_content' => __( 'Degrees, positions, supervision, editorial work and academic service.', 'mkadmi' ),
			),
			'research'  => array(
				'post_type'  => 'page',
				'post_title' => __( 'Research', 'mkadmi' ),
				'post_content' => __( 'Current lines of enquiry, projects and collaborations.', 'mkadmi' ),
			),
			'contact'   => array(
				'post_type'  => 'page',
				'post_title' => __( 'Contact', 'mkadmi' ),
				'post_content' => __( 'How to get in touch, and where to find me.', 'mkadmi' ),
			),
			'home'      => array(
				'post_type'  => 'page',
				'post_title' => __( 'Home', 'mkadmi' ),
				'post_content' => '',
			),
		),

		'nav_menus'   => array(
			'primary'  => array(
				'name'  => __( 'Primary menu', 'mkadmi' ),
				'items' => array(
					'page_home'     => array( 'type' => 'post_type', 'object' => 'page', 'object_id' => '{{home}}' ),
					'page_about'    => array( 'type' => 'post_type', 'object' => 'page', 'object_id' => '{{about}}' ),
					'page_cv'       => array( 'type' => 'post_type', 'object' => 'page', 'object_id' => '{{cv}}' ),
					'page_research' => array( 'type' => 'post_type', 'object' => 'page', 'object_id' => '{{research}}' ),
					'teaching'      => array(
						'type'  => 'custom',
						'title' => __( 'Teaching', 'mkadmi' ),
						'url'   => home_url( '/teaching/' ),
					),
					'publications'  => array(
						'type'  => 'custom',
						'title' => __( 'Publications', 'mkadmi' ),
						'url'   => home_url( '/publications/' ),
					),
				),
			),
			'language' => array(
				'name'  => __( 'Language switcher', 'mkadmi' ),
				'items' => array(
					'english' => array(
						'type'        => 'custom',
						'title'       => __( 'English', 'mkadmi' ),
						'url'         => home_url( '/' ),
						'description' => 'EN',
					),
					'arabic'  => array(
						'type'        => 'custom',
						'title'       => 'العربية',
						'url'         => home_url( '/' ),
						'description' => 'AR',
					),
				),
			),
		),

		'options'     => array(
			'show_on_front'  => 'page',
			'page_on_front'  => '{{home}}',
			'blogname'       => __( 'Abderrazak Mkadmi', 'mkadmi' ),
			'blogdescription' => __( 'Assistant Professor in Information &amp; Library Sciences', 'mkadmi' ),
		),

		'theme_mods'  => array(
			'brand_monogram'  => 'AM',
			'profile_role'    => __( 'PhD · Habilitation · Assistant Professor in Information Sciences', 'mkadmi' ),
			'profile_affiliations' => __( "Department of Information Studies · Sultan Qaboos University · Muscat, Oman\nHigher Institute of Documentation (ISD) · University of Manouba · Tunisia", 'mkadmi' ),
			'clocks_show'     => true,
			'clocks_list'     => __( "Paris | Europe/Paris\nTunis | Africa/Tunis\nMuscat | Asia/Muscat", 'mkadmi' ),
			'stats_show'      => true,
			'stats_list'      => __( "93+ | scholarly publications\n25+ | years in academia\n5 | doctoral students supervised\n38 | conference papers\n37 | journal articles\n6 | books published", 'mkadmi' ),
			'topics_title'    => __( 'Current research topics', 'mkadmi' ),
			'topics_list'     => __( "🤖 | Artificial intelligence and archives\n☁ | Cloud archiving\n📦 | Big data governance\n🗄 | Long-term digital preservation\n♿ | Archive access for people with disabilities\n📚 | Digital libraries\n🔒 | The right to be forgotten\n🗂 | Records and archives management", 'mkadmi' ),
			'news_title'      => __( 'Selected academic news', 'mkadmi' ),
			'events_title'    => __( 'Conferences organised', 'mkadmi' ),
			'pubs_title'      => __( 'Books and refereed work', 'mkadmi' ),
			'pubs_more'       => __( 'See all publications', 'mkadmi' ),
			'courses_title'   => __( 'Teaching', 'mkadmi' ),
			'projects_title'  => __( 'Research projects', 'mkadmi' ),
			'footer_links'    => __( "Sultan Qaboos University | https://www.squ.edu.om/\nISD — Manouba | http://www.isd.rnu.tn/\nUniversity of Paris 8 | https://www.univ-paris8.fr/", 'mkadmi' ),
		),

		'widgets'     => array(
			'sidebar-1' => array(
				'mkadmi_nav_panel'  => array(
					'mkadmi_nav',
					array( 'title' => __( 'Navigation', 'mkadmi' ) ),
				),
				'mkadmi_profiles_panel' => array(
					'mkadmi_profiles',
					array(
						'title' => __( 'Academic profiles', 'mkadmi' ),
						'items' => __( "ORCID | https://orcid.org/ | iD\nScopus | https://www.scopus.com/ | SC\nResearchGate | https://www.researchgate.net/ | RG\nGoogle Scholar | https://scholar.google.com/ | GS", 'mkadmi' ),
						'ids'   => '',
					),
				),
				'mkadmi_links_panel' => array(
					'mkadmi_links',
					array(
						'title' => __( 'Quick links', 'mkadmi' ),
						'items' => __( "⬇ | Download the CV (PDF) | #\n📄 | Curriculum vitae (web) | #", 'mkadmi' ),
					),
				),
				'mkadmi_language_panel' => array(
					'mkadmi_language',
					array( 'title' => __( 'Language', 'mkadmi' ) ),
				),
				'mkadmi_visits_panel' => array(
					'mkadmi_visitors',
					array( 'title' => __( 'Visits', 'mkadmi' ) ),
				),
				'calendar' => array( 'calendar', array( 'title' => __( 'Calendar', 'mkadmi' ) ) ),
			),
		),
	);

	add_theme_support( 'starter-content', $content );
}
add_action( 'after_setup_theme', 'mkadmi_starter_content', 20 );
