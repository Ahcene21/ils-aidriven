<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Default content so a fresh install looks finished before anything is edited. */
function fh_defaults() {
	return array(
		'hero_eyebrow'  => 'Libraries · Archives · Knowledge · People',
		'hero_title'    => 'Knowledge Builds Brighter',
		'hero_accent'   => 'Tomorrows.',
		'hero_lead'     => 'Integrated library, archiving, and knowledge technology solutions for libraries, archives, research centers, and cultural institutions through consultancy, advanced platforms, and long-term technical support.',
		'hero_cta1'     => 'Explore our solutions',
		'hero_cta1_url' => '#solutions',
		'hero_cta2'     => 'Discuss your project',
		'hero_cta2_url' => '#contact',
		'pillars'       => "Libraries\nArchives\nResearch\nCommunities",
		'steps'         => "Plan\nImplement\nSupport\nGrow",
		'strip'         => 'Experience. Expertise. Lasting Partnerships.',
		'stats'         => "15+|Years Experience\n12|Open Platforms\n10|FihrisTech Products\n16|Institutional Relationships",
		'sol_eyebrow'   => 'Our Solutions',
		'sol_title'     => 'End-to-End Solutions for the Knowledge Lifecycle',
		'sol_lead'      => 'From strategic planning and collection development to digital transformation and preservation, FihrisTech brings together consultancy, technology, and ongoing support.',
		'about_title'   => 'Smart Library Solutions · حلول ذكية للمكتبات',
		'about_text'    => 'FihrisTech (فهرستك) partners with institutions to turn collections into living knowledge services. Our team combines library science, archival practice, and open-source engineering to deliver solutions that last.',
		'contact_title' => "Let's build your knowledge future",
		'contact_lead'  => 'Tell us about your library, archive, or research project.',
		'contact_email' => '',
		'contact_phone' => '',
		'contact_addr'  => '',
	);
}

/** Fallback cards shown until real Solution / Product posts exist. */
function fh_default_solutions() {
	return array(
		array( '📖', 'Library Solutions', 'Plan, develop, and operate modern libraries through consultancy, collection services, technologies, and professional support.', array( 'Library strategy', 'Operations support', 'Integrated library systems' ) ),
		array( '🗄️', 'Archive Solutions', 'Organize, digitize, and preserve institutional and national records with trusted archival platforms and practices.', array( 'Records management', 'Digitization', 'Digital preservation' ) ),
		array( '🎓', 'Research Services', 'Open repositories and research infrastructure that make scholarly output discoverable and citable.', array( 'Institutional repositories', 'Metadata and discovery', 'Research analytics' ) ),
		array( '👥', 'Training & Communities', 'Build capability and communities of practice around your teams, users, and partners.', array( 'Professional training', 'Workshops', 'Community engagement' ) ),
	);
}

function fh_default_product() {
	return array(
		'title'    => 'FihrisTech Portal',
		'headline' => 'One Portal. Every Library Experience.',
		'text'     => "A unified, intelligent discovery platform that brings your library's digital catalogs, subscribed databases, and physical resources together in one seamless interface.",
		'features' => array(
			'Unified discovery, federated search, and single sign-on.',
			'Modern, responsive, and multilingual user experience.',
			'Scalable architecture with seamless integration to Koha, FOLIO, and DSpace.',
		),
	);
}
