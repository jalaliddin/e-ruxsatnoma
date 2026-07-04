<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Kirish | E-Ruxsatnoma</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 px-4">
    <div class="w-full max-w-sm bg-white/95 backdrop-blur p-8 rounded-2xl shadow-2xl animate-fade-in">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-20 mx-auto mb-4">
        <div class="text-center mb-6">
            <div class="text-lg font-bold text-indigo-700 tracking-tight">"Urganchtransgaz" UK</div>
            <div class="text-sm text-gray-500">E-Ruxsatnomalar Byurosi</div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="Email"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input id="password" type="password" name="password" required placeholder="Parol"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 rounded-xl transition-colors duration-150 shadow-md hover:shadow-lg">
                Kirish
            </button>
        </form>
    </div>
</body>
</html>
