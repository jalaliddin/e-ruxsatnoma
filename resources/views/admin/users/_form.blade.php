<div>
    <label class="block text-gray-700 mb-1">Ism</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
           class="w-full border px-3 py-2 rounded-lg">
</div>

<div>
    <label class="block text-gray-700 mb-1">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
           class="w-full border px-3 py-2 rounded-lg">
</div>

<div>
    <label class="block text-gray-700 mb-1">Telefon</label>
    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
           class="w-full border px-3 py-2 rounded-lg">
</div>

<div>
    <label class="block text-gray-700 mb-1">Rol</label>
    <select name="role" required class="w-full border px-3 py-2 rounded-lg">
        @foreach (['admin' => 'Admin', 'hr' => 'Kadrlar bo\'limi', 'manager' => 'Rahbar'] as $value => $label)
            <option value="{{ $value }}" @selected(old('role', $user->role ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-gray-700 mb-1">Parol {{ isset($user) ? '(o\'zgartirmaslik uchun bo\'sh qoldiring)' : '' }}</label>
    <input type="password" name="password" {{ isset($user) ? '' : 'required' }}
           class="w-full border px-3 py-2 rounded-lg">
</div>
