# FihrisTech WordPress theme

Custom bilingual (English / Arabic, RTL-ready) theme for FihrisTech — Smart Library Solutions.

## Install
1. Zip this folder (`cd wordpress && zip -r fihristech.zip fihristech`) and upload it in **Appearance → Themes → Add New → Upload**, or copy it to `wp-content/themes/fihristech`.
2. Activate it. The front page works immediately with default content.
3. Needs WordPress 6.3+ and PHP 7.4+.

## Edit content
- **Appearance → Customize → FihrisTech: Hero / Stats / Sections / Contact** — headline, buttons, pillars, stats (`value|label` per line), About text, contact details.
- **Solutions** and **Products** (admin menu) — each post becomes a card: title, excerpt (description), icon and bullet points (the "Card details" box). The first Product feeds the featured block. With none created, built-in defaults show.
- **Appearance → Menus** — assign a *Primary* (and optional *Footer*) menu; without one, anchor links are used.
- **Appearance → Customize → Site Identity** — upload a custom logo to replace the bundled one.

## News and solution pages
- **Posts** are the news section: the latest three appear on the homepage (hidden until a post exists) and all posts list at the posts page (Settings → Reading → Posts page). Featured images and excerpts show on the cards.
- Each **Solution** and **Product** has its own page (`/solutions/...`, `/products/...`) with icon, description, "What is included" list, related items and a contact button. Archives live at `/solutions/` and `/products/`.
- On a fresh site, **Customize → Starter Content** offers demo solutions, a product and two news posts.

## Contact form
Nonce-protected, honeypot, minimum fill time and a 5/hour per-IP limit. Messages are saved under **Messages** in the dashboard and emailed to the Contact email (or the site admin email). For reliable delivery install an SMTP plugin.

## Arabic / RTL
Set the site language to Arabic (Settings → General) or use a multilingual plugin such as Polylang; `rtl.css` and the Tajawal font load automatically for RTL locales. Theme strings use the `fihristech` text domain with a complete Arabic translation in `languages/ar.po` / `ar.mo` (frontend text; Customizer labels stay English). Recompile after editing the `.po`, e.g. with Poedit or `msgfmt`.

## Notes
- Basic SEO (meta description, Open Graph, Organization schema) is output unless Yoast, Rank Math or AIOSEO is active.
- Fonts load from Google Fonts; self-host them if privacy rules require.
- After activating, visit Settings → Permalinks and click Save once so `/solutions/` and `/products/` resolve.
- Not yet tested on a live WordPress install; PHP files pass `php -l`.
