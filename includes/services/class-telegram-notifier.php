<?php
if (!defined('ABSPATH')) exit;

class KCB_Telegram_Notifier {

    private string $bot_token;
    private string $chat_id;

    public function __construct() {
        $this->bot_token = get_option('kcb_mpesa_telegram_token', '');
        $this->chat_id   = get_option('kcb_mpesa_telegram_chat_id', '');
    }

    public function is_ready(): bool {
        return !empty($this->bot_token) && !empty($this->chat_id);
    }

    public function send(string $message): void {
        if (!$this->is_ready()) return;

        wp_remote_post("https://api.telegram.org/bot{$this->bot_token}/sendMessage", [
            'timeout' => 15,
            'blocking' => false,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                'chat_id'    => $this->chat_id,
                'text'       => $message,
                'parse_mode' => 'Markdown'
            ])
        ]);
    }
}
