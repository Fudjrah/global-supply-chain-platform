<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\Port;

class CountryAndPortSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to safely truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Port::truncate();
        Country::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Masukkan Data Negara Contoh
        $countries = [
            [
                'id' => 1,
                'name' => 'Indonesia',
                'country_code' => 'ID',
                'currency' => 'IDR',
                'region' => 'South-Eastern Asia',
                'language' => 'Indonesian',
                'gdp' => 1319000000000,
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
            [
                'id' => 5,
                'name' => 'China',
                'country_code' => 'CN',
                'currency' => 'CNY',
                'region' => 'Eastern Asia',
                'language' => 'Mandarin',
                'gdp' => 17960000000000,
                'inflation' => 0.7,
                'population' => 1412000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Australia',
                'country_code' => 'AU',
                'currency' => 'AUD',
                'region' => 'Oceania',
                'language' => 'English',
                'gdp' => 1675000000000,
                'inflation' => 3.6,
                'population' => 26000000,
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
                'type' => 'Container Seaport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 1, // Indonesia
                'name' => 'Port of Tanjung Perak',
                'latitude' => -7.2023,
                'longitude' => 112.7297,
                'type' => 'Commercial Seaport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 2, // United States
                'name' => 'Port of Los Angeles',
                'latitude' => 33.7423,
                'longitude' => -118.2658,
                'type' => 'Deepwater Port',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 3, // Germany
                'name' => 'Port of Hamburg',
                'latitude' => 53.5458,
                'longitude' => 9.9654,
                'type' => 'Container Terminal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 4, // Singapore
                'name' => 'Port of Singapore',
                'latitude' => 1.2644,
                'longitude' => 103.8385,
                'type' => 'Global Transshipment Hub',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 5, // China
                'name' => 'Port of Shanghai',
                'latitude' => 31.2222,
                'longitude' => 121.4900,
                'type' => 'Mega Seaport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 5, // China
                'name' => 'Port of Shenzhen',
                'latitude' => 22.5086,
                'longitude' => 113.8828,
                'type' => 'Deepwater Terminal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 6, // Australia
                'name' => 'Port of Sydney',
                'latitude' => -33.8688,
                'longitude' => 151.2093,
                'type' => 'Commercial Port',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 6, // Australia
                'name' => 'Port of Melbourne',
                'latitude' => -37.8136,
                'longitude' => 144.9631,
                'type' => 'Cargo Terminal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('ports')->insert($ports);
    }
}