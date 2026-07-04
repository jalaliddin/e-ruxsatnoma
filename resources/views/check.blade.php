@extends('layouts.app')

@section('title', 'Ruxsatnoma tekshirish')

@section('content')
<div class="flex justify-center">
    <form method="POST" action="{{ route('permission.check') }}" class="space-y-4 w-full max-w-md bg-white p-6 rounded-xl shadow-sm">
        @csrf

        <div>
            <label class="block text-gray-700 mb-1">4 xonali kodni kiriting:</label>
            <input type="text" name="code" required maxlength="4" class="w-full border px-3 py-2 rounded-lg">
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg w-full transition">
            Tekshirish
        </button>

        @if (session('result'))
            <div class="mt-4 p-4 rounded-lg text-center {{ str_contains(session('result'), '✅') ? 'bg-green-50 border border-green-300' : 'bg-red-50 border border-red-300' }}">
                {{ session('result') }}
            </div>
        @endif
    </form>
</div>
@endsection
