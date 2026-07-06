<?php

namespace App\Http\Controllers;

use App\Services\GNewsService;
use App\Services\WorldBankService;
use App\Services\WeatherService;
use App\Services\RestCountriesService;
use Illuminate\Http\Request;

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

   public function getRiskProfile(Request $request, string $countryName, string $countryCode)
    {
        // Validasi: Pastikan kode negara hanya 2 huruf
        if (strlen($countryCode) !== 2) {
            return response()->json(['error' => 'Format kode negara salah. Harus 2 huruf (contoh: ID, TH, SG).'], 400);
        }

        // 1. Ambil info negara (nama resmi, populasi, dll)
        $countryInfo = $this->countriesService->getCountryInfoAsync($countryCode);
        $officialName = $countryInfo['official_name'] ?? $countryName;

        // 2. Cari koordinat NYATA
        $coords = $this->countriesService->getCoordinatesAsync($officialName);

        // 3. Ambil data dari semua Service
        $weather = $this->weatherService->getWeatherAsync($coords['lat'], $coords['lon']);
        $news = $this->newsService->getLatestNewsAsync($officialName . ' shipping logistics');
        $economy = $this->worldBankService->getEconomicIndicatorsAsync($countryCode);

        // 4. Bungkus data (Hanya SATU variabel $data yang lengkap)
        $data = [
            'country' => $officialName,
            'country_info' => $countryInfo,
            'economic_indicators' => $economy,
            'recent_supply_chain_news' => $news,
            'weather' => $weather,
            'risk_profile_generated_at' => now()->toDateTimeString(),
        ];

        // 5. Kirim ke API atau View
        if ($request->has('api')) {
            return response()->json($data);
        }

        return view('risk_dashboard', compact('data'));
    
    }
    public function getRiskProfileFromForm(Request $request)
{
    // Mengambil data dari form
    $name = $request->input('name');
    $code = $request->input('code');

    // Menggunakan logika yang sudah kita buat sebelumnya
    return $this->getRiskProfile($request, $name, $code);
}
}