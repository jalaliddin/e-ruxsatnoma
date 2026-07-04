<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Eshik | E-Ruxsatnoma</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 px-4">
    <div class="w-full max-w-sm bg-white/95 backdrop-blur p-8 rounded-2xl shadow-2xl animate-fade-in text-center space-y-4">
        <div class="text-4xl mb-2">🛡️</div>

        <div class="rounded-xl px-4 py-3 text-xs bg-amber-50 text-amber-800 border border-amber-300 text-left">
            ⚠️ <strong>Eslatma:</strong> Turniket faqat korxona hududidagi Wi-Fi/internet orqali ishlaydi.
            Iltimos, turniket oldida turgan holda tugmani bosing.
        </div>

        @if (session('result'))
            <div class="rounded-xl px-4 py-3 text-sm {{ str_contains(session('result'), '✅') ? 'bg-green-50 text-green-800 border border-green-300' : 'bg-red-50 text-red-800 border border-red-300' }}">
                {{ session('result') }}
            </div>
        @endif

        <form method="POST" action="{{ route('door.open', $code) }}">
            @csrf
            <input type="hidden" name="direction" value="kirish">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-4 rounded-xl transition-colors duration-150 shadow-md hover:shadow-lg text-lg">
                🚪 Kirish
            </button>
        </form>

        <form method="POST" action="{{ route('door.open', $code) }}">
            @csrf
            <input type="hidden" name="direction" value="chiqish">
            <button type="submit" class="w-full bg-slate-700 hover:bg-slate-600 text-white font-semibold py-4 rounded-xl transition-colors duration-150 shadow-md hover:shadow-lg text-lg">
                🚪 Chiqish
            </button>
        </form>
    </div>
</body>
</html>
