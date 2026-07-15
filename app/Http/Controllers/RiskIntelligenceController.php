<?php

namespace App\Http\Controllers;

use App\Services\GNewsService;
use App\Services\WorldBankService;
use App\Services\WeatherService;
use App\Services\RestCountriesService;
use App\Services\WpiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EconomicIndicator;

class RiskIntelligenceController extends Controller
{
    protected $newsService;
    protected $worldBankService;
    protected $weatherService;
    protected $countriesService;
    protected $wpiService;

    public function __construct(GNewsService $newsService, WorldBankService $worldBankService, WeatherService $weatherService, RestCountriesService $countriesService, WpiService $wpiService)
    {
        $this->newsService = $newsService;
        $this->worldBankService = $worldBankService;
        $this->weatherService = $weatherService;
        $this->countriesService = $countriesService;
        $this->wpiService = $wpiService;
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
        return response()->json(['error' => 'Format kode negara salah.'], 400);
    }

    $countryInfo = $this->countriesService->getCountryInfoAsync($countryCode);
    $officialName = $countryInfo['official_name'] ?? $countryName;
    $coords = $this->countriesService->getCoordinatesAsync($officialName);
    
    // Ambil semua data API secara berurutan
    $news = $this->newsService->getLatestNewsAsync($officialName . ' shipping logistics');
    $weather = $this->weatherService->getWeatherAsync($coords['lat'] ?? 0, $coords['lon'] ?? 0);
    $economy = $this->worldBankService->getEconomicIndicatorsAsync($countryCode);

    // Hitung sentiment dan skor
    $sentiment = $this->calculateSentiment($news['articles'] ?? []);
    $score = $this->calculateRiskScore($weather, $economy);
    if ($sentiment['label'] === 'Negative') $score += 20;

    $data = [
        'country' => $officialName,
        'country_info' => $countryInfo,
        'economic_indicators' => $economy,
        'recent_supply_chain_news' => $news,
        'weather' => $weather,
        'lat' => $coords['lat'] ?? 0,
        'lon' => $coords['lon'] ?? 0,
        'sentiment' => $sentiment,
        'risk_score' => min($score, 100),
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

public function getGdpData()
{
    // Coba kirim data dummy dulu biar API-nya jalan (tidak error 500)
    return response()->json([
        'history' => [2.1, 2.3, 2.5, 2.4, 2.6, 2.8]
    ]);
}

public function getInflationData()
{
    // Coba kirim data dummy dulu
    return response()->json([
        'history' => [1.1, 1.2, 1.3, 1.4, 1.5, 1.6]
    ]);
}

// GANTI INI di RiskIntelligenceController.php
public function getCountryList()
{
    // Panggil WpiService agar data tidak kosong
    // WpiService sudah ter-inject di __construct
    return response()->json($this->wpiService->getCountryListFromApi());
}
}