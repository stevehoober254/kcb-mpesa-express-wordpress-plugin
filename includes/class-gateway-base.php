<?php

if (!defined('ABSPATH')) exit;

abstract class KCB_Mpesa_Gateway_Base extends WC_Payment_Gateway {

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

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
    }

    public function init_form_fields() {
        $this->form_fields = [
            'enabled' => [
                'title' => 'Enable/Disable',
                'type' => 'checkbox',
                'label' => 'Enable KCB M-Pesa Payment',
                'default' => 'yes'
            ],
            'title' => [
                'title' => 'Title',
                'type' => 'text',
                'default' => 'M-Pesa via KCB'
            ],
            'consumer_key' => [
                'title' => 'Consumer Key',
                'type' => 'text'
            ],
            'consumer_secret' => [
                'title' => 'Consumer Secret',
                'type' => 'password'
            ]
        ];
    }
}
