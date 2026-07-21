<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelabuhan | Admin PortRisk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">

    @include('admin.partials.sidebar', ['active' => 'ports'])

    <main class="flex-1 p-8 overflow-auto">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-5 bg-emerald-50 border border-emerald-300 text-emerald-800 px-5 py-3 rounded-xl text-sm font-medium">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-5 bg-rose-50 border border-rose-300 text-rose-800 px-5 py-3 rounded-xl text-sm font-medium">⚠️ {{ session('error') }}</div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Kelola Dataset Pelabuhan</h1>
                    <p class="text-gray-500 text-sm mt-0.5">Perubahan di sini langsung tercermin di halaman publik "Data Pelabuhan".</p>
                </div>
                <a href="{{ route('ports.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl shadow transition text-sm">
                    + Tambah Pelabuhan
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Pelabuhan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Negara</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Koordinat</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($ports as $port)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 text-sm">{{ $port->name }}</td>
                                <td class="px-6 py-4 text-gray-500 text-sm">{{ $port->country->name ?? 'N/A' }} <span class="text-gray-400 text-xs">({{ $port->country->country_code ?? '-' }})</span></td>
                                <td class="px-6 py-4 text-gray-400 text-xs font-mono">{{ $port->latitude }}, {{ $port->longitude }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($port->type)
                                        <span class="px-2 py-0.5 bg-teal-50 text-teal-700 rounded-full text-xs font-medium">{{ $port->type }}</span>
                                    @else
                                        <span class="text-gray-300 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <a href="{{ route('ports.edit', $port) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                    <form action="{{ route('ports.destroy', $port) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus pelabuhan {{ $port->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-900 font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada data pelabuhan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $ports->links() }}
                </div>
            </div>
        </div>
    </main>
</body>
</html>
