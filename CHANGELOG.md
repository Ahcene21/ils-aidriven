# Changelog

All notable changes to this theme are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project uses
the four-part release numbering that OJS plugins require.

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
