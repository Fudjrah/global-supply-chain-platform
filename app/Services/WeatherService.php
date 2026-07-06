<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    public function getWeatherAsync(float $latitude, float $longitude)
    {
        try {
            $url = "https://api.open-meteo.com/v1/forecast";

            // Menggunakan 'current' daripada 'current_weather' (format terbaru)
            $response = Http::timeout(5)->withoutVerifying()->get($url, [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,wind_speed_10m,weather_code',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'temperature' => $data['current']['temperature_2m'] ?? null,
                    'windspeed'   => $data['current']['wind_speed_10m'] ?? null,
                    'weathercode' => $data['current']['weather_code'] ?? null,
                    'source'      => 'Live API Resmi Open-Meteo'
                ];
            }
        } catch (\Exception $e) {
            // Jika gagal, kita catat error-nya di log agar bisa dicek nanti
            Log::error("Weather API Error: " . $e->getMessage());
        }

        return null;
    }
}