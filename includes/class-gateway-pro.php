<?php

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . '../admin/logs-ui.php';

private KCB_Telegram_Notifier $notifier;


class WC_KCB_Mpesa_Gateway extends KCB_Mpesa_Gateway_Base {
 

    public function __construct() {
        parent::__construct();
        $this->notifier = new KCB_Telegram_Notifier();

        add_action('admin_menu', [$this, 'add_pro_ui']);
        add_action('admin_init', function () {
            register_setting('general', 'kcb_mpesa_license_key');
            add_settings_field(
                'kcb_mpesa_license_key',
                'KCB M-Pesa License Key',
                function () {
                    echo '<input type="text" name="kcb_mpesa_license_key" value="' . esc_attr(get_option('kcb_mpesa_license_key', '')) . '" class="regular-text">';
                },
                'general'
            );
        });
        add_action('admin_menu', function () {
            add_menu_page(
                'M-Pesa Logs',
                'M-Pesa Logs',
                'manage_options',
                'mpesa-logs',
                'kcb_mpesa_render_logs_ui'
            );
        });
        
    }

    public function add_pro_ui() {
        add_menu_page(
            'KCB M-Pesa Pro',
            'KCB M-Pesa Pro',
            'manage_options',
            'kcb-mpesa-pro',
            [$this, 'render_pro_dashboard']
        );
    }

    public function render_pro_dashboard() {
        echo '<div class="wrap"><h1>KCB M-Pesa Pro Dashboard</h1>';
        echo '<p>Slack/Telegram integrations, CSV logs, and advanced settings will be managed here.</p>';
        echo '</div>';
    }

    public function process_payment($order_id) {
        $result = parent::process_payment($order_id);

        // Add any Pro-level extensions here (e.g., logging, analytics)
        
        // Send notifications to Telegram
        $order = wc_get_order($order_id);
        if ($result['result'] === 'success') {
            $this->notifier->send("✅ *M-Pesa Payment Initiated*\nOrder #$order_id\nAmount: KES {$order->get_total()}");
        }else{
            $this->notifier->send("❌ *M-Pesa Payment Failed*\nOrder #$order_id\nAmount: KES {$order->get_total()}");
        }
        
        return $result;
    }
}
