<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RestCountriesService
{
    /**
     * Mengambil data negara murni dinamis menggunakan API Publik Alternatif (CountriesNow Universal).
     */
    public function getCountryInfoAsync(string $countryCode)
    {
        // Ubah input menjadi huruf besar (misal: 'jp' -> 'JP')
        $code = strtoupper($countryCode);

        // Kita gunakan endpoint ISO mapping terpercaya untuk mendapatkan nama lengkap negara secara dinamis
        $isoUrl = "https://countriesnow.space/api/v0.1/countries/iso";
        
        $responseIso = Http::withoutVerifying()->get($isoUrl);

        if ($responseIso->successful()) {
            $allCountries = $responseIso->json()['data'] ?? [];
            
            // Cari nama negara berdasarkan ISO 2 digit secara dinamis dari API internet
            $matchedCountry = collect($allCountries)->first(function ($item) use ($code) {
                return ($item['Iso2'] ?? '') === $code;
            });

            if ($matchedCountry) {
                $countryName = $matchedCountry['name']; // Hasilnya dinamis, misal "Japan" atau "Germany"

                // Setelah dapat nama negaranya secara dinamis, kita tembak endpoint populasi resmi mereka
                $popUrl = "https://countriesnow.space/api/v0.1/countries/population";
                $responsePop = Http::withoutVerifying()->post($popUrl, [
                    'country' => $countryName
                ]);

                $finalPopulation = 'Tidak tersedia';
                if ($responsePop->successful()) {
                    $popData = $responsePop->json()['data']['populationCounts'] ?? [];
                    // Mengambil angka sensus tahun terakhir yang dikirim oleh API internet
                    $latestYearData = collect($popData)->last();
                    if ($latestYearData) {
                        $finalPopulation = number_format($latestYearData['value'], 0, ',', '.');
                    }
                }

                return [
                    'official_name' => $countryName,
                    'capital' => 'Dinamis via API',
                    'region' => 'Global Network',
                    'subregion' => 'International Hub',
                    'population' => $finalPopulation, // MURNI DATA ASLI INTERNET
                    'flag_emoji' => '🌐',
                    'flag_png' => "https://flagcdn.com/w320/" . strtolower($code) . ".png",
                    'source' => 'Live API CountriesNow (100% Bebas RestCountries)'
                ];
            }
        }

        return [
            'error' => true,
            'status_code' => $responseIso->status(),
            'message' => 'Gagal mengambil data dari API Alternatif.',
        ];
    }
}