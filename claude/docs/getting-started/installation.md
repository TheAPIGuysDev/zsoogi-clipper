# Installation

Get Zsoogi Clipper up and running in minutes.

## Free (Community) Edition

### Method 1: Install from WordPress Admin (Recommended)

1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins → Add New**
3. Search for "Zsoogi Clipper"
4. Click **Install Now** next to Zsoogi Clipper
5. Click **Activate**
6. Done! Go to **Zsoogi Clips** in the admin menu

### Method 2: Manual Installation

1. Download the plugin from [WordPress.org](https://wordpress.org/plugins/zsoogi-clipper/)
2. In WordPress admin, go to **Plugins → Add New → Upload Plugin**
3. Choose the downloaded `.zip` file
4. Click **Install Now**
5. Click **Activate Plugin**

### Method 3: FTP Upload

1. Download and extract the plugin ZIP file
2. Upload the `zsoogi-clipper` folder to `/wp-content/plugins/`
3. In WordPress admin, go to **Plugins**
4. Find "Zsoogi Clipper" and click **Activate**

---

## Premium Edition

!!! info "Premium License Required"
    You must purchase a Premium license from [theapiguys.com](https://theapiguys.com/zsoogi-clipper/premium) before installation.

### Step 1: Purchase License
1. Visit [theapiguys.com/zsoogi-clipper/premium](https://theapiguys.com/zsoogi-clipper/premium)
2. Complete the purchase ($79/year)
3. Check your email for the license key
4. Access the customer portal at [theapiguys.com/account](https://theapiguys.com/account)

### Step 2: Download Plugin
1. Log in to your customer portal
2. Download the `zsoogi-clipper-premium.zip` file
3. Save it to your computer

### Step 3: Install Plugin
1. **Important:** If you have the free version installed, **deactivate it** (don't delete it yet)
2. Go to **Plugins → Add New → Upload Plugin**
3. Choose the `zsoogi-clipper-premium.zip` file
4. Click **Install Now**
5. Click **Activate Plugin**

### Step 4: Activate License
1. Navigate to **Zsoogi Clips → Settings**
2. Click the **License** tab
3. Enter your license key
4. Click **Activate License**
5. You should see "License activated successfully" message
6. Premium features are now unlocked!

### Step 5: Clean Up (Optional)
After confirming everything works:
1. Go to **Plugins**
2. Delete the free version (your clips are safe - they're preserved)

---

## Enterprise Edition

!!! info "Enterprise License Required"
    Purchase an Enterprise license from [theapiguys.com](https://theapiguys.com/zsoogi-clipper/enterprise) first.

### Installation Steps
Follow the same steps as Premium, but:
- Download `zsoogi-clipper-enterprise.zip` from your customer portal
- Use your Enterprise license key for activation

### Additional Setup (Enterprise)
After activation, configure Enterprise features:

#### 1. Team Access
1. Create WordPress user accounts for team members
2. Assign roles in **Zsoogi Clips → Team Settings**
3. Configure permissions for each role

#### 2. AI Features (Optional)
1. Get an API key from [OpenAI](https://platform.openai.com/) or [Anthropic](https://www.anthropic.com/)
2. Go to **Zsoogi Clips → Settings → AI**
3. Enter your API key
4. Configure AI features (summarization, tagging, etc.)

#### 3. Integrations (Optional)
Set up integrations as needed:
- **Slack:** Install Slack app and configure webhook
- **Microsoft Teams:** Configure Teams connector
- **Zapier/Make.com:** Set up webhook triggers

#### 4. SSO (Optional)
For Single Sign-On:
1. Contact enterprise@theapiguys.com for SSO setup assistance
2. Provide your SAML/OAuth provider details
3. We'll help configure the integration

---

## System Requirements

### Minimum Requirements
- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+

### Recommended Requirements
- WordPress 6.0 or higher
- PHP 8.0 or higher
- MySQL 8.0+ or MariaDB 10.6+
- HTTPS enabled

### For Enterprise
- PHP 8.0+ required
- 256MB PHP memory limit (512MB recommended)
- VPS or dedicated hosting (shared hosting may have limitations)

---

## Browser Requirements

To use the bookmarklet, you need:
- Modern browser (Chrome, Firefox, Safari, or Edge)
- Bookmarks bar visible
- JavaScript enabled

---

## Post-Installation

After installing, complete these steps:

### 1. Install the Bookmarklet
1. Go to **Zsoogi Clips → Grab Zsoogi** in WordPress admin
2. Drag the bookmarklet button to your browser's bookmarks bar
3. Test it by clicking the bookmarklet on any webpage

### 2. Configure Settings
1. Go to **Zsoogi Clips → Settings**
2. Configure options:
   - Auto-set featured images (recommended: on)
   - Include capture metadata (optional)

### 3. Create Your First Clip
1. Visit any webpage
2. Optionally select some text
3. Click the bookmarklet in your bookmarks bar
4. A new WordPress editor opens with pre-filled content
5. Add your notes and publish

---

## Troubleshooting

### Bookmarklet Won't Install?
**Problem:** Can't drag bookmarklet to bookmarks bar

**Solution:**
- Make your bookmarks bar visible:
  - **Chrome/Edge:** Press `Ctrl+Shift+B` (Windows) or `Cmd+Shift+B` (Mac)
  - **Firefox:** Press `Ctrl+Shift+B` (Windows) or `Cmd+Shift+B` (Mac)
  - **Safari:** View → Show Favorites Bar

### License Won't Activate?
**Problem:** "Invalid license key" error

**Solutions:**
1. Double-check you copied the entire key (no extra spaces)
2. Ensure you're using the correct tier (Premium key won't work on Free version)
3. Check your license hasn't expired
4. Verify your domain is correct (license is domain-locked)
5. Contact support: premium@theapiguys.com

### Plugin Conflicts?
**Problem:** Plugin doesn't work after activation

**Solutions:**
1. Deactivate other plugins one by one to find conflicts
2. Switch to a default WordPress theme temporarily
3. Check PHP error logs for specific errors
4. Contact support with error details

### Can't See Bookmarklet Page?
**Problem:** 404 error on "Grab Zsoogi" page

**Solution:**
1. Go to **Settings → Permalinks**
2. Click **Save Changes** (this flushes rewrite rules)
3. Try accessing the page again

---

## Upgrading Between Tiers

### Free → Premium
1. Purchase Premium license
2. Download Premium plugin
3. Deactivate Free version
4. Install and activate Premium version
5. Enter license key
6. All your clips are automatically preserved!

### Premium → Enterprise
1. Purchase Enterprise license (pay the difference)
2. Download Enterprise plugin
3. Deactivate Premium version
4. Install and activate Enterprise version
5. Enter Enterprise license key
6. Configure new Enterprise features

**Data Safety:** All your clips, taxonomies, and metadata are preserved when upgrading. The custom post type stays the same across all tiers.

---

## Need Help?

- **Free Users:** [WordPress.org Support Forum](https://wordpress.org/support/plugin/zsoogi-clipper/)
- **Premium Users:** Email premium@theapiguys.com
- **Enterprise Users:** Email enterprise@theapiguys.com

---

## What's Next?

[Quick Start Guide →](quickstart.md){ .md-button .md-button--primary }
[View Settings →](../support/faq.md){ .md-button }
