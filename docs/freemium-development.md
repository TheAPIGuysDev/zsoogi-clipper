# Freemium Development Guide

## Strategy: One Codebase, Feature Flags

We maintain a **single repository and single branch** (`premium`) for both the free and premium editions. Premium features are gated at runtime by the `License` class — the same ZIP file is distributed to both free and premium users.

This approach was chosen over separate branches or repos because:

- One place to fix bugs — no cherry-picking across branches
- WordPress.org allows license checks (they only prohibit obfuscated code)
- The `composer build-zip` pipeline already handles packaging
- The free and premium editions share ~90% of code

---

## The `License` Class

**File:** `includes/class-license.php`  
**Namespace:** `Zsoogi\License`  

The single source of truth for feature gating. All premium checks go through this class — never check options or constants directly in feature code.

### API

```php
// Is any premium feature available?
License::is_premium(): bool

// Is a specific feature available?
License::has_feature( 'transcripts' ): bool
License::has_feature( 'citation_formats' ): bool
License::has_feature( 'pdf_extraction' ): bool

// License management
License::activate( $key ): bool
License::deactivate(): void
License::get_status(): string   // 'valid' | 'inactive' | ''
License::get_display_key(): string  // redacted: "ABCD****WXYZ"
```

### Feature Registry

Features are declared in `License::$features`:

```php
private static $features = array(
    'abilities'        => 'premium', // WordPress Abilities API / MCP integration
    'transcripts'      => 'premium', // YouTube transcript capture
    'citation_formats' => 'premium', // detailed + academic citation styles
    'pdf_extraction'   => 'premium', // PDF content extraction (future)
);
```

**To add a new premium feature:** add it to `$features`, then gate all its code with `License::has_feature('your_feature')`.  

---

## How License Detection Works

`License::is_premium()` checks in this order:

1. **`ZSOOGI_PREMIUM_LICENSE` constant** — for local dev/staging (add to `wp-config.php`)
2. **`zsoogi_clipper_license_status` option** — set to `'valid'` by `License::activate()`

The `sanitize_license_key()` callback in `Admin_Menu` calls `License::activate()` or `License::deactivate()` automatically when the Settings form is saved.

---

## Local Development

To test premium features without a real license key, add to `wp-config.php`:

```php
define( 'ZSOOGI_PREMIUM_LICENSE', true );
```

Remove this line before deploying to production. Never commit it.

---

## Adding a New Premium Feature

**Step 1 — Register it in `License::$features`:**  

```php
private static $features = array(
    'citation_formats' => 'premium',
    'transcripts'      => 'premium',
    'pdf_extraction'   => 'premium',
    'your_feature'     => 'premium',  // ← add here
);
```

**Step 2 — Gate the backend logic:**  

```php
// In the relevant class method:
if ( License::has_feature( 'your_feature' ) ) {
    // premium code
}
```

**Step 3 — Gate the settings UI in `Admin_Menu::register_settings()`:**  

```php
if ( License::has_feature( 'your_feature' ) ) {
    register_setting( self::OPTION_GROUP, 'your_option', [...] );
    add_settings_field( 'your_field', ..., 'render_your_field' );
} else {
    add_settings_field( 'your_field_locked', ..., 'render_premium_upsell_field' );
}
```

`render_premium_upsell_field()` renders the locked placeholder automatically — you don't need a custom renderer for the upsell row.

---

## Currently Gated Features

### `abilities` — WordPress Abilities API / MCP integration

**File:** `includes/class-abilities.php`  

Registers four WordPress Abilities so MCP-connected AI agents can interact with the clip library via natural language.

**Free behaviour:** `Abilities::init()` returns immediately; no abilities registered.  

**Premium behaviour:** Registers on `wp_abilities_api_init`. Bails silently if `wp_register_ability()` doesn't exist (pre-WP 6.9).  

**Registered abilities:**  

| Ability | Requires |
|---|---|
| `zsoogi/create-clip` | `abilities` feature |
| `zsoogi/search-clips` | `abilities` feature |
| `zsoogi/get-transcript` | `abilities` + `transcripts` features |
| `zsoogi/export-clips` | `abilities` feature |

