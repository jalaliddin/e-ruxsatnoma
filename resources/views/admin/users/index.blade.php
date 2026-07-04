@extends('layouts.app')

@section('title', 'Foydalanuvchilar')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">➕ Yangi foydalanuvchi</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left px-4 py-2">#</th>
                        <th class="text-left px-4 py-2">Ism</th>
                        <th class="text-left px-4 py-2">Email</th>
                        <th class="text-left px-4 py-2">Rol</th>
                        <th class="text-left px-4 py-2">Telegram</th>
                        <th class="text-left px-4 py-2">Amallar</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $user->id }}</td>
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2 capitalize">{{ $user->role }}</td>
                            <td class="px-4 py-2">
                                @if ($user->telegram_chat_id)
                                    <span class="text-green-600">✅ ulangan</span>
                                @else
                                    <form action="{{ route('admin.users.telegram-link', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:underline">Havola yaratish</button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">Tahrirlash</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Ishonchingiz komilmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">O'chirish</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center px-4 py-6 text-gray-500">Foydalanuvchilar topilmadi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $users->links() }}
        </div>
    </div>
@endsection
