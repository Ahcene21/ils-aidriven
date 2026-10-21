<?php
/**
 * Front page. Every block reads Customizer options and falls back to defaults.
 */
get_header();

$status = isset( $_GET['fh_contact'] ) ? sanitize_key( wp_unslash( $_GET['fh_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$stats  = array_filter( array_map( function ( $l ) {
	$p = array_map( 'trim', explode( '|', $l, 2 ) );
	return 2 === count( $p ) ? $p : null;
}, fh_lines( fh_opt( 'stats' ) ) ) );
?>
<section class="hero">
	<div class="container hero-grid">
		<div class="hero-copy">
			<span class="pill"><i></i><?php echo esc_html( fh_opt( 'hero_eyebrow' ) ); ?></span>
			<h1><?php echo esc_html( fh_opt( 'hero_title' ) ); ?> <em><?php echo esc_html( fh_opt( 'hero_accent' ) ); ?></em></h1>
			<p class="lead"><?php echo esc_html( fh_opt( 'hero_lead' ) ); ?></p>
			<div class="cta">
				<a class="btn btn-primary" href="<?php echo esc_url( fh_opt( 'hero_cta1_url' ) ); ?>"><?php echo esc_html( fh_opt( 'hero_cta1' ) ); ?> <span aria-hidden="true">→</span></a>
				<a class="btn btn-ghost" href="<?php echo esc_url( fh_opt( 'hero_cta2_url' ) ); ?>"><?php echo esc_html( fh_opt( 'hero_cta2' ) ); ?></a>
			</div>
		</div>
		<div class="hero-card">
			<ul class="pillars">
				<?php foreach ( fh_lines( fh_opt( 'pillars' ) ) as $p ) { echo '<li>' . esc_html( $p ) . '</li>'; } ?>
			</ul>
			<div class="steps">
				<?php foreach ( fh_lines( fh_opt( 'steps' ) ) as $s ) { echo '<span>' . esc_html( $s ) . '</span>'; } ?>
			</div>
		</div>
	</div>
</section>

<section class="stats">
	<div class="container stats-grid">
		<div class="stat stat-lead"><p><?php echo esc_html( fh_opt( 'strip' ) ); ?></p></div>
		<?php foreach ( $stats as $s ) :
			$num = preg_match( '/^(\d+)(\D*)$/', $s[0], $m ) ? $m : null; ?>
			<div class="stat">
				<b<?php echo $num ? ' data-count="' . (int) $num[1] . '" data-suffix="' . esc_attr( $num[2] ) . '"' : ''; ?>><?php echo esc_html( $s[0] ); ?></b>
				<span><?php echo esc_html( $s[1] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="section" id="solutions">
	<div class="container">
		<div class="eyebrow"><?php echo esc_html( fh_opt( 'sol_eyebrow' ) ); ?></div>
		<h2><?php echo esc_html( fh_opt( 'sol_title' ) ); ?></h2>
		<p class="sub"><?php echo esc_html( fh_opt( 'sol_lead' ) ); ?></p>
		<a class="link" href="<?php echo esc_url( get_post_type_archive_link( 'fh_solution' ) ); ?>"><?php esc_html_e( 'Explore all services', 'fihristech' ); ?> →</a>
		<div class="cards">
			<?php
			$sols = new WP_Query( array( 'post_type' => 'fh_solution', 'posts_per_page' => 8, 'orderby' => 'menu_order date', 'order' => 'ASC', 'no_found_rows' => true ) );
			if ( $sols->have_posts() ) {
				while ( $sols->have_posts() ) {
					$sols->the_post();
					get_template_part( 'template-parts/card', null, array(
						'icon' => get_post_meta( get_the_ID(), 'fh_icon', true ), 'title' => get_the_title(), 'text' => get_the_excerpt(),
						'bullets' => fh_lines( get_post_meta( get_the_ID(), 'fh_bullets', true ) ), 'url' => get_permalink(),
					) );
				}
				wp_reset_postdata();
			} else {
				foreach ( fh_default_solutions() as $d ) {
					get_template_part( 'template-parts/card', null, array( 'icon' => $d[0], 'title' => $d[1], 'text' => $d[2], 'bullets' => $d[3] ) );
				}
			}
			?>
		</div>
	</div>
</section>

<?php
$prod  = fh_default_product();
$query = new WP_Query( array( 'post_type' => 'fh_product', 'posts_per_page' => 1, 'orderby' => 'menu_order date', 'order' => 'ASC', 'no_found_rows' => true ) );
if ( $query->have_posts() ) {
	$query->the_post();
	$prod = array(
		'title' => get_the_title(), 'headline' => get_the_title(), 'text' => get_the_excerpt(),
		'features' => fh_lines( get_post_meta( get_the_ID(), 'fh_bullets', true ) ), 'url' => get_permalink(),
	);
	wp_reset_postdata();
}
?>
<section class="section alt" id="products">
	<div class="container">
		<div class="eyebrow"><?php esc_html_e( 'Explore our ecosystem', 'fihristech' ); ?></div>
		<article class="feature">
			<div>
				<span class="badge"><?php esc_html_e( 'Featured product', 'fihristech' ); ?></span>
				<h3 class="prod">▦ <?php echo esc_html( $prod['title'] ); ?></h3>
				<h2><?php echo esc_html( $prod['headline'] ); ?></h2>
				<p class="sub"><?php echo esc_html( $prod['text'] ); ?></p>
				<ul class="dots"><?php foreach ( $prod['features'] as $f ) { echo '<li>' . esc_html( $f ) . '</li>'; } ?></ul>
				<?php if ( ! empty( $prod['url'] ) ) : ?><a class="link" href="<?php echo esc_url( $prod['url'] ); ?>"><?php esc_html_e( 'View product', 'fihristech' ); ?> →</a><?php endif; ?>
			</div>
			<div class="browser" aria-hidden="true">
				<div class="bar"><i></i><i></i><i></i><span><?php echo esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ); ?></span></div>
				<div class="search"><?php esc_html_e( 'Search books, digital theses, archives…', 'fihristech' ); ?></div>
				<div class="results"><span></span><span></span><span></span></div>
			</div>
		</article>
	</div>
