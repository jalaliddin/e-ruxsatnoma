@extends('layouts.app')

@section('title', 'Oxirgi Ruxsatnomalar')

@section('content')
<div class="overflow-x-auto">
    <table class="min-w-full bg-white rounded shadow">
        <thead class="bg-gray-100">
            <tr>
                <th class="text-left px-4 py-2 border-b">#</th>
                <th class="text-left px-4 py-2 border-b">Kod</th>
                <th class="text-left px-4 py-2 border-b">Kimdan</th>
                <th class="text-left px-4 py-2 border-b">Kimga</th>
                <th class="text-left px-4 py-2 border-b">Qayerga</th>
                <th class="text-left px-4 py-2 border-b">Boshlanish</th>
                <th class="text-left px-4 py-2 border-b">Tugash</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permissions as $permission)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b">{{ ($permissions->currentPage() - 1) * $permissions->perPage() + $loop->iteration }}</td>
                    <td class="px-4 py-2 border-b font-semibold">{{ $permission->code }}</td>
                    <td class="px-4 py-2 border-b">{{ $permission->user->name ?? 'Nomaʼlum' }}</td>
                    <td class="px-4 py-2 border-b">{{ $permission->employee_name }}</td>
                    <td class="px-4 py-2 border-b">{{ $permission->destination }}</td>
                    <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($permission->from_time)->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($permission->to_time)->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center px-4 py-6 text-gray-500">Ruxsatnomalar topilmadi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Sahifalash linklari --}}
    <div class="mt-4">
        {{ $permissions->links() }}
    </div>
</div>
@endsection
