@extends('layouts.app')

@section('title', 'Ruxsatnoma so\'rovlari')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left px-4 py-2">#</th>
                        <th class="text-left px-4 py-2">Kod</th>
                        <th class="text-left px-4 py-2">Xodim</th>
                        <th class="text-left px-4 py-2">Kategoriya</th>
                        <th class="text-left px-4 py-2">Muddat</th>
                        <th class="text-left px-4 py-2">Holat</th>
                        <th class="text-left px-4 py-2">Amallar</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($permissions as $permission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $permission->id }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $permission->code ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $permission->employee->full_name ?? $permission->employee_name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $permission->category->name ?? '—' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                {{ optional($permission->from_time)->format('d.m.Y H:i') }} —
                                {{ optional($permission->to_time)->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-4 py-2">@include('partials.status-badge', ['status' => $permission->status])</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('permission.show', $permission) }}" class="text-blue-600 hover:underline">Ko'rish</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center px-4 py-6 text-gray-500">Ruxsatnomalar topilmadi.</td>
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
