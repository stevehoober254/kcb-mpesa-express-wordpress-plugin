<?php
/**
 * Plugin Name: KCB M-Pesa STK Push Gateway
 * Description: Custom WooCommerce payment gateway for KCB M-Pesa STK Push API.
 * Author: Stevehoober
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

add_filter('woocommerce_payment_gateways', 'kcb_add_gateway_class');
function kcb_add_gateway_class($gateways) {
    $gateways[] = 'WC_KCB_Mpesa_Gateway';
    return $gateways;
}

add_action('plugins_loaded', 'kcb_init_gateway_class');
function kcb_init_gateway_class() {
    class WC_KCB_Mpesa_Gateway extends WC_Payment_Gateway {

        public function __construct() {
            $this->id = 'kcb_mpesa';
            $this->method_title = 'KCB M-Pesa';
            $this->method_description = 'Pay via M-Pesa STK Push using KCB API';
            $this->has_fields = true;

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->consumer_key = $this->get_option('consumer_key');
            $this->consumer_secret = $this->get_option('consumer_secret');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Enable/Disable',
                    'type' => 'checkbox',
                    'label' => 'Enable KCB M-Pesa Payment',
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'default' => 'M-Pesa via KCB'
                ),
                'consumer_key' => array(
                    'title' => 'Consumer Key',
                    'type' => 'text'
                ),
                'consumer_secret' => array(
                    'title' => 'Consumer Secret',
                    'type' => 'password'
                )
            );
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);
            $phone = get_post_meta($order_id, '_billing_phone', true);
            $amount = $order->get_total();

            $credentials = base64_encode("{$this->consumer_key}:{$this->consumer_secret}");

            $token_response = wp_remote_post("https://uat.buni.kcbgroup.com/token?grant_type=client_credentials", [
                'headers' => [
                    'Authorization' => "Basic $credentials"
                ]
            ]);

            if (is_wp_error($token_response)) {
                wc_add_notice('Payment failed: Unable to authenticate.', 'error');
                return;
            }

            $token_body = json_decode(wp_remote_retrieve_body($token_response), true);
            $access_token = $token_body['access_token'] ?? '';
            if (!$access_token) {
                wc_add_notice('Payment failed: Token missing.', 'error');
                return;
            }

            $stk_payload = [
                'phoneNumber' => $phone,
                'amount' => (string) $amount,
                'invoiceNumber' => 'KCBTILLNO-' . $order_id,
                'sharedShortCode' => true,
                'orgShortCode' => '',
                'orgPassKey' => '',
                'callbackUrl' => site_url('/mpesa-callback'),
                'transactionDescription' => 'WooCommerce Order #' . $order_id
            ];

            $stk_response = wp_remote_post("https://uat.buni.kcbgroup.com/mm/api/request/1.0.0/stkpush", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer $access_token",
                    'accept' => 'application/json',
                    'routeCode' => '207',
                    'operation' => 'STKPush',
                    'messageId' => uniqid("order_"),
                ],
                'body' => json_encode($stk_payload)
            ]);

            if (is_wp_error($stk_response)) {
                wc_add_notice('Payment failed: STK push error.', 'error');
                return;
            }

            $order->update_status('on-hold', 'Awaiting M-Pesa payment confirmation');
            wc_reduce_stock_levels($order_id);
            WC()->cart->empty_cart();

            return [
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            ];
        }
    }
}

// Register custom endpoint for callback
add_action('init', function () {
    add_rewrite_rule('^mpesa-callback/?$', 'index.php?mpesa_callback=1', 'top');
    add_rewrite_tag('%mpesa_callback%', '1');
});

// Handle M-Pesa callback and update order
add_action('template_redirect', function () {
    if (get_query_var('mpesa_callback') == 1) {
        $body = file_get_contents('php://input');
        $log_file = WP_CONTENT_DIR . '/mpesa-callback-log.txt';
        file_put_contents($log_file, date('Y-m-d H:i:s') . "\n" . $body . "\n\n", FILE_APPEND);

        $data = json_decode($body, true);
        if (!$data || !isset($data['Body']['stkCallback'])) {
            status_header(400);
            exit;
        }

        $callback = $data['Body']['stkCallback'];
        $invoice_number = $callback['MerchantRequestID'] ?? '';
        if (strpos($invoice_number, 'KCBTILLNO-') === 0) {
            $order_id = intval(str_replace('KCBTILLNO-', '', $invoice_number));
            $order = wc_get_order($order_id);

            if ($order && $callback['ResultCode'] === 0) {
                $order->payment_complete();
                $order->add_order_note('M-Pesa payment confirmed.');
            } else {
                $order->update_status('failed', 'M-Pesa payment failed.');

                // Email admin on payment failure
                wp_mail(
                    get_option('admin_email'),
                    'M-Pesa Payment Failed for Order #' . $order_id,
                    "The M-Pesa payment failed for order ID: $order_id\n\nCallback Data:\n" . print_r($callback, true)
                );
            }
        }

        status_header(200);
        exit;
    }
});

// Add admin page to view, download, and clear logs
add_action('admin_menu', function () {
    add_menu_page(
        'M-Pesa Logs',
        'M-Pesa Logs',
        'manage_options',
        'mpesa-logs',
        function () {
            echo '<div class="wrap"><h1>M-Pesa Callback Logs</h1>';
            $log_path = WP_CONTENT_DIR . '/mpesa-callback-log.txt';
            if (file_exists($log_path)) {
                echo '<form method="post">';
                echo '<textarea readonly style="width:100%; height:500px">' . esc_textarea(file_get_contents($log_path)) . '</textarea><br><br>';
                echo '<a href="' . admin_url('admin-ajax.php?action=download_mpesa_log') . '" class="button button-primary">Download Log File</a> ';
                echo '<a href="' . admin_url('admin-ajax.php?action=clear_mpesa_log') . '" class="button button-secondary" onclick="return confirm(\'Are you sure you want to clear the log file?\')">Clear Log File</a>';
                echo '</form>';
            } else {
                echo '<p>No log file found.</p>';
            }
            echo '</div>';
        }
    );
});

add_action('wp_ajax_download_mpesa_log', function () {
    $log_path = WP_CONTENT_DIR . '/mpesa-callback-log.txt';
    if (file_exists($log_path)) {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="mpesa-callback-log.txt"');
        readfile($log_path);
        exit;
    } else {
        wp_die('Log file not found.');
    }
});

add_action('wp_ajax_clear_mpesa_log', function () {
    $log_path = WP_CONTENT_DIR . '/mpesa-callback-log.txt';
    if (file_exists($log_path)) {
        file_put_contents($log_path, '');
        wp_redirect(admin_url('admin.php?page=mpesa-logs'));
        exit;
    } else {
        wp_die('Log file not found.');
    }
});
