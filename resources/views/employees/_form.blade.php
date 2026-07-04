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
    <select name="department_id" class="w-full border px-3 py-2 rounded-lg">
        <option value="">— Tanlanmagan —</option>
        @foreach ($departments as $dept)
            <option value="{{ $dept->id }}" @selected(old('department_id', $employee->department_id ?? null) == $dept->id)>{{ $dept->name }}</option>
        @endforeach
    </select>
</div>
