<?php
if (!defined('ABSPATH')) exit;

class KCB_License_Manager {
    private $license_key;

    public function __construct() {
        $this->license_key = get_option('kcb_mpesa_license_key');
    }

    /**
     * Check if the current license key is valid.
     */
    public function is_valid(): bool {
        $valid_keys = ['KCBPRO-2025-VALID']; // In production, validate via API or license server
        return in_array($this->license_key, $valid_keys);
    }

    /**
     * Return a human-readable license status message.
     */
    public function get_message(): string {
        return $this->is_valid()
            ? '✅ License is valid.'
            : '❌ License is invalid.';
    }
}
