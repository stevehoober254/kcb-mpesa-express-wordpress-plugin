# KCB M-Pesa STK Push Gateway

**Custom WooCommerce payment gateway for KCB M-Pesa STK Push API.**

## Overview

KCB M-Pesa STK Push Gateway enables WooCommerce stores in Kenya and East Africa to accept mobile money payments via KCB's M-Pesa Express (STK Push) API. Customers can pay directly from their mobile devices, improving conversion rates and user experience.

- **Seamless STK Push payments** at checkout
- **KCB API token generation** and secure authentication
- **Custom callback handling** for real-time order status updates
- **Admin logging dashboard** for payment callbacks
- **Email alerts** on payment failures
- **Pro version**: Slack/Telegram alerts, CSV logs, advanced UI, and more

---

## Features

### Free Version

- Accepts M-Pesa payments via KCB's STK Push API
- Secure token-based authentication with KCB
- Handles payment callbacks and updates WooCommerce order statuses
- Logs all callback data for troubleshooting
- Admin dashboard for viewing, downloading, and clearing logs
- Email notifications to admin on payment failures

### Pro Version

- License key validation for unlocking Pro features
- Slack/Telegram integration (planned)
- CSV export of logs (planned)
- Advanced admin dashboard
- UI customizations

---

## Installation

1. Upload the plugin folder to `/wp-content/plugins/kcb-mpesa-gateway`.
2. Activate the plugin via the **Plugins** screen in WordPress.
3. Go to **WooCommerce > Settings > Payments** and enable **KCB M-Pesa**.
4. Enter your **Consumer Key** and **Consumer Secret** from KCB's developer portal.
5. For Pro features, go to **WooCommerce > Settings > KCB M-Pesa License** and enter your license key.

---

## Usage

- During checkout, customers select **M-Pesa via KCB** and enter their phone number.
- The plugin initiates an STK Push request to the customer's phone.
- Upon payment, the order status is updated automatically.
- All callback data is logged and viewable in the admin dashboard.

---

## Admin & Developer Notes

- **Logs:** Callback logs are stored in `wp-content/mpesa-callback-log.txt`. View, download, or clear logs from the WordPress admin menu (**M-Pesa Logs**).
- **Pro License:** Add your license key in **WooCommerce > Settings > KCB M-Pesa License**. The Pro version is activated if the license is valid.
- **Customization:** Extend or override gateway classes in the `includes/` directory for custom logic.

---

## File Structure

```
kcb-mpesa-gateway/
├── admin/
│   └── logs-ui.php                # Admin UI for viewing logs
├── assets/
│   └── license-check.js           # JS for license validation (Pro)
├── includes/
│   ├── class-gateway-base.php     # Abstract base class for gateways
│   ├── class-gateway-free.php     # Free gateway implementation
│   ├── class-gateway-pro.php      # Pro gateway implementation
├── pro/
│   ├── class-license-manager.php  # License validation logic
│   └── license-check.php          # Pro license check
├── kcb-mpesa-gateway.php          # Main plugin file
├── init_kcb-mpesa-gateway.php     # Gateway registration and callback handling
├── readme.txt                     # WordPress.org readme
└── README.md                      # (You are here)
```

---

## Frequently Asked Questions

**Where do I get my Consumer Key and Secret?**  
Register on [KCB's Developer Portal](https://sandbox.buni.kcbgroup.com/devportal/apis) and subscribe to the M-Pesa Express API.

**How do I test STK Push in sandbox mode?**  
Use test phone numbers and credentials provided by KCB in the sandbox environment.

**Is this plugin free?**  
Yes, the core plugin is free. Pro features require a paid license.

---

## Changelog

See `readme.txt` for a detailed changelog.

---

## License

This plugin is free software released under the [GPLv2 license](https://www.gnu.org/licenses/gpl-2.0.html).