**Setup:** Requires WordPress 6.9+ and the [MCP Adapter plugin](https://github.com/WordPress/mcp-adapter/releases). See [AI-Ready WordPress (MCP)](ai-ready-wordpress.md) for connecting Claude Desktop or Claude Code.  

---

### `citation_formats` — Multiple citation styles

**Files affected:**  

- `includes/class-zsoogi-clipper.php` — `default_content()` reads `zsoogi_clipper_citation_format` option only when licensed; falls back to `'simple'` for free users
- `includes/class-admin-menu.php` — citation format radio buttons shown only when licensed

**Free behaviour:** always uses `simple` format (`Source: [Title](URL)`)  

**Premium behaviour:** user chooses `simple`, `detailed`, or `academic` in Settings  

### `transcripts` — YouTube transcript capture

**Files affected:**  

- `includes/class-zsoogi-clipper.php` — `default_content()` and `save_youtube_transcript()` skip transcript processing when not licensed
- `includes/class-admin-menu.php` — "YouTube Transcript Settings" section only shown when licensed

**Free behaviour:** YouTube video ID and transcript data are ignored even if the bookmarklet sends them  

**Premium behaviour:** transcript excerpt added to post content; full transcript saved to `_youtube_transcript` post meta  

---

## Settings Page Layout

The settings page (`Zsoogi Clips → Settings`) always shows:

1. **General Settings** — menu label (all users)
2. **Zsoogi Clipper Settings** — auto featured image + citation format (citation format locked/upsell for free users)
3. **YouTube Transcript Settings** — only shown when `License::has_feature('transcripts')`
4. **Premium License** — always shown so users can enter/manage their key

---

## Packaging: Free vs Premium

The same ZIP is used for both editions — premium features activate via license key.

```bash
# Build production ZIP (works for both free and premium distribution)
composer build-zip

# Output: dist/zsoogi-clipper-{version}.zip
```

For **WordPress.org submission** (free edition), submit the same ZIP. Free users who install it will see the locked placeholders in settings and `'simple'` citation format by default. No hidden code, fully GPL-compliant.

For **premium customers**, distribute the same ZIP alongside a license key purchased at theapiguys.com.

---

## License Server (TODO)

`License::activate()` currently stubs the remote validation. Replace the TODO block with a real API call when the license server is set up.

The method signature to implement:

```php
public static function activate( string $key ): bool {
    $response = wp_remote_post( 'https://theapiguys.com/edd/api', [
        'body' => [
            'edd_action' => 'activate_license',
            'license'    => $key,
            'item_name'  => urlencode( 'Zsoogi Clipper Premium' ),
            'url'        => home_url(),
        ],
    ] );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $data   = json_decode( wp_remote_retrieve_body( $response ) );
    $status = $data->license ?? 'invalid'; // 'valid' | 'invalid' | 'expired' | 'disabled'

    update_option( self::OPTION_KEY, $key );
    update_option( self::OPTION_STATUS, $status );

    return 'valid' === $status;
}
```

Options for the license server:

- **EDD Software Licensing** (recommended) — integrates with Easy Digital Downloads
- **Freemius** — managed SaaS, handles billing + licensing
- **Custom** — simple POST endpoint returning `{ "license": "valid" }`

---

## Option Names Reference

| Option | Type | Default | Tier |
|---|---|---|---|
| `zsoogi_clipper_license_key` | string | `''` | all |
| `zsoogi_clipper_license_status` | string | `''` | all |
| `zsoogi_clipper_auto_featured_image` | bool | `true` | free |
| `zsoogi_clipper_include_metadata` | bool | `false` | free |
| `zsoogi_clipper_citation_format` | string | `'simple'` | premium |
| `zsoogi_clipper_youtube_transcripts_enabled` | bool | `false` | premium |
| `zsoogi_clipper_youtube_excerpt_length` | int | `500` | premium |
| `zsoogi_clipper_youtube_language` | string | `'en'` | premium |
| `zsoogi_clips_label` | string | `'Zsoogi Clips'` | all |
