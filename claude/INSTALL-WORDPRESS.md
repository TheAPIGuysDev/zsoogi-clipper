# Installing MkDocs to WordPress

Quick guide for serving MkDocs documentation at `https://sites.theapiguys.com/zsoogi-clipper/`

---

## Overview

This approach uses a simple WordPress plugin to serve static MkDocs files from `/wp-content/zsoogi-clipper-docs/` via the URL `/zsoogi-clipper/`.

**Benefits:**
- ✅ Fast (static files, no database)
- ✅ SEO-friendly (proper URLs, no iframes)
- ✅ Easy to update (just rebuild and copy)
- ✅ No theme modifications required
- ✅ Works with any WordPress setup

---

## Installation Steps

### Step 1: Install the Plugin

Copy the plugin file to your WordPress plugins directory:

```bash
# From your local machine
scp claude/zsoogi-docs-server.php user@sites.theapiguys.com:/path/to/wordpress/wp-content/plugins/
```

Or manually:
1. Download `zsoogi-docs-server.php`
2. Upload to WordPress via FTP: `/wp-content/plugins/zsoogi-docs-server.php`

### Step 2: Activate the Plugin

1. Log in to WordPress admin
2. Go to **Plugins**
3. Find "Zsoogi Clipper Documentation Server"
4. Click **Activate**

You'll see a warning notice that the docs directory doesn't exist yet - that's expected!

### Step 3: Build and Deploy Documentation

#### Option A: Automatic Deployment (Recommended)

From your local machine:

```bash
cd claude
./deploy.sh
```

This will:
- Build the MkDocs site
- Copy files to the correct WordPress directory
- Set proper permissions

#### Option B: Manual Deployment

```bash
# Build the site
cd claude
mkdocs build

# Copy to WordPress
scp -r site/* user@sites.theapiguys.com:/path/to/wordpress/wp-content/zsoogi-clipper-docs/
```

#### Option C: Local WordPress Development

If working on Local by Flywheel or similar:

```bash
cd claude
mkdocs build

# Copy to WordPress directory
cp -r site/* ../../../zsoogi-clipper-docs/
```

### Step 4: Test

Visit: **https://sites.theapiguys.com/zsoogi-clipper/**

You should see your MkDocs documentation!

---

## Directory Structure

After deployment, your WordPress directory should look like:

```
wordpress/
├── wp-content/
│   ├── plugins/
│   │   └── zsoogi-docs-server.php          # The plugin
│   └── zsoogi-clipper-docs/                # MkDocs static files
│       ├── index.html
│       ├── getting-started/
│       │   ├── installation/
│       │   │   └── index.html
│       │   └── ...
│       ├── css/
│       ├── js/
│       └── ...
└── ...
```

---

## Updating Documentation

When you make changes to the docs:

```bash
cd claude

# Edit files in docs/
vim docs/index.md

# Rebuild and deploy
./deploy.sh
```

That's it! Changes are live immediately (no WordPress cache flush needed for static files).

---

## Configuration

### Custom Docs Directory

Edit `zsoogi-docs-server.php` line 30 to change the directory:

```php
const DOCS_DIR = 'wp-content/your-custom-path/';
```

### Custom URL Path

Edit `zsoogi-docs-server.php` line 36 to change the URL:

```php
const DOCS_URL_BASE = 'your-custom-path';
```

After changing, deactivate and reactivate the plugin to flush rewrite rules.

---

## Troubleshooting

### 404 Error

**Problem:** `/zsoogi-clipper/` shows WordPress 404 page

**Solutions:**
1. Make sure plugin is activated
2. Flush WordPress permalinks:
   - Go to **Settings → Permalinks**
   - Click **Save Changes** (without changing anything)
3. Check that docs directory exists: `/wp-content/zsoogi-clipper-docs/`

### CSS/JS Not Loading

**Problem:** Page loads but no styling

**Solutions:**
1. Check browser console for 404 errors
2. Verify `site_url` in `mkdocs.yml` matches your domain:
   ```yaml
   site_url: https://sites.theapiguys.com/zsoogi-clipper/
   ```
3. Rebuild MkDocs: `mkdocs build --clean`
4. Clear browser cache

### Internal Links 404

