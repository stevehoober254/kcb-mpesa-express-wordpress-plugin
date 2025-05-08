<?php
if (!defined('ABSPATH')) exit;

class KCB_License_Manager {
    private $license_key;

    public function __construct() {
        $this->license_key = get_option('kcb_mpesa_license_key');
    }

    public function is_valid() {
        $valid_keys = ['KCBPRO-2025-VALID'];
        return in_array($this->license_key, $valid_keys);
    }

    public function get_message() {
        return $this->is_valid()
            ? '✅ License is valid.'
            : '❌ License is invalid.';
    }
}
