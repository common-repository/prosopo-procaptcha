=== Prosopo Procaptcha ===
Contributors: 1prosopo
Tags: Captcha, Procaptcha, antispam, anibot, spam.
Requires at least: 5.5
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

GDPR compliant, privacy-friendly, and better-value CAPTCHA for your WordPress website.

== Description ==

[Prosopo Procaptcha](https://prosopo.io/) is a GDPR-compliant, privacy-first CAPTCHA solution, offering seamless bot protection without compromising user data.

== Key Features of Procaptcha ==

* **Private & GDPR Friendly** - [No data storage](https://prosopo.io/articles/stop-giving-your-website-data-away/) ensures full compliance with privacy laws.
* **Seamless Integration** - A drop-in replacement for reCaptcha and hCaptcha, allowing setup within minutes.
* **Customizable Defense** - Easily adjust bot protection settings to meet your site's specific needs.
* **Affordable** - Enjoy a top-value CAPTCHA solution with a generous [free tier](https://prosopo.io/pricing/).

== Why Use Procaptcha in WordPress? ==

* **Official WordPress Plugin** - Specifically built for WordPress, ensuring secure and reliable integration.
* **Multiple Built-In Integrations** - Works seamlessly with core WordPress forms and popular form plugins. Supported list provided below.
* **Multilingual** - Completely translated into German (DE), Spanish (ES), French (FR), Italian (IT), and Portuguese (PT).
* **Documentation** - [Plugin documentation](https://docs.prosopo.io/en/wordpress-plugin/) is available to help you get the most out of the plugin.

== Third-Party Service Notice ==

For proper functionality, the plugin loads the [Prosopo Procaptcha](https://prosopo.io/) JavaScript on your chosen forms to display the CAPTCHA on the client side.

Upon form submission, the plugin communicates with the [Prosopo Procaptcha](https://prosopo.io/) API server-side to verify the CAPTCHA response.

Please review the [Prosopo Privacy Policy](https://prosopo.io/privacy-policy/) and [Terms and Conditions](https://prosopo.io/terms-and-conditions/) to fully understand data handling practices.

== Supported forms ==

**WordPress Core Forms**:

* Login
* Registration
* Lost Password
* Comments
* Post/Page password protection

Visit the plugin settings to enable protection for these forms. Note: The plugin also supports custom login URLs, like those created by [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/).

**Form Plugins**:

* [Fluent Forms](https://wordpress.org/plugins/fluentform/) - Add the `Prosopo Procaptcha` field to your form (the `Advanced Fields` group).
* [Formidable Forms](https://wordpress.org/plugins/formidable/) - Add the `Prosopo Procaptcha` field to your form.
* [Gravity Forms](https://www.gravityforms.com/) - Add the `Prosopo Procaptcha` field to your form (the `Advanced Fields` group).
* [Ninja Forms](https://wordpress.org/plugins/ninja-forms/) - Add the `Prosopo Procaptcha` field to your form (the `Miscellaneous` group).
* [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) - Add the `[prosopo_procaptcha]` field to your form.

**Other Integrations**:

* [BBPress](https://wordpress.org/plugins/bbpress/) - Forum forms: Open the target forum settings to enable topic and reply forms protection. Account forms: enable in the Procaptcha plugin settings (the same as WordPress Core Forms).

More integrations coming soon!

== Screenshots ==

1. Customize the CAPTCHA appearance and behavior through the plugin settings.
2. Automatic integration with all supported forms for seamless bot protection.

== Frequently Asked Questions ==

= Where is the plugin Docs?   =

You can access the plugin documentation in the official [Prosopo Procaptcha documentation](https://docs.prosopo.io/en/wordpress-plugin/).

= My form plugin is missing. Can you add it? =

Please start a thread in the [support forum](https://wordpress.org/support/plugin/prosopo-procaptcha/). We'll review your request and consider adding support for your form plugin.

= The plugin is not available in my language. Can you translate it? =

Please start a thread in the [support forum](https://wordpress.org/support/plugin/prosopo-procaptcha/). We'll review your request and consider adding support for your language.

== Changelog ==

= 1.3.0 (2024-10-30) =
- Added support for [bbPress](https://wordpress.org/plugins/bbpress/)
- Added support for [Gravity Forms](https://www.gravityforms.com/)

= 1.2.0 (2024-10-24) =
- Added support for [Formidable Forms](https://wordpress.org/plugins/formidable/)

= 1.1.0 (2024-10-22) =
- Added support for [Ninja Forms](https://wordpress.org/plugins/ninja-forms/)
- Added support for [Fluent Forms](https://wordpress.org/plugins/fluentform/)

= 1.0.1 (2024-10-16) =
- Screenshots and readme updates

= 1.0.0 (2024-10-16) =
- Initial release
