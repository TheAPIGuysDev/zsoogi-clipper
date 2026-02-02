# Simple Setup - No Theme Changes Needed!

Everything is handled in the plugin. Zero theme modifications required.

---

## 🚀 Setup (2 Steps!)

### Step 1: Upload docs-built to Server

Upload the entire `docs-built` folder to:

```
/srv/htdocs/wp-content/plugins/zsoogi-clipper/claude/docs-built/
```

**What to upload:**
- `docs-built/index.html`
- `docs-built/css/` (entire directory)
- `docs-built/js/` (entire directory)
- `docs-built/getting-started/` (entire directory)
- All other files and folders

### Step 2: Add Shortcode to Page

You already have a "Docs" page! Just add this to the content:

```
[zsoogi_docs]
```

**That's it!** The plugin will:
- ✅ Automatically use a blank template (no header/footer)
- ✅ Serve the docs in a full-screen iframe
- ✅ Load all CSS/JS assets correctly

---

## 📱 How It Works

1. **You add** `[zsoogi_docs]` to any WordPress page
2. **Plugin detects** the shortcode automatically
3. **Plugin applies** blank template (no theme needed!)
4. **Docs display** full-screen with all assets working

---

## 🎯 Access Your Docs

**URL:** `https://sites.theapiguys.com/zsoogi-clipper/docs/`

(Or whatever permalink you set for your "Docs" page)

---

## 🔧 Optional: Shortcode Parameters

### Show specific page:
```
[zsoogi_docs page="getting-started/installation/index.html"]
```

### Custom height (if not full-screen):
```
[zsoogi_docs height="800px"]
```

---

## 📁 Files the Plugin Provides

**From the plugin (no theme changes):**
- `zsoogi-docs-server.php` - Main plugin with shortcode
- `template-blank-docs.php` - Blank template (automatically applied)

**What you provide:**
- Upload `docs-built/` to plugin directory
- Add `[zsoogi_docs]` to a WordPress page

---

## ✅ Checklist

- [ ] Upload `docs-built/` to `/wp-content/plugins/zsoogi-clipper/claude/docs-built/`
- [ ] Edit your "Docs" page
- [ ] Add shortcode: `[zsoogi_docs]`
- [ ] Save/Publish
- [ ] Visit the page - docs should display full-screen!

---

## 🐛 Troubleshooting

### Blank page or 404 in iframe

**Problem:** `docs-built` not uploaded

**Fix:** Upload entire `docs-built` directory to plugin folder

### Theme header/footer still showing

**Problem:** Shortcode not detected

**Fix:** Make sure shortcode is spelled exactly: `[zsoogi_docs]` (with underscore)

### Assets not loading

**Problem:** Files not uploaded or wrong location

**Fix:**
```bash
# Verify files exist
ls /srv/htdocs/wp-content/plugins/zsoogi-clipper/claude/docs-built/
ls /srv/htdocs/wp-content/plugins/zsoogi-clipper/claude/docs-built/css/
```

---

## 🎉 Done!

No theme changes, no complex setup. Just:
1. Upload docs
2. Add shortcode
3. It works!

**Questions?** Email docs@theapiguys.com
