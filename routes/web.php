<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\RiskIntelligenceController;
use App\Http\Controllers\WpiProxyController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CompareController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Halaman Perbandingan & Pelabuhan
Route::get('/perbandingan', function () {
    return view('perbandingan');
});
Route::get('/pelabuhan', function () {
    return view('pelabuhan');
});

// Route Track & Form
Route::get('/risk/{name}/{code}', [RiskIntelligenceController::class, 'getRiskProfile']);
Route::get('/track', [RiskIntelligenceController::class, 'getRiskProfileFromForm']);

// Route API
Route::get('/api/ports', [PortController::class, 'apiIndex']);
Route::get('/api/currency', [CurrencyController::class, 'getCurrency']);
Route::get('/api/compare', [CompareController::class, 'compare']);
Route::get('/api/risk', [RiskIntelligenceController::class, 'apiRiskProfile']);
Route::get('/api/gdp-data', [RiskIntelligenceController::class, 'getGdpData']);
Route::get('/api/inflation-data', [RiskIntelligenceController::class, 'getInflationData']);
Route::get('/api/countries', [RiskIntelligenceController::class, 'getCountryList']);
Route::get('/api/get-countries', [RiskIntelligenceController::class, 'getCountryList']);
Route::get('/api/country-stats/{country}', [RiskIntelligenceController::class, 'getCountryStats']);

// Route Proxy WPI
Route::get('/proxy/wpi', [WpiProxyController::class, 'proxyWpi']);
Route::get('/compare-countries', [WpiProxyController::class, 'compareCountries']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
    // Rute untuk kelola User, Dataset, dan Artikel
    Route::resource('/admin/users', UserController::class);
    Route::resource('/admin/ports', PortController::class);
    Route::resource('/admin/articles', ArticleController::class);
});