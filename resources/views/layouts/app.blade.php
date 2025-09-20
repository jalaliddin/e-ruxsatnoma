<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Jaloliddin Saidov')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
                /* Oddiy hover effektlar uchun */
        a.menu-link {
            display: block;
            padding: 8px 0;
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        a.menu-link:hover {
            color: #3b82f6; /* Tailwind blue-500 */
        }
        button.logout-btn {
            background: none;
            border: none;
            color: #f87171; /* Tailwind red-400 */
            cursor: pointer;
            font-weight: 500;
            padding: 8px 0;
            text-align: left;
            width: 100%;
        }
        button.logout-btn:hover {
            color: #ef4444; /* Tailwind red-500 */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-black text-white flex flex-col">
        <div class="text-2xl font-bold p-6 border-b border-gray-800">
            🛡️ E-Ruxsatnoma
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('welcome') }}"
               class="block px-4 py-2 rounded hover:bg-gray-800 {{ request()->is('welcome') ? 'bg-gray-800' : '' }}">
                🏠 Bosh sahifa
            </a>
            <a href="{{ route('permission.create') }}"
               class="block px-4 py-2 rounded hover:bg-gray-800 {{ request()->is('ruxsatnoma') ? 'bg-gray-800' : '' }}">
                ➕ Ruxsatnoma
            </a>
            <a href="{{ route('permission.index') }}"
            class="block px-4 py-2 rounded hover:bg-gray-800 {{ request()->is('ruxsatnomalar') ? 'bg-gray-800' : '' }}">
                📋 Ruxsatnomalar
            </a>
            <a href="{{ route('permission.checkForm') }}"
               class="block px-4 py-2 rounded hover:bg-gray-800 {{ request()->is('tekshir') ? 'bg-gray-800' : '' }}">
                🔍 Tekshirish
            </a>
        </nav>
        <div class="p-4 border-t border-gray-800 text-sm text-center">
            &copy; {{ date('Y') }} Panel
        </div>
        @guest
                    <a href="{{ route('login') }}" class="menu-link">🔐 Kirish</a>
                @endguest

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">🚪 Chiqish</button>
                    </form>
                @endauth
            </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h1 class="text-2xl font-semibold mb-4">@yield('title')</h1>
        @yield('content')
    </div>
</div>

</body>
</html>
