<?php

if (!defined('ABSPATH')) exit;

class WC_KCB_Mpesa_Gateway extends KCB_Mpesa_Gateway_Base {

    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        $phone = get_post_meta($order_id, '_billing_phone', true);
        $amount = $order->get_total();

        $credentials = base64_encode("{$this->consumer_key}:{$this->consumer_secret}");

        $token_response = wp_remote_post("https://uat.buni.kcbgroup.com/token?grant_type=client_credentials", [
            'headers' => ['Authorization' => "Basic $credentials"]
        ]);

        if (is_wp_error($token_response)) {
            wc_add_notice('Payment failed: Authentication error.', 'error');
            return;
        }

        $token_body = json_decode(wp_remote_retrieve_body($token_response), true);
        $access_token = $token_body['access_token'] ?? '';
        if (!$access_token) {
            wc_add_notice('Payment failed: Token missing.', 'error');
            return;
        }

        $payload = [
            'phoneNumber' => $phone,
            'amount' => (string) $amount,
            'invoiceNumber' => 'KCBTILLNO-' . $order_id,
            'sharedShortCode' => true,
            'orgShortCode' => '',
            'orgPassKey' => '',
            'callbackUrl' => site_url('/mpesa-callback'),
            'transactionDescription' => 'Order #' . $order_id
        ];

        $response = wp_remote_post("https://uat.buni.kcbgroup.com/mm/api/request/1.0.0/stkpush", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $access_token",
                'accept' => 'application/json',
                'routeCode' => '207',
                'operation' => 'STKPush',
                'messageId' => uniqid("order_"),
            ],
            'body' => json_encode($payload)
        ]);

        if (is_wp_error($response)) {
            wc_add_notice('STK push failed.', 'error');
            return;
        }

        $order->update_status('on-hold', 'Waiting for M-Pesa confirmation');
        wc_reduce_stock_levels($order_id);
        WC()->cart->empty_cart();

        return ['result' => 'success', 'redirect' => $this->get_return_url($order)];
    }
}
