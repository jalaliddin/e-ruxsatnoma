<div>
    <label class="block text-gray-700 mb-1">F.I.Sh (rasmiy)</label>
    <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name ?? '') }}" required
           class="w-full border px-3 py-2 rounded-lg">
    @if (($employee->telegram_full_name ?? null) && $employee->telegram_full_name !== $employee->full_name)
        <p class="text-xs text-amber-600 mt-1">Telegramda ko'rsatilgan ismi: {{ $employee->telegram_full_name }}</p>
    @endif
</div>

<div>
    <label class="block text-gray-700 mb-1">Telefon raqami</label>
    <input type="text" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" required placeholder="998901234567"
           class="w-full border px-3 py-2 rounded-lg">
</div>

<div>
    <label class="block text-gray-700 mb-1">Bo'lim</label>
    <input type="text" name="department" value="{{ old('department', $employee->department ?? '') }}"
           class="w-full border px-3 py-2 rounded-lg">
</div>
