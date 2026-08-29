=== Mkadmi ===

Contributors: ilsaidriven
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GNU General Public License v3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Tags: education, blog, two-columns, right-sidebar, left-sidebar, custom-colors, custom-menu, custom-logo, featured-images, translation-ready, rtl-language-support, accessibility-ready

An Arabic-first theme for an academic personal site: publications, teaching,
research and conferences, each with real fields.

== Description ==

Mkadmi gives a researcher the site their work actually needs. A green and gold
masthead carries the menu and the language switcher. Under it, a profile band
pairs the portrait with position, affiliations and contact details, and shows
the local time in each city the site spans. A strip of figures follows, then the
sections: current research topics, academic news, conferences organised, and an
illustrated list of books and refereed work.

The scholarly record is not typed into pages. Publications, courses, research
projects and conferences are each a post type with their own fields — authors,
year, venue, DOI, links; institution, level, academic year; role, funder,
period — so a new entry is a form to fill in, and the archives sort and group
themselves.

The right-hand column is built from widgets: section navigation, affiliations
with their emblems, academic profiles (ORCID, Scopus, ResearchGate, Google
Scholar), quick links, the language switcher, a visit counter and the calendar.

Written for Arabic first and laid out with CSS logical properties, so the same
rules read correctly in Arabic and in English. Nothing is loaded from anywhere
else: no web fonts, no CDNs, no third-party scripts, and the visit counter keeps
two integers and no personal data at all.

== Installation ==

1. Appearance → Themes → Add New → Upload Theme, choose mkadmi.zip, install and
   activate.
2. On a new site, Appearance → Customize offers the starter content: menus,
   pages, sidebar panels and homepage sections, already wired together. Publish
   it, then replace the text with your own.
3. Customize → Academic site holds the masthead, the profile band, the clocks,
   the figures, the colours, the homepage sections and the footer.
4. Appearance → Menus: build the Primary menu, and a Language switcher menu with
   one item per language. An item's description becomes its short code (EN, AR);
   add the CSS class "is-active" to the language currently being read.
5. Appearance → Widgets → Sidebar panels: add the Mkadmi panels you want.
6. Publications, Teaching, Research and Conferences appear in the admin menu.

== Frequently Asked Questions ==

= Is a translation plugin required for the language switcher? =

No. The switcher renders whatever is in the Language switcher menu, so it works
with a translation plugin, with WordPress multisite, or with plain links to a
separate site — the theme makes no assumption.

= Does the visit counter store anything about readers? =

No. It keeps a running total, a count for today and the date the count started.
No address, no identifier, no cookie.

= How do I add a second PDF, a HAL record or a publisher page to a publication? =

The publication's "Further links" field takes one per line, as "Label | URL".

== Changelog ==

= 1.0.0 =
* First release.
