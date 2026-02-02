# Deploying MkDocs to WordPress Subsite

This guide explains how to deploy the MkDocs documentation to `https://sites.theapiguys.com/zsoogi-clipper/`

## Prerequisites

- WordPress multisite or subdirectory site setup
- FTP/SSH access or file manager
- MkDocs installed locally (`pip install mkdocs`)

---

## Option 1: Direct File Deployment (Recommended)

Deploy the static MkDocs site directly to your WordPress directory.

### Step 1: Build the Site

```bash
cd claude
mkdocs build
```

This creates a `site/` directory with all static HTML files.

### Step 2: Upload to WordPress

Upload the contents of `claude/site/` to your WordPress installation.

**Target Location:**
```
/path/to/wordpress/wp-content/zsoogi-clipper-docs/
```

Or if using WordPress multisite:
```
/path/to/wordpress/wp-content/blogs.dir/[site-id]/files/zsoogi-clipper-docs/
```

**Via FTP/SFTP:**
```bash
# From your local machine
cd claude/site
sftp user@sites.theapiguys.com
cd /path/to/wordpress/wp-content/
mkdir zsoogi-clipper-docs
cd zsoogi-clipper-docs
put -r *
```

**Via rsync:**
```bash
rsync -avz claude/site/ user@sites.theapiguys.com:/path/to/wordpress/wp-content/zsoogi-clipper-docs/
```

### Step 3: Configure WordPress Rewrite Rules

Add this to your theme's `functions.php` or a custom plugin:

```php
<?php
/**
 * Serve MkDocs documentation from custom directory
 */
add_action('init', 'zsoogi_docs_rewrite_rule');
function zsoogi_docs_rewrite_rule() {
    // Rewrite /zsoogi-clipper/* to static docs
    add_rewrite_rule(
        '^zsoogi-clipper/(.*)$',
        'wp-content/zsoogi-clipper-docs/$1',
        'top'
    );
}

// Flush rewrite rules on activation
register_activation_hook(__FILE__, 'flush_rewrite_rules');
```

**Or add to `.htaccess`:**
```apache
# Serve MkDocs documentation
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /

# Redirect /zsoogi-clipper/ to static docs
RewriteCond %{REQUEST_URI} ^/zsoogi-clipper/(.*)
RewriteCond %{DOCUMENT_ROOT}/wp-content/zsoogi-clipper-docs/%1 -f [OR]
RewriteCond %{DOCUMENT_ROOT}/wp-content/zsoogi-clipper-docs/%1 -d
RewriteRule ^zsoogi-clipper/(.*)$ /wp-content/zsoogi-clipper-docs/$1 [L]
</IfModule>
```

### Step 4: Test

Visit: `https://sites.theapiguys.com/zsoogi-clipper/`

---

## Option 2: Subdomain (Easier Alternative)

Instead of a subdirectory, use a subdomain for cleaner separation.

### Setup

1. Point subdomain to docs directory:
   - DNS: `docs.theapiguys.com` → Server IP
   - Web server config: Document root = `/path/to/claude/site/`

2. Build and deploy:
```bash
cd claude
mkdocs build
rsync -avz site/ user@server:/path/to/docs-root/
```

3. Access at: `https://docs.theapiguys.com/zsoogi-clipper/`

**Benefits:**
- No WordPress conflicts
- Faster (no WordPress overhead)
- Easier caching
- Simpler deployment

---

## Option 3: WordPress Page with Iframe

Embed the docs in a WordPress page using an iframe.

### Step 1: Upload MkDocs Site

Upload `claude/site/` contents to:
```
/wp-content/uploads/zsoogi-docs/
```

### Step 2: Create WordPress Page

Create a page at `/zsoogi-clipper/` with this content:

```html
<iframe
    src="/wp-content/uploads/zsoogi-docs/index.html"
    width="100%"
    height="1200px"
    frameborder="0"
    style="border: none; min-height: 100vh;"
></iframe>

<script>
// Auto-resize iframe to content
window.addEventListener('message', function(e) {
    if (e.data.height) {
        document.querySelector('iframe').style.height = e.data.height + 'px';
    }
});
</script>
```

**Cons:**
- Iframe limitations (scrolling, SEO)
- Navigation issues
- Not recommended for production

---

## Option 4: GitHub Pages + Redirect

Host on GitHub Pages and redirect from WordPress.

### Setup

```bash
# Deploy to GitHub Pages
cd claude
mkdocs gh-deploy
```

Docs will be at: `https://[your-org].github.io/zsoogi-clipper/`

### WordPress Redirect

Add to `functions.php`:
```php
add_action('template_redirect', function() {
    if (is_page('zsoogi-clipper')) {
        wp_redirect('https://theapiguysdv.github.io/zsoogi-clipper/', 301);
        exit;
    }
});
```

---

## Option 5: WordPress Plugin Integration

Create a WordPress plugin that serves the MkDocs content directly.

### Create Plugin Structure

```
wp-content/plugins/zsoogi-docs/
├── zsoogi-docs.php
└── docs/              # Copy of claude/site/
    ├── index.html
    ├── css/
    ├── js/
    └── ...
```

### Plugin Code (`zsoogi-docs.php`)

