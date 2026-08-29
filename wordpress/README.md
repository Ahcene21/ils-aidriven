# Mkadmi — a WordPress theme

A WordPress theme for an academic personal site, modelled on
[www.mkadmi.tn](https://www.mkadmi.tn/): the personal site of Dr Abderrazak
Mkadmi, Assistant Professor in Information & Library Sciences (Sultan Qaboos
University, Oman; ISD, University of Manouba, Tunisia).

The theme lives in [`mkadmi/`](mkadmi). It is a classic (PHP template) theme
with `theme.json` support, not a block theme — the homepage is a fixed sequence
of sections driven by the customizer, which is what the reference site does.

```
mkadmi/
├── style.css                     theme header (metadata only)
├── theme.json                    editor palette and typography
├── functions.php                 loads inc/
├── inc/
│   ├── setup.php                 theme supports, menus, widget areas
│   ├── assets.php                styles and scripts
│   ├── post-types.php            publications, courses, projects, conferences
│   ├── meta-boxes.php            the editing UI for their fields
│   ├── template-tags.php         helpers used by the templates
│   ├── widgets.php               the six sidebar panels
│   ├── customizer.php            settings, generated from one declaration
│   ├── customizer-css.php        palette generation from two colours
│   └── starter-content.php       what a fresh site is offered
├── templates/full-width.php      page template without the sidebar
├── template-parts/
│   ├── profile-band.php          portrait, titles, contact, clocks
│   ├── home/                     one file per homepage section
│   └── content/                  entries and single views per post type
├── assets/{css,js}
├── languages/                    mkadmi.pot, ar.po, ar.mo
└── screenshot.png
```

## Installing

Upload `mkadmi.zip` under **Appearance → Themes → Add New → Upload Theme**, or
copy the `mkadmi/` folder into `wp-content/themes/`.

On a site with no content yet, **Appearance → Customize** offers the theme's
starter content — menus, pages, sidebar panels and homepage sections already
wired together. Publish it and edit from there.

## Reproducing the reference homepage

Everything below is entered through the admin; no template needs editing.

**Customize → Academic site**

| Section | What goes in it |
| --- | --- |
| Masthead | The monogram for the gold frame (or a logo image under Site Identity), and the name. |
| Profile band | Portrait, the titles line, affiliations (one per line), email addresses, website, telephone numbers, CV. |
| Date and clocks | One per line, `City | Time zone` — e.g. `Muscat | Asia/Muscat`. Times are rendered server-side and then kept ticking by the browser, which also resolves daylight saving. |
| Figures | One per line, `Figure | Label` — e.g. `93+ | scholarly publications`. |
| Colours | The green and the gold. Every other shade in the palette, and the black-or-white text on each, is derived from those two. |
| Homepage sections | A switch, a heading and a count for each section: research topics, news, conferences, publications, teaching, projects. |
| Footer | The institution links, and the copyright line. |

**Appearance → Menus**

- *Primary menu* — the row in the masthead.
- *Language switcher* — one item per language. The item's **description** is its
  short code (`EN`, `AR`); adding the CSS class `is-active` marks the language
  being read. (Enable Description and CSS Classes under Screen Options.)

**Appearance → Widgets → Sidebar panels**

Add *Mkadmi: section navigation*, *affiliations*, *academic profiles*, *quick
links*, *language*, *visits*, and the core *Calendar*. Each panel's list is a
textarea, one entry per line, with the columns named in its own help text.

**Content**

| Admin menu | Fields |
| --- | --- |
| Publications | Authors, year, journal/book/conference, publisher, volume-issue-pages, DOI, ISBN/ISSN, link, PDF, and "Further links" (`Label \| URL`, one per line) for the publisher page, HAL, Zenodo, a second chapter PDF. The cover is the featured image. |
| Teaching | Course code, institution, academic year, hours, syllabus link; levels as a taxonomy. |
| Research | Role, funder, partners, period, status, link. |
| Conferences | Badge (`CIBAHN-20`), subtitle, place and proceedings, link. |
| Posts | Used for the homepage news list; a post's "Icon" field is the emoji beside it. |

The publications archive groups entries under a heading per year and sorts on
the year field, so a publication with no year still appears.

## Languages

The theme ships `languages/mkadmi.pot` and a complete Arabic translation
(`ar.po` / `ar.mo`, 257 strings). Setting the site language to العربية switches
the interface and puts the page in RTL. The layout itself is written with CSS
logical properties, so both directions come from the same rules — there is no
`rtl.css` to keep in step.

## Notes

- No external requests: no web fonts, no CDNs, no analytics, no social widgets.
- The visits panel stores a total, a count for today, and the date counting
  started. Nothing about the reader.
- No build step. The CSS and JS in `assets/` are the files that ship.
- Colour contrast is computed, not assumed: pick a pale green and the text on it
  turns dark automatically.

## Licence

GNU GPL v3, as with the rest of this repository.
