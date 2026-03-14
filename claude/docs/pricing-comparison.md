# Zsoogi Clipper - Pricing Comparison & Delivery Guide

**Last Updated:** 2026-01-31  
**Document Version:** 1.0  

---

## Quick Comparison

| Tier | Price | Distribution | Delivery Method | Target User |
|------|-------|--------------|-----------------|-------------|
| **Free** | $0 | WordPress.org | Standard WP plugin | Individual users |
| **Premium** | $79/year | theapiguys.com | Separate plugin + License key | Power users, researchers |
| **Enterprise** | $199/year | theapiguys.com | Separate plugin + License key | Teams, organizations |

---

## How Features Are Delivered

### Free (Community) Edition
**Distribution:** WordPress.org Plugin Repository  

**How You Get It:**  

1. Install directly from WordPress admin (Plugins → Add New → Search "Zsoogi Clipper")
2. OR download from wordpress.org/plugins/zsoogi-clipper
3. No account, no license key, no activation needed

**Updates:**  

- Automatic via WordPress update system
- No authentication required

**Source Code:**  

- Open source GPL v2+
- Available on GitHub (main branch)

---

### Premium Edition
**Distribution:** Direct sale from theapiguys.com  

**How You Get It:**  

1. **Purchase license** at theapiguys.com/zsoogi-clipper/premium
2. **Receive license key** via email immediately
3. **Download plugin** from customer dashboard (theapiguys.com/account)
4. **Upload to WordPress** (Plugins → Add New → Upload Plugin)
5. **Activate plugin** and **enter license key** in Settings → License
6. Premium features unlock instantly

**Plugin Details:**  

- **Plugin Slug:** `zsoogi-clipper-premium`
- **Separate Plugin:** Not an add-on, replaces free version
- **License Validation:** Contacts theapiguys.com API on activation
- **Cannot Coexist:** Deactivate free version before installing Premium

**Updates:**  

- Automatic updates via built-in update checker
- Checks theapiguys.com update server twice daily
- Active license required for updates
- One-click updates from WordPress Plugins page

**License Management:**  

- Managed via theapiguys.com API
- Validate on activation, cache for 24 hours
- Can deactivate license to move between sites
- View license status in WordPress admin

---

### Enterprise Edition
**Distribution:** Direct sale from theapiguys.com  

**How You Get It:**  

1. **Purchase license** at theapiguys.com/zsoogi-clipper/enterprise
    - Single-site, multi-site, or agency licenses available
    - Invoice billing available (Net-30)
2. **Receive license key** via email + customer portal access
3. **Download plugin** from enterprise portal (theapiguys.com/account)
4. **Upload to WordPress**
5. **Activate & enter license key**
6. **Configure Enterprise features:**
    - Invite team members to WordPress
    - Set up role-based access control
    - Configure integrations (Slack, Teams, etc.)
    - Add AI API keys (optional - your OpenAI/Anthropic keys)
7. **Optional:** Schedule onboarding call with support team

**Plugin Details:**  

- **Plugin Slug:** `zsoogi-clipper-enterprise`
- **Separate Plugin:** Enterprise-grade version
- **Replaces:** Free and Premium (cannot run simultaneously)
- **Multi-Site Support:** Single license can activate on multiple sites (depending on license tier)

**Updates:**  

- Priority automatic updates
- Beta access to new features
- More stable release channel
- Rollback capability

**External API Requirements:**  

- **Included (No extra cost):**
  - License validation API (theapiguys.com)
  - Update server (theapiguys.com)
  - YouTube transcript API (public, free)

- **Your API Keys (Optional for AI features):**
  - OpenAI API key (~$5-50/month depending on usage)
  - Anthropic API key (alternative to OpenAI)

- **Your Credentials (Optional for integrations):**
  - Slack workspace app
  - Microsoft Teams app
  - Zapier account
  - Make.com account

---

## Feature Delivery Breakdown

### Features via Plugin Code
✅ **Included in plugin download (no external dependencies):**

- Basic content capture
- YouTube title cleanup and thumbnails
- YouTube transcript capture (Premium+)
- PDF extraction (Premium+)
- Custom branding (Premium+)
- Multiple citation formats (Premium+)
- Team collaboration & roles (Enterprise)
- Comments and annotations (Enterprise)
- Analytics dashboard (Enterprise)
- White-label options (Enterprise)
- SSO integration (Enterprise)

