=== Zsoogi Clipper ===
Contributors: pbarthmaier, pbrocks
Tags: documentation, bookmarklet, research, notes, knowledge-base
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.9.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create admin-only Zsoogi clips with a modern jQuery-free bookmarklet for web research and documentation.

== Description ==

Zsoogi Clipper enables administrators to create, organize, and manage wiki-style research documentation within WordPress. The plugin combines a custom post type for Zsoogi clips with a modern bookmarklet, allowing you to easily capture and curate content from anywhere on the web.

Perfect for teams building internal documentation, knowledge bases, API documentation, or any content that benefits from wiki-style organization and quick content capture from external sources.

= Key Features =

**Zsoogi Clip Management**

* Dedicated custom post type optimized for documentation
* Hierarchical taxonomy to organize clips by type/category
* Full Gutenberg block editor support with classic editor compatibility
* Support for thumbnails, comments, and revisions
* REST API ready for headless applications

**Modern Bookmarklet**

* Pure vanilla JavaScript with no jQuery dependency
* Smart YouTube integration with automatic title cleanup and high-quality thumbnails
* Capture selected text and images from any webpage
* Automatically set featured images from captured content
* Pre-formatted content with blockquotes and citations
* Quick publishing workflow

**Administrator-Only Access**

* Secure access restricted to administrator users
* Non-administrators redirected from Zsoogi clip pages
* Private documentation without public exposure

**Settings & Customization**

* Dedicated settings page under Zsoogi clips menu
* Auto-set featured image option
* Clean, WordPress-native admin experience

= Perfect For =

* Internal documentation teams
* Research projects
* Knowledge base management
* API documentation
* Content curation
* Reference libraries

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Go to Plugins > Add New
3. Search for "Zsoogi Clipper"
4. Click "Install Now" and then "Activate"
5. Navigate to Zsoogi clips > Settings to configure

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Go to Plugins > Add New > Upload Plugin
4. Choose the downloaded ZIP file and click "Install Now"
5. Click "Activate Plugin"
6. Navigate to Zsoogi clips > Settings to configure

= Setting Up the Bookmarklet =

1. Go to Zsoogi clips > Settings in your WordPress admin
2. Drag the "Clip to Zsoogi" bookmarklet to your browser's bookmarks bar
3. Visit any webpage you want to capture
4. Click the bookmarklet to capture content
5. Review and publish as a Zsoogi clip

== Frequently Asked Questions ==

= Who can access Zsoogi clips? =

Only logged-in administrators can create and view Zsoogi clips. Non-administrators are redirected to the homepage when trying to access Zsoogi clip pages.

= Does the bookmarklet require jQuery? =

No! The bookmarklet uses pure vanilla JavaScript with no dependencies, making it fast and compatible with all modern browsers.

= Does the bookmarklet work with YouTube videos? =

Yes! The bookmarklet includes smart YouTube integration that:
* Automatically cleans up YouTube titles (removes view counts and "- YouTube" suffix)
* Fetches high-quality thumbnails from YouTube
* Captures video URLs and metadata

= Can I organize my Zsoogi clips? =

Yes! Use the hierarchical "Zsoogi Type" taxonomy to categorize and organize your clips.

= Is the plugin compatible with Gutenberg? =

Absolutely! The plugin fully supports the Gutenberg block editor, and captured content is pre-formatted with proper blocks.

= Does this work with the Classic Editor? =

Yes, the plugin works with both Gutenberg and the Classic Editor.

= Can I use this for public documentation? =

The plugin is designed for admin-only access. If you need public documentation, you would need to customize the access controls or use a different solution.

= Will this work with my theme? =

Yes! Zsoogi Clipper uses WordPress standards and works with any properly coded WordPress theme.

= Does this require Composer? =

No! Composer is only needed for development (PHP CodeSniffer for linting). The plugin works perfectly in production without any Composer dependencies.

== Screenshots ==

1. Zsoogi clips custom post type in the WordPress admin
2. Bookmarklet settings page
3. Captured content pre-formatted in the editor
4. Zsoogi Type taxonomy for organizing clips

== Changelog ==

= 0.9.0 =
* Initial beta release on WordPress.org
* Modern jQuery-free bookmarklet for web content capture
* Smart YouTube integration with title cleanup and thumbnail capture
* Admin-only custom post type with hierarchical taxonomy
* Auto-set featured images from captured content
* Gutenberg and Classic Editor support
* REST API enabled

= 2.4.2 (Development) =
* Added translation support with proper text domain loading
* Improved code documentation and standards compliance
* Enhanced error handling for missing files

= 2.4.0 =
* Added smart YouTube integration with title cleanup
* Automatic removal of view count prefix from YouTube titles
* Automatic removal of "- YouTube" suffix from titles
* High-quality thumbnail capture from YouTube API
* Enhanced image capture with YouTube-specific handling
* Improved title sanitization across all URLs

= 2.2.0 =
* Major rebranding: Renamed plugin to Zsoogi Clipper
* Replaced Press This with modern jQuery-free bookmarklet
* Added customizable citation formats (simple, detailed, academic)
* Added auto-set featured image functionality
* Added metadata capture options
* Improved Gutenberg block formatting
* Enhanced admin settings page

= 2.1.4 =
* Added Zsoogi Clipper bookmarklet functionality
* Removed legacy Press This dependencies
* Improved security and capability checks

= 2.1.1 =
* Restructured plugin architecture with object-oriented approach
* Added comprehensive documentation and docblocks
* Created dedicated Admin Menu class with settings page
* Improved security and capability checks
* Added PSR-4 autoloading support

= 2.0.0 =
* Initial release of Wiki Clipper (original name)
* Custom post type for wiki-style content
* Basic Press This integration

== Upgrade Notice ==

= 0.9.0 =
Initial beta release. Please report any issues on the WordPress.org support forum or GitHub.

= 2.4.0 (Development) =
Adds smart YouTube integration with automatic title cleanup and high-quality thumbnails. Recommended upgrade for all users.

= 2.2.0 =
Major update with new name (Zsoogi Clipper) and modern bookmarklet. Backward compatible with existing wiki content.

== Technical Details ==

**Post Type Slug:** `zsoogiclips`
**Taxonomy Slug:** `zsoogi_type`
**REST API:** Fully enabled
**Text Domain:** `zsoogi-clipper`

**Plugin Constants:**
* `ZSOOGI_CLIPPER_VERSION` - Plugin version
* `ZSOOGI_CLIPPER_PLUGIN_FILE` - Main plugin file path
* `ZSOOGI_CLIPPER_PLUGIN_DIR` - Plugin directory path
* `ZSOOGI_CLIPPER_PLUGIN_URL` - Plugin URL

== Privacy Policy ==

Zsoogi Clipper does not collect, store, or transmit any personal data. All content is stored locally in your WordPress database. The bookmarklet operates entirely within your browser and only communicates with your WordPress installation.

== Support ==

For support, feature requests, or bug reports:
* Visit the plugin page on WordPress.org
* Submit issues on GitHub: https://github.com/TheAPIGuysDev/zsoogi-clipper
* Contact: The API Guys - https://theapiguys.com

== Credits ==

* Inspired by WordPress Press This functionality
* Built with modern vanilla JavaScript
* Developed by The API Guys and pbrocks
