# Changelog

All notable changes to this theme are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project uses
the four-part release numbering that OJS plugins require.

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
