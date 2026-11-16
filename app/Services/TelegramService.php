<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private function url(string $method): string
    {
        return 'https://api.telegram.org/bot' . config('services.telegram.token') . '/' . $method;
    }

    public function send(string|int $chatId, string $text): bool
    {
        try {
            return Http::withoutVerifying()->timeout(8)
                ->post($this->url('sendMessage'), ['chat_id' => $chatId, 'text' => $text])
                ->successful();
        } catch (\Throwable $e) {
            Log::error('Telegram: ' . $e->getMessage());
            return false;
        }
    }

    public function getUpdates(int $offset): array
    {
        try {
            return Http::withoutVerifying()->timeout(35)
                ->get($this->url('getUpdates'), ['offset' => $offset, 'timeout' => 25])
                ->json('result', []);
        } catch (\Throwable $e) {
            Log::error('Telegram: ' . $e->getMessage());
            return [];
        }
    }
}
