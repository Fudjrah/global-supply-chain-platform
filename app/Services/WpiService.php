<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WpiService
{
    // Mengambil daftar negara untuk Dropdown secara live
    public function getCountryListFromApi()
    {
        try {
            $response = Http::get('https://restcountries.com/v3.1/all');
            if ($response->successful()) {
                return collect($response->json())
                    ->pluck('name.common')
                    ->sort()
                    ->values()
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::error('Gagal ambil list negara: ' . $e->getMessage());
        }

        return ['Indonesia', 'Malaysia', 'Singapore', 'Thailand', 'Vietnam']; // Fallback aman
    }

    // Mengambil seluruh data detail secara Real-Time (Maps, Kurs, GDP, Bahasa, Pelabuhan)
    public function getCountryDetailData($countryName)
    {
        try {
            // A. Ambil Data Dasar (Bahasa, Maps, Kurs Code) dari RestCountries API
            $countryRes = Http::get("https://restcountries.com/v3.1/name/" . urlencode($countryName));
            if (!$countryRes->successful()) return null;

            $countryData = $countryRes->json()[0];
            $cca2 = $countryData['cca2'] ?? 'ID';
            $currencyCode = array_key_first($countryData['currencies'] ?? ['USD' => []]);
            $languageName = array_values($countryData['languages'] ?? ['ind' => 'Indonesian'])[0];

            // B. Ambil Data KURS secara Real-Time terhadap USD
            $rateRes = Http::get("https://open.er-api.com/v6/latest/USD");
            $rates = $rateRes->successful() ? $rateRes->json()['rates'] : [];
            $kursValue = $rates[$currencyCode] ?? 'N/A';

            // C. Ambil Data GDP secara Real-Time dari World Bank API
            $gdpRes = Http::get("https://api.worldbank.org/v2/country/{$cca2}/indicator/NY.GDP.MKTP.CD?format=json");
            $gdpJson = $gdpRes->successful() ? $gdpRes->json() : null;
            $gdpValue = isset($gdpJson[1][0]['value']) 
                ? '$' . number_format($gdpJson[1][0]['value'] / 1000000000, 2) . " Billion" 
                : 'N/A';

            // D. Ambil Data Pelabuhan dari Open Repository (Anti Blokir NGA 403)
            $portRes = Http::get("https://raw.githubusercontent.com/datasets/port-codes/master/data/port-codes.json");
            $allPorts = $portRes->successful() ? $portRes->json() : [];
            
            // Filter pelabuhan berdasarkan kode negara (ambil maksimal 4 pelabuhan utama)
            $ports = collect($allPorts)
                ->where('country', $cca2)
                ->pluck('name')
                ->take(4)
                ->values()
                ->toArray();

            // Jika data pelabuhan CDN kosong, beri fallback pelabuhan utama agar UI tetap cantik
            if (empty($ports)) {
                $ports = $cca2 === 'ID' ? ['Tanjung Priok', 'Tanjung Perak', 'Belawan'] : 
                        ($cca2 === 'MY' ? ['Port Klang', 'Penang Port', 'Johor Port'] : ['Main Strategic Port']);
            }

            return [
                'name' => $countryData['name']['common'] ?? $countryName,
                'flag' => $countryData['flags']['png'] ?? '',
                'lat' => $countryData['latlng'][0] ?? 0,
                'lon' => $countryData['latlng'][1] ?? 0,
                'bahasa' => $languageName,
                'kurs' => "1 USD = {$kursValue} {$currencyCode}",
                'gdp' => $gdpValue,
                'ports' => $ports
            ];

        } catch (\Exception $e) {
            Log::error("Gagal memproses data negara {$countryName}: " . $e->getMessage());
            return null;
        }
    }
}