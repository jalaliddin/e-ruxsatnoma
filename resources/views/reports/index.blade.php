@extends('layouts.app')

@section('title', 'Hisobotlar')

@section('content')
    <form method="GET" action="{{ route('reports.index') }}" class="bg-white rounded-xl shadow-sm p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sanadan</label>
            <input type="date" name="from" value="{{ request('from') }}" class="w-full border px-3 py-2 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sanagacha</label>
            <input type="date" name="to" value="{{ request('to') }}" class="w-full border px-3 py-2 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Holat</label>
            <select name="status" class="w-full border px-3 py-2 rounded-lg text-sm">
                <option value="">Barchasi</option>
                @foreach (['pending' => 'Kutilmoqda', 'awaiting_manager' => 'Rahbarda', 'approved' => 'Tasdiqlangan', 'rejected' => 'Rad etilgan'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Bo'lim</label>
            <select name="department_id" class="w-full border px-3 py-2 rounded-lg text-sm">
                <option value="">Barchasi</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" @selected((string) request('department_id') === (string) $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Xodim</label>
            <select name="employee_id" class="w-full border px-3 py-2 rounded-lg text-sm">
                <option value="">Barchasi</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>{{ $employee->full_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2 lg:col-span-5 flex gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Filtrlash</button>
            <a href="{{ route('reports.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Tozalash</a>
            <a href="{{ route('reports.export', request()->query()) }}" class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">⬇ CSV yuklab olish</a>
        </div>
    </form>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-gray-400">
            <div class="text-sm text-gray-500">Jami so'rovlar</div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-400">
            <div class="text-sm text-gray-500">Kutilmoqda</div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-400">
            <div class="text-sm text-gray-500">Tasdiqlangan</div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['approved'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-400">
            <div class="text-sm text-gray-500">Rad etilgan</div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['rejected'] }}</div>
        </div>
    </div>

    @if ($byDepartment->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-5 py-3 border-b font-semibold text-gray-700">Bo'limlar kesimida</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="text-left px-4 py-2">Bo'lim</th>
                            <th class="text-left px-4 py-2">So'rovlar soni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($byDepartment as $row)
                            <tr>
                                <td class="px-4 py-2">{{ $row->department_name }}</td>
                                <td class="px-4 py-2">{{ $row->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left px-4 py-2">#</th>
                        <th class="text-left px-4 py-2">Xodim</th>
                        <th class="text-left px-4 py-2">Bo'lim</th>
                        <th class="text-left px-4 py-2">Kategoriya</th>
                        <th class="text-left px-4 py-2">Muddat</th>
                        <th class="text-left px-4 py-2">Holat</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($permissions as $permission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <a href="{{ route('permission.show', $permission) }}" class="text-blue-600 hover:underline">#{{ $permission->id }}</a>
                            </td>
                            <td class="px-4 py-2">{{ $permission->employee->full_name ?? $permission->employee_name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $permission->employee?->department?->name ?? $permission->employee?->legacy_department ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $permission->category->name ?? '—' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                {{ optional($permission->from_time)->format('d.m.Y H:i') }} —
                                {{ optional($permission->to_time)->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-4 py-2">@include('partials.status-badge', ['status' => $permission->status])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center px-4 py-6 text-gray-500">So'rovlar topilmadi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $permissions->links() }}
        </div>
    </div>
@endsection
