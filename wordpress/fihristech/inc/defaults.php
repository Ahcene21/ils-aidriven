<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Default content so a fresh install looks finished before anything is edited. Translatable. */
function fh_defaults() {
	return array(
		'hero_eyebrow'  => __( 'Libraries · Archives · Knowledge · People', 'fihristech' ),
		'hero_title'    => __( 'Knowledge Builds Brighter', 'fihristech' ),
		'hero_accent'   => __( 'Tomorrows.', 'fihristech' ),
		'hero_lead'     => __( 'Integrated library, archiving, and knowledge technology solutions for libraries, archives, research centers, and cultural institutions through consultancy, advanced platforms, and long-term technical support.', 'fihristech' ),
		'hero_cta1'     => __( 'Explore our solutions', 'fihristech' ),
		'hero_cta1_url' => '#solutions',
		'hero_cta2'     => __( 'Discuss your project', 'fihristech' ),
		'hero_cta2_url' => '#contact',
		'pillars'       => implode( "\n", array( __( 'Libraries', 'fihristech' ), __( 'Archives', 'fihristech' ), __( 'Research', 'fihristech' ), __( 'Communities', 'fihristech' ) ) ),
		'steps'         => implode( "\n", array( __( 'Plan', 'fihristech' ), __( 'Implement', 'fihristech' ), __( 'Support', 'fihristech' ), __( 'Grow', 'fihristech' ) ) ),
		'strip'         => __( 'Experience. Expertise. Lasting Partnerships.', 'fihristech' ),
		'stats'         => implode( "\n", array(
			'15+|' . __( 'Years Experience', 'fihristech' ),
			'12|' . __( 'Open Platforms', 'fihristech' ),
			'10|' . __( 'FihrisTech Products', 'fihristech' ),
			'16|' . __( 'Institutional Relationships', 'fihristech' ),
		) ),
		'sol_eyebrow'   => __( 'Our Solutions', 'fihristech' ),
		'sol_title'     => __( 'End-to-End Solutions for the Knowledge Lifecycle', 'fihristech' ),
		'sol_lead'      => __( 'From strategic planning and collection development to digital transformation and preservation, FihrisTech brings together consultancy, technology, and ongoing support.', 'fihristech' ),
		'news_title'    => __( 'News & Insights', 'fihristech' ),
		'about_title'   => __( 'Smart Library Solutions · حلول ذكية للمكتبات', 'fihristech' ),
		'about_text'    => __( 'FihrisTech (فهرستك) partners with institutions to turn collections into living knowledge services. Our team combines library science, archival practice, and open-source engineering to deliver solutions that last.', 'fihristech' ),
		'contact_title' => __( "Let's build your knowledge future", 'fihristech' ),
		'contact_lead'  => __( 'Tell us about your library, archive, or research project.', 'fihristech' ),
		'contact_email' => '',
		'contact_phone' => '',
		'contact_addr'  => '',
	);
}

/** Fallback cards shown until real Solution posts exist: icon, title, text, bullets. */
function fh_default_solutions() {
	return array(
		array( '📖', __( 'Library Solutions', 'fihristech' ), __( 'Plan, develop, and operate modern libraries through consultancy, collection services, technologies, and professional support.', 'fihristech' ), array( __( 'Library strategy', 'fihristech' ), __( 'Operations support', 'fihristech' ), __( 'Integrated library systems', 'fihristech' ) ) ),
		array( '🗄️', __( 'Archive Solutions', 'fihristech' ), __( 'Organize, digitize, and preserve institutional and national records with trusted archival platforms and practices.', 'fihristech' ), array( __( 'Records management', 'fihristech' ), __( 'Digitization', 'fihristech' ), __( 'Digital preservation', 'fihristech' ) ) ),
		array( '🎓', __( 'Research Services', 'fihristech' ), __( 'Open repositories and research infrastructure that make scholarly output discoverable and citable.', 'fihristech' ), array( __( 'Institutional repositories', 'fihristech' ), __( 'Metadata and discovery', 'fihristech' ), __( 'Research analytics', 'fihristech' ) ) ),
		array( '👥', __( 'Training & Communities', 'fihristech' ), __( 'Build capability and communities of practice around your teams, users, and partners.', 'fihristech' ), array( __( 'Professional training', 'fihristech' ), __( 'Workshops', 'fihristech' ), __( 'Community engagement', 'fihristech' ) ) ),
	);
}

function fh_default_product() {
	return array(
		'title'    => __( 'FihrisTech Portal', 'fihristech' ),
		'headline' => __( 'One Portal. Every Library Experience.', 'fihristech' ),
		'text'     => __( "A unified, intelligent discovery platform that brings your library's digital catalogs, subscribed databases, and physical resources together in one seamless interface.", 'fihristech' ),
		'features' => array(
			__( 'Unified discovery, federated search, and single sign-on.', 'fihristech' ),
			__( 'Modern, responsive, and multilingual user experience.', 'fihristech' ),
			__( 'Scalable architecture with seamless integration to Koha, FOLIO, and DSpace.', 'fihristech' ),
		),
	);
}

/** Demo content offered in the Customizer on a fresh site (Starter Content). */
function fh_starter_content() {
	$sols = fh_default_solutions();
	$posts = array();
	foreach ( $sols as $i => $s ) {
		$posts[ 'solution_' . $i ] = array(
			'post_type' => 'fh_solution', 'post_title' => $s[1], 'post_excerpt' => $s[2],
			'post_content' => $s[2], 'menu_order' => $i,
			'meta_input' => array( 'fh_icon' => $s[0], 'fh_bullets' => implode( "\n", $s[3] ) ),
		);
	}
	$p = fh_default_product();
	$posts['product_portal'] = array(
		'post_type' => 'fh_product', 'post_title' => $p['title'], 'post_excerpt' => $p['text'], 'post_content' => $p['text'],
		'meta_input' => array( 'fh_icon' => '▦', 'fh_bullets' => implode( "\n", $p['features'] ) ),
	);
	$posts['news_1'] = array( 'post_type' => 'post', 'post_title' => __( 'Welcome to our new website', 'fihristech' ), 'post_excerpt' => __( 'We are launching a new home for our solutions, products and news.', 'fihristech' ), 'post_content' => __( 'We are launching a new home for our solutions, products and news. Check back soon for updates on projects, platforms and events.', 'fihristech' ) );
	$posts['news_2'] = array( 'post_type' => 'post', 'post_title' => __( 'Open platforms for modern libraries', 'fihristech' ), 'post_excerpt' => __( 'Why open-source library platforms give institutions control over their future.', 'fihristech' ), 'post_content' => __( 'Why open-source library platforms such as Koha, FOLIO and DSpace give institutions control over their future.', 'fihristech' ) );
	return array( 'posts' => $posts );
}
