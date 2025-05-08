<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/class-license-manager.php';

$manager = new KCB_License_Manager();

if ($manager->is_valid()) {
    return true;
}

add_action('admin_notices', function () use ($manager) {
    echo '<div class="notice notice-error"><p><strong>KCB M-Pesa Pro:</strong> ' . esc_html($manager->get_message()) . '</p></div>';
});

return false;
