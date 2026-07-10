<!DOCTYPE html>
<html>
<head>
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

<!-- Grafik GDP -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 w-full">
    <h2 class="text-lg font-bold mb-4 text-gray-700">Tren GDP Growth (%)</h2>
  <div class="relative w-full h-[300px]">
    <canvas id="gdpChart"></canvas>
</div>
</div>

<!-- Grafik Inflasi -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 w-full">
    <h2 class="text-lg font-bold mb-4 text-gray-700">Tren Inflasi (%)</h2>
  <div class="relative w-full h-[300px]">
    <canvas id="inflationChart"></canvas>
</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Konfigurasi umum agar tidak perlu tulis ulang berkali-kali
        const chartOptions = { responsive: true, maintainAspectRatio: false };

        // 1. Inisialisasi Chart GDP
        const ctxGdp = document.getElementById('gdpChart').getContext('2d');
        const gdpChart = new Chart(ctxGdp, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{ label: 'GDP Growth', data: [], borderColor: '#3b82f6', tension: 0.3 }]
            },
            options: chartOptions
        });

        // 2. Inisialisasi Chart Inflasi
        const ctxInflation = document.getElementById('inflationChart').getContext('2d');
        const inflationChart = new Chart(ctxInflation, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{ label: 'Inflasi', data: [], borderColor: '#ef4444', tension: 0.3 }]
            },
            options: chartOptions
        });

        // FUNGSI FETCH DATA GDP
        fetch('/api/gdp-data')
            .then(res => res.json())
            .then(data => {
                gdpChart.data.datasets[0].data = data.history;
                gdpChart.update();
            });

        // FUNGSI FETCH DATA INFLASI (Pastikan endpoint ini sudah dibuat di route/controller)
        fetch('/api/inflation-data')
            .then(res => res.json())
            .then(data => {
                inflationChart.data.datasets[0].data = data.history;
                inflationChart.update();
            });
    });
</script>
       

        <!-- Bagian Peta Interaktif -->
<div class="mb-8">
    <h2 class="text-xl font-bold mb-4">Lokasi Negara</h2>
    <div id="map" class="h-64 rounded-xl shadow-sm border border-gray-100"></div>
</div>

<script>
    if(document.getElementById('map')) {
        // Ambil data dari elemen HTML tadi
        var element = document.getElementById('country-data');
        var lat = parseFloat(element.getAttribute('data-lat'));
        var lon = parseFloat(element.getAttribute('data-lon'));
        var name = element.getAttribute('data-name');

        // Logika peta murni
        var map = L.map('map').setView([lat, lon], 5);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        L.marker([lat, lon]).addTo(map)
            .bindPopup(name)
            .openPopup();
    }
</script>
        <!-- Berita Logistik -->
      <h2 class="text-xl font-bold mb-4">Latest Logistics News</h2>
        @foreach($data['recent_supply_chain_news']['articles'] ?? [] as $article)
            <div class="bg-white p-5 mb-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <h3 class="font-bold text-lg">{{ $article['title'] }}</h3>
                <p class="text-sm text-gray-600">{{ $article['description'] ?? 'No description.' }}</p>
                <a href="{{ $article['url'] }}" target="_blank" class="text-blue-500 text-sm hover:underline">Baca Selengkapnya &rarr;</a>
            </div>
        @endforeach
      

<!-- Risk Scoring Card -->
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

        <!-- Status Footer -->
        <div class="mt-8 text-center">
            <span class="text-sm text-gray-500 font-mono">
                Status: <span class="text-green-600 font-bold">ONLINE</span> | 
                Update: {{ $data['risk_profile_generated_at'] }}
            </span>
        </div>
    </div>
    
    <p class="text-sm text-gray-500">
    Data diambil: {{ \Carbon\Carbon::parse($data['risk_profile_generated_at'])->diffForHumans() }}
</p>
<!-- Data tersembunyi untuk dibaca oleh JS -->
<div id="country-data" 
     data-lat="{{ $data['lat'] ?? 0 }}" 
     data-lon="{{ $data['lon'] ?? 0 }}" 
     data-name="{{ $data['country'] }}" 
     style="display:none;">
</div>
<!-- Di bagian bawah file, SEBELUM penutup </body> -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Inisialisasi peta hanya setelah Leaflet JS dimuat
    document.addEventListener("DOMContentLoaded", function() {
        var element = document.getElementById('country-data');
        var lat = parseFloat(element.getAttribute('data-lat'));
        var lon = parseFloat(element.getAttribute('data-lon'));
        
        var map = L.map('map').setView([lat, lon], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([lat, lon]).addTo(map);
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fungsi untuk mengambil daftar negara
        fetch('/api/countries')
            .then(response => response.json())
            .then(countries => {
                const selects = [document.getElementById('country1'), document.getElementById('country2')];
                
                selects.forEach(select => {
                    countries.forEach(country => {
                        let option = document.createElement('option');
                        option.value = country;
                        option.text = country;
                        select.appendChild(option);
                    });
                });
            });
    });

    // Fungsi saat tombol Bandingkan diklik
    function compareCountries() {
        const country1 = document.getElementById('country1').value;
        const country2 = document.getElementById('country2').value;

        if (country1 === country2) {
            alert("Pilih dua negara yang berbeda!");
            return;
        }

        // Di sini kita akan panggil data perbandingan
        console.log("Membandingkan: " + country1 + " vs " + country2);
        // Kita akan isi bagian ini di langkah berikutnya setelah UI-nya siap!
    }
</script>
</body>
</body>
</html>