# Development

## Environment

- **Local dev:** LocalWP at `zsoogi.local`
- **Plugin path:** `/Users/pbrocks/Documents/Local/zsoogi/app/public/wp-content/plugins/zsoogi-clipper`
- **Branch in use:** `pwa` (main branch: `main`)

## Composer

Composer is **optional** — only needed for development tooling. The plugin works in production without `vendor/`.

```bash
# Install dev dependencies (PHPCS + WPCS)
composer install

# Lint
composer lint

# Auto-fix PHPCS violations
composer lint-fix

# Build production ZIP
composer build-zip
```

### Dev Dependencies

| Package                     | Purpose                    |
| --------------------------- | -------------------------- |
| `squizlabs/php_codesniffer` | PHP_CodeSniffer            |
| `wp-coding-standards/wpcs`  | WordPress Coding Standards |
| `phpcsstandards/phpcsextra` | Extra PHPCS rules          |
| `phpcsstandards/phpcsutils` | PHPCS utilities            |

### PSR-4 Autoloading

```json
"autoload": {
    "psr-4": {
        "Zsoogi\\": "includes/"
    }
}
```

Without Composer, the 3 class files are `require_once`'d directly in `zsoogi_clipper_load_includes()`.

## PHPCS Configuration

`phpcs.xml.dist` at the project root configures:

- Standard: WordPress
- Installed paths auto-configured via `post-install-cmd` Composer script

Run manually:

```bash
vendor/bin/phpcs
vendor/bin/phpcbf   # auto-fix
```

## Build Process

`build.sh` creates a production-ready ZIP at `dist/zsoogi-clipper-{version}.zip`.

The ZIP excludes:

- `vendor/`
- `.git/`
- `.env`
- `composer.json`, `composer.lock`
- `build.sh`
- `phpcs.xml.dist`
- `claude/` (this docs directory)

Version is extracted from the plugin header `Version:` line automatically.

## Git Branches

| Branch       | Version | Description                                            |
| ------------ | ------- | ------------------------------------------------------ |
| `main`       | v2.4.0  | Stable — YouTube title cleanup + thumbnail integration |
| `pwa`        | current | Active development                                     |
| `ProVersion` | v2.4.3  | YouTube transcript capture (advanced)                  |

The ProVersion branch uses a `window.name` bridge pattern for cross-origin data passing. See [GitHub Issue #5](https://github.com/pbrocks/zsoogi-clipper/issues/5) for technical details.

## MkDocs (This Site)

All docs live in `claude/docs/`. The `claude/` directory contains:

```
claude/
├── mkdocs.yml          # Site config
├── hooks.py            # Auto-injects plugin version at build time
├── mkdocs-serve.sh     # Local preview: cd claude/ && mkdocs serve
├── mkdocs-deploy.sh    # Build + rsync deploy
└── docs/
    ├── index.md
    ├── architecture.md
    ├── bookmarklet.md
    ├── post-type.md
    ├── settings.md
    └── development.md
```

### Local Preview

```bash
# Requires: pip install mkdocs
./claude/mkdocs-serve.sh
# Visit http://127.0.0.1:8000
```

### Deploy

Configure `.env` in the plugin root (gitignored):

```
DOCS_SSH_HOST=your.server.com
DOCS_SSH_USER=username
DOCS_SSH_PRIVATE_KEY=/Users/you/.ssh/id_rsa
DOCS_SSH_PATH=/var/www/docs/zsoogi-clipper
DOCS_SSH_PORT=22
```

Then:

```bash
./claude/mkdocs-deploy.sh           # build + deploy
./claude/mkdocs-deploy.sh --build   # build only
./claude/mkdocs-deploy.sh --deploy  # deploy only
```

## WP-CLI Commands

The plugin registers custom WP-CLI commands under the `wp zsoogi` namespace.

### `wp zsoogi pwa-files`

Writes physical `manifest.json` and `sw.js` files to the WordPress document root (`ABSPATH`).

```bash
wp zsoogi pwa-files
```

**When to run:** After every plugin deployment to a host where nginx intercepts requests before WordPress rewrite rules run (e.g. SiteGround). Without physical files, requests to `/manifest.json` and `/sw.js` trigger nginx bot-protection challenges before WordPress can serve them dynamically.

**What it does:**

1. Builds the Web App Manifest from live site data (site name, plugin URL, icon paths)
2. Reads `assets/js/sw.js` and replaces the `__VERSION__` placeholder with the current plugin version
3. Writes both files to `ABSPATH` (e.g. `/home/user/www/site/public_html/`)

**Output example:**

```
Writing PWA static files to /home/u90-108mttff08fo/www/zsooginotes.site/public_html/ ...
Success: manifest.json and sw.js written to /home/.../public_html/ (plugin v0.9.3)
```

> `deploy.sh` calls this automatically after a successful rsync. If it fails, run it manually via SSH.

## Known Issues / Watch Out For

- **Text domains:** Internal code uses `zsoogi-clipss` and `zsoogi-clips` in some legacy spots alongside the current `zsoogi-clipper` domain. Keep backward-compatible.
- **REST API visibility:** `zsoogiclips` is `publicly_queryable: true` so the REST endpoint may expose posts to unauthenticated requests. Frontend template redirect guards the site, but REST is a separate surface.
- **Rewrite rules:** After any CPT/taxonomy change, flush via Settings → Permalinks.
- **Renaming from Wiki Clipper:** The plugin was originally "Wiki Clipper". Spot-check for any remaining `wiki` or `wikiclipper` references in strings, slugs, or option names.
