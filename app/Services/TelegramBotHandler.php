<?php

namespace App\Services;

use App\Models\BotState;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\User;
use Carbon\Carbon;

class TelegramBotHandler
{
    public function __construct(protected TelegramService $telegram)
    {
    }

    public function handleUpdate(array $update): void
    {
        if (isset($update['callback_query'])) {
            $this->handleCallback($update['callback_query']);

            return;
        }

        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }
    }

    protected function handleMessage(array $message): void
    {
        $chatId = (string) $message['chat']['id'];
        $text = trim($message['text'] ?? '');
        $contact = $message['contact'] ?? null;

        if (str_starts_with($text, '/start')) {
            $this->handleStart($chatId, trim(substr($text, 6)));

            return;
        }

        if ($contact) {
            $this->handleContact($chatId, $contact);

            return;
        }

        $state = BotState::firstOrCreate(['telegram_chat_id' => $chatId], ['state' => 'idle']);

        match ($state->state) {
            'awaiting_reason' => $this->handleReason($state, $text),
            'awaiting_from' => $this->handleFromTime($state, $text),
            'awaiting_to' => $this->handleToTime($state, $text),
            default => $this->handleMenu($chatId, $text),
        };
    }

    protected function handleStart(string $chatId, string $payload): void
    {
        if (str_starts_with($payload, 'link_')) {
            $token = substr($payload, 5);
            $user = User::where('telegram_link_token', $token)->first();

            if (! $user) {
                $this->telegram->sendMessage($chatId, "❌ Havola yaroqsiz yoki muddati o'tgan.");

                return;
            }

            $user->update(['telegram_chat_id' => $chatId, 'telegram_link_token' => null]);
            $this->telegram->sendMessage($chatId, "✅ Hisobingiz ({$user->name}) Telegram bilan bog'landi.");

            return;
        }

        $employee = Employee::where('telegram_chat_id', $chatId)->first();

        if ($employee) {
            $this->sendMainMenu($chatId);

            return;
        }

        BotState::updateOrCreate(
            ['telegram_chat_id' => $chatId],
            ['state' => 'awaiting_contact', 'payload' => null]
        );

        $this->telegram->sendMessage(
            $chatId,
            "Assalomu alaykum! Ruxsatnoma so'rash uchun avval telefon raqamingizni ulashing.",
            $this->telegram->requestContactKeyboard()
        );
    }

    protected function handleContact(string $chatId, array $contact): void
    {
        $phone = ltrim($contact['phone_number'], '+');

        $employee = Employee::where('phone', 'like', "%{$phone}")->first();

        if ($employee) {
            $employee->update([
                'telegram_chat_id' => $chatId,
                'telegram_username' => $contact['username'] ?? null,
            ]);
        } else {
            $fullName = trim(($contact['first_name'] ?? '').' '.($contact['last_name'] ?? ''));

            $employee = Employee::create([
                'full_name' => $fullName !== '' ? $fullName : 'Nomaʼlum xodim',
                'phone' => $phone,
                'telegram_chat_id' => $chatId,
                'telegram_username' => $contact['username'] ?? null,
                'registered_via' => 'bot',
            ]);
        }

        BotState::updateOrCreate(['telegram_chat_id' => $chatId], ['state' => 'idle', 'payload' => null]);

        $this->telegram->sendMessage($chatId, "Rahmat, {$employee->full_name}! Ro'yxatdan o'tdingiz.");
        $this->sendMainMenu($chatId);
    }

    protected function sendMainMenu(string $chatId): void
    {
        $this->telegram->sendMessage(
            $chatId,
            "Asosiy menyu:",
            $this->telegram->mainMenuKeyboard()
        );
    }

    protected function handleMenu(string $chatId, string $text): void
    {
        $employee = Employee::where('telegram_chat_id', $chatId)->first();

        if (! $employee) {
            $this->handleStart($chatId, '');

            return;
        }

        if (str_contains($text, 'Ruxsatnoma so\'rash')) {
            $this->sendCategoryList($chatId);

            return;
        }

        if (str_contains($text, 'Mening so\'rovlarim')) {
            $this->sendMyRequests($employee, $chatId);

            return;
        }

        $this->sendMainMenu($chatId);
    }

    protected function sendCategoryList(string $chatId): void
    {
        $categories = PermissionCategory::where('is_active', true)->get();

        if ($categories->isEmpty()) {
            $this->telegram->sendMessage($chatId, "Hozircha kategoriyalar sozlanmagan. Administratorga murojaat qiling.");

            return;
        }

        $buttons = $categories->map(fn (PermissionCategory $c) => [[
            'text' => $c->name,
            'callback_data' => "category:{$c->id}",
        ]])->values()->all();

        $this->telegram->sendMessage(
            $chatId,
            "Ruxsatnoma maqsadini tanlang:",
            $this->telegram->inlineKeyboard($buttons)
        );
    }

    protected function sendMyRequests(Employee $employee, string $chatId): void
    {
        $requests = $employee->permissions()->latest()->take(5)->get();

        if ($requests->isEmpty()) {
            $this->telegram->sendMessage($chatId, "Sizda hali so'rovlar yo'q.");

            return;
        }

        $lines = $requests->map(function (Permission $p) {
            $statusLabel = match ($p->status) {
                'pending' => '⏳ Kutilmoqda',
                'awaiting_manager' => '⏳ Rahbarda',
                'approved' => '✅ Tasdiqlangan',
                'rejected' => '❌ Rad etilgan',
                default => $p->status,
            };

            return "#{$p->id} — {$statusLabel} ({$p->created_at->format('d.m.Y')})";
        })->implode("\n");

        $this->telegram->sendMessage($chatId, "📋 Oxirgi so'rovlaringiz:\n\n{$lines}");
    }

    protected function handleReason(BotState $state, string $text): void
    {
        if ($text === '') {
            $this->telegram->sendMessage($state->telegram_chat_id, "Iltimos, sababni matn ko'rinishida yozing.");

            return;
        }

        $payload = $state->payload ?? [];
        $payload['reason'] = $text;

        $state->update(['state' => 'awaiting_from', 'payload' => $payload]);

        $this->telegram->sendMessage(
            $state->telegram_chat_id,
            "Ruxsat qachondan boshlanadi? Sanani shu formatda yuboring:\nmisol: 04.07.2026 09:00"
        );
    }

    protected function handleFromTime(BotState $state, string $text): void
    {
        $from = $this->parseDateTime($text);

        if (! $from) {
            $this->telegram->sendMessage(
                $state->telegram_chat_id,
                "Format noto'g'ri. Iltimos, shu ko'rinishda yuboring: 04.07.2026 09:00"
            );

            return;
        }

        $payload = $state->payload ?? [];
        $payload['from_time'] = $from->toDateTimeString();

        $state->update(['state' => 'awaiting_to', 'payload' => $payload]);

        $this->telegram->sendMessage(
            $state->telegram_chat_id,
            "Ruxsat qachongacha? Sanani shu formatda yuboring:\nmisol: 04.07.2026 18:00"
        );
    }

    protected function handleToTime(BotState $state, string $text): void
    {
        $to = $this->parseDateTime($text);
        $payload = $state->payload ?? [];
        $from = Carbon::parse($payload['from_time']);

        if (! $to || $to->lessThanOrEqualTo($from)) {
            $this->telegram->sendMessage(
                $state->telegram_chat_id,
                "Format noto'g'ri yoki tugash vaqti boshlanish vaqtidan oldin. Qaytadan yuboring: 04.07.2026 18:00"
            );

            return;
        }

        $payload['to_time'] = $to->toDateTimeString();
        $state->update(['state' => 'awaiting_confirm', 'payload' => $payload]);

        $category = PermissionCategory::find($payload['category_id']);

        $this->telegram->sendMessage(
            $state->telegram_chat_id,
            "Tekshiring:\n\n📂 Kategoriya: {$category?->name}\n📝 Sabab: {$payload['reason']}\n🕒 {$from->format('d.m.Y H:i')} — {$to->format('d.m.Y H:i')}",
            $this->telegram->inlineKeyboard([[
                ['text' => '✅ Yuborish', 'callback_data' => 'confirm:send'],
                ['text' => '❌ Bekor qilish', 'callback_data' => 'confirm:cancel'],
            ]])
        );
    }

    protected function handleCallback(array $callbackQuery): void
    {
        $chatId = (string) $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'] ?? '';
        $callbackId = $callbackQuery['id'];

        [$action, $rest] = array_pad(explode(':', $data, 2), 2, '');

        match ($action) {
            'category' => $this->onCategorySelected($chatId, (int) $rest, $callbackId),
            'confirm' => $this->onConfirm($chatId, $rest, $messageId, $callbackId),
            'assign' => $this->onAssign($chatId, $rest, $messageId, $callbackId),
            'decide' => $this->onDecide($chatId, $rest, $messageId, $callbackId),
            default => $this->telegram->answerCallbackQuery($callbackId),
        };
    }

    protected function onCategorySelected(string $chatId, int $categoryId, string $callbackId): void
    {
        $state = BotState::firstOrCreate(['telegram_chat_id' => $chatId], ['state' => 'idle']);
        $state->update(['state' => 'awaiting_reason', 'payload' => ['category_id' => $categoryId]]);

        $this->telegram->answerCallbackQuery($callbackId);
        $this->telegram->sendMessage($chatId, "Sababini qisqacha yozing:");
    }

    protected function onConfirm(string $chatId, string $decision, int $messageId, string $callbackId): void
    {
        $state = BotState::where('telegram_chat_id', $chatId)->first();

        if (! $state || $state->state !== 'awaiting_confirm') {
            $this->telegram->answerCallbackQuery($callbackId);

            return;
        }

        if ($decision === 'cancel') {
            $state->update(['state' => 'idle', 'payload' => null]);
            $this->telegram->answerCallbackQuery($callbackId, "Bekor qilindi.");
            $this->telegram->editMessageReplyMarkup($chatId, $messageId, ['inline_keyboard' => []]);

            return;
        }

        $employee = Employee::where('telegram_chat_id', $chatId)->first();
        $payload = $state->payload;

        $permission = Permission::create([
            'employee_id' => $employee->id,
            'category_id' => $payload['category_id'],
            'reason' => $payload['reason'],
            'from_time' => $payload['from_time'],
            'to_time' => $payload['to_time'],
            'status' => 'pending',
        ]);

        $state->update(['state' => 'idle', 'payload' => null]);

        $this->telegram->answerCallbackQuery($callbackId, "Yuborildi!");
        $this->telegram->editMessageReplyMarkup($chatId, $messageId, ['inline_keyboard' => []]);
        $this->telegram->sendMessage($chatId, "✅ So'rovingiz #{$permission->id} kadrlar bo'limiga yuborildi.");

        $this->telegram->notifyHrNewRequest($permission);
    }

    protected function onAssign(string $chatId, string $rest, int $messageId, string $callbackId): void
    {
        [$permissionId, $managerId] = array_pad(explode(':', $rest, 2), 2, null);

        $permission = Permission::find($permissionId);
        $manager = User::find($managerId);

        if (! $permission || ! $manager) {
            $this->telegram->answerCallbackQuery($callbackId, "So'rov topilmadi.");

            return;
        }

        $permission->update([
            'approver_id' => $manager->id,
            'status' => 'awaiting_manager',
        ]);

        $this->telegram->answerCallbackQuery($callbackId, "{$manager->name} ga yuborildi.");
        $this->telegram->editMessageReplyMarkup($chatId, $messageId, ['inline_keyboard' => []]);

        if ($manager->telegram_chat_id) {
            $this->telegram->sendManagerDecisionRequest($permission, $manager);
        }
    }

    protected function onDecide(string $chatId, string $rest, int $messageId, string $callbackId): void
    {
        [$permissionId, $decision] = array_pad(explode(':', $rest, 2), 2, null);

        $permission = Permission::find($permissionId);

        if (! $permission) {
            $this->telegram->answerCallbackQuery($callbackId, "So'rov topilmadi.");

            return;
        }

        $manager = User::where('telegram_chat_id', $chatId)->first();

        if ($decision === 'approve') {
            $permission->update([
                'code' => $this->generateCode(),
                'status' => 'approved',
                'approver_id' => $manager?->id ?? $permission->approver_id,
                'decided_at' => now(),
            ]);

            $this->telegram->answerCallbackQuery($callbackId, "Tasdiqlandi.");

            if ($permission->employee?->telegram_chat_id) {
                $this->telegram->sendApprovalToEmployee($permission);
            }
        } else {
            $permission->update([
                'status' => 'rejected',
                'approver_id' => $manager?->id ?? $permission->approver_id,
                'decided_at' => now(),
            ]);

            $this->telegram->answerCallbackQuery($callbackId, "Rad etildi.");

            if ($permission->employee?->telegram_chat_id) {
                $this->telegram->sendRejectionToEmployee($permission);
            }
        }

        $this->telegram->editMessageReplyMarkup($chatId, $messageId, ['inline_keyboard' => []]);
    }

    protected function parseDateTime(string $text): ?Carbon
    {
        try {
            return Carbon::createFromFormat('d.m.Y H:i', trim($text));
        } catch (\Throwable) {
            return null;
        }
    }

    protected function generateCode(): string
    {
        do {
            $code = (string) random_int(1000, 9999);
        } while (Permission::where('code', $code)->exists());

        return $code;
    }
}
