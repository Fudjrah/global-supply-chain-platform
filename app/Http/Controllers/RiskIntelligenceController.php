<?php

namespace App\Http\Controllers;

use App\Services\GNewsService;
use App\Services\WorldBankService;
use App\Services\WeatherService;
use App\Services\RestCountriesService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EconomicIndicator;

class RiskIntelligenceController extends Controller
{
    protected $newsService;
    protected $worldBankService;
    protected $weatherService;
    protected $countriesService;
    
    public function __construct(GNewsService $newsService, WorldBankService $worldBankService, WeatherService $weatherService, RestCountriesService $countriesService)
    {
        $this->newsService = $newsService;
        $this->worldBankService = $worldBankService;
        $this->weatherService = $weatherService;
        $this->countriesService = $countriesService;
    }

    // Fungsi untuk menghitung skor risiko
    private function calculateRiskScore($weather, $economy)
    {
        $score = 0;

        // 1. Logika Ekonomi: Inflasi > 5% dianggap risiko tinggi
        $inflation = (float)str_replace(['%', ','], ['', '.'], $economy['inflation_rate']['value'] ?? 0);
        if ($inflation > 5) $score += 40;
        elseif ($inflation > 2) $score += 20;

        // 2. Logika Cuaca: Angin > 30 km/h dianggap risiko tinggi
        $wind = $weather['windspeed'] ?? 0;
        if ($wind > 30) $score += 30;
        elseif ($wind > 15) $score += 15;

        return min($score, 100);
    }

    public function getRiskProfile(Request $request, string $countryName, string $countryCode)
    {
        if (strlen($countryCode) !== 2) {
            return response()->json(['error' => 'Format kode negara salah. Harus 2 huruf (contoh: ID, TH, SG).'], 400);
        }

        $countryInfo = $this->countriesService->getCountryInfoAsync($countryCode);
        $officialName = $countryInfo['official_name'] ?? $countryName;
        $coords = $this->countriesService->getCoordinatesAsync($officialName);
        // Hitung sentiment
         $sentiment = $this->calculateSentiment($news['articles'] ?? []);

        $weather = $this->weatherService->getWeatherAsync($coords['lat'], $coords['lon']);
        $news = $this->newsService->getLatestNewsAsync($officialName . ' shipping logistics');
        $economy = $this->worldBankService->getEconomicIndicatorsAsync($countryCode);

        // Hitung skor menggunakan fungsi baru
     $score = $this->calculateRiskScore($weather, $economy);
    if ($sentiment['label'] === 'Negative') $score += 20; // Penalti risiko jika berita negatif

    $data = [
        // ... (data lainnya) ...
        'sentiment' => $sentiment, // Kirim ke view
        'risk_score' => min($score, 100),
        // ...
    ];
        $sentiment = $this->calculateSentiment($news['articles'] ?? []); // Hitung sentimen
        $data = [
            'country' => $officialName,
            'country_info' => $countryInfo,
            'economic_indicators' => $economy,
            'recent_supply_chain_news' => $news,
            'weather' => $weather,
            'lat' => $coords['lat'] ?? 0,
            'lon' => $coords['lon'] ?? 0,
            'sentiment' => $sentiment, // <--- INI WAJIB ADA
            'risk_score' => $score,
            'risk_level' => $score > 50 ? 'High Risk' : ($score > 25 ? 'Medium Risk' : 'Low Risk'),
            'risk_profile_generated_at' => now()->toDateTimeString(),
        ];

        return view('risk_dashboard', compact('data'));
    }

    public function getRiskProfileFromForm(Request $request)
    {
        $name = $request->input('name');
        $code = $request->input('code');
        return $this->getRiskProfile($request, $name, $code);
    }

    private function calculateSentiment($newsArticles)
{
    // Kamus kata sederhana
    $positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'revives', 'launch'];
    $negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'hurdles', 'opposition', 'busy'];

    $positiveScore = 0;
    $negativeScore = 0;

    foreach ($newsArticles as $article) {
        $text = strtolower($article['title'] . ' ' . ($article['description'] ?? ''));
        
        foreach ($positiveWords as $word) {
            if (strpos($text, $word) !== false) $positiveScore++;
        }
        foreach ($negativeWords as $word) {
            if (strpos($text, $word) !== false) $negativeScore++;
        }
    }

    return [
        'positive' => $positiveScore,
        'negative' => $negativeScore,
        'label' => $positiveScore >= $negativeScore ? 'Positive' : 'Negative'
    ];
}
public function show($country)
{
    // ... logika pengambilan data lainnya ...

    $data = [
        'country' => 'Indonesia',
        // ... data lainnya ...
        
        // TAMBAHKAN INI (Contoh data dummy)
        'gdp_history' => [2.1, 2.3, 2.5, 2.4, 2.6, 2.8], 
        'inflation_history' => [1.5, 1.6, 1.8, 1.7, 1.9, 2.0],
    ];

    return view('risk_dashboard', ['data' => $data]);
}

public function getInflationData()
{
    // Ganti dengan data real dari DB kamu
    $inflationData = [1.5, 1.2, 1.8, 1.7, 1.9, 2.0];

    return response()->json([
        'history' => $inflationData
    ]);
}
public function getGdpData()
{
    // Mengambil 6 data terakhir dari database berdasarkan tanggal atau urutan
    $data = \App\Models\EconomicIndicator::where('indicator_name', 'GDP Growth')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->pluck('value') // Hanya mengambil kolom 'value'
            ->reverse()      // Agar urutan dari bulan terlama ke terbaru
            ->values();

    return response()->json([
        'history' => $data
    ]);
}

}