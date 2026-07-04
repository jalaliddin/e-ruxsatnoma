<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel') | E-Ruxsatnoma</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans text-gray-800">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 shrink-0 bg-gray-900 text-white flex flex-col">
        <div class="text-xl font-bold p-6 border-b border-gray-800 flex items-center gap-2">
            <span>🛡️</span> <span>E-Ruxsatnoma</span>
        </div>
        <nav class="flex-1 px-3 py-6 space-y-1 text-sm">
            <a href="{{ route('home') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('home') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                🏠 Bosh sahifa
            </a>
            <a href="{{ route('permission.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('permission.index') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                📋 Ruxsatnoma so'rovlari
            </a>
            <a href="{{ route('permission.create') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('permission.create') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                ➕ Qo'lda ruxsatnoma
            </a>
            <a href="{{ route('permission.checkForm') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('permission.checkForm') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                🔍 Kodni tekshirish
            </a>

            @auth
                <a href="{{ route('reports.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('reports.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    📊 Hisobotlar
                </a>

                @if (auth()->user()->isAdmin() || auth()->user()->isHr())
                    <div class="pt-4 mt-4 border-t border-gray-800 text-xs uppercase text-gray-500 px-3">Boshqaruv</div>
                    <a href="{{ route('employees.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('employees.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                        👷 Xodimlar
                    </a>
                @endif

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('categories.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('categories.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                        🗂️ Kategoriyalar
                    </a>
                    <a href="{{ route('departments.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('departments.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                        🏢 Bo'limlar
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                        🧑‍💼 Foydalanuvchilar
                    </a>
                @endif
            @endauth
        </nav>
        <div class="p-4 border-t border-gray-800 text-xs text-center text-gray-400">
            &copy; {{ date('Y') }} AKT bo'limi
        </div>
        <div class="p-4 border-t border-gray-800 text-sm">
            @guest
                <a href="{{ route('login') }}" class="block text-center py-2 rounded-lg bg-blue-600 hover:bg-blue-700 transition">🔐 Kirish</a>
            @endguest

            @auth
                <div class="flex items-center justify-between gap-2">
                    <span class="text-gray-300 truncate">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-400 hover:text-red-300 transition">🚪 Chiqish</button>
                    </form>
                </div>
            @endauth
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b px-6 py-4 shadow-sm">
            <h1 class="text-xl font-semibold text-gray-800">@yield('title')</h1>
        </header>

        <main class="flex-1 p-6">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-300 bg-green-50 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-300 bg-red-50 text-red-800 px-4 py-3">
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
