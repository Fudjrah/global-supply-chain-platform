<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * GET /api/currency?country={nama_negara}
     * Returns: country, currency_code, currency_name, currency_symbol, rate_vs_usd, last_update
     */
    public function getCurrency(Request $request)
    {
        $countryName = trim($request->query('country', ''));

        if (empty($countryName)) {
            return response()->json([
                'success' => false,
                'error' => 'Parameter "country" wajib diisi.',
            ], 400);
        }

        // Step 1: Ambil kode mata uang dari REST Countries API
        $currencyInfo = $this->currencyService->getCurrencyCode($countryName);

        if (!$currencyInfo) {
            return response()->json([
                'success' => false,
                'error' => "Tidak dapat menemukan data mata uang untuk negara \"{$countryName}\". Pastikan nama negara dalam bahasa Inggris (contoh: Indonesia, Germany, China).",
            ], 404);
        }

        // Step 2: Ambil nilai tukar terhadap USD
        $rateInfo = $this->currencyService->getExchangeRate($currencyInfo['code']);

        if (!$rateInfo) {
            return response()->json([
                'success' => false,
                'currency_code' => $currencyInfo['code'],
                'currency_name' => $currencyInfo['name'],
                'currency_symbol' => $currencyInfo['symbol'],
                'error' => "Data mata uang ditemukan ({$currencyInfo['code']}), namun nilai tukar sedang tidak tersedia dari ExchangeRate API.",
            ], 503);
        }

        // Siapkan data sparkline: 7 major currencies sebagai perbandingan visual mini
        $sparklineRates = [];
        $compareCurrencies = ['EUR', 'GBP', 'JPY', 'CNY', 'AUD', 'SGD', 'IDR'];
        foreach ($compareCurrencies as $sym) {
            if (isset($rateInfo['all_rates'][$sym])) {
                $sparklineRates[$sym] = round($rateInfo['all_rates'][$sym], 4);
            }
        }

        return response()->json([
            'success'          => true,
            'country'          => $countryName,
            'currency_code'    => $currencyInfo['code'],
            'currency_name'    => $currencyInfo['name'],
            'currency_symbol'  => $currencyInfo['symbol'],
            'rate_vs_usd'      => $rateInfo['rate_usd_to_currency'],      // 1 USD = X mata uang ini
            'rate_to_usd'      => $rateInfo['rate_currency_to_usd'],      // 1 mata uang ini = X USD
            'last_update'      => $rateInfo['last_update'],
            'sparkline_vs_usd' => $rateInfo['sparkline'],                  // data komparasi mini chart
        ]);
    }
}
