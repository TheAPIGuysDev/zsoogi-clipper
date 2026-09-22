# Zsoogi Clipper - Premium Edition

**Version:** 3.0.0 (Planned)  
**Price:** $79/year  
**Distribution:** Purchase from theapiguys.com  
**Target Audience:** Power users, researchers, content creators, academics  

---

## What You Get

### Everything in Free, PLUS:

#### 1. WordPress Abilities API — AI Agent Integration 🤖
**Status:** ✅ Built (`premium` branch) | Requires WordPress 6.9+ + MCP Adapter plugin  

This is the defining premium feature. Zsoogi Clipper registers itself as a set of **WordPress Abilities**, making your entire clip library accessible to any MCP-connected AI agent — Claude Desktop, Claude Code, Cursor, VS Code — via natural language.

**What you can ask your AI:**  

- *"Create a clip from this URL with the title 'Market Research Q2'"*
- *"Search my clips for anything about competitor pricing"*
- *"Get the YouTube transcript for clip #42"*
- *"Export all my clips tagged 'research' as Markdown"*

**Registered Abilities:**  

- `zsoogi/create-clip` — Create a new clip from URL, title, and optional excerpt
- `zsoogi/search-clips` — Search clips by keyword, domain, or tag
- `zsoogi/get-transcript` — Retrieve the stored YouTube transcript for a clip
- `zsoogi/export-clips` — Export a filtered clip set to Markdown or JSON

**Why it matters:** Free users have a clip archive. Premium users have an AI-queryable knowledge base. The same clips you've been saving are now reachable by any AI agent with MCP support — no extra tooling, no separate app.  

See [AI-Ready WordPress (MCP)](ai-ready-wordpress.md) for setup details.

#### 2. Flexible Access Control 🔐
**Status:** ✅ Built (`premium` branch, 2.6.x)  

The free plugin is strictly administrator-only. Premium opens that up along two
independent axes, without ever exposing clips publicly.

**Site-wide minimum role**

Set the lowest role that may view clips on the frontend, in Settings → Access Control:

| Tier | Representative capability |
|---|---|
| Administrator | `manage_options` |
| Editor | `edit_others_posts` |
| Author | `publish_posts` |
| Contributor | `edit_posts` |
| Subscriber | `read` |

Tiers are matched by capability rather than role slug, so a custom role is slotted
into the highest tier whose capability it holds — no per-role configuration needed.

**Per-clip user sharing**

Any individual clip can be shared with named users via the User Access meta box,
stored in `_zsoogi_shared_users`. Those users may read that clip even if they fall
below the site-wide minimum role. It is a per-clip grant, not a global one.

**What never changes**

Logged-out visitors are redirected unconditionally. `restrict_frontend_access()`
bails before the capability check runs, so no combination of settings can make a
clip publicly readable. Privacy is the plugin's premise, and access control widens
a private circle — it does not publish.

!!! note "Fixed in 2.6.8"

    Per-clip sharing had no effect on the MCP abilities before 2.6.8: the shared-user
    list is stored as integers while both ability-side checks compared strings. The
    failure was closed, not open — shared users were denied rather than over-granted.
    Frontend sharing was never affected.

#### 3. YouTube Transcript Capture 🔥
**Status:** ✅ Built (merged into `premium` branch)  

- **Full Transcript Scraping:** Automatically captures complete video transcripts from YouTube
- **Searchable Storage:** Transcripts stored in post metadata for easy searching
- **Timestamp Links:** Automatic clickable timestamps that jump to video positions
- **Transcript Excerpts:** First 500 characters included in post content preview
- **Time Savings:** ~15 minutes per video vs. manual transcription
- **Value:** Save 12.5 hours/year capturing 50 transcripts = **$625 value at $50/hr**

**Use Cases:**  

- Academic research with video sources
- Content creation research
- Podcast/interview note-taking
- Educational video reference
- Training material compilation

#### 4. Citation Formats
**Status:** ✅ Built (simple/detailed/academic) | 📋 Planned Q2 2026 (academic styles + export)  

**Built and gated now:**  

- **Simple:** `Source: [Title](URL)` ← free tier default
- **Detailed:** `Source: [Title](URL) - Captured on Mar 26, 2026`
- **Academic:** Structured citation with author, date, and URL

**Planned Q2 2026:**  

- **Academic Styles:** MLA, APA, Chicago, IEEE
- **Custom Templates:** Build your own citation format
- **Automatic Bibliography:** Generate formatted bibliographies from clips
- **Export Options:** BibTeX, Zotero, EndNote
- **In-Text Citations:** Shortcodes for inserting citations in posts/pages

