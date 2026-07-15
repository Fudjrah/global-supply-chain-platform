<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WpiService;
use Illuminate\Support\Facades\Log;

class WpiProxyController extends Controller
{
    protected $wpiService;

    public function __construct(WpiService $wpiService)
    {
        $this->wpiService = $wpiService;
    }

    public function compareCountries(Request $request)
    {
        // Ambil input dari query string (hasil dari JS fetch)
        $country1 = $request->query('country1');
        $country2 = $request->query('country2');

        // Log untuk debug di storage/logs/laravel.log jika ada masalah
        Log::info("Data diterima Controller: C1=$country1, C2=$country2");

        if (!$country1 || !$country2) {
            return response()->json(['error' => 'Nama negara tidak ditemukan!'], 400);
        }

        // Panggil service untuk ambil data detail (Real-time API)
        $data1 = $this->wpiService->getCountryDetailData($country1);
        $data2 = $this->wpiService->getCountryDetailData($country2);

        // Jika salah satu data gagal diambil
        if (!$data1 || !$data2) {
            return response()->json(['error' => 'Gagal mengambil data dari API publik.'], 500);
        }

        // Kirim balik ke JS
        return response()->json([
            'country1' => $data1,
            'country2' => $data2
        ]);
    }
}