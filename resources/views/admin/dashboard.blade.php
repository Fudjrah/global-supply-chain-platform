<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | PortRisk Integra</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white p-6 flex-shrink-0 flex flex-col">
        <h2 class="text-2xl font-bold text-blue-400 mb-2">PortRisk Integra</h2>
        <p class="text-xs text-gray-500 mb-8 font-semibold uppercase tracking-wider">Admin Panel</p>
        <nav class="space-y-1 flex-1">
            <a href="/" class="block py-2 px-4 hover:bg-gray-800 rounded-lg text-sm transition">↩ Kembali ke Dashboard</a>
            <div class="pt-4 pb-1">
                <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-2">Kelola Data</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg text-sm transition">
                🏠 Overview
            </a>
            <a href="{{ route('users.index') }}" class="block py-2 px-4 {{ request()->segment(2) === 'users' ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg text-sm transition">
                👥 Kelola User
            </a>
            <a href="{{ route('ports.index') }}" class="block py-2 px-4 {{ request()->segment(2) === 'ports' ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg text-sm transition">
                ⚓ Kelola Pelabuhan
            </a>
            <a href="{{ route('articles.index') }}" class="block py-2 px-4 {{ request()->segment(2) === 'articles' ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg text-sm transition">
                📰 Kelola Artikel
            </a>
        </nav>
        <div class="mt-6 border-t border-gray-700 pt-4">
            <p class="text-xs text-gray-400 mb-1">Login sebagai:</p>
            <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
            <p class="text-xs text-blue-400">{{ Auth::user()->role }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="text-xs text-gray-400 hover:text-rose-400 transition">Logout →</button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8 overflow-auto">
        <div class="max-w-7xl mx-auto">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-300 text-emerald-800 px-5 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-300 text-rose-800 px-5 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900">Admin Control Panel</h1>
                <p class="text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}. Kelola semua data platform dari sini.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Users Card -->
                <a href="{{ route('users.index') }}" class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl shadow-lg p-6 flex items-center justify-between hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
                    <div>
                        <p class="text-sm font-semibold opacity-80 uppercase tracking-wider">Total Users</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ $userCount }}</h3>
                        <p class="text-xs opacity-70 mt-2">Kelola User →</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </a>

                <!-- Ports Card -->
                <a href="{{ route('ports.index') }}" class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-2xl shadow-lg p-6 flex items-center justify-between hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
                    <div>
                        <p class="text-sm font-semibold opacity-80 uppercase tracking-wider">Dataset Pelabuhan</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ $portCount }}</h3>
                        <p class="text-xs opacity-70 mt-2">Kelola Dataset →</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                </a>

                <!-- Articles Card -->
                <a href="{{ route('articles.index') }}" class="bg-gradient-to-br from-orange-500 to-pink-600 text-white rounded-2xl shadow-lg p-6 flex items-center justify-between hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
                    <div>
                        <p class="text-sm font-semibold opacity-80 uppercase tracking-wider">Artikel Analisis</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ $articleCount }}</h3>
                        <p class="text-xs opacity-70 mt-2">Kelola Artikel →</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                </a>

                <!-- Countries Card -->
                <div class="bg-gradient-to-br from-fuchsia-500 to-purple-600 text-white rounded-2xl shadow-lg p-6 flex items-center justify-between hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
                    <div>
                        <p class="text-sm font-semibold opacity-80 uppercase tracking-wider">Negara Dapat Diakses</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ $countryCount }}</h3>
                        <p class="text-[10px] opacity-75 mt-2">Didukung oleh REST Countries API</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5A2.5 2.5 0 0019 9.5V8a2 2 0 00-2-2h-3.172a2 2 0 01-1.414-.586l-.828-.828A2 2 0 0010.172 4H8.2c-.394 0-.776.116-1.1.332L5 6m12 9v4a2 2 0 01-2 2H5.2c-.394 0-.776-.116-1.1-.332L3 17" /></svg>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h4 class="text-lg font-bold text-gray-800 mb-2">Panduan Admin Panel</h4>
                <p class="text-gray-500 text-sm leading-relaxed">Gunakan menu di sidebar untuk berpindah antar modul. Perubahan data pelabuhan akan <strong>langsung tercermin</strong> di halaman publik "Data Pelabuhan". Hanya akun dengan role <span class="font-mono bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded text-xs">admin</span> yang dapat mengakses panel ini.</p>
            </div>
        </div>
    </main>

</body>
</html>