#### 5. PDF Content Extraction
**Status:** 📋 Planned Q2 2026  

- **Text Extraction:** Pull text content from PDF files via URL or upload
- **Image Extraction:** Capture images embedded in PDFs
- **Metadata Capture:** Author, title, date from PDF properties
- **Page References:** Include specific page numbers in citations
- **Archive PDFs:** Option to download and store PDFs on your server

#### 6. Enhanced Content Capture
**Status:** 📋 Planned Q2-Q3 2026  

- **Multi-Image Capture:** Capture multiple images with gallery support
- **Full-Page Screenshots:** Capture entire page as image (via API)
- **Archive.org Integration:** Capture wayback machine versions of pages
- **Authenticated Sites:** Forward cookies to capture content behind logins
- **Better Image Detection:** AI-powered detection of meaningful content images

#### 7. Custom Branding
**Status:** ✅ Available (removed from free version)  

- **Custom Menu Labels:** Change "Zsoogi Clips" to your preferred name
- **Custom Post Type Labels:** Rename throughout WordPress admin
- **Your Brand:** Use your company/project name instead of "Zsoogi"

**Example Use Cases:**  

- "Research Library" for academic institutions
- "Client Projects" for agencies
- "Case Studies" for consultants
- "Content Ideas" for writers

#### 8. Enhanced Organization
**Status:** 📋 Planned Q3 2026  

- **Unlimited Taxonomies:** Create multiple custom taxonomies beyond "Zsoogi Type"
- **Custom Fields:** Add metadata fields for tracking custom information
- **Content Templates:** Pre-defined structures for consistent clip formatting
- **Bulk Operations:** Bulk tagging, categorization, and editing
- **Smart Folders:** Dynamic collections based on rules

#### 9. Priority Support
**Status:** ✅ Available immediately upon purchase  

- **Email Support:** Direct email access to development team
- **Response Time:** 24-hour response guarantee on business days
- **Video Tutorials:** Access to premium video training library
- **Feature Requests:** Priority consideration for feature requests
- **Beta Access:** Early access to new features

---

## Pricing

### Annual Subscription
**$79/year** ($6.58/month)

- All Premium features
- Priority email support
- Automatic updates
- 1-year of updates & support
- Cancel anytime, keep using current version

### Launch Pricing (Limited Time)
**$55/year** (30% off first year)

- Lock in launch pricing
- Renews at regular price
- Early adopter benefits

### Educational Discount
**$47/year** (40% off with .edu email)

- Verify .edu email address
- For students, faculty, researchers
- Renewable annually with verification

---

## How You Get It

### Purchase & Delivery