### Features via External APIs
🔌 **Requires external services (you provide API keys):**

- **AI Summarization** (Enterprise): OpenAI or Anthropic API key
- **Smart Tag Suggestions** (Enterprise): OpenAI/Anthropic API
- **Slack Integration** (Enterprise): Your Slack workspace
- **Microsoft Teams** (Enterprise): Your Teams tenant
- **Zapier/Make.com** (Enterprise): Your automation account

### Features via theapiguys.com API
🔐 **Requires active license & internet connection:**

- License validation
- Automatic updates
- REST API access (Enterprise)
- Usage analytics (Enterprise)
- Audit log storage (Enterprise - optional)

---

## License Key System

### How License Validation Works

**On Activation:**  

1. You enter license key in Settings → License
2. Plugin contacts `api.theapiguys.com/v1/licenses/validate`
3. API returns:
    - License validity (active/expired/invalid)
    - Tier (Premium or Enterprise)
    - Enabled features
    - Expiration date
4. Response cached locally for 24 hours
5. Features unlocked based on tier

**Daily Background Check:**  

- Plugin checks license status once per 24 hours
- If expired: Features disabled, notice shown
- If valid: Features remain active
- **Offline grace period:** 7 days before disabling features

**What Happens If License Expires:**  

- Premium/Enterprise features stop working
- All your clips and data remain intact
- Automatically reverts to free version features
- You can reactivate anytime by renewing

### Moving Between Sites
1. Go to theapiguys.com/account
2. Deactivate license from old site
3. Activate on new site with same license key
4. OR purchase additional site licenses ($29/year)

---

## Data Migration Path

### Free → Premium
✅ **Seamless migration:**

- All clips preserved (same custom post type)
- All taxonomies and metadata intact
- No manual export/import needed
- Just deactivate Free, activate Premium

### Premium → Enterprise
✅ **Seamless migration:**

- All Premium data preserved
- Enterprise features immediately available
- Just deactivate Premium, activate Enterprise

### Downgrading (Premium/Enterprise → Free)
⚠️ **Some feature data lost:**

- **Preserved:**
  - All clips (custom post type)
  - Basic taxonomies
  - Featured images
  - Core metadata
- **Lost access to:**
  - YouTube transcripts (still stored, just not accessible)
  - Advanced citation formats (reverts to simple)
  - Team collaboration features
  - AI-generated summaries (still stored as metadata)

You can re-upgrade later and regain access to all Premium/Enterprise data.

---

## Upgrade Pricing

### From Free to Premium
- **Full Price:** $79/year
- **Launch Discount:** $55/year (30% off)
- **Educational:** $47/year (.edu email)

### From Premium to Enterprise
- **Pay Difference:** $120/year ($199 - $79)
- **Pro-rated:** If upgrading mid-year, only pay remaining months
- Example: Upgrade after 6 months of Premium = ~$60 for remaining 6 months

### From Free to Enterprise
- **Full Price:** $199/year
- **Launch Discount:** $139/year (30% off)
- **Non-Profit:** $99/year (50% off)

---

## Multi-Site Licensing

### Premium Multi-Site
- **Single Site:** $79/year
- **Additional Sites:** $29/year each
- **5-Site Bundle:** Not offered for Premium
- **Unlimited Sites:** Not offered for Premium

### Enterprise Multi-Site
- **Single Site:** $199/year
- **5-Site License:** $399/year ($79/site, save 60%)
- **Unlimited Sites:** $699/year (perfect for agencies)
- **Agency/Reseller:** Custom pricing

---

## Support & Update Comparison

| Support Type | Free | Premium | Enterprise |
|--------------|------|---------|-----------|
| **Documentation** | ✅ Public docs | ✅ Premium docs | ✅ Custom docs |
| **Video Tutorials** | ❌ | ✅ | ✅ |
| **Community Forum** | ✅ WordPress.org | ❌ | ❌ |
| **Email Support** | ❌ | ✅ 24hr response | ✅ 8hr response |
| **Priority Support** | ❌ | ❌ | ✅ |
| **Video Calls** | ❌ | ❌ | ✅ Scheduled |
| **Dedicated Slack** | ❌ | ❌ | ✅ |
| **Implementation Help** | ❌ | ❌ | ✅ |
| **Custom Development** | ❌ | ❌ | ✅ $150/hr |
| **Auto Updates** | ✅ WordPress | ✅ Built-in | ✅ Priority |
| **Beta Access** | ❌ | ❌ | ✅ |