**Problem:** Clicking links within docs shows 404

**Solution:** Check `use_directory_urls` in `mkdocs.yml`:
```yaml
use_directory_urls: true
```

### Permission Denied

**Problem:** Can't write to WordPress directory

**Solutions:**
1. Check directory permissions:
   ```bash
   ls -la /path/to/wordpress/wp-content/
   ```
2. Create directory manually:
   ```bash
   mkdir -p /path/to/wordpress/wp-content/zsoogi-clipper-docs
   chmod 755 /path/to/wordpress/wp-content/zsoogi-clipper-docs
   ```
3. On shared hosting, use FTP/File Manager instead

---

## Advanced: Automated Deployment

### GitHub Actions

Create `.github/workflows/deploy-docs.yml`:

```yaml
name: Deploy Docs to WordPress

on:
  push:
    branches: [main]
    paths:
      - 'claude/docs/**'
      - 'claude/mkdocs.yml'

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup Python
        uses: actions/setup-python@v4
        with:
          python-version: '3.x'

      - name: Install MkDocs
        run: pip install mkdocs

      - name: Build
        run: cd claude && mkdocs build

      - name: Deploy via SFTP
        uses: SamKirkland/FTP-Deploy-Action@4.3.0
        with:
          server: ${{ secrets.FTP_HOST }}
          username: ${{ secrets.FTP_USER }}
          password: ${{ secrets.FTP_PASSWORD }}
          local-dir: ./claude/site/
          server-dir: /wp-content/zsoogi-clipper-docs/
```

Add secrets in GitHub:
- `FTP_HOST`: sites.theapiguys.com
- `FTP_USER`: your FTP username
- `FTP_PASSWORD`: your FTP password

### SSH Deploy Script

For direct SSH access, update `deploy.sh`:

```bash
#!/bin/bash

# Configuration
REMOTE_USER="your-user"
REMOTE_HOST="sites.theapiguys.com"
REMOTE_PATH="/var/www/html/wp-content/zsoogi-clipper-docs/"

# Build
echo "Building..."
mkdocs build --clean

# Deploy
echo "Deploying..."
rsync -avz --delete site/ $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH

echo "Done! Visit https://sites.theapiguys.com/zsoogi-clipper/"
```

---

## Performance Optimization

### Enable Caching

Add to `/wp-content/zsoogi-clipper-docs/.htaccess`:

```apache
<IfModule mod_expires.c>
    ExpiresActive On

    # HTML (1 hour)
    ExpiresByType text/html "access plus 1 hour"

    # CSS, JS (1 month)
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"

    # Images (1 year)
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"

    # Fonts (1 year)
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

<IfModule mod_headers.c>
    # Enable gzip
    Header set Vary "Accept-Encoding"
</IfModule>
```

### Disable Directory Listing

Add to same `.htaccess`:

```apache
Options -Indexes
```

---

## Security

### Protect Source Files

Never upload your markdown source files (`claude/docs/`) to production. Only upload the built HTML (`claude/site/`).

### File Permissions

```bash
# Directories: 755
find /path/to/zsoogi-clipper-docs -type d -exec chmod 755 {} \;

# Files: 644
find /path/to/zsoogi-clipper-docs -type f -exec chmod 644 {} \;
```

---

## Uninstalling

1. Deactivate plugin in WordPress
2. Delete plugin file: `/wp-content/plugins/zsoogi-docs-server.php`
3. (Optional) Delete docs directory: `/wp-content/zsoogi-clipper-docs/`
4. Flush permalinks: Settings → Permalinks → Save

---

## Summary

**Setup (one time):**
1. Upload `zsoogi-docs-server.php` to `/wp-content/plugins/`
2. Activate plugin in WordPress admin

**Deploy (every update):**
1. Edit docs in `claude/docs/`
2. Run `./deploy.sh`
3. Done!

**URL:** https://sites.theapiguys.com/zsoogi-clipper/

---

## Need Help?

- **Plugin issues:** Check WordPress admin → Plugins → Zsoogi Docs Server
- **Build issues:** Check `mkdocs.yml` configuration
- **Deploy issues:** Check file permissions and paths
- **Support:** Email docs@theapiguys.com

---

**Happy Documenting! 📚**