#### Step 1: Purchase License
1. Visit [theapiguys.com/zsoogi-clipper/premium](https://theapiguys.com/zsoogi-clipper/premium)
2. Complete purchase with credit card or PayPal
3. Receive license key via email immediately

#### Step 2: Activate License
1. Navigate to **Zsoogi Clips → Settings → License**
2. Enter your license key
3. Click "Activate License"
4. Premium features unlocked immediately

### Delivery Method

**Distribution Channel:** WordPress.org (free) or theapiguys.com customer portal (with license key)  

**Plugin Type:** Same plugin as the free version — premium features unlock with a license key  

- **Plugin Name:** `zsoogi-clipper` (identical slug, no separate install needed)
- **No Migration Required:** Enter your license key in Settings → License and features unlock immediately
- **All data preserved:** No reinstall, no data loss, no downtime

**Updates:**  

- Automatic updates via built-in update system
- Update checks twice daily
- One-click updates from WordPress admin

**License Management:**  

- Managed via theapiguys.com API
- License key validates on activation
- Annual renewal required for updates/support
- Can deactivate license to move between sites

---

## Upgrade Process

### From Free to Premium

#### Data Safety
✅ **All your clips are preserved** - Custom post type stays the same
✅ **No data loss** - Taxonomies and metadata remain intact
✅ **Seamless transition** - Premium adds features, doesn't replace data

#### Upgrade Steps
1. **Purchase Premium license** from theapiguys.com
2. **Navigate to Zsoogi Clips → Settings → License**
3. **Enter your license key** and click "Activate License"
4. **Premium features unlock immediately** — no reinstall, no data loss

**Upgrade Time:** ~1 minute  
**Downtime:** None  

---

## Technical Details

### System Requirements
- **WordPress:** 6.0 or higher (slightly higher than free version)
- **PHP:** 7.4 or higher (8.0+ recommended)
- **Database:** MySQL 5.7+ or MariaDB 10.3+
- **Disk Space:** 50MB minimum (more for PDF storage)
- **External APIs:** Internet connection for YouTube transcript API

### API Dependencies
- **YouTube Transcript API:** For fetching transcripts (no API key required)
- **License Validation API:** theapiguys.com API for license checks
- **Update Server:** theapiguys.com update endpoint

### Performance
- **Transcript Fetch Time:** 2-5 seconds per video
- **PDF Processing:** 5-10 seconds per PDF (depends on size)
- **License Check:** Once per 24 hours (cached locally)

---

## Limitations & Fair Use

### Per-License Restrictions
- **Sites:** 1 site per license (additional sites $29/year each)
- **Users:** Unlimited administrators can use features
- **Clips:** Unlimited clips (storage limited by hosting)
- **API Calls:** Fair use policy (~1000 requests/day)

### Content Capture Limits
- **YouTube Transcripts:** Any video with available transcripts
- **PDF Size:** Up to 50MB per PDF
- **Images:** Up to 10 images per clip
- **Page Screenshots:** Up to 1 full page per capture

---

## Comparison with Free

| Feature | Free | Premium |
|---------|------|---------|
| Basic Content Capture | ✅ | ✅ |
| YouTube Title Cleanup | ✅ | ✅ |
| AI Agent Integration (Abilities API) | ❌ | ✅ |
| YouTube Transcripts | ❌ | ✅ |
| Citation Styles | 1 (Simple) | All + Custom |
| PDF Extraction | ❌ | ✅ |
| Multi-Image Capture | ❌ (1 only) | ✅ (up to 10) |
| Custom Branding | ❌ | ✅ |
| Custom Taxonomies | 1 | Unlimited |
| Priority Support | ❌ | ✅ 24hr |
| Team Features | ❌ | ❌ (Enterprise) |
| AI Features | ❌ | ❌ (Enterprise) |

---

## Who Should Upgrade?

### Premium is Perfect For:

✅ **Academic Researchers**

- Multiple citation format requirements
- YouTube video source documentation
- PDF research paper capture
- Bibliography generation

✅ **Content Creators**

- Video research with transcripts
- Competitive content analysis
- Source material organization
- Quick reference lookup

✅ **Journalists & Writers**

- Interview transcript capture
- Source citation management
- Multi-source research
- Fact-checking references

✅ **Students**

- Lecture video transcripts
- Research paper references
- Study material organization
- Citation format compliance

✅ **Consultants & Professionals**

- Client research documentation
- Branded knowledge base
- Professional citations
- Organized project research

### Stick with Free If:

❌ Simple bookmarking is enough
❌ Don't need YouTube transcripts
❌ One citation format is fine
❌ Don't capture PDFs or multiple images
❌ Community support is sufficient

---

## Return Policy

**30-Day Money-Back Guarantee**

- Try Premium risk-free for 30 days
- Full refund if not satisfied, no questions asked
- Keep using free version if you downgrade
- All captured content remains yours

---

## Frequently Asked Questions

### Can I try before buying?
Yes! Use the free version to test core functionality. All data migrates seamlessly to Premium.

### What if I don't renew after a year?
Premium features stop working, but your content remains intact. You can downgrade to free version without losing clips.

### Can I use on multiple sites?
One license = one site. Additional sites are $29/year each. Contact us for multi-site licenses.

### Do you offer refunds?
Yes, 30-day money-back guarantee. No questions asked.

### How do updates work?
Automatic updates via WordPress, just like any plugin. Active license required for updates.

### Will you add more Premium features?
Yes! Roadmap includes PDF extraction (Q2 2026), enhanced organization (Q3 2026), and more.

### Can I upgrade to Enterprise later?
Absolutely! Pay the difference for pro-rated upgrade anytime.

### Is my license key tied to a domain?
Yes, but you can deactivate and move to a new domain anytime via customer dashboard.

---

## Support & Resources

### Premium Support Channels
- **Email:** premium@theapiguys.com (24hr response)
- **Customer Portal:** theapiguys.com/account
- **Documentation:** theapiguys.com/docs/premium
- **Video Tutorials:** theapiguys.com/videos
- **Live Chat:** Coming Q3 2026

### Included Resources
- Detailed feature documentation
- Video walkthroughs for each feature
- Email templates for common use cases
- Citation format cheat sheets
- Best practices guide

---

**Ready to Upgrade?**

[Purchase Premium ($79/year)](https://theapiguys.com/zsoogi-clipper/premium) | [Compare All Plans](https://theapiguys.com/zsoogi-clipper/pricing) | [View Demo](https://demo.theapiguys.com/zsoogi)

**Questions?** Email sales@theapiguys.com
