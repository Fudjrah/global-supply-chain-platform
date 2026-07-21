<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbandingan Negara | PortRisk Integra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .risk-badge-low    { background: #dcfce7; color: #15803d; }
        .risk-badge-medium { background: #fef9c3; color: #a16207; }
        .risk-badge-high   { background: #fee2e2; color: #dc2626; }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .skeleton { background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200%; animation: skel 1.2s infinite; border-radius: 8px; }
        @keyframes skel { 0%{background-position:200%} 100%{background-position:-200%} }
    </style>
</head>
<body class="bg-gray-50 flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white p-6 shadow-xl flex-shrink-0">
        <h2 class="text-2xl font-bold text-blue-400 mb-10">PortRisk Integra</h2>
        <nav class="space-y-4">
            <a href="/" class="block py-2 px-4 hover:bg-gray-800 rounded-lg transition">Dashboard</a>
            <a href="/perbandingan" class="block py-2 px-4 bg-blue-600 rounded-lg shadow-lg">Perbandingan Negara</a>
            <a href="/pelabuhan" class="block py-2 px-4 hover:bg-gray-800 rounded-lg transition">Data Pelabuhan</a>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 hover:bg-gray-800 rounded-lg transition">Admin Panel</a>
            @endif
            @auth
                <div class="mt-8 pt-8 border-t border-gray-800">
                    <p class="text-xs text-gray-400">Masuk sebagai:</p>
                    <p class="text-sm font-semibold text-white truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-blue-400 capitalize mb-4">{{ Auth::user()->role }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-2 px-4 rounded-lg text-sm transition">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <div class="mt-8 pt-8 border-t border-gray-800 space-y-2">
                    <a href="{{ route('login') }}" class="block text-center bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg text-sm transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="block text-center border border-gray-700 hover:bg-gray-800 text-gray-300 font-bold py-2 px-4 rounded-lg text-sm transition">
                        Register
                    </a>
                </div>
            @endauth
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-10 overflow-auto">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-1">Perbandingan Negara</h1>
            <p class="text-gray-500 mb-8">Analisis komparatif GDP, inflasi, cuaca, kurs mata uang, dan skor risiko logistik antar dua negara secara real-time.</p>

            <!-- INPUT FORM -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Negara Pertama</label>
                        <input type="text" id="country1" placeholder="Contoh: Germany atau DE" value=""
                            class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition">
                    </div>
                    <div class="md:col-span-1 text-center hidden md:flex items-center justify-center">
                        <span class="text-gray-400 font-bold text-xl">vs</span>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Negara Kedua</label>
                        <input type="text" id="country2" placeholder="Contoh: Australia atau AU" value=""
                            class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition">
                    </div>
                </div>
                <button onclick="doCompare()"
                    class="mt-6 w-full bg-gray-900 hover:bg-blue-600 text-white font-bold py-4 rounded-xl transition duration-300 shadow-lg text-lg">
                    Bandingkan Data Sekarang
                </button>
            </div>

            <!-- SKELETON LOADING -->
            <div id="loadingState" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-3">
                    <div class="skeleton h-10 w-1/2"></div>
                    <div class="skeleton h-5 w-full"></div>
                    <div class="skeleton h-5 w-3/4"></div>
                    <div class="skeleton h-5 w-full"></div>
                    <div class="skeleton h-5 w-2/3"></div>
                    <div class="skeleton h-5 w-full"></div>
                </div>
                <div class="space-y-3">
                    <div class="skeleton h-10 w-1/2"></div>
                    <div class="skeleton h-5 w-full"></div>
                    <div class="skeleton h-5 w-3/4"></div>
                    <div class="skeleton h-5 w-full"></div>
                    <div class="skeleton h-5 w-2/3"></div>
                    <div class="skeleton h-5 w-full"></div>
                </div>
            </div>

            <!-- ERROR STATE -->
            <div id="errorState" class="hidden bg-rose-50 border border-rose-200 rounded-2xl p-6 text-center mb-8">
                <p class="text-rose-600 font-medium text-lg">⚠️ <span id="errorMsg"></span></p>
                <p class="text-rose-400 text-sm mt-1">Periksa koneksi internet atau coba lagi.</p>
            </div>

            <!-- RESULTS: COMPARISON TABLE -->
            <div id="resultsSection" class="hidden">

                <!-- Side-by-side comparison table -->
                <div id="comparisonTable" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8 fade-in">
                    <table class="w-full">
                        <thead>
                            <tr id="tableHeader" class="border-b border-gray-100"></tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
                    <!-- GDP Growth Chart -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 text-sm mb-3">GDP Growth (%)</h4>
                        <div class="relative h-48">
                            <canvas id="gdpChart"></canvas>
                        </div>
                    </div>
                    <!-- Inflation Chart -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 text-sm mb-3">Inflation Rate (%)</h4>
                        <div class="relative h-48">
                            <canvas id="inflationChart"></canvas>
                        </div>
                    </div>
                    <!-- Risk Score Chart -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 text-sm mb-3">Risk Score (0-100)</h4>
                        <div class="relative h-48">
                            <canvas id="riskChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Currency Rate Chart -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 fade-in">
                    <h4 class="font-semibold text-gray-700 text-sm mb-4">Exchange Rate vs USD</h4>
                    <div class="relative h-36">
                        <canvas id="currencyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
    // No stale instance variables needed — we use Chart.getChart() for safe cleanup

    function show(id) { document.getElementById(id).classList.remove('hidden'); }
    function hide(id) { document.getElementById(id).classList.add('hidden'); }

    function getRiskClass(level) {
        if (!level) return 'risk-badge-low';
        const l = level.toLowerCase();
        if (l.includes('high')) return 'risk-badge-high';
        if (l.includes('medium')) return 'risk-badge-medium';
        return 'risk-badge-low';
    }

    function weatherIcon(code) {
        if (code === null || code === undefined) return '❓';
        if (code === 0) return '☀️';
        if (code <= 3) return '🌤️';
        if (code <= 48) return '🌫️';
        if (code <= 67) return '🌧️';
        if (code <= 77) return '🌨️';
        if (code <= 82) return '🌦️';
        if (code <= 99) return '⛈️';
        return '🌡️';
    }

    function val(v, fallback = 'N/A') {
        return (v !== null && v !== undefined && v !== '') ? v : fallback;
    }

    function destroyChart(canvasId) {
        // Chart.getChart() is the safe way to find and destroy any existing instance on a canvas
        const existing = Chart.getChart(document.getElementById(canvasId));
        if (existing) existing.destroy();
    }

    function renderBarChart(canvasId, labels, values, colors) {
        destroyChart(canvasId);
        const ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 10 } }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    function parseNumeric(str) {
        if (!str) return 0;
        return parseFloat(String(str).replace(/[^0-9.\-]/g, '')) || 0;
    }

    function renderResults(data) {
        const c1 = data.country1;
        const c2 = data.country2;

        // ——— TABLE HEADER ———
        document.getElementById('tableHeader').innerHTML = `
            <th class="text-left py-4 px-6 text-gray-400 font-semibold text-sm uppercase tracking-wider w-1/3">Indikator</th>
            <th class="py-4 px-6 text-center w-1/3">
                <span class="text-xl font-extrabold text-blue-700">${c1.country_name}</span>
                <span class="ml-2 text-xs font-bold bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">${c1.iso_code}</span>
            </th>
            <th class="py-4 px-6 text-center w-1/3">
                <span class="text-xl font-extrabold text-purple-700">${c2.country_name}</span>
                <span class="ml-2 text-xs font-bold bg-purple-100 text-purple-600 px-2 py-0.5 rounded-full">${c2.iso_code}</span>
            </th>
        `;

        // ——— TABLE ROWS ———
        const rows = [
            {
                label: '🏦 GDP Growth',
                v1: val(c1.economy?.gdp_growth?.value) + (c1.economy?.gdp_growth?.year ? ` (${c1.economy.gdp_growth.year})` : ''),
                v2: val(c2.economy?.gdp_growth?.value) + (c2.economy?.gdp_growth?.year ? ` (${c2.economy.gdp_growth.year})` : ''),
            },
            {
                label: '📈 Inflasi',
                v1: val(c1.economy?.inflation_rate?.value) + (c1.economy?.inflation_rate?.year ? ` (${c1.economy.inflation_rate.year})` : ''),
                v2: val(c2.economy?.inflation_rate?.value) + (c2.economy?.inflation_rate?.year ? ` (${c2.economy.inflation_rate.year})` : ''),
            },
            {
                label: '🌡️ Suhu Sekarang',
                v1: c1.weather?.temperature != null ? `${c1.weather.temperature} °C ${weatherIcon(c1.weather.weathercode)}` : 'N/A',
                v2: c2.weather?.temperature != null ? `${c2.weather.temperature} °C ${weatherIcon(c2.weather.weathercode)}` : 'N/A',
            },
            {
                label: '💨 Kecepatan Angin',
                v1: c1.weather?.windspeed != null ? `${c1.weather.windspeed} km/h` : 'N/A',
                v2: c2.weather?.windspeed != null ? `${c2.weather.windspeed} km/h` : 'N/A',
            },
            {
                label: '💱 Mata Uang',
                v1: c1.currency ? `${c1.currency.symbol} ${c1.currency.code} — ${c1.currency.name}` : 'N/A',
                v2: c2.currency ? `${c2.currency.symbol} ${c2.currency.code} — ${c2.currency.name}` : 'N/A',
            },
            {
                label: '💵 Kurs vs 1 USD',
                v1: c1.currency?.rate_vs_usd != null ? `${parseFloat(c1.currency.rate_vs_usd).toLocaleString('id-ID')} ${c1.currency.code}` : 'N/A',
                v2: c2.currency?.rate_vs_usd != null ? `${parseFloat(c2.currency.rate_vs_usd).toLocaleString('id-ID')} ${c2.currency.code}` : 'N/A',
            },
            {
                label: '⚠️ Risk Score',
                v1: `<span class="font-bold text-xl">${val(c1.risk_score, 0)}</span> / 100`,
                v2: `<span class="font-bold text-xl">${val(c2.risk_score, 0)}</span> / 100`,
                raw: true,
            },
            {
                label: '🚦 Risk Level',
                v1: `<span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${getRiskClass(c1.risk_level)}">${val(c1.risk_level)}</span>`,
                v2: `<span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${getRiskClass(c2.risk_level)}">${val(c2.risk_level)}</span>`,
                raw: true,
            },
            {
                label: '📡 Sumber Data',
                v1: val(c1.economy?.source, '-'),
                v2: val(c2.economy?.source, '-'),
            },
        ];

        document.getElementById('tableBody').innerHTML = rows.map((row, i) => `
            <tr class="${i % 2 === 0 ? 'bg-white' : 'bg-gray-50'} border-b border-gray-100">
                <td class="py-3 px-6 text-sm font-medium text-gray-600">${row.label}</td>
                <td class="py-3 px-6 text-sm text-center text-blue-800 font-medium">${row.raw ? row.v1 : `<span>${row.v1}</span>`}</td>
                <td class="py-3 px-6 text-sm text-center text-purple-800 font-medium">${row.raw ? row.v2 : `<span>${row.v2}</span>`}</td>
            </tr>
        `).join('');

        // ——— SHOW SECTION FIRST — canvases must be visible before Chart.js renders ———
        show('resultsSection');

        // ——— CHARTS ———
        const names = [c1.country_name, c2.country_name];
        const blue = 'rgba(59, 130, 246, 0.75)';
        const purple = 'rgba(147, 51, 234, 0.75)';

        const gdpVals = [parseNumeric(c1.economy?.gdp_growth?.value), parseNumeric(c2.economy?.gdp_growth?.value)];
        const inflVals = [parseNumeric(c1.economy?.inflation_rate?.value), parseNumeric(c2.economy?.inflation_rate?.value)];
        const riskVals = [c1.risk_score ?? 0, c2.risk_score ?? 0];
        const currVals = [
            parseFloat(c1.currency?.rate_vs_usd ?? 0),
            parseFloat(c2.currency?.rate_vs_usd ?? 0),
        ];

        // Use Chart.getChart() to safely destroy before re-render (no stale instance refs needed)
        renderBarChart('gdpChart', names, gdpVals, [blue, purple]);
        renderBarChart('inflationChart', names, inflVals, [blue, purple]);
        renderBarChart('riskChart', names, riskVals, [
            riskVals[0] > 60 ? 'rgba(220,38,38,0.75)' : riskVals[0] > 35 ? 'rgba(234,179,8,0.75)' : 'rgba(34,197,94,0.75)',
            riskVals[1] > 60 ? 'rgba(220,38,38,0.75)' : riskVals[1] > 35 ? 'rgba(234,179,8,0.75)' : 'rgba(34,197,94,0.75)',
        ]);

        // Currency chart — use log scale if values differ hugely
        destroyChart('currencyChart');
        const currCtx = document.getElementById('currencyChart').getContext('2d');
        const maxRatio = currVals[0] > 0 && currVals[1] > 0 ? Math.max(...currVals) / Math.min(...currVals) : 1;
        new Chart(currCtx, {
            type: 'bar',
            data: {
                labels: names.map((n, i) => `${n} (${i===0?c1.currency?.code:c2.currency?.code})`),
                datasets: [{ data: currVals, backgroundColor: [blue, purple], borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `1 USD = ${ctx.parsed.y.toLocaleString()} ${ctx.label.match(/\(([^)]+)\)/)?.[1] ?? ''}`
                        }
                    }
                },
                scales: {
                    y: {
                        type: maxRatio > 100 ? 'logarithmic' : 'linear',
                        beginAtZero: false,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 10 } }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    async function doCompare() {
        const c1 = document.getElementById('country1').value.trim();
        const c2 = document.getElementById('country2').value.trim();

        if (!c1 || !c2) {
            alert('Harap isi kedua nama/kode negara!');
            return;
        }

        hide('resultsSection');
        hide('errorState');
        show('loadingState');

        try {
            const resp = await fetch(`/api/compare?country1=${encodeURIComponent(c1)}&country2=${encodeURIComponent(c2)}`);
            const data = await resp.json();

            hide('loadingState');

            if (!resp.ok || !data.success) {
                document.getElementById('errorMsg').textContent = data.error || 'Gagal mengambil data perbandingan.';
                show('errorState');
                return;
            }

            renderResults(data);
        } catch (err) {
            hide('loadingState');
            document.getElementById('errorMsg').textContent = 'Terjadi kesalahan jaringan. Periksa koneksi dan coba lagi.';
            show('errorState');
            console.error('Compare error:', err);
        }
    }

</script>

</body>
</html>