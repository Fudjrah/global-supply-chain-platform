<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Perbandingan Negara | PortRisk Integra</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white p-6 shadow-xl">
        <h2 class="text-2xl font-bold text-blue-400 mb-10">PortRisk Integra</h2>
        <nav class="space-y-4">
            <a href="/" class="block py-2 px-4 hover:bg-gray-800 rounded-lg transition">Dashboard</a>
            <a href="/perbandingan" class="block py-2 px-4 bg-blue-600 rounded-lg shadow-lg">Perbandingan Negara</a>
            <a href="/pelabuhan" class="block py-2 px-4 hover:bg-gray-800 rounded-lg transition">Data Pelabuhan</a>
            <a href="/admin" class="block py-2 px-4 hover:bg-gray-800 rounded-lg transition">Admin Panel</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-10">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Perbandingan Negara</h1>
            <p class="text-gray-500 mb-8">Analisis data ekonomi dan logistik antar dua negara secara instan.</p>

            <!-- FORM PERBANDINGAN -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Negara Pertama</label>
                        <input type="text" id="country1" placeholder="Contoh: Indonesia" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Negara Kedua</label>
                        <input type="text" id="country2" placeholder="Contoh: Singapore" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition">
                    </div>
                </div>
                <button onclick="compareCountries()" class="w-full bg-gray-900 hover:bg-blue-600 text-white font-bold py-4 rounded-xl transition duration-300 shadow-lg">
                    Bandingkan Data Sekarang
                </button>
            </div>

            <!-- HASIL ANALISIS -->
            <div id="result" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
        </div>
    </main>

    <script>
        function compareCountries() {
            const c1 = document.getElementById('country1').value;
            const c2 = document.getElementById('country2').value;

            if (!c1 || !c2) { alert("Harap isi kedua nama negara!"); return; }

            const resDiv = document.getElementById('result');
            resDiv.innerHTML = `<p class="text-center col-span-2 py-10 text-gray-400 animate-pulse">Sedang menganalisis data...</p>`;

            fetch(`/compare-countries?country1=${encodeURIComponent(c1)}&country2=${encodeURIComponent(c2)}`)
                .then(res => res.json())
                .then(data => {
                    resDiv.innerHTML = `
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h3 class="text-xl font-bold mb-4 text-blue-600">${data.country1.name}</h3>
                            <div class="space-y-2 text-gray-600">
                                <p>Bahasa: <span class="font-medium text-gray-900">${data.country1.bahasa}</span></p>
                                <p>GDP: <span class="font-medium text-gray-900">${data.country1.gdp}</span></p>
                                <p>Kurs: <span class="font-medium text-gray-900">${data.country1.kurs}</span></p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h3 class="text-xl font-bold mb-4 text-blue-600">${data.country2.name}</h3>
                            <div class="space-y-2 text-gray-600">
                                <p>Bahasa: <span class="font-medium text-gray-900">${data.country2.bahasa}</span></p>
                                <p>GDP: <span class="font-medium text-gray-900">${data.country2.gdp}</span></p>
                                <p>Kurs: <span class="font-medium text-gray-900">${data.country2.kurs}</span></p>
                            </div>
                        </div>
                    `;
                })
                .catch(err => {
                    resDiv.innerHTML = `<p class="text-red-500 text-center">Gagal mengambil data. Coba lagi nanti.</p>`;
                });
        }
    </script>
</body>
</html>