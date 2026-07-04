@extends('layouts.app')

@section('title', 'Xodimlar')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">➕ Yangi xodim</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left px-4 py-2">#</th>
                        <th class="text-left px-4 py-2">F.I.Sh</th>
                        <th class="text-left px-4 py-2">Telefon</th>
                        <th class="text-left px-4 py-2">Bo'lim</th>
                        <th class="text-left px-4 py-2">Telegram</th>
                        <th class="text-left px-4 py-2">Amallar</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $employee->id }}</td>
                            <td class="px-4 py-2">{{ $employee->full_name }}</td>
                            <td class="px-4 py-2">{{ $employee->phone }}</td>
                            <td class="px-4 py-2">{{ $employee->department ?? '—' }}</td>
                            <td class="px-4 py-2">
                                @if ($employee->telegram_chat_id)
                                    <span class="text-green-600">✅ ulangan</span>
                                @else
                                    <span class="text-gray-400">ulanmagan</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('employees.edit', $employee) }}" class="text-blue-600 hover:underline">Tahrirlash</a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Ishonchingiz komilmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">O'chirish</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center px-4 py-6 text-gray-500">Xodimlar topilmadi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $employees->links() }}
        </div>
    </div>
@endsection
