<div>
    <label class="block text-gray-700 mb-1">F.I.Sh</label>
    <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name ?? '') }}" required
           class="w-full border px-3 py-2 rounded-lg">
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
