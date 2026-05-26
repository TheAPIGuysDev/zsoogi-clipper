# WordPress.org Submission Checklist

Tracking the preparation and submission of Zsoogi Clipper v1.0.0 to the WordPress Plugin Repository.

---

## Pre-Submission — Completed 2026-05-26

### Plugin Files

- [x] Version reset to `1.0.0` in plugin header and `ZSOOGI_CLIPS_VERSION` constant
- [x] License changed to `GPL v2 or later` (header + readme.txt consistent)
- [x] Author URI updated to `https://theapiguys.com`
- [x] Plugin URI updated to `https://theapiguys.com/zsoogi-clipper`
- [x] CSS class typo fixed: `zsoogi-clipss-info` → `zsoogi-clipper-info`
- [x] `class-license.php` deleted — pro-only, no place in free repo
- [x] `class-abilities.php` deleted — pro-only, no place in free repo

### readme.txt

- [x] `Stable tag` set to `1.0.0`
- [x] Contributors: `pbarthmaier, pbrocks` (both valid WordPress.org usernames)
- [x] Changelog cleaned up — single `1.0.0` entry, no dev version history
- [x] Upgrade Notice updated to `1.0.0`
- [x] Plugin constants corrected: `ZSOOGI_CLIPS_*` (not `ZSOOGI_CLIPPER_*`)
- [x] Screenshots section restored with 4 real screenshots

### Assets

- [x] `assets/screenshot-1.png` — Zsoogi clips post type list
- [x] `assets/screenshot-2.png` — Settings page
- [x] `assets/screenshot-3.png` — Bookmarklet installation page
- [x] `assets/screenshot-4.png` — Zsoogi Type taxonomy
- [x] Duplicate `assets/api-guys.png` removed
- [x] `wiki-clipper-demo.gif` renamed to `zsoogi-clipper-demo.gif`

### Build

- [x] `build.sh` excludes: `claude/`, `deploy.sh`, `README.md`, premium classes, screencasts, `screenshot-*.png`
- [x] `deploy.sh` excludes: `build.sh`, `CLAUDE.md`, `claude/`, `README.md`, `composer.json`
- [x] Final ZIP: `dist/zsoogi-clipper-1.0.0.zip` (~115KB, clean)

---

## Submission

- [x] Log in to WordPress.org as `pbarthmaier`
- [x] Go to [wordpress.org/plugins/developers/add](https://wordpress.org/plugins/developers/add)
- [x] Upload `dist/zsoogi-clipper-1.0.0.zip`
- [x] Complete submission form — submitted 2026-05-26
- [x] Slug confirmed: `zsoogi-clipper`
- [ ] Await review email to paul@theapiguys.com — subject: "[WordPress Plugin Directory] Review in Progress: Zsoogi Clipper"

---

## After Approval — SVN Setup

WP.org will email SVN credentials to the `pbarthmaier` account.

```bash
# Check out the SVN repo
svn co https://plugins.svn.wordpress.org/zsoogi-clipper/ zsoogi-clipper-svn
cd zsoogi-clipper-svn

# Copy plugin files to trunk
cp -r /path/to/dist/zsoogi-clipper-1.0.0/* trunk/

# Copy WP.org listing assets (NOT inside trunk)
cp /path/to/assets/screenshot-*.png assets/
# Also add: assets/banner-1544x500.png, assets/icon-256x256.png

# Commit trunk
svn add trunk/*
svn ci -m "Add version 1.0.0" --username pbarthmaier

# Tag the release
svn cp trunk tags/1.0.0
svn ci -m "Tag version 1.0.0" --username pbarthmaier
```

---

## SVN Assets Still Needed

These go in the SVN `assets/` folder (outside `trunk/`) and control the WP.org listing appearance:

- [ ] `banner-1544x500.png` — listing page header banner
- [ ] `banner-772x250.png` — retina banner (optional)
- [ ] `icon-256x256.png` — plugin icon (square)
- [ ] `icon-128x128.png` — smaller icon (optional)
- [ ] Screenshots are ready (`screenshot-1.png` through `screenshot-4.png`)

---

## Ongoing Release Process

For each future version:

1. Update version in `zsoogi-clipper.php` header and `ZSOOGI_CLIPS_VERSION`
2. Update `Stable tag` in `readme.txt`
3. Add changelog entry under `== Changelog ==`
4. Run `composer build-zip`
5. Commit and push to GitHub
6. Copy files to SVN `trunk/`
7. `svn ci -m "Update to vX.X.X"`
8. `svn cp trunk tags/X.X.X && svn ci -m "Tag vX.X.X"`
