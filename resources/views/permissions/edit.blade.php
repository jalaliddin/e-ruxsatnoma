@extends('layouts.app')

@section('title', 'So\'rovni tahrirlash #'.$permission->id)

@section('content')
    <div class="max-w-lg bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('permission.update', $permission) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-700 mb-1">Xodim</label>
                <select name="employee_id" class="w-full border px-3 py-2 rounded-lg">
                    <option value="">— Tanlanmagan —</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(old('employee_id', $permission->employee_id) == $employee->id)>{{ $employee->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Kategoriya</label>
                <select name="category_id" class="w-full border px-3 py-2 rounded-lg">
                    <option value="">— Tanlanmagan —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $permission->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Sabab</label>
                <textarea name="reason" rows="3" class="w-full border px-3 py-2 rounded-lg">{{ old('reason', $permission->reason) }}</textarea>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Qachondan</label>
                <input type="datetime-local" name="from_time" required
                       value="{{ old('from_time', optional($permission->from_time)->format('Y-m-d\TH:i')) }}"
                       class="w-full border px-3 py-2 rounded-lg">
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Qachongacha</label>
                <input type="datetime-local" name="to_time" required
                       value="{{ old('to_time', optional($permission->to_time)->format('Y-m-d\TH:i')) }}"
                       class="w-full border px-3 py-2 rounded-lg">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg w-full">Saqlash</button>
        </form>
    </div>
@endsection
