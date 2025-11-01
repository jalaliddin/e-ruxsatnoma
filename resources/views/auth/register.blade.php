@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-20 bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-center">Ro‘yxatdan o‘tish</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Ism --}}
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-semibold mb-1">Ism va Familiya</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name')
                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-semibold mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')
                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        {{-- Parol --}}
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-semibold mb-1">Parol</label>
            <input id="password" type="password" name="password" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('password')
                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        {{-- Parolni tasdiqlash --}}
        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 font-semibold mb-1">Parolni tasdiqlash</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <input class="form-check-input mb-6" type="checkbox" name="special_user" id="special_user">
            <label class="form-check-label" for="special_user">
                Special User sifatida ro'yxatdan o'tish
            </label>
        <button type="submit"
            class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition">
            Ro‘yxatdan o‘tish
        </button>
    </form>
</div>
@endsection
