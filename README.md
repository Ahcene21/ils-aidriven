# ILS AI-Driven — an OJS journal theme

A configurable, accessible, reading-first theme plugin for
[Open Journal Systems](https://pkp.sfu.ca/software/ojs/).

It is a standalone theme (it does not inherit from the OJS default theme), built
around a token-based design system: one primary colour picked in the admin UI
drives the whole palette, in both light and dark mode.

---

## Features

**Layout**

- Three-column layout: a theme-owned menu rail on the left, the content column,
  and the usual OJS block sidebar on the right. Either side column disappears
  when it has nothing in it, and the whole thing is switchable to two columns or
  one from the admin UI.
- The left rail is a normal OJS navigation menu assigned to the theme's
  `sidebar` menu area, so editors build it under Website Settings → Navigation
  rather than by editing a template.
- Utility bar above the masthead carrying the ISSNs and the user menu.

**Articles and listings**

- Article cards with cover-image thumbnails, linked author names (each opens an
  author search), page range, DOI, abstract snippet and galley buttons.
- Focus mode: clicking a cover image enlarges it over a dimmed, blurred page.
- Optional metrics badge (Dimensions, Altmetric or PlumX) on cards and article
  pages, keyed off the article's DOI.
- Share links for Facebook, X, LinkedIn, WhatsApp, Telegram, Reddit, Mendeley
  and email, plus copy-link. Plain anchors — no vendor widgets, so nothing is
  requested from those services until a reader clicks.
- Issue archive grouped under a heading per year, with core pagination intact.
- "Journal information" table on the homepage, built from journal settings.

**Design**

- Token-driven palette — the journal's primary colour tints the neutral greys, so
  the interface stays coherent for any brand colour.
- Light / dark / follow-the-device colour scheme, with a masthead toggle whose
  choice is remembered per reader. The scheme is applied before first paint, so
  dark-mode readers never get a white flash.
- Three masthead layouts, three content widths, three corner styles, three
  typographic pairings and three text sizes — all from Website Settings.
- Homepage opens with the journal profile — cover image beside the description,
  with ISSN, publisher and current issue alongside it — then journal
  information, announcements, latest articles and the current issue's table of
  contents. A magazine variant with a full hero banner is one option away.
- Section headings are filled bars in the journal's own colour, so a dense
  homepage stays scannable.

**Reading**

- Sticky, self-collapsing masthead with an inline journal search.
- Reading progress bar on article pages.
- Automatic "On this page" outline built from an article's headings, with
  scroll-spy highlighting. Appears only when an article has enough structure to
  need it.
- Sticky article metadata column that scrolls independently on large screens.
- Copy-to-clipboard for the DOI and for the rendered citation.
- Wide tables in author-supplied content scroll in place instead of stretching
  the page; off-site links are flagged.

**Standards**

- Schema.org `ScholarlyArticle` JSON-LD on article pages, complementing the
  Dublin Core and Google Scholar tags OJS already emits.
- WCAG-minded: a real skip link, one consistent focus ring, keyboard-operable
  submenus, `prefers-reduced-motion` support, and screen-reader labels on every
  icon-only control.
- Full right-to-left support and a dedicated print stylesheet that drops the
  page chrome and expands link destinations.
- No tracking, no external requests. Web fonts are opt-in; the default is a
  system font stack.

---

## Requirements

| | |
|---|---|
| OJS | 3.4.x or 3.5.x |
| PHP | 8.0+ |
| Build step | none — OJS compiles the LESS itself |

The theme deliberately restricts itself to long-standing OJS template variables
and helpers (`{load_menu}`, `{url}`, `{translate}`, `$displayPageHeaderTitle`,
`$publicFilesDir`, …) and reads entity data through `getData()` /
`getLocalizedData()`, so it is resilient across point releases. Where an API is
less stable — the "latest articles" query and the structured-data builder — the
call is wrapped so a failure logs and degrades to nothing rather than taking the
site down.

---

## Installation

Clone the repository into your OJS installation. **The directory must be named
`ilsAiDriven`**, because OJS derives the plugin's namespace from it:

```bash
cd /path/to/ojs/plugins/themes
git clone https://github.com/Ahcene21/ils-aidriven.git ilsAiDriven
```

Then, in the OJS admin interface:

1. **Settings → Website → Plugins → Theme Plugins** — enable *ILS AI-Driven Theme*.
2. **Settings → Website → Appearance → Theme** — select it and press *Save*.
3. Configure the options that appear underneath the theme selector.

To install as an archive instead, zip the contents of this repository into a
folder named `ilsAiDriven` and upload it under **Plugins → Upload A New Plugin**.

After changing a theme option, OJS recompiles the stylesheet. If you are
iterating on the LESS sources directly, clear the caches:

```bash
php tools/clearCache.php     # or: Administration → Clear Data Caches
```

---

## Options

All options live under **Website Settings → Appearance** once the theme is active.

| Option | Default | Notes |
|---|---|---|
| Primary colour | `#0F4C81` | Drives links, buttons, masthead and the grey ramp |
| Accent colour | `#C2410C` | Highlights, announcement badges, focus ring |
| Typography | Serif headings / sans body | Also: all-sans, all-serif |
| Base text size | Regular | Compact (15px), Regular (16px), Large (18px) |
| Content width | Wide | Narrow 1080px, Wide 1280px, Extra wide 1600px |
| Corner style | Softly rounded | Square, soft (8px), round (18px) |
| Masthead layout | Classic | Classic, centred, compact |
| Homepage layout | Journal profile | Profile, magazine or classic |
| Submission call to action | On | "Make a Submission" button atop the left rail |
| Default colour scheme | Match device | Always light, match device, always dark |
| Colour scheme toggle | On | Masthead light/dark button |
| Sticky masthead | On | |
| Masthead search | On | |
| Reading progress | On | Article pages only |
| Article outline | On | Needs ≥ 3 headings to appear |
| Latest articles | On | Six most recent published articles on the homepage |
| Structured data | On | Schema.org JSON-LD |
| Web fonts | Off | Source Sans 3 + Source Serif 4 from Google Fonts |
| Notice bar | empty | Plain-text strip above the masthead |
| Column layout | Three columns | Rail + content + sidebar, or two, or one |
| Utility bar | On | ISSNs and user menu above the masthead |
| Journal information | On | Homepage table built from journal settings |
| Article cover images | On | Thumbnails in article lists |
| Abstract snippets | On | First ~260 characters in article lists |
| Sharing | On | Share links on article pages |
| Focus mode | On | Click a cover image to enlarge it |
| Metrics badge | None | Dimensions, Altmetric or PlumX |

Colour values are validated as hex before they reach the stylesheet, and the
notice bar is escaped, so neither option can inject markup or CSS.

---

## Layout of the repository

```
IlsAiDrivenThemePlugin.php   Options, asset registration, hooks, JSON-LD
index.php                    Plugin entry point
version.xml                  Plugin descriptor

styles/
  index.less                 Import order — read this first
  _variables.less            Defaults; overridden by the admin options
  _tokens.less               CSS custom properties, incl. the dark palette
  _base.less  _typography.less
  _pkp-core.less             Helper classes core OJS markup depends on
  _layout.less _topbar.less _header.less _nav.less _rail.less _footer.less
  _components.less _forms.less _objects.less
  _home.less _article.less _search.less _share.less _focus.less
  _utilities.less _rtl.less _print.less

js/main.js                   Progressive enhancement (no dependencies)

templates/frontend/
  components/header.tpl      Top bar, masthead, left rail, opens scaffolding
  components/footer.tpl      Right sidebar, footer, closes the scaffolding
  pages/indexJournal.tpl     Journal homepage
  pages/issueArchive.tpl     Archive grouped by year
  objects/article_summary.tpl        Article card
  objects/announcement_summary.tpl

locale/en/locale.po          Interface strings (mirrored in locale/en_US)
```

Everything not listed under `templates/` falls through to the OJS core
templates, which this theme styles rather than replaces. That is deliberate:
the article page, issue table of contents, search and static pages keep working
unchanged when OJS updates their markup.

---

## Customising

**Change the palette.** Set the primary and accent colours in the admin UI. If
you need finer control, edit `styles/_tokens.less` — every colour in the theme
resolves to a custom property defined there, once for light and once for dark.

**Change a size or radius.** `styles/_variables.less` holds the defaults; the
admin options override them at compile time by appending declarations after the
file is parsed, so you never have to edit the file to change a configured value.

**Add your own CSS.** Add a stylesheet under
**Website Settings → Appearance → Advanced**, or add an `@import` at the end of
`styles/index.less`.

**Show your indexing logos.** Paste them into Website Settings → Appearance →
Additional Homepage Content as a single paragraph of images; the theme lays that
paragraph out as an even strip. The class `ils-logo-strip` on a wrapper does the
same thing anywhere else.

**Fill the left rail.** Website Settings → Navigation → create a menu, assign it
to the **sidebar** area. Nested items are indented rather than turned into
flyouts, because the rail is a list of links rather than a menu bar.

**Override another template.** Copy it from `templates/frontend/…` in the OJS
core into the same path under this plugin's `templates/` directory. It takes
precedence automatically.

**Notes on writing LESS here.** OJS compiles with a PHP port of LESS. Escape
anything the compiler might try to evaluate as arithmetic or strip as a comment
— `calc()` expressions, shadows and data URIs are written as `~"…"` escaped
strings for this reason. Keep media-query breakpoints as literals.

---

## Development

There is no build step, but the sources can be validated:

```bash
# Stylesheet compiles (a close stand-in for the compiler OJS uses)
npx lessc styles/index.less /dev/null

# JavaScript parses
node --check js/main.js

# PHP parses
php -l IlsAiDrivenThemePlugin.php
```

---

## License

GNU General Public License v3.0 — see [LICENSE](LICENSE). This is the same
license as OJS itself.
