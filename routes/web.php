<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiskIntelligenceController;
use App\Http\Controllers\WpiProxyController;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Halaman Perbandingan
Route::get('/perbandingan', function () {
    return view('perbandingan');
});

// Route Track & Form
Route::get('/risk/{name}/{code}', [RiskIntelligenceController::class, 'getRiskProfile']);
Route::get('/track', [RiskIntelligenceController::class, 'getRiskProfileFromForm']);

// Route API (Tetap gunakan /api/ di URL jika kamu ingin rapi, 
// tapi ingat: JavaScript harus sinkron dengan URL ini)
Route::get('/api/gdp-data', [RiskIntelligenceController::class, 'getGdpData']);
Route::get('/api/inflation-data', [RiskIntelligenceController::class, 'getInflationData']);
Route::get('/api/countries', [RiskIntelligenceController::class, 'getCountryList']);
Route::get('/api/get-countries', [RiskIntelligenceController::class, 'getCountryList']);
Route::get('/api/country-stats/{country}', [RiskIntelligenceController::class, 'getCountryStats']);

// Route Proxy WPI
Route::get('/proxy/wpi', [WpiProxyController::class, 'proxyWpi']);
Route::get('/compare-countries', [WpiProxyController::class, 'compareCountries']);

Route::get('/', function () { return view('welcome'); });
Route::get('/perbandingan', function () { return view('perbandingan'); });
Route::get('/pelabuhan', function () { return view('pelabuhan'); });
Route::get('/admin', function () { 
    return "Halaman Admin Panel Sedang Dalam Pengembangan"; 
});