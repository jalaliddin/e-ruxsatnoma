<div>
    <label class="block text-gray-700 mb-1">Nomi</label>
    <input type="text" name="name" value="{{ old('name', $department->name ?? '') }}" required
           class="w-full border px-3 py-2 rounded-lg">
</div>

<div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" id="is_active" value="1"
           @checked(old('is_active', $department->is_active ?? true))
           class="rounded">
    <label for="is_active" class="text-gray-700">Faol</label>
</div>
