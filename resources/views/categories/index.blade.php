@extends('layouts.app')

@section('title', 'Ruxsatnoma kategoriyalari')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">➕ Yangi kategoriya</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left px-4 py-2">#</th>
                        <th class="text-left px-4 py-2">Nomi</th>
                        <th class="text-left px-4 py-2">Holati</th>
                        <th class="text-left px-4 py-2">Amallar</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $category->id }}</td>
                            <td class="px-4 py-2">{{ $category->name }}</td>
                            <td class="px-4 py-2">
                                @if ($category->is_active)
                                    <span class="text-green-600">Faol</span>
                                @else
                                    <span class="text-gray-400">Faol emas</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:underline">Tahrirlash</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Ishonchingiz komilmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">O'chirish</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center px-4 py-6 text-gray-500">Kategoriyalar topilmadi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
