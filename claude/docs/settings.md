# Settings & Admin

## Admin Menu Structure

The plugin adds two subpages under the **Zsoogi Clips** CPT menu:

| Submenu Label | Slug | Purpose |
|---|---|---|
| Grab Zsoogi | `zsoogi-clipper-bookmarklet` | Bookmarklet installation page |
| Settings | `zsoogi-clipper-settings` | Plugin settings |

Both require `manage_options` capability.

## Settings Page

**Location:** Zsoogi Clips → Settings

**Option Group:** `zsoogi_clipper_settings`

**Settings Section:** `zsoogi_clipper`

### Options

#### `zsoogi_clipper_auto_featured_image`

- **Type:** boolean
- **Default:** `true`
- **Effect:** When enabled, the first image captured by the bookmarklet is downloaded and set as the post's featured image on first save. Uses `media_sideload_image()`.
- **Sanitize:** `sanitize_checkbox()` — returns `true`/`false`

#### `zsoogi_clipper_include_metadata`

- **Type:** boolean
- **Default:** `false`
- **Effect:** When enabled, adds a capture timestamp paragraph to the post content:

    ```html
    <p><em>Captured on {date} {time}</em></p>
    ```

- **Sanitize:** `sanitize_checkbox()` — returns `true`/`false`

## Bookmarklet Installation Page

**Location:** Zsoogi Clips → Grab Zsoogi

Shows:

- Plugin version
- Draggable bookmarklet link (blue button) — the `href` contains the minified JS
- Step-by-step installation instructions
- Feature list
- How-it-works explanation
- Troubleshooting tips

The bookmarklet link is auto-configured for the current site URL, so it works identically in local, staging, and production environments.

## Settings Form

Uses standard WordPress Settings API:

```php
settings_fields('zsoogi_clipper_settings');  // nonce + hidden fields
do_settings_sections('zsoogi-clipper-settings');
submit_button('Save Settings');
```

The form posts to `options.php`. On save, `?settings-updated=true` triggers a success notice.

## Plugin Information Panel

The settings page also displays a read-only info panel:

- **Version:** current `ZSOOGI_CLIPPER_VERSION`
- **Post Type:** `zsoogiclips`
- **Taxonomy:** `zsoogi_type`

## Notes on Citation Format

The free version hardcodes citation format to `simple`:

```
Source: [Title](URL)
```

The `format_citation()` private method supports `detailed` and `academic` formats in the code, but only `simple` is used. The settings page does not expose a citation format selector (intentional design decision for consistent branding in the free version).
