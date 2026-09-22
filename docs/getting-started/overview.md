# Overview

Zsoogi Clipper is a WordPress plugin designed to help you capture, organize, and manage web research efficiently.

## What Makes Zsoogi Clipper Different?

### WordPress-Native
Unlike browser extensions or third-party services, Zsoogi Clipper integrates directly into your WordPress site. Your research stays on your server, under your control.

### Bookmarklet Simplicity
No browser extension to install or update. Just drag a bookmarklet to your bookmarks bar once, and you're ready to capture content from any webpage.

### Administrator-Only Security
All Zsoogi clips are private by default. Non-administrator users who try to view clips are redirected to your homepage, keeping your research secure.

### Pure Vanilla JavaScript
No jQuery dependency means faster load times and better compatibility with modern WordPress themes.

## Core Concepts

### Custom Post Type
Zsoogi clips are stored as a custom post type called `zsoogiclips`. This means:

- Full WordPress editor support
- Native search functionality
- Works with any theme
- Compatible with WordPress REST API

### Taxonomy Organization
Clips can be organized using the "Zsoogi Type" taxonomy (like categories). Premium and Enterprise users get unlimited custom taxonomies.

### Bookmarklet Capture
The bookmarklet captures:

- Page URL and title
- Selected text (as blockquote)
- First meaningful image (auto-sets as featured image)
- YouTube metadata (titles, thumbnails, transcripts in Premium)

## Architecture

```
┌─────────────────┐
│   Webpage       │
│  (Any Site)     │
└────────┬────────┘
         │ Click Bookmarklet
         ▼
┌─────────────────┐
│   Bookmarklet   │
│   (JavaScript)  │
└────────┬────────┘
         │ Sends Data
         ▼
┌─────────────────┐
│   WordPress     │
│   New Clip      │
└────────┬────────┘
         │ Saves
         ▼
┌─────────────────┐
│  Your Database  │
│  (zsoogiclips)  │
└─────────────────┘
```

## Who Should Use Zsoogi Clipper?

### Perfect For:
- **Researchers:** Capture sources with proper citations
- **Writers:** Collect inspiration and reference material
- **Journalists:** Track sources and build story research
- **Students:** Organize academic research
- **Marketers:** Competitive analysis and content research
- **Teams:** Collaborative knowledge management (Enterprise)

### Not Ideal For:
- Simple bookmarking (browser bookmarks are simpler)
- Public content curation (clips are admin-only)
- E-commerce product lists (use a different tool)

## Feature Tiers

Zsoogi Clipper is available in three editions:

1. **Free (Community):** Core features for individual users
2. **Premium ($79/year):** Advanced features for power users
3. **Enterprise ($199/year):** Team collaboration and AI features

[Compare all plans →](../pricing-comparison.md)

## Technology Stack

- **Backend:** PHP 7.4+, WordPress 5.8+
- **Frontend:** Vanilla JavaScript (no jQuery)
- **Storage:** WordPress custom post type
- **APIs:** YouTube (transcripts), OpenAI/Anthropic (Enterprise AI)
- **Standards:** WordPress Coding Standards, PSR-4 autoloading

## What's Next?

Ready to get started?

[Install Zsoogi Clipper →](installation.md)
