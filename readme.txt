=== Privacy & Consent Assistant ===
Contributors: Third River Marketing, alexdemchak
Tags: Third River Marketing
Requires at least: 3.5.1
Tested up to: 5.6
Stable tag: 1.2.0.2
License URI: http://www.gnu.org/licenses/gpl-3.0.html

An easy-to-use interface to aide with Consent and Privacy compliance.

== Description ==

This plugin provides an interface to assist with consent and privacy compliance. It is not guaranteed to satisfy all clauses in the GDPR or any other legal requirements. The policies included in this plugin should be reviewed by your legal team before use.

== Installation ==

1. Upload the unzipped plugin file to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. It's recommended to insert the Company information in the Privacy & Consent admin panel.

== Changelog ==
= 1.2.0.2
* Fixed issues with filtering content and showing update-policy nags

= 1.2
* Introduced basic CCPA compliance
* Yearly notices added in admin for custom policies
* Default policies now based in remote SVN for easier updating
* Policies language updated to include 12 month default

= 1.1.0.2
* fixed overwriting consent bar not working in some cases

= 1.1.0.1
* added default color overrides

= 1.1
* Shortened default Consent Bar text, and adjusted CSS/svg placementment to accommodate

= 1.0.8.5 =
* Fixed `genesis_get_option` error

= 1.0.8.4 =
* Stable Tag Version Mixed Up

= 1.0.8.3 =
* Updated JS to prevent erroneous console errors while still removing notices

= 1.0.8 = 
* JavaScript is now compiled with Babel for compatibilty
* Removed PHP functionality for Consent Bar removal, as it conflicted with caching mechanisms on the server
* Consent Bar is always rendered, and removed if consented to or faded in if not.

= 1.0.7.4 =
* Removed spaces from cookie for syntactic purposes

= 1.0.7.3 =
* Fixed a syntax Error when setting cookies

= 1.0.7.2 =
* Default Consent Cookie to expire in 1 year

= 1.0.7 =
* Prevented Genesis Specific errors

= 1.0.6 =
* Replaced Dynamic CSS file with Dynamic CSS option.
* Forced Positioning and colors on Consent Messages to better accommodate all themes.

= 1.0.5 =
* Better sanitization/validation methods when saving options
* Fixed minor JS errors on non-post editor pages. 

= 1.0.4 =
* Increased security with hiding Dynamic Delete form consents.
* Modified JS errors thrown when illegally submitting Dynamic Delete requests.
* Increased security and sanitization with Admin Options.
* Prevented "Direct File Access" to PHP files.

= 1.0.3 =
* Attempt to hide existing policy links with CSS
* Add option to disable "hide existing links" feature
* Make Feature Options easier to add in the future.
* Changed Name to Privacy & Consent Assistant

= 1.0.2 =
* Options weren't setting and Policies not being created on Network Activation. Accounted for that now.

= 1.0.1 =
* Initial Release