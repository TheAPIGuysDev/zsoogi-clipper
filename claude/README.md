# Zsoogi Clipper Documentation

This directory contains the MkDocs documentation site for Zsoogi Clipper.

## Quick Start

### 1. Install Dependencies

```bash
# Navigate to the claude directory
cd claude

# Install Python dependencies
pip install -r requirements.txt
```

### 2. Run Development Server

```bash
# Start the local development server
mkdocs serve

# Or specify a different port
mkdocs serve --dev-addr=127.0.0.1:8001
```

The documentation site will be available at: http://127.0.0.1:8000

**Hot Reload:** Changes to markdown files will automatically refresh the browser.

### 3. Build Static Site

```bash
# Build the static site
mkdocs build

# Output will be in the ./site directory
```

## Directory Structure

```
claude/
├── mkdocs.yml              # MkDocs configuration
├── requirements.txt        # Python dependencies
├── README.md              # This file
└── docs/                  # Documentation content
    ├── index.md           # Homepage
    ├── getting-started/   # Getting started guides
    │   ├── overview.md
    │   ├── installation.md
    │   └── quickstart.md
    ├── support/           # Support pages
    │   ├── faq.md
    │   └── contact.md
    ├── free-features.md
    ├── premium-features.md
    ├── enterprise-features.md
    ├── pricing-comparison.md
    └── freemium-roadmap.md
```

## Theme

This documentation uses the default **ReadTheDocs** theme that comes with MkDocs.

**Features enabled:**
- Clean, simple design
- Full-text search
- Responsive layout
- Syntax highlighting
- Navigation sidebar

## Adding Content

### Create a New Page

1. Add a new `.md` file in the `docs/` directory
2. Add the page to `mkdocs.yml` in the `nav:` section

Example:
```yaml
nav:
  - Home: index.md
  - Your New Page: your-page.md
```

### Markdown Extensions

This setup includes several useful extensions:

#### Admonitions

```markdown
!!! note
    This is a note admonition.

!!! warning
    This is a warning.

!!! tip
    This is a tip.
```

#### Code Blocks

````markdown
```python
def hello():
    print("Hello, World!")
```
````

#### Tables

```markdown
| Header 1 | Header 2 |
|----------|----------|
| Cell 1   | Cell 2   |
```

#### Links

```markdown
[Link Text](page.md)
```

## Configuration

Edit `mkdocs.yml` to customize:

- Site name and description
- Theme colors and fonts
- Navigation structure
- Enabled plugins
- Markdown extensions

[Full MkDocs documentation →](https://www.mkdocs.org/)

## Deployment

### GitHub Pages

```bash
# Build and deploy to gh-pages branch
mkdocs gh-deploy
```

### Manual Deployment

```bash
# Build the static site
mkdocs build

# Upload the ./site directory to your web host
```

### Netlify / Vercel

Both platforms auto-detect MkDocs projects. Just connect your repository.

**Build command:** `mkdocs build`
**Publish directory:** `site`

## Common Commands

```bash
# Start development server
mkdocs serve

# Build static site
mkdocs build

# Deploy to GitHub Pages
mkdocs gh-deploy

# Check for broken links
mkdocs build --strict

# Clean build directory
rm -rf site/
```

## Troubleshooting

### Port Already in Use

```bash
# Use a different port
mkdocs serve --dev-addr=127.0.0.1:8001
```

### Module Not Found

```bash
# Reinstall dependencies
pip install -r requirements.txt --upgrade
```

### Changes Not Showing

1. Hard refresh browser (`Ctrl+F5` or `Cmd+Shift+R`)
2. Clear browser cache
3. Restart MkDocs server

## Resources

- [MkDocs Documentation](https://www.mkdocs.org/)
- [Material for MkDocs](https://squidfunk.github.io/mkdocs-material/)
- [Markdown Guide](https://www.markdownguide.org/)
- [PyMdown Extensions](https://facelessuser.github.io/pymdown-extensions/)

## Support

Questions about the documentation?

- Open an issue on GitHub
- Email: docs@theapiguys.com

---

**Happy Documenting! 📚**
