<?php
/**
 * Plugin Name: KCB M-Pesa STK Push Gateway
 * Description: Custom WooCommerce payment gateway for KCB M-Pesa STK Push API.
 * Author: Stevehoober
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

define('KCB_MPESA_PRO', file_exists(__DIR__ . '/pro/license-check.php'));

// Register license key field in WooCommerce settings
add_filter('woocommerce_get_settings_pages', function ($settings) {
    $settings[] = new class extends WC_Settings_Page {
        public function __construct() {
            $this->id = 'kcb_mpesa_license';
            $this->label = 'KCB M-Pesa License';
            parent::__construct();
        }

        public function get_settings() {
            return [
                [
                    'title' => 'KCB M-Pesa Pro License',
                    'type' => 'title',
                    'id' => 'kcb_mpesa_license_section'
                ],
                [
                    'title' => 'License Key',
                    'desc' => 'Enter your license key to unlock Pro features.',
                    'id' => 'kcb_mpesa_license_key',
                    'type' => 'text',
                    'default' => '',
                    'desc_tip' => true
                ],
                [
                    'type' => 'sectionend',
                    'id' => 'kcb_mpesa_license_section'
                ]
            ];
        }
    };
    return $settings;
});

require_once __DIR__ . '/includes/class-kcb-gateway-base.php';

if (KCB_MPESA_PRO && include __DIR__ . '/pro/license-check.php') {
    require_once __DIR__ . '/includes/class-kcb-gateway-pro.php';
} else {
    require_once __DIR__ . '/includes/class-kcb-gateway-free.php';
} ?>