---

## Technical Requirements Summary

### Free
- WordPress 5.8+
- PHP 7.4+
- Standard WordPress hosting

### Premium
- WordPress 6.0+
- PHP 7.4+ (8.0+ recommended)
- Internet connection for updates
- ~50MB disk space

### Enterprise
- WordPress 6.0+
- PHP 8.0+ (8.1+ recommended)
- 256MB PHP memory (512MB recommended)
- Internet connection for updates & API
- ~100MB disk space
- VPS or dedicated hosting recommended

---

## Payment & Billing

### Payment Methods
- **Free:** No payment required
- **Premium/Enterprise:**
  - Credit card (Stripe)
  - PayPal
  - Invoice/PO (Enterprise only, Net-30)

### Billing Cycle
- **Annual subscriptions** (no monthly option currently)
- Auto-renewal optional
- Email reminders 30 days before renewal
- Cancel anytime (features work until expiration)

### Refund Policy
- **30-day money-back guarantee** on Premium and Enterprise
- No questions asked
- Full refund within 30 days of purchase
- Can downgrade to Free and keep all clips

---

## Quick Decision Guide

### Choose FREE if:
- You're an individual user (not a team)
- Basic web clipping is all you need
- You don't need YouTube transcripts
- Simple citation format is fine
- You don't capture PDFs
- Community support is sufficient

### Choose PREMIUM if:
- You need YouTube transcript capture
- You work with PDFs regularly
- You need multiple citation formats
- You want custom branding
- You need priority email support
- You're a solo power user or researcher

### Choose ENTERPRISE if:
- You have a team (collaboration needed)
- You need role-based access control
- You want AI-powered features
- You need integrations (Slack, Teams, Zapier)
- You require SSO/SAML authentication
- You need analytics and reporting
- You want white-label options
- You need API access for custom integrations

---

## Frequently Asked Questions

### Can I try Premium/Enterprise before buying?
Use the free version to test core functionality. 30-day money-back guarantee on paid tiers.

### Can I switch between tiers?
Yes! Upgrade or downgrade anytime. When upgrading, pay the prorated difference.

### What if I have 3 sites - do I need 3 licenses?
Yes, one license per site. Consider multi-site bundles for Enterprise.

### Can I run Free on one site and Premium on another?
Yes! Mix and match tiers across different sites.

### Do I need to sign a contract?
No contracts! Just annual subscriptions. Cancel anytime.

### What happens to my data if I don't renew?
All your clips remain intact. Premium/Enterprise features stop working. You can still use free features.

### Can I get a discount for multiple years?
Contact sales@theapiguys.com for multi-year pricing.

### Do you offer student/educational discounts?
Yes! 40% off Premium with .edu email. Contact for academic institution licensing.

### Can I resell or white-label this?
Enterprise tier includes white-label options. Contact partners@theapiguys.com for reseller program.

---

## Contact & Sales

**General Inquiries:** info@theapiguys.com  
**Sales Questions:** sales@theapiguys.com  
**Premium Support:** premium@theapiguys.com  
**Enterprise Support:** enterprise@theapiguys.com  
**Partnership Inquiries:** partners@theapiguys.com  

**Website:** [theapiguys.com/zsoogi-clipper](https://theapiguys.com/zsoogi-clipper)  
**Documentation:** [theapiguys.com/docs](https://theapiguys.com/docs)  
**Customer Portal:** [theapiguys.com/account](https://theapiguys.com/account)  

---

**Ready to Choose Your Plan?**

[Start Free](https://wordpress.org/plugins/zsoogi-clipper) | [Buy Premium ($79/yr)](https://theapiguys.com/zsoogi-clipper/premium) | [Buy Enterprise ($199/yr)](https://theapiguys.com/zsoogi-clipper/enterprise)
