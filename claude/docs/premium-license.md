Searched for "license"
Viewed class-license.php:1-170
Viewed freemium-development.md:1-223

Based on the current codebase for the `zsoogi-clipper` plugin, the external license validation system is currently stubbed out and not yet connected to a live license server (like EDD Software Licensing or Freemius). 

Because of this, you don't actually need to generate a real license key yet! To engage the Premium features on your production site right now, you can do one of two things:

**Option 1: Enter any text in the Settings (Easiest)**
You can go to the plugin's settings page (`Zsoogi Clips → Settings`) and simply enter **any text** (such as `test` or `premium-active`) into the License Key field and save it. The current code will accept any non-empty string and immediately unlock the premium features like citation formats and YouTube transcripts.

**Option 2: Use wp-config.php**
If you prefer to bypass the license check programmatically, you can add the following constant to your `wp-config.php` file:

```php
define( 'ZSOOGI_PREMIUM_LICENSE', true );
```

*(Note: Once you've implemented the `TODO` block in [includes/class-license.php](cci:7://file:///Users/pbrocks/Documents/Local/vangeek/app/public/wp-content/plugins/zsoogi-clipper/includes/class-license.php:0:0-0:0) to connect to your actual license server, it will start requiring real keys generated from that specific platform.)*