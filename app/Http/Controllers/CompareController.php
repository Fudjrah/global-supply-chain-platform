<?php

namespace App\Http\Controllers;

use App\Services\WorldBankService;
use App\Services\WeatherService;
use App\Services\CurrencyService;
use App\Services\RestCountriesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompareController extends Controller
{
    protected $worldBankService;
    protected $weatherService;
    protected $currencyService;
    protected $countriesService;

    public function __construct(
        WorldBankService $worldBankService,
        WeatherService $weatherService,
        CurrencyService $currencyService,
        RestCountriesService $countriesService
    ) {
        $this->worldBankService = $worldBankService;
        $this->weatherService = $weatherService;
        $this->currencyService = $currencyService;
        $this->countriesService = $countriesService;
    }

    /**
     * GET /api/compare?country1=DE&country2=AU
     * Accepts: 2-letter ISO codes OR country names (English)
     */
    public function compare(Request $request)
    {
        $input1 = trim($request->query('country1', ''));
        $input2 = trim($request->query('country2', ''));

        if (empty($input1) || empty($input2)) {
            return response()->json([
                'success' => false,
                'error' => 'Parameter "country1" dan "country2" wajib diisi (kode ISO 2 huruf atau nama negara dalam bahasa Inggris).',
            ], 400);
        }

        $data1 = $this->buildCountryData($input1);
        $data2 = $this->buildCountryData($input2);

        return response()->json([
            'success' => true,
            'country1' => $data1,
            'country2' => $data2,
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Build full comparison data for a single country (ISO code or name).
     */
    private function buildCountryData(string $input): array
    {
        // Resolve ISO code and country name from input
        [$isoCode, $countryName] = $this->resolveCountry($input);

        $result = [
            'input'        => $input,
            'iso_code'     => $isoCode,
            'country_name' => $countryName,
            'economy'      => null,
            'weather'      => null,
            'currency'     => null,
            'risk_score'   => null,
            'risk_level'   => null,
        ];

        // 1. GDP & Inflation from World Bank API
        try {
            $result['economy'] = $this->worldBankService->getEconomicIndicatorsAsync($isoCode);
        } catch (\Exception $e) {
            Log::warning("CompareController: economy fetch failed for {$isoCode}: " . $e->getMessage());
        }

        // 2. Coordinates (to fetch weather)
        $lat = 0;
        $lon = 0;
        try {
            $coords = $this->countriesService->getCoordinatesAsync($countryName);
            $lat = $coords['lat'] ?? 0;
            $lon = $coords['lon'] ?? 0;
        } catch (\Exception $e) {
            Log::warning("CompareController: coords fetch failed for {$countryName}: " . $e->getMessage());
        }

        // 3. Weather from Open-Meteo
        try {
            if ($lat !== 0 || $lon !== 0) {
                $result['weather'] = $this->weatherService->getWeatherAsync($lat, $lon);
            }
        } catch (\Exception $e) {
            Log::warning("CompareController: weather fetch failed for {$countryName}: " . $e->getMessage());
        }

        // 4. Currency from local mapping + ExchangeRate API
        try {
            $currencyInfo = $this->currencyService->getCurrencyCode($isoCode);
            if ($currencyInfo) {
                $rateInfo = $this->currencyService->getExchangeRate($currencyInfo['code']);
                $result['currency'] = [
                    'code'         => $currencyInfo['code'],
                    'name'         => $currencyInfo['name'],
                    'symbol'       => $currencyInfo['symbol'],
                    'rate_vs_usd'  => $rateInfo['rate_usd_to_currency'] ?? null,
                    'rate_to_usd'  => $rateInfo['rate_currency_to_usd'] ?? null,
                    'last_update'  => $rateInfo['last_update'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::warning("CompareController: currency fetch failed for {$isoCode}: " . $e->getMessage());
        }

        // 5. Risk Score calculation (reuse same formula as RiskIntelligenceController)
        $result['risk_score'] = $this->calculateRiskScore(
            $result['weather'],
            $result['economy'],
            $result['currency']
        );
        $result['risk_level'] = $this->getRiskLevel($result['risk_score']);

        return $result;
    }

    /**
     * Calculate composite risk score.
     * Max: 100
     */
    private function calculateRiskScore(?array $weather, ?array $economy, ?array $currency): int
    {
        $score = 0;

        // --- Economy Risk (max 40) ---
        if ($economy) {
            $inflation = (float) str_replace(['%', ',', ' '], ['', '.', ''], $economy['inflation_rate']['value'] ?? '0');
            if ($inflation > 8) $score += 40;
            elseif ($inflation > 5) $score += 30;
            elseif ($inflation > 2) $score += 15;
        }

        // --- Weather Risk (max 30) ---
        if ($weather) {
            $wind = (float)($weather['windspeed'] ?? 0);
            $wcode = (int)($weather['weathercode'] ?? 0);

            if ($wind > 50 || $wcode >= 80) $score += 30;
            elseif ($wind > 30 || $wcode >= 60) $score += 20;
            elseif ($wind > 15 || $wcode >= 30) $score += 10;
        }

        // --- Currency Volatility Risk (max 20) ---
        // A very weak currency (high units per 1 USD) suggests economic instability
        if ($currency && isset($currency['rate_vs_usd'])) {
            $rate = (float) $currency['rate_vs_usd'];
            if ($rate > 5000) $score += 20;   // e.g. IDR, VND
            elseif ($rate > 500) $score += 12;  // e.g. JPY, KRW
            elseif ($rate > 10) $score += 5;    // e.g. MXN, INR
        }

        return min($score, 100);
    }

    private function getRiskLevel(int $score): string
    {
        if ($score > 60) return 'High Risk';
        if ($score > 35) return 'Medium Risk';
        return 'Low Risk';
    }

    /**
     * Resolve input (2-char ISO or country name) → [isoCode, countryName]
     */
    private function resolveCountry(string $input): array
    {
        // Map: country name → ISO code
        $nameToIso = [
            'indonesia' => 'ID', 'germany' => 'DE', 'china' => 'CN',
            'australia' => 'AU', 'singapore' => 'SG', 'japan' => 'JP',
            'india' => 'IN', 'france' => 'FR', 'united kingdom' => 'GB', 'uk' => 'GB',
            'united states' => 'US', 'usa' => 'US', 'us' => 'US',
            'brazil' => 'BR', 'canada' => 'CA', 'russia' => 'RU',
            'south korea' => 'KR', 'korea' => 'KR', 'mexico' => 'MX',
            'turkey' => 'TR', 'italy' => 'IT', 'spain' => 'ES',
            'netherlands' => 'NL', 'sweden' => 'SE', 'norway' => 'NO',
            'denmark' => 'DK', 'switzerland' => 'CH', 'new zealand' => 'NZ',
            'south africa' => 'ZA', 'malaysia' => 'MY', 'thailand' => 'TH',
            'philippines' => 'PH', 'vietnam' => 'VN', 'pakistan' => 'PK',
            'egypt' => 'EG', 'saudi arabia' => 'SA', 'uae' => 'AE',
            'united arab emirates' => 'AE', 'hong kong' => 'HK', 'nigeria' => 'NG',
            'argentina' => 'AR', 'bangladesh' => 'BD', 'ukraine' => 'UA',
            'poland' => 'PL', 'czech republic' => 'CZ', 'romania' => 'RO',
        ];

        // Map: ISO code → country name (reverse)
        $isoToName = [
            'ID' => 'Indonesia', 'DE' => 'Germany', 'CN' => 'China',
            'AU' => 'Australia', 'SG' => 'Singapore', 'JP' => 'Japan',
            'IN' => 'India', 'FR' => 'France', 'GB' => 'United Kingdom',
            'US' => 'United States', 'BR' => 'Brazil', 'CA' => 'Canada',
            'RU' => 'Russia', 'KR' => 'South Korea', 'MX' => 'Mexico',
            'TR' => 'Turkey', 'IT' => 'Italy', 'ES' => 'Spain',
            'NL' => 'Netherlands', 'SE' => 'Sweden', 'NO' => 'Norway',
            'DK' => 'Denmark', 'CH' => 'Switzerland', 'NZ' => 'New Zealand',
            'ZA' => 'South Africa', 'MY' => 'Malaysia', 'TH' => 'Thailand',
            'PH' => 'Philippines', 'VN' => 'Vietnam', 'PK' => 'Pakistan',
            'EG' => 'Egypt', 'SA' => 'Saudi Arabia', 'AE' => 'United Arab Emirates',
            'HK' => 'Hong Kong', 'NG' => 'Nigeria', 'AR' => 'Argentina',
            'BD' => 'Bangladesh', 'UA' => 'Ukraine', 'PL' => 'Poland',
            'CZ' => 'Czech Republic', 'RO' => 'Romania',
        ];

        $upper = strtoupper(trim($input));

        // Input is 2-letter ISO code
        if (strlen($upper) === 2 && isset($isoToName[$upper])) {
            return [$upper, $isoToName[$upper]];
        }

        // Input is country name
        $lower = strtolower(trim($input));
        if (isset($nameToIso[$lower])) {
            $iso = $nameToIso[$lower];
            return [$iso, $isoToName[$iso] ?? ucwords($input)];
        }

        // Fallback: use input as-is, treat first 2 chars as ISO
        return [strtoupper(substr($input, 0, 2)), ucwords($input)];
    }
}
