# Changelog

All notable changes to this theme are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project uses
the four-part release numbering that OJS plugins require.

## 1.2.0.0 — 2026-08-23

Homepage moved closer to the layout conventions of commercial OJS journal
themes. Built from Novelty's documented feature list rather than its design —
the demo site is not reachable from here.

### Added

- "Journal profile" homepage layout, now the default: the cover image sits
  beside the journal description with ISSN, publisher and current issue listed
  alongside, rather than opening on a large type banner. The previous hero
  layout remains available as "magazine".
- Optional "Make a Submission" button at the top of the left rail. The rail now
  appears for this button even when no menu is assigned to the sidebar area.
- Layout for a run of index and partner logos pasted into Additional Homepage
  Content, plus an `ils-logo-strip` class for the same effect elsewhere.

### Changed

- Section headings are filled bars in the journal's primary colour; issue
  table-of-contents section headings use a lighter accent-edged variant.
- Tighter vertical rhythm between homepage sections.

## 1.1.0.0 — 2026-08-23

Reshapes the theme along the lines of a classic three-column scholarly journal
layout, of the kind commercial OJS themes such as Novelty use.

### Added

- Three-column layout with a configurable column count. The left rail is driven
  by a new `sidebar` navigation menu area, so it is built in the OJS admin UI.
- Utility bar above the masthead with the journal's ISSNs and the user menu.
- Article cards: cover thumbnail, linked author names, page range, DOI,
  abstract snippet, galley buttons and an optional metrics badge.
- Focus mode — a cover image opens enlarged over a dimmed, blurred page.
- Share links (Facebook, X, LinkedIn, WhatsApp, Telegram, Reddit, Mendeley,
  email, copy link), rendered as plain anchors with no vendor scripts.
- Optional Dimensions / Altmetric / PlumX badge, off by default.
- "Journal information" table on the homepage, built from journal settings.
- Issue archive grouped by year, keeping core pagination.
- Eight new appearance options covering all of the above.

### Changed

- The DOI is now resolved from the 3.4+ DOI object with a fallback to the 3.3
  pub-id setting, using `getData()` only, so no accessor that moved between
  releases is called.

## 1.0.0.0 — 2026-08-23

Initial release.

### Added

- Standalone theme plugin for OJS 3.4/3.5 with eighteen configurable options.
- Token-based design system with light and dark palettes derived from the
  journal's primary colour, applied before first paint to avoid a flash.
- Sticky masthead with inline search, colour-scheme toggle, and accessible
  multi-level navigation built on `{load_menu}` output.
- Magazine homepage: hero, facts strip, announcements, latest-articles grid and
  the current issue's table of contents.
- Article reading aids: progress bar, auto-generated outline with scroll-spy,
  sticky metadata column, copy-to-clipboard for DOI and citation.
- Schema.org `ScholarlyArticle` JSON-LD on article pages.
- Right-to-left support, print stylesheet, and reduced-motion handling.
