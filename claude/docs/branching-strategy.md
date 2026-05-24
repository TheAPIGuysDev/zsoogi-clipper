# Branch Strategy & Distribution

This document defines the canonical branch structure, what each branch represents, and how code flows from development through to WordPress.org (free) and Pressable (premium).

---

## The Core Principle: One Codebase, Two Distributions

The plugin uses a **single codebase** with premium features gated at runtime by the `License` class. There is no separate premium codebase — the same PHP runs for both free and premium users. What differs is whether a valid license key is present.

This means:

- **Free users** install the plugin from WordPress.org. Without a license key, `License::is_premium()` returns `false` and premium UI is replaced with upsell placeholders.
- **Premium users** install the same plugin (or receive it via Pressable / theapiguys.com) and activate a license key. `License::is_premium()` returns `true` and all gated features unlock.

**Note:** Several marketing docs (`premium-features.md`, `pricing-comparison.md`, `enterprise-features.md`) describe a "separate plugin" model with different slugs (`zsoogi-clipper-premium`, `zsoogi-clipper-enterprise`). **This is aspirational/future planning, not current architecture.** The current architecture is single codebase. Treat those docs as product vision, not implementation spec.  

---

## Branch Map

| Branch | Purpose | Deploys To |
|---|---|---|
| `main` | Stable free version — what WordPress.org gets | WordPress.org plugin repository |
| `premium` | Premium feature development — source of truth for paid tier | Pressable / theapiguys.com distribution |
| `develop` | Integration branch — features merged here before going to `main` or `premium` | Staging / local only |
| `planning` | Non-code planning docs, roadmap notes | Not deployed |

---

## Branch Details

### `main` — WordPress.org Free Version

- Always reflects the **current stable release** on WordPress.org
- Contains only features available to free users (or premium features behind `License::has_feature()` gates — these are present in the code but locked without a key)
- Built and submitted to WordPress.org using `composer build-zip`
- **Must be GPL-compliant** — no obfuscated code, no runtime checks that block core WordPress functionality
- WordPress.org does not prohibit license checks; they only prohibit code that prevents deactivation or degrades core WordPress features

**What to never merge into `main`:**  

- Hardcoded license keys or `ZSOOGI_PREMIUM_LICENSE` constants
- Any code that bypasses `License::has_feature()` checks without a key
- Development-only config or debug output

### `premium` — Pressable / Paid Distribution

- Source of truth for **all active development**, including features not yet ready for WordPress.org
- Premium features are developed here first, then the feature-flagged version is merged back to `main`
- The same `composer build-zip` ZIP from this branch is what paying customers receive from Pressable or theapiguys.com (it unlocks with a valid license key)
- Contains the `ZSOOGI_PREMIUM_LICENSE` constant in local dev `wp-config.php` (never committed)

**This is not a separate plugin.** The plugin slug remains `zsoogi-clipper`. License key activation is what distinguishes a free install from a premium one.

### `develop` — Integration

- Working branch for features in progress
- Both `main` and `premium` features can be developed here
- Nothing is deployed from `develop` directly — it feeds into `premium` (and subsequently `main`)

### `planning` — Non-Code Docs

- Roadmap, research notes, architecture proposals
- Not merged into `main` or `premium`

---

## Flow: How Code Moves

```
develop
   │
   ▼
premium  ←── new premium features land here first
   │
   │  (strip/flag any unreleased premium-only UI)
   │  (ensure all premium code is behind License::has_feature())
   ▼
main     ←── merged when feature is stable and ready for WordPress.org
   │
   ▼
WordPress.org submission (composer build-zip → upload ZIP)
```

Premium customers receive the build from `premium` (via Pressable or direct download). Free users receive the build from `main` (via WordPress.org). Both ZIPs contain the same code — the license key is the only differentiator at runtime.

---

## Distribution: WordPress.org vs Pressable

### WordPress.org (Free)

**Source branch:** `main`  

**Build command:**  
```bash
composer build-zip
# Output: dist/zsoogi-clipper-{version}.zip
```

**Submit at:** wordpress.org/plugins/zsoogi-clipper (once listed)  

**Checklist before submission:**  

- [ ] No hardcoded license keys or `ZSOOGI_PREMIUM_LICENSE` in committed code
- [ ] All premium features gated behind `License::has_feature()`
- [ ] `readme.txt` updated with current version and changelog
- [ ] `zsoogi-clipper.php` version header bumped
- [ ] `composer lint` passes cleanly
- [ ] Tested on a fresh WordPress install with no license key

### Pressable (Premium)

**Source branch:** `premium`  

**Build command:**  
```bash
composer build-zip
# Output: dist/zsoogi-clipper-{version}.zip
```

**Distribute via:** Pressable managed WordPress or theapiguys.com customer portal, paired with a valid license key.  

**Checklist before distribution:**  

- [ ] License validation in `License::activate()` is wired to real license server (not stubbed)
- [ ] Tested with a valid license key — all premium features unlock correctly
- [ ] Tested with an invalid/expired key — premium features correctly degrade to free tier
- [ ] Version bumped and matches `main` base version (e.g., `main` is `2.4.0`, `premium` is `2.4.1`)

---

## Versioning Convention

| Stream | Example Version | Notes |
|---|---|---|
| Free (main) | `2.4.0` | Major.Minor.Patch |
| Premium (premium) | `2.4.1` | Patch bump on top of free base |

When a premium feature is merged into `main` (becomes free or feature-flagged-free), both streams get a minor bump: `2.5.0`.

---

## What Happened to `ProVersion` and `pwa`?

- **`ProVersion`** — the original branch where YouTube transcript capture was built (v2.4.3). This work has been absorbed into the `premium` branch. `ProVersion` no longer exists as an active remote branch.
- **`pwa`** — progressive web app experiments. Remote branch preserved but not active. Do not base new work on this.
- **`vangeek`** — contributor fork/branch. Remote only. Not part of the active branch strategy.

---

## Local Development

To test premium features locally without a real license key, add to `wp-config.php`:

```php
define( 'ZSOOGI_PREMIUM_LICENSE', true );
```

**Never commit this line.** It bypasses license validation entirely and is for development only. Remove before building a distribution ZIP.

To confirm the constant is not committed:
```bash
git grep ZSOOGI_PREMIUM_LICENSE
# Should only appear in class-license.php, never in wp-config.php
```
