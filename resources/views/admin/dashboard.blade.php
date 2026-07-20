<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Control Panel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Stats Card Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- User Card -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-2xl shadow-lg p-6 relative overflow-hidden transition transform hover:-translate-y-1 hover:shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider opacity-80">Total Users</p>
                            <h3 class="text-3xl font-extrabold mt-1">{{ $userCount }}</h3>
                        </div>
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('users.index') }}" class="text-sm font-medium hover:underline flex items-center gap-1">
                            Kelola User &rarr;
                        </a>
                    </div>
                </div>

                <!-- Port Card -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl shadow-lg p-6 relative overflow-hidden transition transform hover:-translate-y-1 hover:shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider opacity-80">Dataset Pelabuhan</p>
                            <h3 class="text-3xl font-extrabold mt-1">{{ $portCount }}</h3>
                        </div>
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('ports.index') }}" class="text-sm font-medium hover:underline flex items-center gap-1">
                            Kelola Dataset &rarr;
                        </a>
                    </div>
                </div>

                <!-- Articles Card -->
                <div class="bg-gradient-to-r from-orange-500 to-pink-600 text-white rounded-2xl shadow-lg p-6 relative overflow-hidden transition transform hover:-translate-y-1 hover:shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider opacity-80">Artikel Analisis</p>
                            <h3 class="text-3xl font-extrabold mt-1">{{ $articleCount }}</h3>
                        </div>
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2-2v3a2 2 0 002 2V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('articles.index') }}" class="text-sm font-medium hover:underline flex items-center gap-1">
                            Kelola Artikel &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dashboard Welcome message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-150">
                <div class="p-8 text-gray-800">
                    <h4 class="text-xl font-bold text-indigo-900 mb-2">Selamat datang di Panel Otoritas PortRisk Integra</h4>
                    <p class="text-gray-600 leading-relaxed">Gunakan menu di atas atau sidebar/navigasi untuk mengelola parameter sistem, data pelabuhan global, pengguna platform, dan memublikasikan artikel analisis risiko secara berkala.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
