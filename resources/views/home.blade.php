@extends('layouts.app')

@section('title', 'Boshqaruv paneli')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-400">
            <div class="text-sm text-gray-500">Kutilayotgan so'rovlar</div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-400">
            <div class="text-sm text-gray-500">Bugun tasdiqlangan</div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['approvedToday'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-400">
            <div class="text-sm text-gray-500">Rad etilgan</div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['rejected'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-400">
            <div class="text-sm text-gray-500">Xodimlar</div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['employees'] }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b font-semibold text-gray-700">So'nggi so'rovlar</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left px-4 py-2">#</th>
                        <th class="text-left px-4 py-2">Xodim</th>
                        <th class="text-left px-4 py-2">Kategoriya</th>
                        <th class="text-left px-4 py-2">Holat</th>
                        <th class="text-left px-4 py-2">Sana</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($recent as $permission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $permission->id }}</td>
                            <td class="px-4 py-2">{{ $permission->employee->full_name ?? $permission->employee_name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $permission->category->name ?? '—' }}</td>
                            <td class="px-4 py-2">@include('partials.status-badge', ['status' => $permission->status])</td>
                            <td class="px-4 py-2">{{ $permission->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center px-4 py-6 text-gray-500">So'rovlar topilmadi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
