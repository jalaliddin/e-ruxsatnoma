<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected Client $client;

    public function __construct()
    {
        $token = config('services.telegram.bot_token');

        $this->client = new Client([
            'base_uri' => "https://api.telegram.org/bot{$token}/",
            'timeout' => 10,
        ]);
    }

    public function call(string $method, array $params = []): ?array
    {
        if (! config('services.telegram.bot_token')) {
            Log::warning("Telegram: TELEGRAM_BOT_TOKEN sozlanmagan, so'rov yuborilmadi ({$method}).");
            return null;
        }

        try {
            $response = $this->client->post($method, ['json' => $params]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error("Telegram API xatosi ({$method}): ".$e->getMessage());

            return null;
        }
    }

    public function sendMessage(string $chatId, string $text, ?array $replyMarkup = null): ?array
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = $replyMarkup;
        }

        return $this->call('sendMessage', $params);
    }

    public function editMessageReplyMarkup(string $chatId, int $messageId, array $replyMarkup): ?array
    {
        return $this->call('editMessageReplyMarkup', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => $replyMarkup,
        ]);
    }

    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null): ?array
    {
        return $this->call('answerCallbackQuery', array_filter([
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]));
    }

    public function requestContactKeyboard(): array
    {
        return [
            'keyboard' => [[
                ['text' => '📞 Raqamni ulashish', 'request_contact' => true],
            ]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
    }

    public function mainMenuKeyboard(): array
    {
        return [
            'keyboard' => [
                ['📝 Ruxsatnoma so\'rash'],
                ['📋 Mening so\'rovlarim'],
            ],
            'resize_keyboard' => true,
        ];
    }

    public function inlineKeyboard(array $buttons): array
    {
        return ['inline_keyboard' => $buttons];
    }

    public function notifyHrNewRequest(Permission $permission): void
    {
        $hrUsers = User::where('role', 'hr')->whereNotNull('telegram_chat_id')->get();
        // Botni hali bog'lamagan rahbarlar ham tanlov tugmasida ko'rinishi kerak —
        // ular faqat keyinroq (bog'langach) xabar oladi, hozircha ro'yxatdan
        // chiqarib tashlamaymiz.
        $managers = User::where('role', 'manager')->get();

        $text = $this->requestSummary($permission, "🆕 <b>Yangi ruxsatnoma so'rovi</b>");

        $buttons = $managers->map(fn (User $m) => [[
            'text' => $m->telegram_chat_id ? $m->name : "{$m->name} (bog'lanmagan)",
            'callback_data' => "assign:{$permission->id}:{$m->id}",
        ]])->values()->all();

        foreach ($hrUsers as $hr) {
            $this->sendMessage($hr->telegram_chat_id, $text, $this->inlineKeyboard($buttons));
        }
    }

    public function sendManagerDecisionRequest(Permission $permission, User $manager): void
    {
        $text = $this->requestSummary($permission, "📥 <b>Tasdiqlash uchun so'rov</b>");

        $buttons = [[
            ['text' => '✅ Roziman', 'callback_data' => "decide:{$permission->id}:approve"],
            ['text' => '❌ Rad etaman', 'callback_data' => "decide:{$permission->id}:reject"],
        ]];

        $this->sendMessage($manager->telegram_chat_id, $text, $this->inlineKeyboard($buttons));
    }

    public function sendApprovalToEmployee(Permission $permission): void
    {
        $from = $this->formatUzDate($permission->from_time);
        $to = $this->formatUzDate($permission->to_time);
        $doorUrl = url("/eshik/{$permission->code}");

        $text = "✅ Sizning ruxsatnoma so'rovingiz tasdiqlandi!\n\n"
            ."🔑 Kodingiz: <b>{$permission->code}</b>\n"
            ."🕒 Amal qilish muddati: {$from} — {$to}\n\n"
            .'<a href="'.$doorUrl.'">🔓 Turniketni ochish</a>';

        $this->sendMessage($permission->employee->telegram_chat_id, $text);
    }

    public function sendRejectionToEmployee(Permission $permission): void
    {
        $this->sendMessage(
            $permission->employee->telegram_chat_id,
            "❌ Afsuski, sizning ruxsatnoma so'rovingiz rad etildi."
        );
    }

    protected function requestSummary(Permission $permission, string $title): string
    {
        $from = $this->formatUzDate($permission->from_time);
        $to = $this->formatUzDate($permission->to_time);
        $department = $permission->employee?->department?->name ?? $permission->employee?->legacy_department;

        return "{$title}\n\n"
            ."👤 Xodim: {$permission->employee?->full_name}\n"
            ."🏢 Bo'lim: ".($department ?? '—')."\n"
            ."📂 Kategoriya: {$permission->category?->name}\n"
            ."📝 Sabab: {$permission->reason}\n"
            ."🕒 Muddat: {$from} — {$to}";
    }

    /**
     * "4-iyul 09:00" ko'rinishidagi o'zbekcha sana formati.
     */
    protected function formatUzDate(?\Carbon\Carbon $date): string
    {
        if (! $date) {
            return '—';
        }

        $months = [
            1 => 'yanvar', 2 => 'fevral', 3 => 'mart', 4 => 'aprel',
            5 => 'may', 6 => 'iyun', 7 => 'iyul', 8 => 'avgust',
            9 => 'sentabr', 10 => 'oktabr', 11 => 'noyabr', 12 => 'dekabr',
        ];

        return "{$date->day}-{$months[$date->month]} {$date->format('H:i')}";
    }
}
