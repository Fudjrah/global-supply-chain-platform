<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Pelabuhan | PortRisk Integra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        /* Memastikan peta selalu tampil di depan dan bisa diklik */
        #map { z-index: 10; height: 600px; width: 100%; border-radius: 1rem; }
        .loading-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 flex min-h-screen">

<aside class="w-64 bg-gray-900 text-white p-6">
    <h2 class="text-2xl font-bold text-blue-400 mb-10">PortRisk Integra</h2>
    <nav class="space-y-4">
        <a href="/" class="block py-2 px-4 hover:bg-gray-800 rounded-lg">Dashboard</a>
        <a href="/perbandingan" class="block py-2 px-4 hover:bg-gray-800 rounded-lg">Perbandingan Negara</a>
        <a href="/pelabuhan" class="block py-2 px-4 bg-blue-600 rounded-lg">Data Pelabuhan</a>
        @auth
            @can('manage-admin')
                <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 hover:bg-gray-800 rounded-lg">Admin Panel</a>
            @endcan
        @endauth
    </nav>
</aside>

    <main class="flex-1 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Peta Risiko Pelabuhan Global</h1>

        <!-- Search Bar Container -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 mb-6 flex gap-3 items-center">
            <div class="relative flex-grow">
                <input type="text" id="searchInput" placeholder="Cari berdasarkan nama pelabuhan atau negara..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                <div class="absolute left-3 top-3 text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <button id="searchButton" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-2.5 rounded-xl shadow-sm hover:shadow transition text-sm">
                Cari Pelabuhan
            </button>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 relative">
            <!-- Loading Indicator -->
            <div id="loadingOverlay" class="loading-overlay">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-blue-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-700 font-medium">Memuat ribuan pelabuhan dunia...</p>
                </div>
            </div>
            <div id="map"></div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Cluster group untuk performa ribuan marker
        var markerCluster = L.markerClusterGroup({
            chunkedLoading: true, // Meningkatkan performa loading beruntun
            maxClusterRadius: 50  // Mengatur kepadatan cluster
        });

        var markers = [];

        // Fetch data pelabuhan dari backend Laravel API
        fetch('/api/ports')
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil data dari API /api/ports!');
                return res.json();
            })
            .then(data => {
                data.forEach(port => {
                    var marker = L.marker([port.lat, port.lng]);
                    marker.bindPopup(`
                        <div style="padding: 5px; min-width: 180px;">
                            <h3 style="font-weight: bold; color: #2563eb; font-size: 15px; margin-bottom: 5px;">${port.name}</h3>
                            <p style="margin: 0; font-size: 13px; color: #4b5563;"><strong>Negara:</strong> ${port.country}</p>
                            <p style="margin: 4px 0; font-size: 13px; color: #4b5563;"><strong>Koordinat:</strong> ${port.lat}, ${port.lng}</p>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #4b5563;"><strong>Tipe:</strong> ${port.type || 'Seaport'}</p>
                        </div>
                    `);
                    
                    markerCluster.addLayer(marker);
                    
                    markers.push({
                        port: port,
                        marker: marker
                    });
                });

                // Tambahkan cluster ke peta
                map.addLayer(markerCluster);
                // Sembunyikan loading indicator
                document.getElementById('loadingOverlay').style.display = 'none';
            })
            .catch(err => {
                console.error("Error:", err);
                document.getElementById('loadingOverlay').innerHTML = `
                    <div class="text-rose-600 font-semibold p-4 text-center">
                        ⚠️ Gagal memuat data pelabuhan. Coba muat ulang halaman.
                    </div>
                `;
            });

        // Fungsi pencarian pelabuhan
        function searchPort() {
            var query = document.getElementById('searchInput').value.toLowerCase().trim();
            if (!query) return;

            var found = false;
            for (var i = 0; i < markers.length; i++) {
                var p = markers[i].port;
                if (p.name.toLowerCase().includes(query) || p.country.toLowerCase().includes(query)) {
                    var markerObj = markers[i].marker;
                    
                    // PENTING: Untuk markercluster, gunakan zoomToShowLayer agar cluster dibuka otomatis sebelum popup
                    markerCluster.zoomToShowLayer(markerObj, function() {
                        markerObj.openPopup();
                    });
                    
                    found = true;
                    break;
                }
            }

            if (!found) {
                alert('Nama pelabuhan atau negara tidak ditemukan!');
            }
        }

        document.getElementById('searchButton').addEventListener('click', searchPort);
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPort();
            }
        });
    </script>
</body>
</html>