# Post Type & Taxonomy

## Custom Post Type: `zsoogiclips`

**Do not change this slug** — it is kept for backward compatibility with existing content and installations.

### Registration Arguments

| Argument | Value |
|---|---|
| `label` | Zsoogi Clips |
| `supports` | title, editor, thumbnail, author, comments, revisions |
| `hierarchical` | false |
| `public` | true |
| `has_archive` | true |
| `exclude_from_search` | false |
| `publicly_queryable` | true |
| `capability_type` | `page` (uses edit_pages, delete_pages, etc.) |
| `show_in_rest` | true |
| `menu_icon` | `dashicons-rest-api` |
| `menu_position` | 5 |

### Archive URL

`/zsoogiclips/` — accessible only to logged-in administrators.

### Built-in Taxonomies

`category` and `post_tag` are registered for the CPT after initial registration (priority 10) to avoid Multisite race conditions.

## Taxonomy: `zsoogi_type`

**Do not change this slug** — backward-compatible with existing term assignments.

### Registration Arguments

| Argument | Value |
|---|---|
| `hierarchical` | true (category-style) |
| `public` | true |
| `show_ui` | true |
| `show_admin_column` | true |
| `show_in_nav_menus` | true |
| `show_tagcloud` | true |
| `show_in_rest` | true |

### Default Term

On `init` (priority 20), `ensure_default_term()` creates a "Research" term (slug: `research`) if it doesn't exist. New clips without any term assigned automatically receive "Research" via the `save_post_zsoogiclips` hook.

## Frontend Access Control

`restrict_frontend_access()` runs on `template_redirect` and redirects to `home_url()` when:

- `is_singular('zsoogiclips')` AND user is not a logged-in administrator
- `is_post_type_archive('zsoogiclips')` AND user is not a logged-in administrator
- `is_tax('zsoogi_type')` AND user is not a logged-in administrator

This keeps all clip content private — non-admins see no indication the content exists.

## Custom Template

`templates/single-zsoogiclips.php` is loaded by the `template_include` filter for single clip views.

The template supports both:

- **Block themes** (e.g., Twenty Twenty-Five): constructs full HTML with `block_template_part('header')` and `block_template_part('footer')`
- **Classic themes**: uses `get_header()` / `get_footer()`

It includes inline CSS for the `.zsoogi-post-content` layout and a custom comment section compatible with both theme types.

## REST API

Both `zsoogiclips` and `zsoogi_type` have `show_in_rest: true`. The CPT is accessible via:

```
GET /wp-json/wp/v2/zsoogiclips
GET /wp-json/wp/v2/zsoogi_type
```

Note: REST API responses still respect WordPress authentication — unauthenticated requests will only see publicly viewable data. Since the CPT is `public: true`, posts may be visible via REST even to non-admins. This is a known consideration for future hardening.

## Rewrite Rules

On activation, `zsoogi_clipper_activate()` calls `flush_rewrite_rules()` after registering the CPT and taxonomy. On deactivation, rules are flushed again.

If you modify CPT or taxonomy registration, always flush rewrite rules manually via **Settings → Permalinks**.
