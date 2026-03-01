# Zsoogi Clipper v{{ plugin_version }}

WordPress plugin that creates an **admin-only custom post type** for wiki-style research documentation, paired with a **modern jQuery-free bookmarklet** for capturing content from external sources.

- **GitHub**: [TheAPIGuysDev/zsoogi-clipper](https://github.com/TheAPIGuysDev/zsoogi-clipper)
- **Author**: The API Guys
- **License**: GPLv2 or later
- **Requires**: WordPress 5.0+, PHP 7.4+

## What It Does

Zsoogi Clipper lets administrators capture and curate web content into private research notes ("Zsoogi Clips") stored as a custom post type. Non-administrators are redirected to the homepage when they try to view clips — keeping your research private.

## Quick Start

1. Activate the plugin
2. Go to **Zsoogi Clips → Grab Zsoogi** in the WP admin
3. Drag the blue bookmarklet button to your browser's bookmarks bar
4. On any webpage, optionally select text, then click the bookmarklet
5. A new window opens with the clip pre-filled — add notes and publish

## Key Slugs (backward-compatible, do not change)

| Item | Slug |
|---|---|
| Post Type | `zsoogiclips` |
| Taxonomy | `zsoogi_type` |
| Option Group | `zsoogi_clipper_settings` |

## Docs in This Site

- [Architecture](architecture.md) — class structure, initialization flow, hooks
- [Bookmarklet](bookmarklet.md) — JavaScript, YouTube handling, image capture
- [Post Type & Taxonomy](post-type.md) — CPT/taxonomy registration, frontend access
- [Settings & Admin](settings.md) — settings page, options, admin menu
- [Progressive Web App](progressive-web-app.md) — PWA install, iOS auth persistence, manifest/SW
- [Development](development.md) — Composer, PHPCS, build process
