<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PortRisk Integra | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">
    

   <aside class="w-64 bg-gray-900 text-white p-6">
    
    <h2 class="text-2xl font-bold text-blue-400 mb-10">PortRisk Integra</h2>
    <nav class="space-y-4">
        <!-- Menggunakan route() adalah cara terbaik di Laravel -->
        <a href="/" class="block py-2 px-4 {{ request()->is('/') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Dashboard</a>
        
        <a href="/perbandingan" class="block py-2 px-4 {{ request()->is('perbandingan') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Perbandingan Negara</a>
        
        <a href="/pelabuhan" class="block py-2 px-4 {{ request()->is('pelabuhan') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Data Pelabuhan</a>
        <a href="#" class="block py-2 px-4 hover:bg-gray-800 rounded-lg">Admin Panel</a>
    </nav>
</aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8">
        <!-- HEADER FORM -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mb-8">
            <h1 class="text-xl font-bold text-gray-800 mb-4">Analisis Risiko Logistik</h1>
            
            <form action="/track" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Pilih Negara</label>
                    <input type="text" name="name" placeholder="Nama Negara..." class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Kode Negara (ISO)</label>
                    <input type="text" name="code" placeholder="Contoh: ID" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 rounded-lg transition">
                    Cek Risiko Sekarang
                </button>
            </form>
        </div>

        <!-- AREA HASIL/PETA (Nantinya di sini) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-64 flex items-center justify-center text-gray-400">
                Statistik & Peta akan muncul di sini setelah klik "Cek Risiko"
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-64 flex items-center justify-center text-gray-400">
                Grafik Risiko / Tren
            </div>
        </div>
    </main>

</body>
</html>