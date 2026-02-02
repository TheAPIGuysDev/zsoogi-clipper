# Shortcode Setup Guide

Use a WordPress shortcode to display MkDocs documentation on any page.

---

## 🚀 Quick Setup

### Step 1: Upload docs-built to Plugin

Upload your `docs-built` directory to:
```
/wp-content/plugins/zsoogi-clipper/claude/docs-built/
```

### Step 2: Create a WordPress Page

1. Go to **Pages → Add New** in WordPress admin
2. Create a page called "Documentation"
3. Set permalink to `/documentation/`
4. Add this shortcode to the page content:

```
[zsoogi_docs]
```

### Step 3: Use a Blank Template (Optional)

For a cleaner look without theme header/footer:

**If using a block theme:**
1. Edit the page
2. Click "Template" in sidebar
3. Choose "Blank" or create a custom template

**If using a classic theme:**
1. Add this to your theme's `functions.php`:

```php
add_filter('template_include', function($template) {
    if (is_page('documentation')) {
        return get_stylesheet_directory() . '/page-docs.php';
    }
    return $template;
});
```

2. Create `page-docs.php` in your theme:

```php
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        body { margin: 0; padding: 0; }
        iframe { border: none; display: block; }
    </style>
</head>
<body <?php body_class(); ?>>
    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
    <?php wp_footer(); ?>
</body>
</html>
```

### Step 4: Publish!

Access your docs at:
```
https://sites.theapiguys.com/zsoogi-clipper/documentation/
```

---

## 📝 Shortcode Options

### Basic Usage

```
[zsoogi_docs]
```

Shows the documentation homepage (`index.html`)

### Specific Page

```
[zsoogi_docs page="getting-started/installation/index.html"]
```

### Custom Height

```
[zsoogi_docs height="1200px"]
```

---

## 🎨 Styling Options

### Full Width Page

Add to page or in Customizer CSS:

```css
.page-documentation .entry-content {
    max-width: 100%;
    padding: 0;
}

.page-documentation iframe {
    width: 100vw;
    height: 100vh;
    margin-left: calc(-50vw + 50%);
}
```

### Remove Theme Padding

```css
.page-documentation {
    padding: 0 !important;
    margin: 0 !important;
}

.page-documentation .site-header,
.page-documentation .site-footer {
    display: none;
}
```

---

## 🔧 Advanced: Dynamic Page Loading

For cleaner URLs like `/documentation/getting-started/`, you can use URL rewriting:

### Add to functions.php:

```php
add_action('init', function() {
    add_rewrite_rule(
        '^documentation/(.+)/?$',
        'index.php?pagename=documentation&doc_page=$matches[1]',
        'top'
    );
});

add_filter('query_vars', function($vars) {
    $vars[] = 'doc_page';
    return $vars;
});
```

### Update shortcode in page:

```php
<?php
$doc_page = get_query_var('doc_page', 'index.html');
if (!empty($doc_page)) {
    $doc_page = $doc_page . '/index.html';
}
echo do_shortcode('[zsoogi_docs page="' . esc_attr($doc_page) . '"]');
?>
```

Now URLs like `/documentation/getting-started/` will load the corresponding doc page!

---

## ✅ Advantages of Shortcode Approach

- ✅ **Simple** - Just add `[zsoogi_docs]` to any page
- ✅ **No Rewrite Rules** - Works immediately
- ✅ **Theme Compatible** - Works with any WordPress theme
- ✅ **Easy Updates** - Just rebuild and upload docs-built
- ✅ **Direct Asset Access** - CSS/JS served directly from plugin URL

---

## 🐛 Troubleshooting

### Iframe Shows 404

**Problem:** `docs-built` directory not uploaded

**Fix:** Upload entire `docs-built` folder to plugin directory

### Assets Not Loading

**Problem:** Files not uploaded or wrong permissions

**Fix:**
```bash
# Set correct permissions
chmod 755 /path/to/docs-built/
chmod 644 /path/to/docs-built/css/*
chmod 644 /path/to/docs-built/js/*
```

### Theme Header/Footer Showing

**Problem:** Using default page template

**Fix:** Use a blank template (see Step 3 above)

---

## 🎯 Complete Example

### 1. Create Page

**Page Title:** Documentation
**URL:** `/documentation/`
**Content:**
```
[zsoogi_docs]
```

### 2. Add Blank Template

Create `page-documentation.php` in your theme:

```php
<?php
/**
 * Template Name: Documentation
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { overflow: hidden; }
        iframe { width: 100vw; height: 100vh; border: none; display: block; }
    </style>
</head>
<body>
    <?php while (have_posts()) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
</body>
</html>
```

### 3. Assign Template

- Edit the Documentation page
- In sidebar: Template → Documentation

### 4. Done!

Visit: `https://sites.theapiguys.com/zsoogi-clipper/documentation/`

---

**Need Help?** Email docs@theapiguys.com
