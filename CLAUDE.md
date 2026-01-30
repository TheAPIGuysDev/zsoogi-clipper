# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Zsoogi Clipper is a WordPress plugin that creates an administrator-only custom post type for wiki-style documentation, integrated with a modern jQuery-free bookmarklet for capturing content from external sources. The plugin follows WordPress Coding Standards and uses object-oriented architecture with PSR-4 autoloading.

## Development Commands

### Composer Commands

**Note**: Composer dependencies are **optional** and only needed for development (linting tools). The plugin runs fine in production without them.

```bash
# Install dependencies (for development only)
composer install

# Run PHP CodeSniffer (lint)
composer lint

# Auto-fix PHPCS violations
composer lint-fix

# Build distributable plugin ZIP file (includes version in filename)
composer build-zip
```

The build process (via `build.sh`) automatically extracts the version from the plugin header and creates a production-ready ZIP file at `dist/zsoogi-clipper-{version}.zip` (e.g., `dist/zsoogi-clipper-2.2.0.zip`). The ZIP excludes development files (vendor/, .git/, .env, composer files, build.sh, etc.) and can be uploaded directly to production without requiring `composer install`.

### PHP CodeSniffer

PHPCS is configured via `phpcs.xml.dist` to enforce WordPress Coding Standards:
- Standard: WordPress
- Configuration: `vendor/bin/phpcs --config-set installed_paths` (auto-configured via composer hooks)
- Includes WPCS, PHPCSExtra, and PHPCSUtils

## Architecture

### Plugin Structure

The plugin follows a modular architecture with clear separation of concerns:

- **Main Plugin File** (`zsoogi-clipper.php`): Bootstrap file that loads dependencies, defines constants, and initializes classes. Handles activation/deactivation hooks.

- **Namespace**: All classes use the `Zsoogi` namespace with PSR-4 autoloading via `includes/` directory

- **Key Classes**:
  - `Zsoogi\Zsoogi_Clips`: Registers the custom post type (`zsoogiclips`) and taxonomy (`api_guys_type`) - keeping original names for backward compatibility
  - `Zsoogi\Admin_Menu`: Manages the settings page under the custom post type menu
  - `Zsoogi\Zsoogi_Clipper`: Handles bookmarklet content capture and processing

### Plugin Constants

```php
ZSOOGI_CLIPS_VERSION       // Current version
ZSOOGI_CLIPS_PLUGIN_FILE   // Main plugin file path
ZSOOGI_CLIPS_PLUGIN_DIR    // Plugin directory path
ZSOOGI_CLIPS_PLUGIN_URL    // Plugin URL
```

### Post Type & Taxonomy

**IMPORTANT**: These slugs are kept from the original "Zsoogi Clipper" plugin for backward compatibility with existing content:

- **Post Type Slug**: `zsoogiclips`
  - Supports: title, editor, thumbnail, comments, revisions
  - REST API enabled
  - Public, has archive, searchable
  - Menu icon: `dashicons-rest-api`

- **Taxonomy Slug**: `api_guys_type`
  - Hierarchical (like categories)
  - REST API enabled
  - Public, show in admin column

### Zsoogi Clipper Bookmarklet

- Pure vanilla JavaScript (no jQuery)
- Captures URL, title, selected text, and images from external pages
- **YouTube Integration** (v2.4.0+):
  - Automatically cleans YouTube titles (removes view count prefix and "- YouTube" suffix)
  - Fetches high-quality thumbnails from YouTube API (`maxresdefault.jpg`)
  - Example: `(153) Video Title - YouTube` → `Video Title`
- Pre-fills new wiki posts with formatted content
- Supports customizable citation formats
- Auto-sets featured images from captured content

### Initialization Flow

1. Composer autoloader loaded (if available)
2. Required files loaded from `includes/` directory
3. `plugins_loaded` action fires `zsoogi_clipper_init()`
4. `Zsoogi_Clips::init()` registers post type and taxonomy on `init` hook
5. `Admin_Menu::init()` registers settings page on `admin_menu` hook
6. Press This classes initialized only for logged-in administrators

### Capability Checks

The plugin restricts functionality to administrators:
- **Frontend Access**: Zsoogi posts, archives, and taxonomy pages redirect non-administrators to homepage
- Press This features: Only loaded if `is_user_logged_in()` AND `current_user_can('manage_options')`
- Settings page: Uses `manage_options` capability
- Custom post type: Uses `page` capability type (edit_pages, delete_pages, etc.)
- Admin-only visibility: Non-logged-in users and non-administrators cannot view wiki content on the frontend

### Settings System

Settings registered via WordPress Settings API:
- Option Group: `zsoogi_clipper_settings`
- Options:
  - `zsoogi_clipper_auto_featured_image` (boolean, default: true)
  - `zsoogi_clipper_citation_format` (string, default: 'simple') - Options: simple, detailed, academic
  - `zsoogi_clipper_include_metadata` (boolean, default: false)

## Code Patterns

### Class Structure

All classes use static initialization with `init()` method:

```php
class Example_Class {
    public static function init() {
        add_action('hook_name', array(__CLASS__, 'method_name'));
    }
}
```

### Text Domain

**IMPORTANT**: Text domains are kept as `zsoogi-clipss` and `zsoogi-clips` for backward compatibility with existing translations and option names stored in the database. The plugin is now called "Zsoogi Clipper" but maintains these legacy text domains internally.

### Escaping & Security

Follow WordPress security best practices:
- Use `esc_html()`, `esc_attr()`, `esc_url()` for output
- Use `sanitize_*()` functions for input
- Check capabilities before privileged operations
- Use nonces for form submissions (via `settings_fields()`)

## WordPress Environment

This is a Local development environment (LocalWP):
- Path: `/Users/pbrocks/Documents/Local/pbrx/app/public/wp-content/plugins/zsoogi-clipper`
- WordPress core is in the parent `app/public/` directory
- Development site likely at `pbrx.local`

## Notes

- **Composer dependencies are optional**: Only needed for development (PHPCS linting). The plugin runs perfectly in production without the vendor/ directory.
- **Backward Compatibility**: Post type slug (`zsoogiclips`), taxonomy slug (`api_guys_type`), option names (`zsoogi_clipper_*`), and text domains are kept from the original "Zsoogi Clipper" plugin to maintain compatibility with existing installations
- Zsoogi Clipper bookmarklet uses pure vanilla JavaScript with no dependencies
- The plugin registers activation/deactivation hooks that flush rewrite rules
- When modifying post type or taxonomy registration, flush rewrite rules (visit Settings → Permalinks)

## Branches

### Main Branch (v2.4.0)
Core functionality with YouTube title cleanup and thumbnail integration. Works uniformly across all URLs.

### ProVersion Branch (v2.4.3)
Advanced YouTube features including full transcript capture. See [GitHub Issue #5](https://github.com/pbrocks/zsoogi-clipper/issues/5) for technical details on the `window.name` bridge pattern implementation.
