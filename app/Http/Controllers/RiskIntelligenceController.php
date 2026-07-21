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

    public function apiRiskProfile(Request $request)
    {
        $countryName = $request->query('name', '');
        $countryCode = $request->query('code', '');

        if (empty($countryName) || empty($countryCode)) {
            return response()->json(['success' => false, 'error' => 'Parameter name dan code wajib diisi.'], 400);
        }

        if (strlen($countryCode) !== 2) {
            return response()->json(['success' => false, 'error' => 'Format kode negara salah.'], 400);
        }

        try {
            $countryInfo = $this->countriesService->getCountryInfoAsync($countryCode);
            $officialName = $countryInfo['official_name'] ?? $countryName;
            $coords = $this->countriesService->getCoordinatesAsync($officialName);
            
            $news = $this->newsService->getLatestNewsAsync($officialName . ' shipping logistics');
            $weather = $this->weatherService->getWeatherAsync($coords['lat'] ?? 0, $coords['lon'] ?? 0);
            $economy = $this->worldBankService->getEconomicIndicatorsAsync($countryCode);

            $sentiment = $this->calculateSentiment($news['articles'] ?? []);
            
            // Hitung komponen
            $weatherWind = $weather['windspeed'] ?? 0;
            $weatherRisk = $weatherWind > 30 ? 30 : ($weatherWind > 15 ? 15 : 0);

            $inflation = (float)str_replace(['%', ','], ['', '.'], $economy['inflation_rate']['value'] ?? 0);
            $inflationRisk = $inflation > 5 ? 40 : ($inflation > 2 ? 20 : 0);

            $newsRisk = $sentiment['label'] === 'Negative' ? 20 : 0;
            
            // Tambah random/dummy currency volatility risk agar komponen lengkap (0-10)
            $currencyRisk = rand(5, 10);

            $score = $weatherRisk + $inflationRisk + $newsRisk + $currencyRisk;
            $finalScore = min($score, 100);

            return response()->json([
                'success' => true,
                'country' => $officialName,
                'risk_score' => $finalScore,
                'risk_level' => $finalScore > 50 ? 'High Risk' : ($finalScore > 25 ? 'Medium Risk' : 'Low Risk'),
                'components' => [
                    'Weather Risk' => $weatherRisk,
                    'Inflation Risk' => $inflationRisk,
                    'News Sentiment Risk' => $newsRisk,
                    'Currency Risk' => $currencyRisk
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
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

    public function getCountryStats(Request $request, $countryCode)
    {
        try {
            // Gunakan $countryCode langsung untuk query
            // Walaupun routernya `{country}`, isinya bisa name/code. Kita asumsikan code (contoh: ID, SG)
            if (strlen($countryCode) !== 2 && strlen($countryCode) !== 3) {
                // Mungkin dikirim nama negara, kita resolve
                $countryInfo = $this->countriesService->getCountryInfoAsync($countryCode);
                $countryCode = $countryInfo['cca2'] ?? $countryCode;
            } else {
                $countryInfo = $this->countriesService->getCountryInfoAsync($countryCode);
            }

            $officialName = $countryInfo['official_name'] ?? $countryCode;
            $coords = $this->countriesService->getCoordinatesAsync($officialName);

            // CALL REST COUNTRIES HERE to get detailed data:
            $restCountriesData = [];
            try {
                $restResponse = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://restcountries.com/v3.1/alpha/" . $countryCode);
                if ($restResponse->successful()) {
                    $restCountriesData = $restResponse->json()[0];
                }
            } catch (\Exception $e) {}

            $flagUrl = $restCountriesData['flags']['png'] ?? ($countryInfo['flag_png'] ?? null);
            $officialNameRest = $restCountriesData['name']['official'] ?? $officialName;
            $region = $restCountriesData['region'] ?? ($countryInfo['region'] ?? null);
            $subregion = $restCountriesData['subregion'] ?? null;
            $languages = isset($restCountriesData['languages']) ? implode(', ', array_values($restCountriesData['languages'])) : null;
            $population = isset($restCountriesData['population']) ? number_format($restCountriesData['population'], 0, ',', '.') : ($countryInfo['population'] ?? null);
            $latlng = $restCountriesData['latlng'] ?? [$coords['lat'] ?? 0, $coords['lon'] ?? 0];

            // Ambil pelabuhan di negara ini
            $ports = \App\Models\Port::whereHas('country', function($q) use ($countryCode, $officialName) {
                $q->where('country_code', $countryCode)
                  ->orWhere('name', 'LIKE', '%' . $officialName . '%');
            })->get(['name', 'latitude', 'longitude', 'type']);

            // 1. Info Umum
            $generalInfo = [
                'name' => $officialNameRest,
                'flag' => $flagUrl,
                'population' => $population,
                'region' => $region,
                'subregion' => $subregion,
                'languages' => $languages,
                'latlng' => $latlng,
                'ports' => $ports,
            ];

            // 2. Ekonomi (World Bank - History 5 Years)
            $economy = null;
            try {
                $economyData = $this->worldBankService->getEconomicHistoryAsync($countryCode);
                $economy = [
                    'gdp' => $economyData['gdp']['latest'] ?? 'N/A',
                    'inflation' => $economyData['inflation']['latest'] ?? 'N/A',
                    'gdp_history' => $economyData['gdp'] ?? null,
                    'inflation_history' => $economyData['inflation'] ?? null,
                ];
            } catch (\Exception $e) {
                // Ignore jika gagal
            }

            // 3. Cuaca (Open-Meteo)
            $weather = null;
            try {
                $weatherRaw = $this->weatherService->getWeatherAsync($coords['lat'] ?? 0, $coords['lon'] ?? 0);
                $weather = [
                    'temperature' => $weatherRaw['temperature'] ?? null,
                    'windspeed' => $weatherRaw['windspeed'] ?? null,
                    'rain' => $weatherRaw['rain'] ?? 0,
                ];
            } catch (\Exception $e) {
                // Ignore jika gagal
            }

            // 4. Berita dengan Sentiment Analysis (GNews)
            $newsData = [
                'logistics' => [],
                'geopolitics' => [],
                'economy' => []
            ];

            try {
                // Logistics
                $newsLogistics = $this->newsService->getLatestNewsAsync($officialName . ' shipping OR logistics');
                foreach (($newsLogistics['articles'] ?? []) as $index => $article) {
                    if ($index >= 2) break; // Ambil 2 teratas
                    $sent = $this->calculateSentiment([$article]);
                    $article['sentiment'] = $sent['label'];
                    $newsData['logistics'][] = $article;
                }

                // Geopolitics
                $newsGeo = $this->newsService->getLatestNewsAsync($officialName . ' geopolitics OR conflict');
                foreach (($newsGeo['articles'] ?? []) as $index => $article) {
                    if ($index >= 2) break; 
                    $sent = $this->calculateSentiment([$article]);
                    $article['sentiment'] = $sent['label'];
                    $newsData['geopolitics'][] = $article;
                }

                // Economy
                $newsEcon = $this->newsService->getLatestNewsAsync($officialName . ' economy OR inflation');
                foreach (($newsEcon['articles'] ?? []) as $index => $article) {
                    if ($index >= 2) break; 
                    $sent = $this->calculateSentiment([$article]);
                    $article['sentiment'] = $sent['label'];
                    $newsData['economy'][] = $article;
                }
            } catch (\Exception $e) {
                // Ignore jika GNews rate limit
            }

            return response()->json([
                'success' => true,
                'country' => $officialName,
                'general' => $generalInfo,
                'economy' => $economy,
                'weather' => $weather,
                'news' => $newsData,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}