<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryAndPortSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
        CountryAndPortSeeder::class,
    ]);
        // 1. Masukkan Data Negara Contoh
        $countries = [
            [
                'id' => 1,
                'name' => 'Indonesia',
                'country_code' => 'ID',
                'currency' => 'IDR',
                'region' => 'South-Eastern Asia',
                'language' => 'Indonesian',
                'gdp' => 1319000000000, // Data dummy awal
                'inflation' => 2.5,
                'population' => 277000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'United States',
                'country_code' => 'US',
                'currency' => 'USD',
                'region' => 'Northern America',
                'language' => 'English',
                'gdp' => 27360000000000,
                'inflation' => 3.1,
                'population' => 335000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Germany',
                'country_code' => 'DE',
                'currency' => 'EUR',
                'region' => 'Western Europe',
                'language' => 'German',
                'gdp' => 4456000000000,
                'inflation' => 2.2,
                'population' => 84000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Singapore',
                'country_code' => 'SG',
                'currency' => 'SGD',
                'region' => 'South-Eastern Asia',
                'language' => 'English, Malay, Mandarin, Tamil',
                'gdp' => 501000000000,
                'inflation' => 2.8,
                'population' => 6000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('countries')->insert($countries);

        // 2. Masukkan Data Pelabuhan (Sesuai ID Negaranya)
        $ports = [
            [
                'country_id' => 1, // Indonesia
                'name' => 'Tanjung Priok Port',
                'latitude' => -6.1033,
                'longitude' => 106.8792,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 2, // United States
                'name' => 'Port of Los Angeles',
                'latitude' => 33.7423,
                'longitude' => -118.2658,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 3, // Germany
                'name' => 'Port of Hamburg',
                'latitude' => 53.5458,
                'longitude' => 9.9654,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 4, // Singapore
                'name' => 'Port of Singapore',
                'latitude' => 1.2644,
                'longitude' => 103.8385,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('ports')->insert($ports);
    }
}