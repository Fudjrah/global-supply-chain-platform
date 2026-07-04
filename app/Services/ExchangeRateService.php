<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    /**
     * Mengambil data kurs mata uang terbaru berbasis IDR dan dikonversi ke Rupiah per mata uang asing.
     */
    public function getLatestRatesAsync()
    {
        $url = "https://open.er-api.com/v6/latest/IDR";

        $response = Http::withoutVerifying()->get($url);

        if ($response->successful()) {
            $data = $response->json();
            $rates = $data['rates'] ?? [];

            // Rumus: 1 dibagi nilai desimal untuk mendapatkan nominal Rupiah asli, lalu dibulatkan
            return [
                'base' => 'IDR',
                'last_update' => $data['time_last_update_utc'] ?? null,
                'rates' => [
                    'USD' => isset($rates['USD']) ? round(1 / $rates['USD'], 2) : null, // Harga 1 USD dalam Rupiah
                    'EUR' => isset($rates['EUR']) ? round(1 / $rates['EUR'], 2) : null, // Harga 1 EUR dalam Rupiah
                    'SGD' => isset($rates['SGD']) ? round(1 / $rates['SGD'], 2) : null, // Harga 1 SGD dalam Rupiah
                    'THB' => isset($rates['THB']) ? round(1 / $rates['THB'], 2) : null, // Harga 1 THB dalam Rupiah
                    'KRW' => isset($rates['KRW']) ? round(1 / $rates['KRW'], 2) : null, // Harga 1 KRW dalam Rupiah
                ],
                'source' => 'Live API Resmi ExchangeRate (Sudah Dikonversi)'
            ];
        }

        return null;
    }
}