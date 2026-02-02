# WordPress Multisite Troubleshooting

Quick fixes for serving MkDocs on WordPress Multisite.

---

## Expected URL

Your docs should be accessible at:

**https://sites.theapiguys.com/zsoogi-clipper/**

**NOT:** `https://sites.theapiguys.com/zsoogi-clipper/zsoogi_docs_page`

> The `zsoogi_docs_page` is an internal WordPress query variable, not part of the URL.

---

## Quick Fix: Flush Permalinks

This solves 90% of rewrite rule issues:

1. Go to **Settings → Permalinks** in WordPress admin
2. Click **Save Changes** (don't change anything)
3. Test: Visit `https://sites.theapiguys.com/zsoogi-clipper/`

---

## Step-by-Step Debugging

### Step 1: Check Plugin is Active

1. Go to **Plugins** in WordPress admin
2. Find "Zsoogi Clipper Documentation Server"
3. Should show **Activate** (not "Activate" button)
4. If you see debug info box, good! Plugin is working

### Step 2: Check Docs Directory Exists

Run this on server or via SSH:

```bash
ls -la /path/to/wordpress/wp-content/zsoogi-clipper-docs/
```

Should see:
```
index.html
css/
js/
getting-started/
...
```

If missing, deploy docs:
```bash
cd claude
./deploy.sh
```

### Step 3: Check File Permissions

```bash
# Check directory permissions
ls -ld /path/to/wordpress/wp-content/zsoogi-clipper-docs/
# Should be: drwxr-xr-x (755)

# Check file permissions
ls -l /path/to/wordpress/wp-content/zsoogi-clipper-docs/index.html
# Should be: -rw-r--r-- (644)
```

Fix if needed:
```bash
chmod 755 /path/to/wordpress/wp-content/zsoogi-clipper-docs/
chmod 644 /path/to/wordpress/wp-content/zsoogi-clipper-docs/index.html
```

### Step 4: Check Rewrite Rules

Add this temporarily to `zsoogi-docs-server.php` (around line 100):

```php
public static function serve_documentation() {
    // DEBUG: Log all requests
    error_log( 'REQUEST_URI: ' . $_SERVER['REQUEST_URI'] );
    error_log( 'Query var: ' . get_query_var( 'zsoogi_docs_page', 'NOT_SET' ) );

    $page = get_query_var( 'zsoogi_docs_page', null );
    // ... rest of function
}
```

Then check error log:
```bash
tail -f /path/to/wordpress/wp-content/debug.log
```

Visit the URL and see what gets logged.

### Step 5: Network Activate (Multisite Only)

If on multisite and docs only work on main site:

1. **Network Deactivate** the plugin
2. **Network Activate** the plugin
3. Go to **Settings → Permalinks** on EACH site
4. Click **Save Changes** on each

---

## Common Multisite Issues

### Issue 1: Works on Main Site, Not on Subsite

**Cause:** Rewrite rules not activated on subsite

**Fix:**
1. Switch to the subsite in admin
2. Go to **Settings → Permalinks**
3. Click **Save Changes**

Or deactivate/reactivate plugin on that specific site.

### Issue 2: 404 on All Multisite Sites

**Cause:** WordPress multisite URL conflicts

**Fix:** Check if "zsoogi-clipper" conflicts with any site slugs:

1. Go to **Network Admin → Sites**
2. Check if any site uses "zsoogi-clipper" as its slug
3. If yes, either:
   - Rename that site, OR
   - Change the docs URL in plugin (edit `DOCS_URL_BASE` constant)

### Issue 3: Shows WordPress 404 Page

**Cause:** Rewrite rules not registered

**Fix:**
```php
// Deactivate plugin
// Reactivate plugin
// Go to Settings → Permalinks → Save
```

### Issue 4: Blank Page (No Error)

**Cause:** PHP error or file not found

**Fix:** Enable WordPress debugging:

Add to `wp-config.php`:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Check `wp-content/debug.log` for errors.

---

## Manual Testing

### Test 1: Check if Query Var is Registered

Add to `functions.php` temporarily:

```php
add_action( 'template_redirect', function() {
    if ( isset( $_GET['test_zsoogi'] ) ) {
        global $wp;
        echo '<pre>';
        echo "Query vars:\n";
        print_r( $wp->query_vars );
        echo "\n\nRewrite rules:\n";
        print_r( get_option( 'rewrite_rules' ) );
        echo '</pre>';
        exit;
    }
});
```

Visit: `https://sites.theapiguys.com/?test_zsoogi=1`

Look for `zsoogi_docs_page` in query_vars and `zsoogi-clipper` in rewrite_rules.

### Test 2: Direct File Access

Try accessing the file directly via URL:

`https://sites.theapiguys.com/wp-content/zsoogi-clipper-docs/index.html`

If this works but `/zsoogi-clipper/` doesn't, it's a rewrite rule issue.

---

## Alternative: .htaccess Approach (If Plugin Fails)

If plugin approach doesn't work, use direct `.htaccess` rewrite:

Add to `/path/to/wordpress/.htaccess` BEFORE WordPress rules:

```apache
# Zsoogi Docs - Serve from /zsoogi-clipper/
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /

# Match /zsoogi-clipper/ and everything after
RewriteCond %{REQUEST_URI} ^/zsoogi-clipper(/.*)?$ [NC]
RewriteCond %{DOCUMENT_ROOT}/wp-content/zsoogi-clipper-docs%1 -f [OR]
RewriteCond %{DOCUMENT_ROOT}/wp-content/zsoogi-clipper-docs%1 -d
RewriteRule ^zsoogi-clipper(/.*)?$ /wp-content/zsoogi-clipper-docs$1 [L]

# Default to index.html for directories
RewriteCond %{REQUEST_URI} ^/zsoogi-clipper(/.*)?$ [NC]
RewriteCond %{DOCUMENT_ROOT}/wp-content/zsoogi-clipper-docs%1/index.html -f
RewriteRule ^zsoogi-clipper(/.*)?$ /wp-content/zsoogi-clipper-docs$1/index.html [L]
</IfModule>
```

Place it ABOVE these lines:
```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
```

---

## Verification Checklist

- [ ] Plugin is activated
- [ ] Docs directory exists at `/wp-content/zsoogi-clipper-docs/`
- [ ] `index.html` exists in docs directory
- [ ] File permissions are correct (755 dirs, 644 files)
- [ ] Permalinks flushed (Settings → Permalinks → Save)
- [ ] No site with slug "zsoogi-clipper" (multisite only)
- [ ] Tested on correct site/subsite

---

## Still Not Working?

### Check Server Configuration

Some hosts block certain URL patterns. Check with your host if:
- Using shared hosting
- Using managed WordPress hosting (WP Engine, Kinsta, etc.)
- Behind CloudFlare or proxy

### Contact Support

If still stuck, gather this info:

1. WordPress version
2. Multisite? (Yes/No)
3. Permalink structure
4. Any custom rewrite rules?
5. Hosting provider
6. Error log output
7. Result of checklist above

Email: docs@theapiguys.com

---

## Success!

Once working, you should see:

**https://sites.theapiguys.com/zsoogi-clipper/** → Your MkDocs homepage

All internal links should work:
- `/zsoogi-clipper/getting-started/installation/`
- `/zsoogi-clipper/premium-features/`
- etc.

Set `DEBUG_MODE = false` in plugin to hide debug notices.

---

**Need More Help?**

- [Full Deployment Guide](DEPLOYMENT.md)
- [Installation Guide](INSTALL-WORDPRESS.md)
- Email: docs@theapiguys.com
