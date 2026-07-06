<!DOCTYPE html>
<html>
<head>
    <title>Global Supply Chain Risk - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header & Navigasi -->
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold">Risk Profile: {{ $data['country'] }}</h1>
            <a href="/" class="text-blue-500 hover:underline">← Kembali ke Home</a>
        </div>

        <!-- Bagian Informasi Negara & Cuaca (Baru) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ $data['country_info']['flag_png'] ?? '#' }}" alt="Flag" class="w-10 h-10 rounded-full border">
                    <h2 class="text-xl font-bold text-gray-800">{{ $data['country'] }}</h2>
                </div>
                <p class="text-gray-600">Populasi: <span class="font-semibold">{{ $data['country_info']['population'] ?? 'N/A' }}</span></p>
            </div>

            @if(isset($data['weather']))
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-semibold text-sky-500 uppercase">Cuaca di Ibu Kota</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $data['weather']['temperature'] }}°C</p>
                <p class="text-gray-600">Kecepatan Angin: {{ $data['weather']['windspeed'] }} km/h</p>
            </div>
            @endif
        </div>
        
        <!-- Indikator Ekonomi -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-500">GDP Growth</h2>
                <p class="text-2xl font-bold">{{ $data['economic_indicators']['gdp_growth']['value'] ?? 'N/A' }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-500">Inflation</h2>
                <p class="text-2xl font-bold">{{ $data['economic_indicators']['inflation_rate']['value'] ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Berita Logistik -->
      <h2 class="text-xl font-bold mb-4">Latest Logistics News</h2>
        @foreach($data['recent_supply_chain_news']['articles'] ?? [] as $article)
            <div class="bg-white p-5 mb-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <h3 class="font-bold text-lg">{{ $article['title'] }}</h3>
                <p class="text-sm text-gray-600">{{ $article['description'] ?? 'No description.' }}</p>
                <a href="{{ $article['url'] }}" target="_blank" class="text-blue-500 text-sm hover:underline">Baca Selengkapnya &rarr;</a>
            </div>
        @endforeach

        <!-- Status Footer -->
        <div class="mt-8 text-center">
            <span class="text-sm text-gray-500 font-mono">
                Status: <span class="text-green-600 font-bold">ONLINE</span> | 
                Update: {{ $data['risk_profile_generated_at'] }}
            </span>
        </div>
    </div>
</body>
</html>