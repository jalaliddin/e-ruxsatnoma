<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel') | E-Ruxsatnoma</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

@php
    $navItem = function (string $route) {
        $active = request()->routeIs($route);
        $base = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150';
        $state = $active
            ? 'bg-white/10 text-white border-l-4 border-indigo-400 pl-2 font-medium shadow-inner'
            : 'text-slate-300 hover:bg-white/5 hover:text-white border-l-4 border-transparent pl-2';

        return "{$base} {$state}";
    };
@endphp

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 shrink-0 bg-gradient-to-b from-slate-900 to-slate-800 text-white flex flex-col">
        <div class="text-lg font-bold p-6 border-b border-white/10 flex items-center gap-2 tracking-tight">
            <span class="text-2xl">🛡️</span>
            <span>E-Ruxsatnoma</span>
        </div>
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('home') }}" class="{{ $navItem('home') }}">🏠 <span>Bosh sahifa</span></a>
            <a href="{{ route('permission.index') }}" class="{{ $navItem('permission.index') }}">📋 <span>Ruxsatnoma so'rovlari</span></a>
            <a href="{{ route('permission.create') }}" class="{{ $navItem('permission.create') }}">➕ <span>Qo'lda ruxsatnoma</span></a>
            <a href="{{ route('permission.checkForm') }}" class="{{ $navItem('permission.checkForm') }}">🔍 <span>Kodni tekshirish</span></a>

            @auth
                <a href="{{ route('reports.index') }}" class="{{ $navItem('reports.*') }}">📊 <span>Hisobotlar</span></a>

                @if (auth()->user()->isAdmin() || auth()->user()->isHr())
                    <div class="pt-4 mt-4 border-t border-white/10 text-[11px] uppercase tracking-wider text-slate-400 px-3">Boshqaruv</div>
                    <a href="{{ route('employees.index') }}" class="{{ $navItem('employees.*') }}">👷 <span>Xodimlar</span></a>
                @endif

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('categories.index') }}" class="{{ $navItem('categories.*') }}">🗂️ <span>Kategoriyalar</span></a>
                    <a href="{{ route('departments.index') }}" class="{{ $navItem('departments.*') }}">🏢 <span>Bo'limlar</span></a>
                    <a href="{{ route('admin.users.index') }}" class="{{ $navItem('admin.users.*') }}">🧑‍💼 <span>Foydalanuvchilar</span></a>
                @endif
            @endauth
        </nav>
        <div class="p-4 border-t border-white/10 text-xs text-center text-slate-500">
            &copy; {{ date('Y') }} AKT bo'limi
        </div>
        <div class="p-4 border-t border-white/10 text-sm">
            @guest
                <a href="{{ route('login') }}" class="block text-center py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 transition-colors duration-150 font-medium">🔐 Kirish</a>
            @endguest

            @auth
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-xs font-bold shrink-0">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-slate-200 truncate text-sm flex-1">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-400 hover:text-red-300 transition-colors duration-150" title="Chiqish">🚪</button>
                    </form>
                </div>
            @endauth
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white/80 backdrop-blur border-b px-6 py-4 shadow-sm sticky top-0 z-10">
            <h1 class="text-xl font-semibold text-gray-900 tracking-tight">@yield('title')</h1>
        </header>

        <main class="flex-1 p-6">
            @if (session('success'))
                <div class="animate-fade-in mb-4 rounded-xl border border-green-300 bg-green-50 text-green-800 px-4 py-3 flex items-center gap-2">
                    <span>✅</span> <span>{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="animate-fade-in mb-4 rounded-xl border border-red-300 bg-red-50 text-red-800 px-4 py-3">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
