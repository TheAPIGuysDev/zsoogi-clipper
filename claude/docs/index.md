# Zsoogi Clipper Documentation

Welcome to the official documentation for **Zsoogi Clipper**, a WordPress plugin that creates an administrator-only custom post type for wiki-style documentation, integrated with a modern jQuery-free bookmarklet for capturing content from external sources.

---

## What is Zsoogi Clipper?

Zsoogi Clipper is a powerful web research tool for WordPress that lets you capture content from any webpage with a single click. Perfect for researchers, content creators, journalists, and anyone who needs to organize web research.

### Key Features

- **One-Click Content Capture** - Install bookmarklet once, capture content anywhere
- **YouTube Integration** - Automatic title cleanup and high-quality thumbnails
- **Smart Image Detection** - Filters out icons and captures meaningful images
- **Administrator-Only** - Private research, hidden from public visitors
- **WordPress Native** - Works with any theme, full block editor support
- **No Dependencies** - Pure vanilla JavaScript, no jQuery required

---

## Quick Links

### Getting Started
- [Installation Guide](getting-started/installation.md) - Get up and running in 5 minutes
- [Quick Start](getting-started/quickstart.md) - Start capturing content
- [Overview](getting-started/overview.md) - Learn about Zsoogi Clipper

### Pricing & Features
- [Compare Plans](pricing-comparison.md) - See all pricing tiers
- [Free Edition](free-features.md) - Community features
- [Premium Edition](premium-features.md) - Power user features
- [Enterprise Edition](enterprise-features.md) - Team features

### Resources
- [Development Roadmap](freemium-roadmap.md) - What's coming next
- [FAQ](support/faq.md) - Common questions
- [Contact Support](support/contact.md) - Get help

---

## Choose Your Edition

### Free (Community)

**Perfect for individual users**

- Basic bookmarklet capture
- YouTube title cleanup
- Simple citations
- Admin-only access

**Price:** Free forever

**[Download from WordPress.org →](https://wordpress.org/plugins/zsoogi-clipper)**

### Premium

**For power users & researchers**

- Everything in Free, PLUS:
- YouTube transcript capture
- Multiple citation formats
- PDF content extraction
- Custom branding
- Priority support

**Price:** $79/year

**[Learn More →](premium-features.md)**

### Enterprise

**For teams & organizations**

- Everything in Premium, PLUS:
- Team collaboration
- AI-powered features
- Advanced integrations
- White-label options
- SSO authentication

**Price:** $199/year

**[Learn More →](enterprise-features.md)**

---

## Use Cases

### Academic Research
Capture sources with proper citations, organize by topic, generate bibliographies automatically.

### Content Creation
Research competitors, save inspiration, capture YouTube transcripts for video analysis.

### Journalism
Track sources, annotate findings, collaborate with team members on investigations.

### Team Knowledge Base
Build internal documentation, share research across teams, integrate with Slack/Teams.

---

## System Requirements

- **WordPress:** 5.8 or higher
- **PHP:** 7.4 or higher
- **Browser:** Modern browser with bookmarks bar (Chrome, Firefox, Safari, Edge)
- **User Role:** Administrator access required

---

## Getting Help

**Need Support?**

- **Free Users:** [WordPress.org Community Forum](https://wordpress.org/support/plugin/zsoogi-clipper/)
- **Premium Users:** Email premium@theapiguys.com (24hr response)
- **Enterprise Users:** Email enterprise@theapiguys.com (8hr response)

Check our [FAQ](support/faq.md) for common questions.

---

## Recent Updates

### Version 2.4.0 (Current - Main Branch)
- YouTube title cleanup (removes view count prefix)
- High-quality thumbnail fetching
- Improved bookmarklet performance
- WordPress 6.0+ compatibility

### Version 2.4.3 (ProVersion Branch)
- YouTube transcript capture
- Searchable transcript storage
- Automatic timestamp links

**[View Full Roadmap →](freemium-roadmap.md)**

---

## Community & Contributing

- **GitHub:** [TheAPIGuysDev/zsoogi-clipper](https://github.com/TheAPIGuysDev/zsoogi-clipper)
- **Report Issues:** [GitHub Issues](https://github.com/TheAPIGuysDev/zsoogi-clipper/issues)
- **WordPress.org:** [Plugin Page](https://wordpress.org/plugins/zsoogi-clipper/)

---

## License

Zsoogi Clipper is open source software licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

**Previous Name:** Originally developed as "Wiki Clipper" by [pbrocks](https://github.com/pbrocks/wiki-clipper)

---

## Ready to Get Started?

- **[Install Free Version](getting-started/installation.md)**
- **[Compare All Plans](pricing-comparison.md)**

---

## Key Slugs (backward-compatible, do not change)

| Item | Slug |
|---|---|
| Post Type | `zsoogiclips` |
| Taxonomy | `zsoogi_type` |
| Option Group | `zsoogi_clipper_settings` |

---

## Developer Docs

- [Architecture](architecture.md) — class structure, initialization flow, hooks
- [Bookmarklet](bookmarklet.md) — JavaScript, YouTube handling, image capture
- [Post Type & Taxonomy](post-type.md) — CPT/taxonomy registration, frontend access
- [Settings & Admin](settings.md) — settings page, options, admin menu
- [Development](development.md) — Composer, PHPCS, build process