</section>

<?php
$news = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
if ( $news->have_posts() ) : ?>
<section class="section alt" id="news">
	<div class="container">
		<div class="eyebrow"><?php esc_html_e( 'Latest', 'fihristech' ); ?></div>
		<h2><?php echo esc_html( fh_opt( 'news_title' ) ); ?></h2>
		<a class="link" href="<?php echo esc_url( fh_news_url() ); ?>"><?php esc_html_e( 'All news', 'fihristech' ); ?> →</a>
		<div class="cards cards-3">
			<?php while ( $news->have_posts() ) { $news->the_post(); fh_post_card(); } wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section" id="about">
	<div class="container narrow">
		<div class="eyebrow"><?php esc_html_e( 'About', 'fihristech' ); ?> <?php bloginfo( 'name' ); ?></div>
		<h2><?php echo esc_html( fh_opt( 'about_title' ) ); ?></h2>
		<p class="sub"><?php echo esc_html( fh_opt( 'about_text' ) ); ?></p>
	</div>
</section>

<section class="section cta-band" id="contact">
	<div class="container narrow center">
		<h2><?php echo esc_html( fh_opt( 'contact_title' ) ); ?></h2>
		<p class="sub"><?php echo esc_html( fh_opt( 'contact_lead' ) ); ?></p>
		<?php if ( 'sent' === $status ) : ?><div class="notice ok" role="status"><?php esc_html_e( 'Thank you — we will be in touch shortly.', 'fihristech' ); ?></div><?php endif; ?>
		<?php if ( 'invalid' === $status ) : ?><div class="notice err" role="alert"><?php esc_html_e( 'Please fill in your name, a valid email and a message.', 'fihristech' ); ?></div><?php endif; ?>
		<?php if ( 'error' === $status ) : ?><div class="notice err" role="alert"><?php esc_html_e( 'Sorry, we could not send your message. Please try again later.', 'fihristech' ); ?></div><?php endif; ?>
		<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="fh_contact">
			<input type="hidden" name="fh_ts" value="<?php echo esc_attr( time() ); ?>">
			<?php wp_nonce_field( 'fh_contact', 'fh_nonce' ); ?>
			<input class="hp" type="text" name="fh_website" tabindex="-1" autocomplete="off" aria-hidden="true">
			<input required name="fh_name" maxlength="120" placeholder="<?php esc_attr_e( 'Full name', 'fihristech' ); ?>" aria-label="<?php esc_attr_e( 'Full name', 'fihristech' ); ?>">
			<input required type="email" name="fh_email" maxlength="160" placeholder="<?php esc_attr_e( 'Email', 'fihristech' ); ?>" aria-label="<?php esc_attr_e( 'Email', 'fihristech' ); ?>">
			<textarea required rows="4" name="fh_message" maxlength="5000" placeholder="<?php esc_attr_e( 'How can we help?', 'fihristech' ); ?>" aria-label="<?php esc_attr_e( 'Message', 'fihristech' ); ?>"></textarea>
			<button class="btn btn-primary" type="submit"><?php esc_html_e( 'Send message', 'fihristech' ); ?></button>
		</form>
		<ul class="contact-info">
			<?php if ( fh_opt( 'contact_email' ) ) : ?><li><a href="mailto:<?php echo esc_attr( antispambot( fh_opt( 'contact_email' ) ) ); ?>"><?php echo esc_html( fh_opt( 'contact_email' ) ); ?></a></li><?php endif; ?>
			<?php if ( fh_opt( 'contact_phone' ) ) : ?><li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', fh_opt( 'contact_phone' ) ) ); ?>"><?php echo esc_html( fh_opt( 'contact_phone' ) ); ?></a></li><?php endif; ?>
			<?php if ( fh_opt( 'contact_addr' ) ) : ?><li><?php echo esc_html( fh_opt( 'contact_addr' ) ); ?></li><?php endif; ?>
		</ul>
	</div>
</section>
<?php
get_footer();
