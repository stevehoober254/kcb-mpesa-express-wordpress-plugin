=== KCB M-Pesa STK Push Gateway ===
Contributors: stevehoober
Tags: woocommerce, mpesa, payment gateway, stk push, kcb, kenya, mobile money
Requires at least: 5.5
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Accept mobile money payments on your WooCommerce store using KCB's M-Pesa Express (STK Push) API.

== Description ==

KCB M-Pesa STK Push Gateway is a powerful integration for WooCommerce stores based in Kenya and East Africa.

It enables merchants to initiate M-Pesa Express (STK Push) requests directly from the checkout page, improving conversion and customer experience.

Features:
- Seamless STK Push payments
- KCB API token generation
- Custom callback handling and order status updates
- Admin logging dashboard
- Email alerts on payment failures
- License key support for Pro version features

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/kcb-mpesa-gateway`.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to **WooCommerce > Settings > Payments** and enable **KCB M-Pesa**.
4. Enter your **Consumer Key** and **Consumer Secret** from KCB's developer portal.
5. For Pro features, go to **WooCommerce > Settings > KCB M-Pesa License** and enter your license key.

== Screenshots ==

1. STK push during checkout.
2. Admin logs dashboard with download and clear log options.
3. License key settings page.
4. Email notification on payment failure.

== Frequently Asked Questions ==

= Where do I get my Consumer Key and Secret? =
Register on [KCB's Developer Portal](https://sandbox.buni.kcbgroup.com/devportal/apis) and subscribe to the M-Pesa Express API.

= How do I test STK Push in sandbox mode? =
Use test phone numbers provided by KCB and the sandbox environment credentials.

= Is this plugin free? =
Yes, the core functionality is free. Pro features like Slack alerts, CSV logs, and UI customizations are available under a paid license.

== Changelog ==

= 1.0 =
* Initial release with full STK Push support
* Logs dashboard, email alerts, and Pro license handling

== Upgrade Notice ==

= 1.0 =
Initial release. Stable for production use.

== License ==

This plugin is free software released under the GPLv2 license.
