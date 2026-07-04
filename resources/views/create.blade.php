@extends('layouts.app')

@section('title', 'Qo\'lda ruxsatnoma yaratish')

@section('content')
<div class="flex justify-center">
    <form method="POST" action="{{ route('permission.store') }}" class="space-y-4 w-full max-w-lg bg-white p-6 rounded-xl shadow-sm">
        @csrf

        <p class="text-sm text-gray-500">
            Bu forma faqat qo'lda, favqulodda hollar uchun. Odatiy oqimda xodim so'rovni Telegram bot orqali yuboradi.
        </p>

        <div>
            <label class="block text-gray-700 mb-1">Kimdan (yaratuvchi):</label>
            <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full border px-3 py-2 rounded-lg bg-gray-100">
        </div>

        <div>
            <label class="block text-gray-700 mb-1">Kimga (xodim):</label>
            <input type="text" name="employee_name" required class="w-full border px-3 py-2 rounded-lg">
        </div>

        <div>
            <label class="block text-gray-700 mb-1">Qayerga / maqsad:</label>
            <input type="text" name="destination" required class="w-full border px-3 py-2 rounded-lg">
        </div>

        <div>
            <label class="block text-gray-700 mb-1">Qachondan:</label>
            <input type="datetime-local" name="from_time" required class="w-full border px-3 py-2 rounded-lg">
        </div>

        <div>
            <label class="block text-gray-700 mb-1">Qachongacha:</label>
            <input type="datetime-local" name="to_time" required class="w-full border px-3 py-2 rounded-lg">
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg w-full transition">
            Saqlash
        </button>

        @if (isset($code))
            <div class="mt-4 p-4 bg-green-50 border border-green-300 rounded-lg text-center">
                ✅ Ruxsatnoma kodi: <strong>{{ $code }}</strong>
            </div>
        @endif
    </form>
</div>
@endsection
