<?php
if (!defined('ABSPATH')) exit;

function kcb_mpesa_render_logs_ui() {
    echo '<div class="wrap"><h1>M-Pesa Callback Logs</h1>';
    $log_path = WP_CONTENT_DIR . '/mpesa-callback-log.txt';

    if (file_exists($log_path)) {
        echo '<form method="post">';
        echo '<textarea readonly style="width:100%; height:500px;">' . esc_textarea(file_get_contents($log_path)) . '</textarea><br><br>';
        echo '<a href="' . admin_url('admin-ajax.php?action=download_mpesa_log') . '" class="button button-primary">Download Log File</a> ';
        echo '<a href="' . admin_url('admin-ajax.php?action=clear_mpesa_log') . '" class="button button-secondary" onclick="return confirm(\'Are you sure you want to clear the log file?\')">Clear Log File</a>';
        echo '</form>';
    } else {
        echo '<p>No log file found.</p>';
    }

    echo '</div>';
}
