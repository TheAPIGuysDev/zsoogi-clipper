# Zsoogi Clipper Freemium Model Roadmap

> Strategic plan for launching Zsoogi Clipper with a tiered freemium model

**Last Updated:** 2026-03-26  
**Status:** Active — Phase 1 complete, Phase 2 (Pro launch) in progress  

---

## Table of Contents

- [Product Tiers Overview](#product-tiers-overview)
- [Free Version (Community)](#free-version-community)
- [Premium Features by Tier](#premium-features-by-tier)
- [Feature Priority Matrix](#feature-priority-matrix)
- [Implementation Roadmap](#implementation-roadmap)
- [Pricing Strategy](#pricing-strategy)
- [Success Metrics](#success-metrics)

---

## Product Tiers Overview

| Tier | Price | Target Audience | Key Differentiator |
|------|-------|----------------|-------------------|
| **Community** | Free | Individual users, bloggers | Core bookmarklet functionality |
| **Pro** | $49-79/year | Power users, researchers | YouTube transcripts, PDF capture |
| **Business** | $149-199/year | Teams, organizations | Collaboration, AI features |
| **Enterprise** | $399+/year | Large organizations | White label, SSO, advanced security |

---

## Free Version (Community)

### Core Value Proposition
Keep the plugin genuinely useful for individual users while creating clear upgrade paths.

### Included Features
- [x] Basic bookmarklet with content capture (text, images, URL)
- [x] Custom post type and single taxonomy
- [x] Simple citation format
- [x] Auto-featured images
- [x] YouTube title cleanup and thumbnail capture
- [x] Administrator-only access
- [x] Basic settings page
- [x] WordPress REST API support

**Status:** ✅ Complete (v2.4.0 on main branch)  

---

## Premium Features by Tier

### Tier 1: Pro ($49-79/year)

**Target:** Power users, researchers, content creators  

#### 1. WordPress Abilities API — AI Agent Integration
**Priority:** 🔥 High | **Status:** ✅ Built (`premium` branch) | Requires WP 6.9+  

- [x] `zsoogi/create-clip` — create clip from URL, title, excerpt via AI agent
- [x] `zsoogi/search-clips` — search clips by keyword, domain, or tag
- [x] `zsoogi/get-transcript` — retrieve stored YouTube transcript for a clip
- [x] `zsoogi/export-clips` — export filtered clips as Markdown or JSON

**Technical Notes:** Implemented in `includes/class-abilities.php`. Registers on `wp_abilities_api_init`. Gracefully does nothing on WP < 6.9. Each ability gated behind `License::has_feature('abilities')`. `get-transcript` additionally requires `License::has_feature('transcripts')`.  

#### 2. YouTube Transcript Capture
**Priority:** 🔥 High | **Status:** ✅ Built (merged into `premium` branch)  

- [x] Full transcript scraping from YouTube pages
- [x] Searchable transcript storage in post metadata
- [x] Automatic timestamp links in content
- [x] Transcript excerpt in post content

**Technical Notes:** Implemented via `window.name` bridge pattern for cross-origin data passing. Originally in `ProVersion` branch (v2.4.3), now merged into `premium`.  

#### 2. Advanced Content Capture
**Priority:** 🔥 High | **Status:** 📋 Planned  

- [ ] PDF content extraction (text and images)
- [ ] Archive.org integration for wayback machine captures
- [ ] Full-page screenshot capture
- [ ] Authenticated site capture (cookie forwarding)
- [ ] Multi-image capture with gallery support

#### 3. Enhanced Organization
**Priority:** 🟡 Medium | **Status:** 📋 Planned  

- [ ] Unlimited custom taxonomies
- [ ] Custom fields for metadata
- [ ] Content templates (pre-defined structures)
- [ ] Bulk tagging and categorization
- [ ] Smart folders/collections

#### 4. Citation Management
**Priority:** 🟡 Medium | **Status:** 📋 Planned  

- [ ] Custom citation template builder
- [ ] Multiple citation styles (MLA, APA, Chicago, IEEE)
- [ ] Automatic bibliography generation
- [ ] Export citations to BibTeX/Zotero
- [ ] In-text citation shortcodes

---

### Tier 2: Business ($149-199/year)

**Target:** Teams, small-to-medium businesses, research groups  

#### 5. Team Collaboration
**Priority:** 🔥 High | **Status:** 📋 Planned  

- [x] Role-based access control (not just admin-only) — shipped 2.6.x
- [ ] Assignment and approval workflows
- [ ] Comments and annotations on clips
- [ ] Shared clip collections
- [ ] Team member activity tracking
- [ ] @mentions in comments

#### 6. AI-Powered Features
**Priority:** 🔥 High | **Status:** 💡 Research  

- [ ] Automatic content summarization (OpenAI/Anthropic API)
- [ ] Smart tag suggestions based on content
- [ ] Duplicate detection and merging
- [ ] Related content suggestions
- [ ] Automatic content categorization
- [ ] Key phrase extraction

#### 7. Advanced Integrations
**Priority:** 🟡 Medium | **Status:** 📋 Planned  

- [ ] Slack notifications for new clips
- [ ] API webhooks for custom workflows
- [ ] Export to Notion
- [ ] Export to Confluence
- [ ] Export to Google Docs
- [ ] RSS feed generation for clips
- [ ] Email digest notifications

#### 8. Analytics Dashboard
**Priority:** 🟢 Low | **Status:** 📋 Planned  

- [ ] Most captured sources tracking
- [ ] Popular clips analytics
- [ ] Team usage statistics
- [ ] Search analytics
- [ ] Content growth trends
- [ ] Exportable reports

---

### Tier 3: Enterprise ($399+/year)

**Target:** Large organizations, agencies, enterprise clients  

#### 9. White Label
**Priority:** 🟢 Low | **Status:** 📋 Planned  

- [ ] Custom branding options
- [ ] Remove plugin attribution
- [ ] Custom admin color schemes
- [ ] Custom email templates
- [ ] Branded exports

#### 10. Advanced Security
**Priority:** 🟡 Medium | **Status:** 📋 Planned  

- [ ] Comprehensive audit logs
- [ ] SSO integration (SAML, OAuth)
- [ ] Content encryption at rest
- [ ] Two-factor authentication
- [ ] IP whitelisting
- [ ] GDPR compliance tools

#### 11. API Access
**Priority:** 🟡 Medium | **Status:** 📋 Planned  

- [ ] Full REST API for custom integrations
- [ ] API rate limiting controls
- [ ] Asyncthing integration
- [ ] Zapier integration
- [ ] Make.com integration
- [ ] N8N integration
- [ ] Custom webhook endpoints
- [ ] GraphQL API (optional)

---

## Feature Priority Matrix

### Phase 1: Launch (Q1 2026)
**Goal:** Get free version on WordPress.org  

- [x] Polish free version (v2.4.0)
- [x] Set up documentation site (MkDocs, this site)
- [ ] Create WordPress.org listing
- [ ] Submit to WordPress.org repository
- [ ] Create demo video

### Phase 2: Pro Launch (Q2 2026)
**Goal:** Launch Pro tier with high-value features  

**Must Have (MVP):**  

1. [ ] YouTube Transcript Capture (already built!)
2. [ ] License management system (Freemius)
3. [ ] PDF content extraction
4. [ ] Auto-update system

**Nice to Have:**  

- [ ] Enhanced organization features
- [ ] Citation management basics

### Phase 3: Business Features (Q3 2026)
**Goal:** Enable team collaboration  

**Must Have:**  

1. [x] Role-based access control — shipped 2.6.x
2. [ ] AI summarization (OpenAI integration)
3. [ ] Slack integration
4. [ ] Comments/annotations

**Nice to Have:**  

- [ ] Analytics dashboard
- [ ] Additional integrations

### Phase 4: Enterprise (Q4 2026)
**Goal:** Target larger organizations  

**Must Have:**  

1. [ ] SSO integration
2. [ ] API access
3. [ ] Audit logs

**Nice to Have:**  

- [ ] White label options
- [ ] Advanced security features

---

## Implementation Roadmap

### Pre-Launch Checklist

#### Technical Setup
- [ ] Set up Freemius account for license management
- [ ] Configure auto-updates for premium versions
- [ ] Set up staging environment for premium testing
- [ ] Create premium plugin repository (private)
- [ ] Implement license validation
- [ ] Add telemetry (opt-in) for usage tracking

#### Marketing Setup
- [ ] Create product website/landing page
- [ ] Set up email marketing (ConvertKit/Mailchimp)
- [ ] Create demo videos for each tier
- [ ] Write feature comparison documentation
- [ ] Design pricing page
- [ ] Create upgrade prompts in free version

#### Legal/Business
- [ ] Draft Terms of Service
- [ ] Create Privacy Policy
- [ ] Set up payment processing (Stripe/PayPal)
- [ ] Configure automated invoicing
- [ ] Create refund policy

### Launch Sequence

#### Week 1-2: Free Version Polish
- [ ] Code review and security audit
- [ ] Performance optimization
- [ ] WordPress.org readme optimization
- [ ] Screenshot creation for WordPress.org
- [ ] Submit to WordPress.org

#### Week 3-4: Pro Version Development
- [ ] Merge ProVersion branch transcript features
- [ ] Implement Freemius integration
- [ ] Add license activation UI
- [ ] Test upgrade/downgrade flows
- [ ] Beta testing with early adopters

#### Week 5-6: Marketing Launch
- [ ] Announce on WordPress forums
- [ ] Product Hunt launch
- [ ] Blog post announcement
- [ ] Email existing premium users
- [ ] Social media campaign

---

## Pricing Strategy

### Psychological Pricing

**Pro Tier: $49-79/year**

- Entry price point: $49/year ($4/month)
- Value proposition: "Save hours manually transcribing YouTube videos"
- Target conversion: 20-30% of free users

**Business Tier: $149-199/year**

- Team price point: $149/year (~$12/month)
- Value proposition: "10x your team's research productivity"
- Target conversion: 5-10% of Pro users

**Enterprise Tier: $399+/year**

- Enterprise price point: Custom pricing, starting at $399/year
- Value proposition: "Enterprise-grade security and compliance"
- Target conversion: 1-3% of Business users

### Value-Based Pricing Calculation

**Time Savings Example:**  

- Manual YouTube transcript: ~15 minutes per video
- With Zsoogi Clipper Pro: ~30 seconds
- If capturing 50 transcripts/year: **12.5 hours saved**
- Value at $50/hour consulting rate: **$625 saved**
- Price: $79/year = **~87% discount on value created**

### Discounts & Promotions

- [ ] Launch discount: 30% off first year
- [ ] Annual vs monthly: Save 30% with annual billing
- [ ] Educational pricing: 40% off for .edu emails
- [ ] Non-profit pricing: 50% off for verified non-profits
- [ ] Lifetime deals: Consider AppSumo launch ($99 lifetime)

---

## Success Metrics

### Key Performance Indicators (KPIs)

#### User Acquisition
- **WordPress.org downloads:** Target 1,000 in first 3 months
- **Active installations:** Target 500 active sites in first 6 months
- **User rating:** Maintain 4.5+ stars

#### Conversion Metrics
- **Free to Pro conversion:** Target 20-30%
- **Pro to Business conversion:** Target 5-10%
- **Trial to paid conversion:** Target 40%+

#### Revenue Targets
- **Q2 2026:** $2,500 MRR (50 Pro customers)
- **Q3 2026:** $5,000 MRR (add Business tier)
- **Q4 2026:** $10,000 MRR (add Enterprise)
- **End of Year 1:** $15,000 MRR

#### Customer Health
- **Churn rate:** Keep under 5% monthly
- **Support tickets:** Average response time under 24 hours
- **NPS score:** Target 50+ (promoters vs detractors)

### Analytics Tracking

**Must Track:**  

- [ ] Which features drive upgrades (in-app surveys)
- [ ] Where users drop off in onboarding
- [ ] Most-used premium features
- [ ] Common support questions
- [ ] Cancellation reasons

---

## Competitive Analysis 

### Direct Competitors
| Product | Price | Key Features | Advantages Over |
|---------|-------|-------------|-----------------|
| Evernote Web Clipper | Free-$15/mo | General web clipper | More specialized for Zsoogi clips |
| Notion Web Clipper | Free-$10/mo | Knowledge management | Better WordPress integration |
| Zotero | Free | Research management | Better citation tools needed |

### Differentiation Strategy
1. **WordPress-native:** Built specifically for WordPress, not a generic tool
2. **YouTube transcripts:** Unique feature not offered by competitors
3. **Admin-only security:** Built for internal documentation
4. **Bookmarklet simplicity:** No browser extension required
5. **Open-source free tier:** Build trust with GPL licensing

---

## Support & Documentation

### Documentation Needs
- [ ] User guide for free version
- [ ] Premium feature documentation
- [ ] Video tutorials for each tier
- [ ] API documentation (Enterprise)
- [ ] Developer hooks reference
- [ ] Migration guide from competitors

### Support Channels
- [ ] WordPress.org support forum (free version)
- [ ] Email support (premium tiers)
- [ ] Priority support (Business/Enterprise)
- [ ] Community Slack/Discord (optional)
- [ ] Knowledge base with FAQs

---

## Next Steps

### Immediate Actions (This Week)
1. [ ] Finalize pricing structure
2. [ ] Set up Freemius account
3. [ ] Create WordPress.org account
4. [x] Merge ProVersion transcript features into `premium` branch (done)
5. [ ] Draft WordPress.org plugin description

### Short Term (This Month)
1. [ ] Submit free version to WordPress.org
2. [ ] Implement license validation system
3. [ ] Create landing page for premium versions
4. [ ] Begin PDF extraction feature development

### Medium Term (Next 3 Months)
1. [ ] Launch Pro tier
2. [ ] Onboard first 50 paying customers
3. [ ] Collect user feedback and iterate
4. [ ] Begin Business tier development

---

## Notes & References

- **Original Plugin:** Wiki Clipper (https://github.com/pbrocks/wiki-clipper)
- **Current Repository:** https://github.com/TheAPIGuysDev/zsoogi-clipper
- **Premium Branch:** Contains YouTube transcript feature (merged from ProVersion v2.4.3)
- **License Management:** Freemius (recommended for WordPress plugins)
- **Payment Processing:** Stripe + PayPal integration via Freemius

### Key Resources
- [Freemius WordPress SDK](https://freemius.com/)
- [WordPress Plugin Directory Guidelines](https://developer.wordpress.org/plugins/wordpress-org/)
- [SaaS Pricing Strategy Guide](https://www.priceintelligently.com/)

---

**Document Owner:** pbrocks  
**Contributors:** Claude Code  
**Version:** 1.0  
**License:** Internal use only  
