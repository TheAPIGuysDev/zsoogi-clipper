# Frequently Asked Questions

Common questions about Zsoogi Clipper.

---

## General

### What is Zsoogi Clipper?

Zsoogi Clipper is a WordPress plugin that lets you capture content from any webpage using a bookmarklet. It's perfect for research, content creation, journalism, and building knowledge bases.

### Is it really free?

Yes! The Community edition is 100% free forever. It's GPL-licensed open source software available on WordPress.org.

### Do I need to create an account?

No for the free version. Premium and Enterprise require a license purchase from theapiguys.com.

### What's a bookmarklet?

A bookmarklet is a small JavaScript program stored as a bookmark. Unlike browser extensions, it doesn't require installation permissions and works across all browsers.

---

## Installation & Setup

### Which version should I choose?

**Choose Free if:**
- You're an individual user
- Basic web clipping is enough
- You don't need YouTube transcripts

**Choose Premium if:**
- You need YouTube transcript capture
- You work with PDFs
- You want multiple citation formats

**Choose Enterprise if:**
- You have a team
- You need collaboration features
- You want AI-powered tools

[Compare all plans →](../pricing-comparison.md)

### Can I install on multiple sites?

**Free:** Yes, unlimited sites
**Premium:** 1 license = 1 site (additional sites $29/year)
**Enterprise:** Multi-site licensing available

### Does it work with my theme?

Yes! Zsoogi Clipper uses WordPress custom post types and works with any properly coded WordPress theme.

### Does it work with Gutenberg?

Yes, full WordPress block editor support.

### Does it work with Classic Editor?

Yes, though we recommend using Gutenberg for the best experience.

---

## Usage

### How do I install the bookmarklet?

1. Go to **Zsoogi Clips → Grab Zsoogi** in WordPress admin
2. Drag the button to your bookmarks bar
3. Done! Click it on any webpage to capture content

[Detailed instructions →](../getting-started/installation.md)

### Can I capture without selecting text?

Yes! The bookmarklet works without text selection. It will still capture URL, title, and image.

### Why isn't the bookmarklet working?

Common issues:

1. **Popup blocked:** Check for popup blocker notification in browser
2. **Bookmarks bar hidden:** Press `Ctrl+Shift+B` (Win) or `Cmd+Shift+B` (Mac)
3. **HTTPS mismatch:** Ensure your WordPress site uses HTTPS
4. **Old bookmarklet:** Re-install if you updated the plugin

### Can non-admin users view my clips?

No. Zsoogi clips are administrator-only. Non-admin users are automatically redirected to the homepage.

### How do I make clips public?

This isn't a built-in feature - clips are designed to be private research. You can copy content to regular posts if you want to publish it.

---

## Features

### What citation formats are available?

**Free:** Simple format only (`Source: [Title](URL)`)
**Premium:** Simple, Detailed, Academic (MLA, APA, Chicago, IEEE), Custom templates
**Enterprise:** Same as Premium

### Does it capture YouTube transcripts?

**Free:** No, only title cleanup and thumbnails
**Premium:** Yes! Full transcript with timestamps
**Enterprise:** Yes, same as Premium

### Can it capture PDFs?

**Free:** No
**Premium:** Yes, text and image extraction from PDFs
**Enterprise:** Yes, same as Premium

### Does it work with private/authenticated sites?

**Free:** No
**Premium:** Cookie forwarding planned for future release
**Enterprise:** Cookie forwarding + authenticated capture

### Can I capture multiple images?

**Free:** First image only
**Premium:** Up to 10 images per clip
**Enterprise:** Unlimited images

---

## License & Pricing

### How does the 30-day guarantee work?

Purchase Premium or Enterprise, try it for 30 days. If you're not satisfied, email us for a full refund. No questions asked.

### What happens if I don't renew?

Premium/Enterprise features stop working. Your content remains intact. You can downgrade to free version without losing clips.

### Can I switch between tiers?

Yes! Upgrade or downgrade anytime. When upgrading, pay the prorated difference.

### Do you offer student discounts?

Yes! 40% off Premium with .edu email verification.

### Do you offer non-profit discounts?

Yes! 50% off Enterprise for verified 501(c)(3) organizations.

### Can I pay monthly?

Not currently. Annual subscriptions only.

### Do you offer refunds?

Yes, 30-day money-back guarantee on Premium and Enterprise.

---

## Data & Privacy

### Where is my data stored?

On your WordPress site's database. Nothing is stored on external servers (except license validation checks).

