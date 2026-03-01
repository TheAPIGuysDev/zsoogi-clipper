# Bookmarklet

## Overview

The Zsoogi Clipper bookmarklet is a self-contained vanilla JavaScript snippet that users drag to their browser bookmarks bar. When clicked on any webpage, it:

1. Captures the page URL, title, selected text, and best image
2. Applies YouTube-specific title cleanup and thumbnail fetching
3. Opens a new window to `wp-admin/post-new.php?post_type=zsoogiclips` with all data as GET parameters

## Source File

`assets/js/bookmarklet.js` — human-readable source. At runtime, `Admin_Menu::get_bookmarklet_code()` reads this file, replaces placeholders, and minifies it for the `href="javascript:..."` link.

## Placeholders Replaced at Runtime

| Placeholder | Replaced With |
|---|---|
| `__VERSION__` | `ZSOOGI_CLIPPER_VERSION` constant |
| `__SITE_URL__` | Site URL derived from `admin_url()` |

## GET Parameters Passed

| Parameter | Source | Sanitized By |
|---|---|---|
| `title` | `document.title` (cleaned) | `sanitize_text_field()` |
| `url` | `location.href` | `esc_url_raw()` |
| `selection` | `window.getSelection().toString()` | `wp_kses_post()` |
| `image` | Best image URL (see below) | `esc_url_raw()` |
| `clipper_version` | `__VERSION__` | display only |

## YouTube Integration

When the page URL matches a YouTube video pattern, special handling applies:

**Title cleanup:**

```js
// Removes "(153) " view count prefix and "- YouTube" suffix
document.title
  .replace(/^\(\d+\)\s*/, '')
  .replace(/\s*-\s*YouTube\s*$/, '')
```

Example: `(153) How to Build APIs - YouTube` → `How to Build APIs`

**Thumbnail:** Uses `maxresdefault.jpg` from YouTube's image CDN:

```
https://i.ytimg.com/vi/{VIDEO_ID}/maxresdefault.jpg
```

The video ID is extracted with a regex that handles all YouTube URL formats (`youtube.com/watch?v=`, `youtu.be/`, `/embed/`, `/live/`, `youtube-nocookie.com`).

## Image Capture (Non-YouTube)

For non-YouTube pages, the bookmarklet finds the first `<img>` that passes all filters:

```js
Array.from(document.querySelectorAll('img')).filter(i =>
    i.naturalWidth > 200 &&
    i.naturalHeight > 200 &&
    !i.src.includes('icon') &&
    !i.src.includes('logo') &&
    !i.src.includes('avatar')
)
```

This filters out small decorative images, icons, logos, and avatars. If no image passes, the `image` parameter is omitted.

## PHP Processing (`Zsoogi_Clipper` class)

On `load-post-new.php` for `zsoogiclips` post type, the class:

1. Verifies `current_user_can('manage_options')`
2. Sanitizes all GET params and stores in `$GLOBALS`
3. `default_title` filter returns captured title
4. `default_content` filter builds:
    - Blockquote of selected text (if any)
    - Citation paragraph: `Source: [Title](URL)`
    - Optional capture date (if `zsoogi_clipper_include_metadata` enabled)
    - "Notes" heading + empty paragraph

5. On `save_post`, `set_featured_image()` downloads the captured image URL and sets it as the post thumbnail (if `zsoogi_clipper_auto_featured_image` is enabled and post has no existing thumbnail)

## Citation Format

Citation format is hardcoded to `simple` in the free version:

```html
<strong>Source:</strong> <a href="{URL}" target="_blank" rel="noopener">{Title}</a>
```

The `format_citation()` method also supports `detailed` and `academic` formats for future use.

## Popup Blocking

The bookmarklet opens the post editor in a new window:

```js
window.open(postUrl, '_blank', 'width=900,height=700,menubar=no,...')
```

If the popup is blocked, an `alert()` prompts the user to allow popups from the source site.

## Troubleshooting

**Popup blocked:** User must allow popups from the site they are clipping from.

**Content not pre-filled:** Verify the user is logged in as administrator and the post type in the URL is `zsoogiclips`.

**Wrong image captured:** The first `>200px` image that isn't an icon/logo/avatar is used. For YouTube, the API thumbnail is always preferred.
