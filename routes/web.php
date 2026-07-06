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