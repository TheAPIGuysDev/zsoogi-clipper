# Progressive Web App

## Why PWA for Zsoogi Clips?

The plugin received a complaint about authentication expiring on mobile. The root cause is **Safari on iOS** — Apple's Intelligent Tracking Prevention (ITP) aggressively purges cookies:

- Auth cookies expire after **7 days** even if the site is visited regularly
- WordPress's 14-day "Remember Me" cookie (`AUTHCOOKIE_EXPIRATION`) gets killed early
- Chrome on Android is much better behaved, but all iOS browsers use WebKit under the hood

When a user adds the site to their iOS home screen as a PWA, it launches in **standalone mode** — a separate WebKit context with its own persistent storage that:

- Is **not subject to ITP** the same way Safari is
- Persists until the user explicitly deletes the app or clears storage
- Feels native (no browser chrome, full-screen)

One login → stays logged in indefinitely. This is exactly what you want for an admin-only research tool.

## How It Works

```
User adds site to home screen
        ↓
Browser reads /manifest.json  (served by class-pwa.php via rewrite rule)
        ↓
Browser registers /sw.js      (service worker, also served by class-pwa.php)
        ↓
Standalone mode launches at start_url:
  /wp-admin/edit.php?post_type=zsoogiclips
        ↓
Auth cookie lives in isolated PWA storage — ITP does not purge it
```

## Implementation

### New Class: `Zsoogi\PWA` (`includes/class-pwa.php`)

Handles everything PWA-related. Initialized for **all users** (manifest and SW must be publicly accessible — browsers fetch them without authentication).

Three responsibilities:

1. **Rewrite rules** — maps `/manifest.json` and `/sw.js` to WordPress query vars
2. **File serving** — outputs manifest JSON and service worker JS with correct headers
3. **Head tags** — injects `<link rel="manifest">`, Apple meta tags, and SW registration script on `wp_head`, `admin_head`, and `login_head`

### New File: `assets/js/sw.js`

Source for the service worker. The `__VERSION__` placeholder is replaced at serve-time by `class-pwa.php` — no build step needed.

**Fetch strategy:**

| Request | Strategy | Reason |
|---|---|---|
| `/wp-admin/*`, `/wp-login.php` | Network-first | Auth state, nonces, post content must be fresh |
| Everything else | Cache-first | Static assets (CSS, JS, images) can be served offline |

### How `/manifest.json` and `/sw.js` Are Served

WordPress rewrite rules route the paths through `index.php`:

```
GET /manifest.json
  → index.php?zsoogi_pwa_file=manifest
  → PWA::serve_manifest()
  → Content-Type: application/manifest+json

GET /sw.js
  → index.php?zsoogi_pwa_file=sw
  → PWA::serve_service_worker()
  → Content-Type: application/javascript
  → Service-Worker-Allowed: /       ← extends scope to root
  → Cache-Control: no-cache         ← browser always re-checks for updates
```

The `Service-Worker-Allowed: /` header is critical — without it, the browser would restrict the SW scope to the plugin's subdirectory, not the full site.

### Manifest Contents

```json
{
  "name": "{Site Name} — Zsoogi Clips",
  "short_name": "Zsoogi",
  "start_url": "/wp-admin/edit.php?post_type=zsoogiclips",
  "display": "standalone",
  "orientation": "portrait",
  "background_color": "#1d2327",
  "theme_color": "#2271b1",
  "scope": "/",
  "icons": [...]
}
```

`start_url` points directly to the clips list — the user lands on their research immediately after tapping the home screen icon.

### Head Tags Output

On every page (frontend, wp-admin, login screen):

```html
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#2271b1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Zsoogi">
<link rel="apple-touch-icon" href=".../icon-192.png">
<script>
if ( 'serviceWorker' in navigator ) {
    navigator.serviceWorker.register( '/sw.js', { scope: '/' } )...
}
</script>
```

The manifest link on the **login page** and **wp-admin** is intentional — the PWA install prompt can appear from any page the user visits, and the `start_url` must be reachable from the install context.

## Icons

The manifest references `assets/images/icon-192.png` and `assets/images/icon-512.png`. If those files don't exist, it falls back to the existing `assets/images/zsoogi.png`.

**Action required:** Generate properly-sized icons:

```bash
# Using ImageMagick (if available):
convert assets/images/zsoogi.png -resize 192x192 assets/images/icon-192.png
convert assets/images/zsoogi.png -resize 512x512 assets/images/icon-512.png
```

Or use an online tool like [realfavicongenerator.net](https://realfavicongenerator.net).

The `purpose: "any maskable"` field in the manifest means the icon should have padding so Android's adaptive icon system doesn't clip important content.

## Installation Flow (User Perspective)

### iOS Safari

1. User visits the site and logs in
2. Tap the **Share** button → **Add to Home Screen**
3. Confirm the name → **Add**
4. Icon appears on home screen
5. Tap → opens in standalone mode at the clips list
6. Stay logged in indefinitely (no ITP interference in standalone mode)

### Android Chrome

Chrome shows an **"Add to Home Screen"** banner or install button automatically after the user visits the site twice. The PWA install criteria are:

- Valid manifest with icons
- HTTPS
- Registered service worker

All three are satisfied by this implementation.

## Troubleshooting

**`/manifest.json` returns 404:** Rewrite rules need flushing. Go to **Settings → Permalinks** and click Save. (Also happens automatically on plugin activation.)

**Service worker not registering:** Check browser DevTools → Application → Service Workers. Common cause: the site is not HTTPS. Service workers require a secure context (or `localhost`).

**PWA not installable on iOS:** Verify `apple-mobile-web-app-capable` meta tag is present in the page source. Check DevTools on a Mac with Safari → Develop → [device name].

**Cookie still expiring after PWA install:** The user may have installed the PWA before logging in, or the site may be running on HTTP. HTTPS is required for the persistent PWA context.

## Scope Considerations

The service worker scope is `/` — it controls the entire origin. This means:

- The WP login page is controlled (offline fallback available)
- All wp-admin pages use network-first (never serve stale admin UI)
- Static assets across the whole site are cached

If this causes unintended behavior for other parts of the WordPress site, the scope can be narrowed to `/wp-admin/` in both the SW registration script and the manifest `scope` field.

## Architecture Note

`PWA::init()` is called unconditionally in `zsoogi_clipper_init()` (not gated behind `is_user_logged_in()`), because:

- `/manifest.json` must be fetchable by the browser before the user logs in
- `/sw.js` must register before the user logs in (the login page itself should be controlled)
- Head tags are harmless for non-admin users (they'll be redirected before seeing clip content anyway)