### Do you collect usage data?

**Free:** No data collection
**Premium/Enterprise:** Opt-in telemetry for feature usage (can be disabled)

### Is my content private?

Yes! Clips are administrator-only by default. Even logged-in non-admins cannot view them.

### Can I export my clips?

Yes! Use WordPress's built-in export tool (Tools → Export) or the WordPress REST API.

### What happens if I delete the plugin?

Clips remain in your database. To fully remove:
1. Deactivate and delete the plugin
2. Manually delete clips from database (not recommended unless you're sure)

### Is it GDPR compliant?

Yes. Premium and Enterprise include GDPR compliance tools for data export and deletion.

---

## Technical

### What are the system requirements?

**Minimum:**
- WordPress 5.8+
- PHP 7.4+
- MySQL 5.7+

**Recommended:**
- WordPress 6.0+
- PHP 8.0+
- MySQL 8.0+

[Full requirements →](../getting-started/installation.md#system-requirements)

### Does it work on shared hosting?

**Free/Premium:** Yes
**Enterprise:** VPS or dedicated hosting recommended for optimal performance

### Does it require jQuery?

No! Pure vanilla JavaScript. Works with modern themes that don't load jQuery.

### Can I customize the bookmarklet?

Advanced users can modify `assets/js/bookmarklet.js` in the plugin directory. Changes will be overwritten on updates unless you fork the plugin.

### Does it have an API?

**Free/Premium:** Basic WordPress REST API
**Enterprise:** Full REST API + optional GraphQL endpoint

### Can I integrate with other plugins?

Yes! Zsoogi Clipper uses standard WordPress hooks and filters. Developers can extend functionality.

---

## Troubleshooting

### Bookmarklet opens but content isn't pre-filled

**Causes:**
1. JavaScript error on source page
2. Cross-origin security restrictions
3. Page structure not compatible

**Solutions:**
1. Check browser console for errors
2. Try on a different page to isolate issue
3. Report issue with page URL to support

### License won't activate

**Causes:**
1. Wrong license key
2. License expired
3. Domain mismatch
4. API connection issue

**Solutions:**
1. Copy entire license key (no spaces)
2. Check expiration at theapiguys.com/account
3. Verify domain matches license
4. Try again in a few minutes

### Clips aren't showing in WordPress admin

**Causes:**
1. Post type not registered (rare)
2. Permalink conflict

**Solutions:**
1. Deactivate and reactivate plugin
2. Go to Settings → Permalinks → Save Changes

### Performance issues with many clips

**Solutions:**
1. Upgrade to PHP 8.0+
2. Enable object caching (Redis/Memcached)
3. Increase PHP memory limit
4. Consider VPS hosting (for Enterprise)

---

## Premium & Enterprise

### How do I upgrade from Free to Premium?

1. Purchase Premium license
2. Download Premium plugin
3. Deactivate Free version
4. Install Premium version
5. Activate license

[Detailed upgrade guide →](../getting-started/installation.md#upgrading-between-tiers)

### Can I try Premium features before buying?

Use the free version to test core functionality. 30-day money-back guarantee on Premium/Enterprise.

### How do AI features work? (Enterprise)

You provide your own OpenAI or Anthropic API key. Zsoogi Clipper sends content to the AI API for summarization, tagging, etc. You're charged by OpenAI/Anthropic based on usage (~$5-50/month depending on volume).

### How many team members can I add? (Enterprise)

Unlimited! Enterprise license covers unlimited WordPress users on that site.

### Can I white-label the plugin? (Enterprise)

Yes! Remove "Powered by Zsoogi Clipper" branding, customize colors, use your company name.

---

## Support

### How do I get help?

**Free Users:**
- [WordPress.org Support Forum](https://wordpress.org/support/plugin/zsoogi-clipper/)
- [GitHub Issues](https://github.com/TheAPIGuysDev/zsoogi-clipper/issues)

**Premium Users:**
- Email: premium@theapiguys.com (24hr response)

**Enterprise Users:**
- Email: enterprise@theapiguys.com (8hr response)
- Dedicated Slack channel

[Contact information →](contact.md)

### Can you help me set up my site?

**Free:** Community support only
**Premium:** Email support for plugin-related questions
**Enterprise:** Implementation assistance included ($500 one-time setup service available)

### Do you offer custom development?

Yes! Enterprise customers get priority for custom feature development at $150/hour.

---

## Still Have Questions?

[Contact Support →](contact.md)
