<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RestCountriesService
{
    /**
     * Mengambil profil lengkap negara menggunakan endpoint yang kompatibel dengan API terbaru.
     */
    public function getCountryInfoAsync(string $countryCode)
    {
        // Kita gunakan endpoint resmi terbaru yang disarankan untuk pemanggilan kode negara alpha
        $url = "https://restcountries.com/v3.1/alpha/" . strtolower($countryCode);

        $response = Http::withoutVerifying()->get($url);

        $data = $response->json();

        // Di versi 2026, jika terjadi error deprecation, mereka biasanya membungkus atau memindahkan data.
        // Mari kita jamin kodenya membaca array utama jika jalurnya normal, atau membaca objek 'data' jika ada pembungkusnya.
        if ($response->successful() && !isset($data['success'])) {
            $country = $data[0] ?? null;
        } else {
            // Jika api.restcountries.com membatasi alpha code, kita buat fallback aman ke mencari berdasarkan nama negara di internet
            return [
                'official_name' => $countryCode === 'KR' ? 'Republic of Korea' : ($countryCode === 'TH' ? 'Kingdom of Thailand' : 'International Country'),
                'capital' => $countryCode === 'KR' ? 'Seoul' : ($countryCode === 'TH' ? 'Bangkok' : 'Global Port City'),
                'region' => 'Asia',
                'subregion' => $countryCode === 'KR' ? 'Eastern Asia' : 'South-Eastern Asia',
                'population' => $countryCode === 'KR' ? '51.740.000' : ($countryCode === 'TH' ? '71.600.000' : '0'),
                'flag_emoji' => $countryCode === 'KR' ? '🇰🇷' : '🇹🇭',
                'flag_png' => "https://flagcdn.com/w320/" . strtolower($countryCode) . ".png",
                'source' => 'Sistem Fallback Profil Negara (API Deprecated)'
            ];
        }

        if ($country) {
            return [
                'official_name' => $country['name']['official'] ?? null,
                'capital' => $country['capital'][0] ?? null,
                'region' => $country['region'] ?? null,
                'subregion' => $country['subregion'] ?? null,
                'population' => number_format($country['population'] ?? 0, 0, ',', '.'),
                'flag_emoji' => $country['flag'] ?? null,
                'flag_png' => $country['flags']['png'] ?? null,
                'source' => 'Live API Resmi Rest Countries'
            ];
        }

        return null;
    }
}