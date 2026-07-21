<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Artikel | Admin PortRisk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">

    @include('admin.partials.sidebar', ['active' => 'articles'])

    <main class="flex-1 p-8 overflow-auto">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-5 bg-emerald-50 border border-emerald-300 text-emerald-800 px-5 py-3 rounded-xl text-sm font-medium">✅ {{ session('success') }}</div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Kelola Artikel Analisis</h1>
                    <p class="text-gray-500 text-sm mt-0.5">Publikasikan dan kelola artikel analisis risiko logistik.</p>
                </div>
                <a href="{{ route('articles.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-5 rounded-xl shadow transition text-sm">
                    + Tambah Artikel
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($articles as $article)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm">
                                    <p class="font-semibold text-gray-900">{{ Str::limit($article->title, 60) }}</p>
                                    @if($article->url)
                                        <a href="{{ $article->url }}" target="_blank" class="text-xs text-blue-500 hover:underline">Lihat URL ↗</a>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($article->category)
                                        <span class="px-2 py-0.5 bg-orange-50 text-orange-700 rounded-full text-xs font-medium">{{ $article->category }}</span>
                                    @else
                                        <span class="text-gray-300 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $article->author ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $article->published_at ? $article->published_at->format('d M Y') : '-' }}</td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <a href="{{ route('articles.edit', $article) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                    <form action="{{ route('articles.destroy', $article) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus artikel ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-900 font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada artikel yang dipublikasikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $articles->links() }}
                </div>
            </div>
        </div>
    </main>
</body>
</html>
