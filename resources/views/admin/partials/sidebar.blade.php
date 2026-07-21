<aside class="w-64 bg-gray-900 text-white p-6 flex-shrink-0 flex flex-col min-h-screen">
    <h2 class="text-2xl font-bold text-blue-400 mb-1">PortRisk Integra</h2>
    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-8">Admin Panel</p>
    <nav class="space-y-1 flex-1">
        <a href="/" class="block py-2 px-4 hover:bg-gray-800 rounded-lg text-sm transition text-gray-300">
            ↩ Kembali ke Dashboard
        </a>
        <div class="pt-4 pb-1">
            <p class="text-xs text-gray-500 uppercase tracking-wider px-4 mb-1">Kelola Data</p>
        </div>
        <a href="{{ route('admin.dashboard') }}"
           class="block py-2 px-4 {{ ($active ?? '') === 'overview' ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 text-gray-300' }} rounded-lg text-sm transition">
            🏠 Overview
        </a>
        <a href="{{ route('users.index') }}"
           class="block py-2 px-4 {{ ($active ?? '') === 'users' ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 text-gray-300' }} rounded-lg text-sm transition">
            👥 Kelola User
        </a>
        <a href="{{ route('ports.index') }}"
           class="block py-2 px-4 {{ ($active ?? '') === 'ports' ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 text-gray-300' }} rounded-lg text-sm transition">
            ⚓ Kelola Pelabuhan
        </a>
        <a href="{{ route('articles.index') }}"
           class="block py-2 px-4 {{ ($active ?? '') === 'articles' ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 text-gray-300' }} rounded-lg text-sm transition">
            📰 Kelola Artikel
        </a>
    </nav>
    <div class="mt-6 border-t border-gray-700 pt-4">
        <p class="text-xs text-gray-400 mb-0.5">Login sebagai:</p>
        <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
        <p class="text-xs text-blue-400 mb-3">{{ Auth::user()->role }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-gray-400 hover:text-rose-400 transition">Logout →</button>
        </form>
    </div>
</aside>
