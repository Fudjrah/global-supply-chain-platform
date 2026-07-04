<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    /**
     * Mengambil data cuaca real-time (Wajib Live API).
     */
    public function getWeatherAsync(float $latitude, float $longitude)
    {
        $url = "https://api.open-meteo.com/v1/forecast";

        // Kita tembak langsung tanpa jaring pengaman fallback
        $response = Http::withoutVerifying()->get($url, [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current_weather' => true,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'temperature' => $data['current_weather']['temperature'] ?? null,
                'windspeed' => $data['current_weather']['windspeed'] ?? null,
                'weathercode' => $data['current_weather']['weathercode'] ?? null,
                'source' => 'Live API Resmi Open-Meteo'
            ];
        }

        return null;
    }
}