```php
<?php
/**
 * Plugin Name: Zsoogi Clipper Docs
 * Description: Serves MkDocs documentation
 * Version: 1.0.0
 */

add_action('init', 'zsoogi_docs_init');
function zsoogi_docs_init() {
    // Register custom rewrite
    add_rewrite_rule(
        '^zsoogi-clipper/?(.*)$',
        'index.php?zsoogi_docs_page=$matches[1]',
        'top'
    );

    // Register query var
    add_filter('query_vars', function($vars) {
        $vars[] = 'zsoogi_docs_page';
        return $vars;
    });
}

add_action('template_redirect', 'zsoogi_docs_serve');
function zsoogi_docs_serve() {
    $page = get_query_var('zsoogi_docs_page');

    if ($page !== '') {
        $docs_dir = plugin_dir_path(__FILE__) . 'docs/';

        // Default to index.html if no page specified
        if (empty($page)) {
            $page = 'index.html';
        }

        // Security: Prevent directory traversal
        $file = realpath($docs_dir . $page);
        if (strpos($file, $docs_dir) !== 0) {
            wp_die('Invalid path');
        }

        // Serve file if exists
        if (file_exists($file)) {
            // Set appropriate content type
            $mime = mime_content_type($file);
            header('Content-Type: ' . $mime);
            readfile($file);
            exit;
        } else {
            wp_die('Page not found', '', 404);
        }
    }
}

// Flush rewrite rules on activation
register_activation_hook(__FILE__, 'flush_rewrite_rules');
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
```

### Deploy Steps

1. Build MkDocs:
```bash
cd claude
mkdocs build
```

2. Copy to plugin:
```bash
cp -r site/* wp-content/plugins/zsoogi-docs/docs/
```

3. Activate plugin in WordPress

4. Visit: `https://sites.theapiguys.com/zsoogi-clipper/`

---

## Recommended Approach

**For Production: Option 1 (Direct File Deployment)**
- Fast (no PHP processing)
- SEO-friendly
- Easy to update

**For Development: Option 2 (Subdomain)**
- Cleanest separation
- No WordPress interference
- Best performance

---

## Automated Deployment Script

Create `claude/deploy.sh`:

```bash
#!/bin/bash

# Deploy MkDocs to WordPress

# Configuration
REMOTE_USER="your-user"
REMOTE_HOST="sites.theapiguys.com"
REMOTE_PATH="/path/to/wordpress/wp-content/zsoogi-clipper-docs/"

# Build
echo "Building MkDocs site..."
mkdocs build

# Deploy
echo "Deploying to $REMOTE_HOST..."
rsync -avz --delete site/ $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH

echo "Deployment complete!"
echo "Visit: https://sites.theapiguys.com/zsoogi-clipper/"
```

Make executable:
```bash
chmod +x claude/deploy.sh
```

Run deployment:
```bash
cd claude
./deploy.sh
```

---

## Testing Locally with Subdirectory

Test the subdirectory setup locally before deploying:

```bash
cd claude

# Build with correct site_url
mkdocs build

# Serve on a different port to test
cd site
python3 -m http.server 8001
```

Visit: `http://localhost:8001/`

---

## Troubleshooting

### CSS/JS Not Loading

**Problem:** Assets return 404

**Solution:** Check `site_url` in `mkdocs.yml` matches your deployment URL:
```yaml
site_url: https://sites.theapiguys.com/zsoogi-clipper/
```

### Links Don't Work

**Problem:** Internal links 404

**Solution:** Ensure `use_directory_urls: true` in `mkdocs.yml`

### WordPress 404s

**Problem:** WordPress shows 404 for docs pages

**Solution:** Flush rewrite rules:
1. Go to Settings → Permalinks
2. Click "Save Changes" (without changing anything)

### Images Not Loading

**Problem:** Images show broken

**Solution:** Check image paths in markdown are relative:
```markdown
![Image](../images/example.png)  # Good
![Image](/images/example.png)    # Bad (absolute)
```

---

## Maintenance

### Updating Documentation

1. Edit markdown files in `claude/docs/`
2. Build: `mkdocs build`
3. Deploy: `./deploy.sh` or manual rsync/FTP

### Automated Updates (GitHub Actions)

Create `.github/workflows/deploy-docs.yml`:

```yaml
name: Deploy MkDocs

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
        uses: wlixcc/SFTP-Deploy-Action@v1.2.4
        with:
          username: ${{ secrets.SFTP_USER }}
          server: ${{ secrets.SFTP_HOST }}
          password: ${{ secrets.SFTP_PASSWORD }}
          local_path: './claude/site/*'
          remote_path: '/path/to/wordpress/wp-content/zsoogi-clipper-docs/'
```

---

## Security Considerations

1. **Disable Directory Listing:**
Add to `.htaccess` in docs directory:
```apache
Options -Indexes
```

2. **Protect Source Files:**
Don't upload `claude/docs/` markdown source to production

3. **Cache Control:**
Add cache headers for static assets:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/html "access plus 1 hour"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 year"
</IfModule>
```

---

## Questions?

- Need help with deployment? Email: docs@theapiguys.com
- Found an issue? [Open GitHub Issue](https://github.com/TheAPIGuysDev/zsoogi-clipper/issues)
