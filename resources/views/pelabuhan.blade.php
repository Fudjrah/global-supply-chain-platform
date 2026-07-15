<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Pelabuhan | PortRisk Integra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Memastikan peta selalu tampil di depan dan bisa diklik */
        #map { z-index: 10; height: 600px; width: 100%; }
    </style>
</head>
<body class="bg-gray-100 flex min-h-screen">

<aside class="w-64 bg-gray-900 text-white p-6">
    <h2 class="text-2xl font-bold text-blue-400 mb-10">PortRisk Integra</h2>
    <nav class="space-y-4">
        <a href="/" class="block py-2 px-4 {{ request()->is('/') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Dashboard</a>
        <a href="/perbandingan" class="block py-2 px-4 {{ request()->is('perbandingan') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Perbandingan Negara</a>
        <a href="/pelabuhan" class="block py-2 px-4 {{ request()->is('pelabuhan') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Data Pelabuhan</a>
        <a href="/admin" class="block py-2 px-4 {{ request()->is('admin') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Admin Panel</a>
    </nav>
</aside>

    <main class="flex-1 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Peta Risiko Pelabuhan Global</h1>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <div id="map"></div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Fetch data dengan path yang benar (mengarah ke folder public)
        fetch('/data_pelabuhan.json')
            .then(res => {
                if (!res.ok) throw new Error('File JSON tidak ditemukan di folder public!');
                return res.json();
            })
            .then(data => {
                data.forEach(port => {
                    var marker = L.marker([port.lat, port.lng]).addTo(map);
                    marker.bindPopup(`
                        <div style="padding: 5px;">
                            <h3 style="font-weight: bold; color: #2563eb; font-size: 16px;">${port.name}</h3>
                            <p style="margin: 0; font-size: 14px;">Negara: ${port.country}</p>
                            <p style="margin: 0; font-size: 14px; font-weight: bold;">Status: ${port.status}</p>
                        </div>
                    `);
                });
            })
            .catch(err => console.error("Error:", err));
    </script>
</body>
</html>