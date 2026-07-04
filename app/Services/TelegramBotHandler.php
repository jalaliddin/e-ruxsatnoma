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
            'awaiting_from', 'awaiting_to' => $this->telegram->sendMessage(
                $chatId,
                "Iltimos, sana va vaqtni yuqoridagi tugmalar orqali tanlang."
            ),
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
        $telegramFullName = trim(($contact['first_name'] ?? '').' '.($contact['last_name'] ?? ''));

        $employee = Employee::where('phone', 'like', "%{$phone}")->first();

        if ($employee) {
            // Rasmiy F.I.Sh (full_name) qo'lda kiritilgan bo'lishi mumkin — uni
            // Telegramdagi ism bilan avtomatik almashtirmaymiz, faqat solishtirish
            // uchun telegram_full_name'ni yangilaymiz.
            $employee->update([
                'telegram_chat_id' => $chatId,
                'telegram_username' => $contact['username'] ?? null,
                'telegram_full_name' => $telegramFullName !== '' ? $telegramFullName : null,
            ]);
        } else {
            $employee = Employee::create([
                'full_name' => $telegramFullName !== '' ? $telegramFullName : 'Nomaʼlum xodim',
                'phone' => $phone,
                'telegram_chat_id' => $chatId,
                'telegram_username' => $contact['username'] ?? null,
                'telegram_full_name' => $telegramFullName !== '' ? $telegramFullName : null,
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

        $today = Carbon::today();
        $this->telegram->sendMessage(
            $state->telegram_chat_id,
            "Ruxsat qachondan boshlanadi? Sanani tanlang:",
            $this->telegram->inlineKeyboard($this->calendarKeyboard($today->year, $today->month, 'from'))
        );
    }

    protected function calendarKeyboard(int $year, int $month, string $ctx): array
    {
        $first = Carbon::create($year, $month, 1);
        $daysInMonth = $first->daysInMonth;
        $startWeekday = $first->dayOfWeekIso; // 1 (Dush) .. 7 (Yak)
        $today = Carbon::today();

        $rows = [];
        $rows[] = collect(['Du', 'Se', 'Ch', 'Pa', 'Ju', 'Sh', 'Ya'])
            ->map(fn ($d) => ['text' => $d, 'callback_data' => 'noop'])->all();

        $week = [];
        for ($i = 1; $i < $startWeekday; $i++) {
            $week[] = ['text' => ' ', 'callback_data' => 'noop'];
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);

            if ($date->lt($today)) {
                $week[] = ['text' => '·', 'callback_data' => 'noop'];
            } else {
                $label = $date->isToday() ? "[{$day}]" : (string) $day;
                $week[] = ['text' => $label, 'callback_data' => "cal:{$ctx}:{$year}:{$month}:{$day}"];
            }

            if (count($week) === 7) {
                $rows[] = $week;
                $week = [];
            }
        }

        if ($week) {
            while (count($week) < 7) {
                $week[] = ['text' => ' ', 'callback_data' => 'noop'];
            }
            $rows[] = $week;
        }

        $prev = $first->copy()->subMonth();
        $next = $first->copy()->addMonth();

        $navRow = [];
        $navRow[] = $prev->copy()->endOfMonth()->lt($today)
            ? ['text' => ' ', 'callback_data' => 'noop']
            : ['text' => '‹', 'callback_data' => "calnav:{$ctx}:{$prev->year}:{$prev->month}"];
        $navRow[] = ['text' => $first->format('m.Y'), 'callback_data' => 'noop'];
        $navRow[] = ['text' => '›', 'callback_data' => "calnav:{$ctx}:{$next->year}:{$next->month}"];
        $rows[] = $navRow;

        return $rows;
    }

    protected function hourKeyboard(string $ctx, int $y, int $m, int $d): array
    {
        $rows = [];
        $row = [];

        for ($h = 0; $h < 24; $h++) {
            $row[] = ['text' => sprintf('%02d', $h), 'callback_data' => "hour:{$ctx}:{$y}:{$m}:{$d}:{$h}"];

            if (count($row) === 6) {
                $rows[] = $row;
                $row = [];
            }
        }

        if ($row) {
            $rows[] = $row;
        }

        return $rows;
    }

    protected function minuteKeyboard(string $ctx, int $y, int $m, int $d, int $h): array
    {
        $row = array_map(
            fn (int $mi) => ['text' => sprintf('%02d', $mi), 'callback_data' => "min:{$ctx}:{$y}:{$m}:{$d}:{$h}:{$mi}"],
            [0, 15, 30, 45]
        );

        return [$row];
    }

    protected function onCalendarNav(string $chatId, string $rest, string $callbackId): void
    {
        [$ctx, $y, $m] = array_pad(explode(':', $rest, 3), 3, null);

        $this->telegram->answerCallbackQuery($callbackId);
        $this->telegram->sendMessage(
            $chatId,
            $ctx === 'from' ? "Ruxsat qachondan boshlanadi? Sanani tanlang:" : "Ruxsat qachongacha? Sanani tanlang:",
            $this->telegram->inlineKeyboard($this->calendarKeyboard((int) $y, (int) $m, $ctx))
        );
    }

    protected function onCalendarDay(string $chatId, string $rest, string $callbackId): void
    {
        [$ctx, $y, $m, $d] = array_pad(explode(':', $rest, 4), 4, null);

        $this->telegram->answerCallbackQuery($callbackId);
        $this->telegram->sendMessage(
            $chatId,
            "Soatni tanlang:",
            $this->telegram->inlineKeyboard($this->hourKeyboard($ctx, (int) $y, (int) $m, (int) $d))
        );
    }

    protected function onHourSelected(string $chatId, string $rest, string $callbackId): void
    {
        [$ctx, $y, $m, $d, $h] = array_pad(explode(':', $rest, 5), 5, null);

        $this->telegram->answerCallbackQuery($callbackId);
        $this->telegram->sendMessage(
            $chatId,
            "Daqiqani tanlang:",
            $this->telegram->inlineKeyboard($this->minuteKeyboard($ctx, (int) $y, (int) $m, (int) $d, (int) $h))
        );
    }

    protected function onMinuteSelected(string $chatId, string $rest, string $callbackId): void
    {
        [$ctx, $y, $m, $d, $h, $mi] = array_pad(explode(':', $rest, 6), 6, null);

        $selected = Carbon::create((int) $y, (int) $m, (int) $d, (int) $h, (int) $mi);
        $state = BotState::firstOrCreate(['telegram_chat_id' => $chatId], ['state' => 'idle']);
        $payload = $state->payload ?? [];

        if ($ctx === 'from') {
            $payload['from_time'] = $selected->toDateTimeString();
            $state->update(['state' => 'awaiting_to', 'payload' => $payload]);

            $this->telegram->answerCallbackQuery($callbackId, "Boshlanish: {$selected->format('d.m.Y H:i')}");
            $this->telegram->sendMessage(
                $chatId,
                "Ruxsat qachongacha? Sanani tanlang:",
                $this->telegram->inlineKeyboard($this->calendarKeyboard($selected->year, $selected->month, 'to'))
            );

            return;
        }

        $from = Carbon::parse($payload['from_time'] ?? null);

        if (! isset($payload['from_time']) || $selected->lessThanOrEqualTo($from)) {
            $this->telegram->answerCallbackQuery($callbackId, "❌ Tugash vaqti boshlanish vaqtidan keyin bo'lishi kerak.");
            $this->telegram->sendMessage(
                $chatId,
                "Ruxsat qachongacha? Sanani qaytadan tanlang:",
                $this->telegram->inlineKeyboard($this->calendarKeyboard($from->year, $from->month, 'to'))
            );

            return;
        }

        $payload['to_time'] = $selected->toDateTimeString();
        $state->update(['state' => 'awaiting_confirm', 'payload' => $payload]);

        $category = PermissionCategory::find($payload['category_id']);

        $this->telegram->answerCallbackQuery($callbackId, "Tugash: {$selected->format('d.m.Y H:i')}");
        $this->telegram->sendMessage(
            $chatId,
            "Tekshiring:\n\n📂 Kategoriya: {$category?->name}\n📝 Sabab: {$payload['reason']}\n🕒 {$from->format('d.m.Y H:i')} — {$selected->format('d.m.Y H:i')}",
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
            'calnav' => $this->onCalendarNav($chatId, $rest, $callbackId),
            'cal' => $this->onCalendarDay($chatId, $rest, $callbackId),
            'hour' => $this->onHourSelected($chatId, $rest, $callbackId),
            'min' => $this->onMinuteSelected($chatId, $rest, $callbackId),
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

    protected function generateCode(): string
    {
        do {
            $code = (string) random_int(1000, 9999);
        } while (Permission::where('code', $code)->exists());

        return $code;
    }
}
