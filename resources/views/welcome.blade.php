@extends('layouts.app')

@section('title', 'Bosh sahifa')

@section('content')
<div class="flex flex-col justify-center items-center min-h-[70vh] text-center space-y-8">
    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900">
        "Urganchtransgaz" UK<br>
        <span class="text-blue-700">E-Ruxsatnomalar Byurosi</span>
    </h1>

    <p class="text-gray-600 text-lg max-w-md">
        Xodimlar uchun ruxsatnoma yaratish va tekshirish tizimiga xush kelibsiz.
    </p>

    <div class="flex flex-wrap justify-center gap-6 mt-6">
        <a href="{{ route('permission.create') }}"
           class="px-6 py-3 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded shadow-md transition">
            ➕ Ruxsatnoma yaratish
        </a>

        <a href="{{ route('permission.checkForm') }}"
           class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded shadow-md transition">
            🔍 Ruxsatnomani tekshirish
        </a>

        <a href="{{ route('permission.index') }}"
           class="px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white font-semibold rounded shadow-md transition">
            📋 Oxirgi ruxsatnomalar
        </a>
    </div>
</div>
@endsection
