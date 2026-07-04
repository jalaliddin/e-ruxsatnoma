<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Kirish | E-Ruxsatnoma</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm bg-white p-8 rounded-2xl shadow-lg">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 mx-auto mb-4">
        <div class="text-center mb-6">
            <div class="text-lg font-bold text-blue-600">"Urganchtransgaz" UK</div>
            <div class="text-sm text-gray-500">E-Ruxsatnomalar Byurosi</div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="Email"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input id="password" type="password" name="password" required placeholder="Parol"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                Kirish
            </button>
        </form>
    </div>
</body>
</html>
