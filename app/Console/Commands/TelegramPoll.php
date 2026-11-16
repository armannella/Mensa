<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Cache;

#[Signature('telegram:poll')]
#[Description('Listen to the bot and link Telegram accounts to students')]
class TelegramPoll extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegram)
    {
        $this->info('Listening... press Ctrl+C to stop.');
        $offset = (int) Cache::get('telegram_offset', 0);

        while (true) {
            foreach ($telegram->getUpdates($offset) as $u) {
                $offset = $u['update_id'] + 1;
                Cache::forever('telegram_offset', $offset);

                $text = $u['message']['text'] ?? '';
                $chatId = $u['message']['chat']['id'] ?? null;
                if (!$chatId || !str_starts_with($text, '/start ')) {
                    continue;
                }

                $code = trim(substr($text, 7));
                $user = User::where('telegram_link_code', $code)
                    ->where('telegram_link_expires_at', '>', now())
                    ->first();

                if (!$user) {
                    $telegram->send($chatId, 'Invalid or expired code.');
                    continue;
                }
                if (User::where('telegram_chat_id', $chatId)->where('id', '!=', $user->id)->exists()) {
                    $telegram->send($chatId, 'This Telegram account is already linked to another user.');
                    continue;
                }

                $user->forceFill([
                    'telegram_chat_id' => $chatId,
                    'telegram_link_code' => null,
                    'telegram_link_expires_at' => null,
                ])->save();

                $telegram->send($chatId, 'Your account is linked. You will get transfer codes here.');
                $this->info("Linked user #{$user->id}");
            }
        }
    }
}
