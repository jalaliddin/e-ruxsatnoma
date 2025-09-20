@extends('layouts.app')

@section('title', 'Ruxsatnoma tekshirish')

@section('content')
<div class="flex justify-center items-center min-h-[70vh]">
    <form method="POST" action="{{ route('permission.check') }}" class="space-y-4 w-full max-w-md bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label class="block text-gray-700">4 xonali kodni kiriting:</label>
            <input type="text" name="code" required maxlength="4" class="w-full border px-3 py-2 rounded">
        </div>

        <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded w-full">
            Tekshirish
        </button>

        @if (session('result'))
            <div class="mt-4 p-4 {{ str_contains(session('result'), '✅') ? 'bg-green-100 border border-green-400' : 'bg-red-100 border border-red-400' }} rounded text-center">
                {{ session('result') }}
            </div>
        @endif
    </form>
</div>
@endsection
