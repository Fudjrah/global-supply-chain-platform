<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Mapping ISO kode negara ke mata uang.
     * Sebagai fallback andal karena REST Countries API v3/v5 deprecated.
     */
    private array $currencyMap = [
        'ID' => ['code' => 'IDR', 'name' => 'Indonesian Rupiah',    'symbol' => 'Rp'],
        'US' => ['code' => 'USD', 'name' => 'United States Dollar', 'symbol' => '$'],
        'DE' => ['code' => 'EUR', 'name' => 'Euro',                 'symbol' => '€'],
        'FR' => ['code' => 'EUR', 'name' => 'Euro',                 'symbol' => '€'],
        'IT' => ['code' => 'EUR', 'name' => 'Euro',                 'symbol' => '€'],
        'ES' => ['code' => 'EUR', 'name' => 'Euro',                 'symbol' => '€'],
        'GB' => ['code' => 'GBP', 'name' => 'British Pound',       'symbol' => '£'],
        'JP' => ['code' => 'JPY', 'name' => 'Japanese Yen',        'symbol' => '¥'],
        'CN' => ['code' => 'CNY', 'name' => 'Chinese Yuan',        'symbol' => '¥'],
        'SG' => ['code' => 'SGD', 'name' => 'Singapore Dollar',    'symbol' => 'S$'],
        'AU' => ['code' => 'AUD', 'name' => 'Australian Dollar',   'symbol' => 'A$'],
        'IN' => ['code' => 'INR', 'name' => 'Indian Rupee',        'symbol' => '₹'],
        'KR' => ['code' => 'KRW', 'name' => 'South Korean Won',   'symbol' => '₩'],
        'BR' => ['code' => 'BRL', 'name' => 'Brazilian Real',      'symbol' => 'R$'],
        'CA' => ['code' => 'CAD', 'name' => 'Canadian Dollar',     'symbol' => 'C$'],
        'MX' => ['code' => 'MXN', 'name' => 'Mexican Peso',       'symbol' => '$'],
        'RU' => ['code' => 'RUB', 'name' => 'Russian Ruble',      'symbol' => '₽'],
        'SA' => ['code' => 'SAR', 'name' => 'Saudi Riyal',        'symbol' => '﷼'],
        'TR' => ['code' => 'TRY', 'name' => 'Turkish Lira',       'symbol' => '₺'],
        'CH' => ['code' => 'CHF', 'name' => 'Swiss Franc',        'symbol' => 'Fr'],
        'SE' => ['code' => 'SEK', 'name' => 'Swedish Krona',      'symbol' => 'kr'],
        'NO' => ['code' => 'NOK', 'name' => 'Norwegian Krone',    'symbol' => 'kr'],
        'DK' => ['code' => 'DKK', 'name' => 'Danish Krone',      'symbol' => 'kr'],
        'NZ' => ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$'],
        'ZA' => ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R'],
        'MY' => ['code' => 'MYR', 'name' => 'Malaysian Ringgit',  'symbol' => 'RM'],
        'TH' => ['code' => 'THB', 'name' => 'Thai Baht',          'symbol' => '฿'],
        'PH' => ['code' => 'PHP', 'name' => 'Philippine Peso',    'symbol' => '₱'],
        'VN' => ['code' => 'VND', 'name' => 'Vietnamese Dong',    'symbol' => '₫'],
        'PK' => ['code' => 'PKR', 'name' => 'Pakistani Rupee',    'symbol' => '₨'],
        'EG' => ['code' => 'EGP', 'name' => 'Egyptian Pound',     'symbol' => '£'],
        'AE' => ['code' => 'AED', 'name' => 'UAE Dirham',         'symbol' => 'د.إ'],
        'HK' => ['code' => 'HKD', 'name' => 'Hong Kong Dollar',   'symbol' => 'HK$'],
        'NG' => ['code' => 'NGN', 'name' => 'Nigerian Naira',     'symbol' => '₦'],
        'AR' => ['code' => 'ARS', 'name' => 'Argentine Peso',     'symbol' => '$'],
        'BD' => ['code' => 'BDT', 'name' => 'Bangladeshi Taka',   'symbol' => '৳'],
        'UA' => ['code' => 'UAH', 'name' => 'Ukrainian Hryvnia',  'symbol' => '₴'],
        'PL' => ['code' => 'PLN', 'name' => 'Polish Zloty',       'symbol' => 'zł'],
        'NL' => ['code' => 'EUR', 'name' => 'Euro',               'symbol' => '€'],
        'BE' => ['code' => 'EUR', 'name' => 'Euro',               'symbol' => '€'],
        'AT' => ['code' => 'EUR', 'name' => 'Euro',               'symbol' => '€'],
        'PT' => ['code' => 'EUR', 'name' => 'Euro',               'symbol' => '€'],
        'GR' => ['code' => 'EUR', 'name' => 'Euro',               'symbol' => '€'],
        'FI' => ['code' => 'EUR', 'name' => 'Euro',               'symbol' => '€'],
        'IE' => ['code' => 'EUR', 'name' => 'Euro',               'symbol' => '€'],
        'CZ' => ['code' => 'CZK', 'name' => 'Czech Koruna',      'symbol' => 'Kč'],
        'RO' => ['code' => 'RON', 'name' => 'Romanian Leu',      'symbol' => 'lei'],
        'HU' => ['code' => 'HUF', 'name' => 'Hungarian Forint',  'symbol' => 'Ft'],
    ];

    /**
     * Mapping nama negara (bahasa Inggris, lowercase) ke kode ISO 2.
     */
    private array $nameToIsoMap = [
        'indonesia' => 'ID',
        'germany'   => 'DE',
        'china'     => 'CN',
        'australia' => 'AU',
        'singapore' => 'SG',
        'japan'     => 'JP',
        'india'     => 'IN',
        'france'    => 'FR',
        'united kingdom' => 'GB',
        'uk'        => 'GB',
        'united states' => 'US',
        'usa'       => 'US',
        'us'        => 'US',
        'brazil'    => 'BR',
        'canada'    => 'CA',
        'russia'    => 'RU',
        'south korea' => 'KR',
        'korea'     => 'KR',
        'mexico'    => 'MX',
        'turkey'    => 'TR',
        'italy'     => 'IT',
        'spain'     => 'ES',
        'netherlands' => 'NL',
        'belgium'   => 'BE',
        'austria'   => 'AT',
        'portugal'  => 'PT',
        'greece'    => 'GR',
        'finland'   => 'FI',
        'ireland'   => 'IE',
        'sweden'    => 'SE',
        'norway'    => 'NO',
        'denmark'   => 'DK',
        'switzerland' => 'CH',
        'new zealand' => 'NZ',
        'south africa' => 'ZA',
        'malaysia'  => 'MY',
        'thailand'  => 'TH',
        'philippines' => 'PH',
        'vietnam'   => 'VN',
        'pakistan'  => 'PK',
        'egypt'     => 'EG',
        'saudi arabia' => 'SA',
        'uae'       => 'AE',
        'united arab emirates' => 'AE',
        'hong kong' => 'HK',
        'nigeria'   => 'NG',
        'argentina' => 'AR',
        'bangladesh' => 'BD',
        'ukraine'   => 'UA',
        'poland'    => 'PL',
        'czech republic' => 'CZ',
        'romania'   => 'RO',
        'hungary'   => 'HU',
    ];

    /**
     * Resolusi mata uang: dari nama negara atau kode ISO.
     */
    public function getCurrencyCode(string $countryNameOrCode): ?array
    {
        $input = trim($countryNameOrCode);
        $isoCode = null;

        // Cek apakah input adalah kode ISO 2 digit
        if (strlen($input) === 2) {
            $isoCode = strtoupper($input);
        } else {
            // Cari dari mapping nama negara
            $isoCode = $this->nameToIsoMap[strtolower($input)] ?? null;
        }

        if ($isoCode && isset($this->currencyMap[$isoCode])) {
            return $this->currencyMap[$isoCode];
        }

        // Fallback: coba via RestCountries API alternatif (mightwork)
        return $this->fetchFromRestCountries($input);
    }

    /**
     * Fallback: coba ambil dari RestCountries API yang aktif.
     */
    private function fetchFromRestCountries(string $countryName): ?array
    {
        $endpoints = [
            "https://restcountries.com/v3.1/name/" . urlencode($countryName) . "?fields=currencies",
        ];

        foreach ($endpoints as $url) {
            try {
                $response = Http::withoutVerifying()->timeout(6)->get($url);
                if ($response->successful()) {
                    $data = $response->json();
                    if (is_array($data) && !empty($data[0]['currencies'])) {
                        $currencies = $data[0]['currencies'];
                        $code = array_key_first($currencies);
                        return [
                            'code'   => $code,
                            'name'   => $currencies[$code]['name'] ?? $code,
                            'symbol' => $currencies[$code]['symbol'] ?? '',
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("CurrencyService fallback failed for {$countryName}: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Ambil nilai tukar terbaru dari open.er-api.com (USD sebagai base).
     */
    public function getExchangeRate(string $currencyCode): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(8)
                ->get("https://open.er-api.com/v6/latest/USD");

            if ($response->successful()) {
                $data = $response->json();
                if (($data['result'] ?? '') !== 'success') return null;

                $rates = $data['rates'] ?? [];
                $code = strtoupper($currencyCode);

                if (!isset($rates[$code])) return null;

                // Siapkan data sparkline: 8 mata uang utama dunia sebagai perbandingan
                $compareCurrencies = ['EUR', 'GBP', 'JPY', 'CNY', 'AUD', 'SGD', 'IDR', 'INR'];
                $sparkline = [];
                foreach ($compareCurrencies as $sym) {
                    if (isset($rates[$sym])) {
                        $sparkline[$sym] = round($rates[$sym], 4);
                    }
                }

                return [
                    'rate_usd_to_currency' => round($rates[$code], 4),       // 1 USD = X currency
                    'rate_currency_to_usd' => round(1 / $rates[$code], 6),   // 1 currency = X USD
                    'last_update'          => $data['time_last_update_utc'] ?? null,
                    'sparkline'            => $sparkline,
                ];
            }
        } catch (\Exception $e) {
            Log::error("CurrencyService: Exchange rate fetch failed for {$currencyCode}: " . $e->getMessage());
        }

        return null;
    }
}
