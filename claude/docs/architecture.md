# Architecture

## Overview

The plugin uses a modular, object-oriented architecture with static `init()` classes and PSR-4 autoloading via Composer. All classes live in the `Zsoogi` namespace (`includes/`).

## File Structure

```
zsoogi-clipper/
├── zsoogi-clipper.php              # Bootstrap: constants, autoloader, hooks
├── includes/
│   ├── class-zsoogi-clips.php      # CPT + taxonomy registration
│   ├── class-admin-menu.php        # Settings page + bookmarklet install page
│   └── class-zsoogi-clipper.php    # Bookmarklet data processing
├── assets/
│   └── js/
│       └── bookmarklet.js          # Vanilla JS bookmarklet source
├── templates/
│   └── single-zsoogiclips.php      # Custom single-post template
├── languages/
│   └── index.php                   # Empty index for security
└── claude/
    ├── mkdocs.yml                  # MkDocs config
    ├── hooks.py                    # Version injection hook
    ├── mkdocs-serve.sh             # Local dev server
    ├── mkdocs-deploy.sh            # Build + rsync deploy
    └── docs/                       # This documentation
```

## Class Responsibilities

### `Zsoogi\Zsoogi_Clips` (`includes/class-zsoogi-clips.php`)

Registers the custom post type and taxonomy. Runs for all users so WordPress routing works.

Key constants:

- `POST_TYPE = 'zsoogiclips'`
- `TAXONOMY = 'zsoogi_type'`

Key methods:

- `register_post_type()` — registers `zsoogiclips` CPT
- `register_taxonomy()` — registers `zsoogi_type` taxonomy
- `register_builtin_taxonomies()` — adds `category` and `post_tag` support to the CPT
- `ensure_default_term()` — creates "Research" term on `init` priority 20
- `set_default_term()` — assigns "Research" to new clips with no term set
- `restrict_frontend_access()` — redirects non-admins on `template_redirect`
- `load_custom_template()` — serves `templates/single-zsoogiclips.php` for single views

### `Zsoogi\Admin_Menu` (`includes/class-admin-menu.php`)

Manages admin settings page and bookmarklet installation page. Has its own capability check (`manage_options`).

Key methods:

- `add_admin_menu()` — adds "Grab Zsoogi" and "Settings" subpages under the CPT menu
- `register_settings()` — registers settings via WordPress Settings API
- `render_bookmarklet_page()` — bookmarklet install page with draggable link
- `get_bookmarklet_code()` — reads `bookmarklet.js`, replaces `__VERSION__` / `__SITE_URL__`, minifies

### `Zsoogi\Zsoogi_Clipper` (`includes/class-zsoogi-clipper.php`)

Processes GET parameters from the bookmarklet into pre-filled post content. **Only initialized for logged-in admins.**

Key methods:

- `process_bookmarklet()` — sanitizes GET params on `load-post-new.php`, stores in `$GLOBALS`
- `default_title()` — `default_title` filter: returns captured title
- `default_content()` — `default_content` filter: builds blockquote + citation + notes scaffold
- `set_featured_image()` — downloads captured image, sets as featured image on first save

## Initialization Flow

```
1. Plugin file loaded
   ├── zsoogi_clipper_load_autoloader()  — loads vendor/autoload.php if present
   ├── zsoogi_clipper_load_includes()    — requires 3 class files
   └── add_action('plugins_loaded', 'zsoogi_clipper_init')

2. plugins_loaded fires → zsoogi_clipper_init()
   ├── Zsoogi_Clips::init()             — always (CPT must be registered for all)
   ├── Admin_Menu::init()               — always (has internal capability checks)
   └── Zsoogi_Clipper::init()           — ONLY if is_user_logged_in() + manage_options

3. init hook fires (from Zsoogi_Clips::init)
   ├── priority 0:  register_post_type()
   ├── priority 0:  register_taxonomy()
   ├── priority 10: register_builtin_taxonomies()
   └── priority 20: ensure_default_term()

4. template_redirect → restrict_frontend_access()
5. template_include  → load_custom_template()
```

## Constants

| Constant | Value |
|---|---|
| `ZSOOGI_CLIPPER_VERSION` | Current version (e.g. `0.9.2`) |
| `ZSOOGI_CLIPPER_PLUGIN_FILE` | Absolute path to `zsoogi-clipper.php` |
| `ZSOOGI_CLIPPER_PLUGIN_DIR` | Absolute path to plugin directory (trailing slash) |
| `ZSOOGI_CLIPPER_PLUGIN_URL` | URL to plugin directory (trailing slash) |

## Security Model

- Frontend: non-admins → `wp_safe_redirect(home_url())` on CPT/taxonomy pages
- Bookmarklet processing: `current_user_can('manage_options')` check before processing GET params
- GET params sanitized with `sanitize_text_field`, `esc_url_raw`, `wp_kses_post`
- No nonce on bookmarklet GET params (by design — cross-origin bookmarklet cannot generate WP nonces)
- Settings form uses standard `settings_fields()` nonce

## Namespace & Autoloading

```json
"autoload": {
    "psr-4": {
        "Zsoogi\\": "includes/"
    }
}
```

Without Composer, files are loaded directly via `require_once` in `zsoogi_clipper_load_includes()`.
