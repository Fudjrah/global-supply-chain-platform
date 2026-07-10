<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiskIntelligenceController;


Route::get('/risk/{name}/{code}', [RiskIntelligenceController::class, 'getRiskProfile']);
Route::get('/', function () {
    return view('welcome');
});
Route::get('/', function () {
    return view('welcome');
});
Route::get('/track', [App\Http\Controllers\RiskIntelligenceController::class, 'getRiskProfileFromForm']);
// Tambahkan route ini di paling bawah

Route::get('/api/gdp-data', [App\Http\Controllers\RiskIntelligenceController::class, 'getGdpData']);
Route::get('/api/inflation-data', [App\Http\Controllers\RiskIntelligenceController::class, 'getInflationData']);

Route::get('/api/countries', [App\Http\Controllers\RiskIntelligenceController::class, 'getCountryList']);

// Ambil daftar semua negara untuk dropdown
Route::get('/api/get-countries', [App\Http\Controllers\RiskIntelligenceController::class, 'getCountryList']);

// Ambil detail statistik negara (inflasi, populasi, mata uang)
Route::get('/api/country-stats/{country}', [App\Http\Controllers\RiskIntelligenceController::class, 'getCountryStats']);
