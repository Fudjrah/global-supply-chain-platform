<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Global Supply Chain Risk - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header & Navigasi -->
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold">Risk Profile: {{ $data['country'] }}</h1>
            <a href="/" class="text-blue-500 hover:underline">← Kembali ke Home</a>
        </div>

        <!-- Bagian Informasi Negara & Cuaca -->
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

        <!-- Grafik -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 w-full">
            <h2 class="text-lg font-bold mb-4 text-gray-700">Tren GDP Growth (%)</h2>
            <div class="relative w-full h-[300px]"><canvas id="gdpChart"></canvas></div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 w-full">
            <h2 class="text-lg font-bold mb-4 text-gray-700">Tren Inflasi (%)</h2>
            <div class="relative w-full h-[300px]"><canvas id="inflationChart"></canvas></div>
        </div>

        <!-- Peta -->
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Lokasi Negara</h2>
            <div id="map" class="h-64 rounded-xl shadow-sm border border-gray-100"></div>
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

        <!-- Risk Scoring -->
        <div class="mb-8 p-6 rounded-2xl text-white shadow-lg {{ $data['risk_score'] > 50 ? 'bg-red-500' : ($data['risk_score'] > 25 ? 'bg-yellow-500' : 'bg-green-500') }}">
            <h2 class="text-lg opacity-80">Risk Level: {{ $data['risk_level'] }}</h2>
            <p class="text-5xl font-bold">{{ $data['risk_score'] }} / 100</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <h3 class="font-bold text-gray-700">Analisis Sentimen Berita Logistik:</h3>
            <div class="flex gap-4 mt-2">
                <span class="text-green-600 font-bold">Positif: {{ $data['sentiment']['positive'] }}</span>
                <span class="text-red-600 font-bold">Negatif: {{ $data['sentiment']['negative'] }}</span>
                <span class="font-semibold text-gray-600">Hasil: {{ $data['sentiment']['label'] }}</span>
            </div>
        </div>

        <div class="mt-8 text-center text-sm text-gray-500 font-mono">
            Status: <span class="text-green-600 font-bold">ONLINE</span> | Update: {{ $data['risk_profile_generated_at'] }}
        </div>
    </div>

    <!-- Hidden Data -->
    <div id="country-data" data-lat="{{ $data['lat'] ?? 0 }}" data-lon="{{ $data['lon'] ?? 0 }}" data-name="{{ $data['country'] }}" style="display:none;"></div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Chart Initializations
            const chartOptions = { responsive: true, maintainAspectRatio: false };
            
            const gdpChart = new Chart(document.getElementById('gdpChart').getContext('2d'), {
                type: 'line', data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'], datasets: [{ label: 'GDP Growth', data: [], borderColor: '#3b82f6', tension: 0.3 }] }, options: chartOptions
            });

            const inflationChart = new Chart(document.getElementById('inflationChart').getContext('2d'), {
                type: 'line', data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'], datasets: [{ label: 'Inflasi', data: [], borderColor: '#ef4444', tension: 0.3 }] }, options: chartOptions
            });

            fetch('/api/gdp-data').then(res => res.json()).then(data => { gdpChart.data.datasets[0].data = data.history; gdpChart.update(); });
            fetch('/api/inflation-data').then(res => res.json()).then(data => { inflationChart.data.datasets[0].data = data.history; inflationChart.update(); });

            // 2. Peta (Hanya jalan jika ID map ada)
            const mapElement = document.getElementById('map');
            const dataElement = document.getElementById('country-data');
            if (mapElement && dataElement) {
                const lat = parseFloat(dataElement.getAttribute('data-lat'));
                const lon = parseFloat(dataElement.getAttribute('data-lon'));
                const name = dataElement.getAttribute('data-name');
                const map = L.map('map').setView([lat, lon], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                L.marker([lat, lon]).addTo(map).bindPopup(name).openPopup();
            }

            // 3. Compare Logic (Hanya jalan jika elemen ID ada)
            const c1 = document.getElementById('country1');
            const c2 = document.getElementById('country2');
            if (c1 && c2) {
                fetch('/api/countries').then(res => res.json()).then(countries => {
                    [c1, c2].forEach(select => {
                        countries.forEach(country => {
                            let opt = document.createElement('option');
                            opt.value = country; opt.text = country;
                            select.appendChild(opt);
                        });
                    });
                });
            }
        });
    </script>
</body>
</html>