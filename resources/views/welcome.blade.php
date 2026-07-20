<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PortRisk Integra | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-idle { opacity: 0.55; }
        .card-active { opacity: 1; transition: opacity 0.4s; }
        .pulse-dot::before {
            content: '';
            display: inline-block; width: 8px; height: 8px;
            background: #22c55e; border-radius: 50%;
            margin-right: 6px; animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(1.3); }
        }
        .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200%; animation: skeleton-loading 1.2s infinite; }
        @keyframes skeleton-loading { 0% { background-position: 200%; } 100% { background-position: -200%; } }
    </style>
</head>
<body class="bg-gray-100 flex min-h-screen">

   <aside class="w-64 bg-gray-900 text-white p-6">
    <h2 class="text-2xl font-bold text-blue-400 mb-10">PortRisk Integra</h2>
    <nav class="space-y-4">
        <a href="/" class="block py-2 px-4 {{ request()->is('/') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Dashboard</a>
        <a href="/perbandingan" class="block py-2 px-4 {{ request()->is('perbandingan') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Perbandingan Negara</a>
        <a href="/pelabuhan" class="block py-2 px-4 {{ request()->is('pelabuhan') ? 'bg-blue-600' : 'hover:bg-gray-800' }} rounded-lg">Data Pelabuhan</a>
        @auth
            @can('manage-admin')
                <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 hover:bg-gray-800 rounded-lg">Admin Panel</a>
            @endcan
        @else
            {{-- If user is not logged in, do not show admin panel option --}}
        @endauth
    </nav>
</aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8">
        <!-- HEADER FORM -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mb-8">
            <h1 class="text-xl font-bold text-gray-800 mb-4">Analisis Risiko Logistik</h1>

            <!-- Ubah action menjadi JavaScript Handler agar halaman tidak reload -->
            <form id="riskForm" onsubmit="handleRiskAnalysis(event)" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Pilih Negara</label>
                    <input id="countryNameInput" type="text" name="name" placeholder="Nama Negara (Bahasa Inggris)..." required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Kode Negara (ISO)</label>
                    <input id="countryCodeInput" type="text" name="code" placeholder="Contoh: ID" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 rounded-lg transition">
                    Cek Risiko Sekarang
                </button>
            </form>
        </div>

        <!-- AREA HASIL/PETA -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- CARD 1: Statistik & Informasi Kurs -->
            <div id="currencyCard" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 card-idle max-h-[800px] overflow-y-auto">
                <!-- State: idle (belum dicari) -->
                <div id="currencyIdle" class="h-64 flex items-center justify-center text-gray-400 flex-col gap-2">
                    <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm">Statistik & Kurs akan muncul setelah klik "Cek Risiko"</p>
                </div>

                <!-- State: loading -->
                <div id="currencyLoading" class="hidden">
                    <div class="skeleton h-6 w-40 rounded mb-4"></div>
                    <div class="skeleton h-12 w-full rounded-xl mb-3"></div>
                    <div class="skeleton h-4 w-3/4 rounded mb-2"></div>
                    <div class="skeleton h-4 w-1/2 rounded mb-6"></div>
                    <div class="skeleton h-28 w-full rounded-xl"></div>
                </div>

                <!-- State: error -->
                <div id="currencyError" class="hidden h-64 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-4xl mb-2">⚠️</div>
                        <p id="currencyErrorMsg" class="text-rose-600 text-sm font-medium"></p>
                        <p class="text-gray-400 text-xs mt-1">Data kurs tidak tersedia saat ini</p>
                    </div>
                </div>

                <!-- State: data tampil -->
                <div id="currencyData" class="hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 text-base">Statistik Negara & Info Kurs</h3>
                        <span id="currencyLiveTag" class="pulse-dot text-xs text-green-600 font-medium">Live</span>
                    </div>

                    <!-- Rate utama -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-4 mb-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">1 USD setara dengan</p>
                                <p id="currencyMainRate" class="text-3xl font-extrabold text-indigo-700"></p>
                                <p id="currencyCodeLabel" class="text-sm text-gray-500 mt-0.5"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Konversi balik</p>
                                <p id="currencyReverseRate" class="text-sm font-semibold text-gray-700 mt-0.5"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Mini chart perbandingan 8 mata uang -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2">Perbandingan vs USD (Major Currencies)</p>
                        <div class="relative h-28">
                            <canvas id="sparklineChart"></canvas>
                        </div>
                    </div>

                    <p id="currencyLastUpdate" class="text-xs text-gray-400 mt-2 text-right"></p>

                    <!-- NEW STATS SECTION (Appended inside CARD 1) -->
                    <div id="countryStatsContainer" class="hidden mt-6 pt-6 border-t border-gray-100">
                        <!-- 1. Info Umum -->
                        <div class="flex items-center mb-4">
                            <img id="csFlag" src="" alt="Flag" class="hidden w-10 h-7 rounded border object-cover shadow-sm mr-3">
                            <div>
                                <h4 id="csName" class="font-bold text-gray-800 text-sm"></h4>
                                <p id="csPop" class="text-xs text-gray-500"></p>
                            </div>
                        </div>

                        <!-- 2 & 3. Ekonomi & Cuaca -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <!-- Ekonomi -->
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <p class="text-xs font-semibold text-gray-600 mb-2">Ekonomi</p>
                                <p class="text-xs text-gray-500 mb-1">GDP: <span id="csGdp" class="font-bold text-gray-800">N/A</span></p>
                                <p class="text-xs text-gray-500">Inflasi: <span id="csInf" class="font-bold text-gray-800">N/A</span></p>
                            </div>
                            <!-- Cuaca -->
                            <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                                <p class="text-xs font-semibold text-blue-800 mb-2">Cuaca Saat Ini</p>
                                <p class="text-xs text-blue-700 mb-1">Suhu: <span id="csTemp" class="font-bold">N/A</span></p>
                                <p class="text-xs text-blue-700">Angin: <span id="csWind" class="font-bold">N/A</span></p>
                            </div>
                        </div>

                        <!-- 4. Berita -->
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-3">Berita Terkait</p>
                            <div id="csNewsContainer" class="space-y-3">
                                <p class="text-xs text-gray-400 italic">Memuat berita...</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- CARD 2: Grafik Risiko / Tren -->
            <div id="riskCard" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 card-idle">
                <!-- State: idle (belum dicari) -->
                <div id="riskIdle" class="h-64 flex items-center justify-center text-gray-400 flex-col gap-2">
                    <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="text-sm">Grafik Risiko akan muncul setelah klik "Cek Risiko"</p>
                </div>

                <!-- State: loading -->
                <div id="riskLoading" class="hidden">
                    <div class="skeleton h-6 w-40 rounded mb-4"></div>
                    <div class="skeleton h-44 w-full rounded-xl mb-3"></div>
                    <div class="skeleton h-4 w-1/2 rounded"></div>
                </div>

                <!-- State: error -->
                <div id="riskError" class="hidden h-64 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-4xl mb-2">⚠️</div>
                        <p id="riskErrorMsg" class="text-rose-600 text-sm font-medium"></p>
                    </div>
                </div>

                <!-- State: data tampil -->
                <div id="riskData" class="hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 text-base" id="riskCardTitle">Komposisi Skor Risiko</h3>
                        <span id="riskLevelBadge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                    </div>

                    <!-- Canvas Chart.js -->
                    <div class="relative h-48 w-full">
                        <canvas id="riskDetailsChart"></canvas>
                    </div>

                    <div class="mt-3 flex justify-between items-center text-xs text-gray-400">
                        <span id="totalRiskScoreLabel"></span>
                        <span>Sumber: PortRisk Engine v1.0</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
    let sparklineChartInstance = null;
    let riskChartInstance = null;

    // --- State Handler Currency Card ---
    function showCurrencyState(state, msg = '') {
        document.getElementById('currencyIdle').classList.add('hidden');
        document.getElementById('currencyLoading').classList.add('hidden');
        document.getElementById('currencyError').classList.add('hidden');
        document.getElementById('currencyData').classList.add('hidden');

        if (state === 'idle') {
            document.getElementById('currencyCard').classList.add('card-idle');
            document.getElementById('currencyCard').classList.remove('card-active');
            document.getElementById('currencyIdle').classList.remove('hidden');
        } else if (state === 'loading') {
            document.getElementById('currencyCard').classList.remove('card-idle');
            document.getElementById('currencyCard').classList.add('card-active');
            document.getElementById('currencyLoading').classList.remove('hidden');
        } else if (state === 'error') {
            document.getElementById('currencyCard').classList.remove('card-idle');
            document.getElementById('currencyCard').classList.add('card-active');
            document.getElementById('currencyError').classList.remove('hidden');
            document.getElementById('currencyErrorMsg').textContent = msg;
        } else if (state === 'data') {
            document.getElementById('currencyCard').classList.remove('card-idle');
            document.getElementById('currencyCard').classList.add('card-active');
            document.getElementById('currencyData').classList.remove('hidden');
        }
    }

    // --- State Handler Risk Card ---
    function showRiskState(state, msg = '') {
        document.getElementById('riskIdle').classList.add('hidden');
        document.getElementById('riskLoading').classList.add('hidden');
        document.getElementById('riskError').classList.add('hidden');
        document.getElementById('riskData').classList.add('hidden');

        if (state === 'idle') {
            document.getElementById('riskCard').classList.add('card-idle');
            document.getElementById('riskCard').classList.remove('card-active');
            document.getElementById('riskIdle').classList.remove('hidden');
        } else if (state === 'loading') {
            document.getElementById('riskCard').classList.remove('card-idle');
            document.getElementById('riskCard').classList.add('card-active');
            document.getElementById('riskLoading').classList.remove('hidden');
        } else if (state === 'error') {
            document.getElementById('riskCard').classList.remove('card-idle');
            document.getElementById('riskCard').classList.add('card-active');
            document.getElementById('riskError').classList.remove('hidden');
            document.getElementById('riskErrorMsg').textContent = msg;
        } else if (state === 'data') {
            document.getElementById('riskCard').classList.remove('card-idle');
            document.getElementById('riskCard').classList.add('card-active');
            document.getElementById('riskData').classList.remove('hidden');
        }
    }

    function renderSparkline(sparklineData) {
        const labels = Object.keys(sparklineData);
        const values = Object.values(sparklineData);

        if (sparklineChartInstance) {
            sparklineChartInstance.destroy();
        }

        const ctx = document.getElementById('sparklineChart').getContext('2d');
        sparklineChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rate vs USD',
                    data: values,
                    backgroundColor: labels.map(l => l === 'IDR' ? 'rgba(99, 102, 241, 0.8)' : 'rgba(99, 102, 241, 0.35)'),
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `1 USD = ${ctx.parsed.y.toLocaleString()} ${ctx.label}`
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'logarithmic',
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 9 } }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    // Render bar chart komponen risiko jika hanya ada 1 data titik (risk score saat ini)
    function renderRiskComponentsChart(componentsData, countryName) {
        const labels = Object.keys(componentsData);
        const values = Object.values(componentsData);

        if (riskChartInstance) {
            riskChartInstance.destroy();
        }

        const ctx = document.getElementById('riskDetailsChart').getContext('2d');
        riskChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Komponen',
                    data: values,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.75)', // Weather: Blue
                        'rgba(239, 68, 68, 0.75)',  // Inflation: Red
                        'rgba(245, 158, 11, 0.75)', // News: Orange
                        'rgba(16, 185, 129, 0.75)'  // Currency: Green
                    ],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `Skor: ${ctx.parsed.y} / 100`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 40, // Komponen maksimal (misal inflation max 40)
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 10 } }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    async function fetchCurrency(countryName) {
        if (!countryName || countryName.trim() === '') return;

        showCurrencyState('loading');

        try {
            const resp = await fetch(`/api/currency?country=${encodeURIComponent(countryName)}`);
            const data = await resp.json();

            if (!resp.ok || !data.success) {
                showCurrencyState('error', data.error || 'Gagal mengambil data kurs mata uang.');
                return;
            }

            // Isi data ke card
            document.getElementById('currencyMainRate').textContent =
                `${data.currency_symbol || ''} ${data.rate_vs_usd.toLocaleString('id-ID')}`;
            document.getElementById('currencyCodeLabel').textContent =
                `${data.currency_name} (${data.currency_code})`;
            document.getElementById('currencyReverseRate').textContent =
                `1 ${data.currency_code} = $ ${Number(data.rate_to_usd).toFixed(6)} USD`;
            document.getElementById('currencyLastUpdate').textContent =
                `Update: ${data.last_update ?? '-'}`;

            // Render sparkline chart
            if (data.sparkline_vs_usd && Object.keys(data.sparkline_vs_usd).length > 0) {
                renderSparkline(data.sparkline_vs_usd);
            }

            showCurrencyState('data');
        } catch (err) {
            showCurrencyState('error', 'Terjadi kesalahan jaringan. Coba lagi.');
            console.error('Currency fetch error:', err);
        }
    }

    async function fetchRiskScore(countryName, countryCode) {
        showRiskState('loading');

        try {
            const resp = await fetch(`/api/risk?name=${encodeURIComponent(countryName)}&code=${encodeURIComponent(countryCode)}`);
            const data = await resp.json();

            if (!resp.ok || !data.success) {
                showRiskState('error', data.error || 'Gagal menganalisis risiko negara.');
                return;
            }

            // Atur Badge Level Risiko
            const levelBadge = document.getElementById('riskLevelBadge');
            levelBadge.textContent = data.risk_level;
            levelBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold ';
            if (data.risk_score > 50) {
                levelBadge.classList.add('bg-rose-100', 'text-rose-700');
            } else if (data.risk_score > 25) {
                levelBadge.classList.add('bg-amber-100', 'text-amber-700');
            } else {
                levelBadge.classList.add('bg-green-100', 'text-green-700');
            }

            // Judul Card & Nilai Total
            document.getElementById('riskCardTitle').textContent = `Risiko Logistik: ${data.country}`;
            document.getElementById('totalRiskScoreLabel').textContent = `Total Skor Risiko: ${data.risk_score} / 100`;

            // Gambar Grafik Komponen
            if (data.components) {
                renderRiskComponentsChart(data.components, data.country);
            }

            showRiskState('data');
        } catch (err) {
            showRiskState('error', 'Gagal memuat analisis risiko.');
            console.error('Risk fetch error:', err);
        }
    }

    async function fetchCountryStats(countryCode) {
        if (!countryCode) return;
        
        // Tampilkan loading skeleton di news container atau biarkan kosong dulu
        const newsContainer = document.getElementById('csNewsContainer');
        newsContainer.innerHTML = '<p class="text-xs text-gray-400 italic">Memuat berita & statistik...</p>';
        document.getElementById('countryStatsContainer').classList.remove('hidden');

        try {
            const resp = await fetch(`/api/country-stats/${encodeURIComponent(countryCode)}`);
            const data = await resp.json();

            if (!resp.ok || !data.success) {
                newsContainer.innerHTML = '<p class="text-xs text-rose-500 italic">Gagal mengambil statistik tambahan.</p>';
                return;
            }

            // 1. Info Umum
            if (data.general) {
                document.getElementById('csName').textContent = data.general.name || '-';
                document.getElementById('csPop').textContent = data.general.population ? `Populasi: ${data.general.population}` : '';
                const flagImg = document.getElementById('csFlag');
                if (data.general.flag) {
                    flagImg.src = data.general.flag;
                    flagImg.classList.remove('hidden');
                } else {
                    flagImg.classList.add('hidden');
                }
            }

            // 2. Ekonomi
            if (data.economy) {
                document.getElementById('csGdp').textContent = data.economy.gdp || 'N/A';
                document.getElementById('csInf').textContent = data.economy.inflation || 'N/A';
            }

            // 3. Cuaca
            if (data.weather) {
                document.getElementById('csTemp').textContent = data.weather.temperature ? `${data.weather.temperature}°C` : 'N/A';
                document.getElementById('csWind').textContent = data.weather.windspeed ? `${data.weather.windspeed} km/h` : 'N/A';
            }

            // 4. Berita
            newsContainer.innerHTML = '';
            if (data.news) {
                let hasNews = false;
                
                // Helper to render news card
                const renderNewsCard = (article, category) => {
                    hasNews = true;
                    // Color badge based on sentiment
                    let badgeColor = 'bg-gray-100 text-gray-600';
                    if (article.sentiment === 'Positive') badgeColor = 'bg-green-100 text-green-700';
                    if (article.sentiment === 'Negative') badgeColor = 'bg-rose-100 text-rose-700';
                    
                    return `
                        <a href="${article.url}" target="_blank" class="block p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-wide text-blue-500">${category}</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold ${badgeColor}">${article.sentiment}</span>
                            </div>
                            <h5 class="text-xs font-semibold text-gray-800 line-clamp-2">${article.title}</h5>
                            <p class="text-[10px] text-gray-400 mt-1">${article.source_name || '-'} &bull; ${new Date(article.published_at).toLocaleDateString()}</p>
                        </a>
                    `;
                };

                if (data.news.logistics && data.news.logistics.length > 0) {
                    data.news.logistics.forEach(a => { newsContainer.innerHTML += renderNewsCard(a, 'Logistik'); });
                }
                if (data.news.geopolitics && data.news.geopolitics.length > 0) {
                    data.news.geopolitics.forEach(a => { newsContainer.innerHTML += renderNewsCard(a, 'Geopolitik'); });
                }
                if (data.news.economy && data.news.economy.length > 0) {
                    data.news.economy.forEach(a => { newsContainer.innerHTML += renderNewsCard(a, 'Ekonomi'); });
                }

                if (!hasNews) {
                    newsContainer.innerHTML = '<p class="text-xs text-gray-500 italic">Tidak ada berita terbaru yang ditemukan.</p>';
                }
            } else {
                newsContainer.innerHTML = '<p class="text-xs text-gray-500 italic">Berita tidak tersedia.</p>';
            }

        } catch (err) {
            console.error('Stats fetch error:', err);
            newsContainer.innerHTML = '<p class="text-xs text-rose-500 italic">Terjadi kesalahan jaringan.</p>';
        }
    }

    // Handler utama cek risiko (AJAX Async)
    function handleRiskAnalysis(event) {
        event.preventDefault();
        
        const countryName = document.getElementById('countryNameInput').value.trim();
        const countryCode = document.getElementById('countryCodeInput').value.trim();

        if (countryName && countryCode) {
            fetchCurrency(countryName);
            fetchRiskScore(countryName, countryCode);
            fetchCountryStats(countryCode); // Tambahkan panggil API baru
        }
    }
</script>

</body>
</html>