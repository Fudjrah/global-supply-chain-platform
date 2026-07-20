<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Port;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PortsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $url = 'https://raw.githubusercontent.com/tayljordan/ports/main/ports.json';
        
        $this->command->info("Mengunduh dataset dari: {$url}");
        
        try {
            $response = Http::withoutVerifying()->timeout(90)->get($url);
            
            if (!$response->successful()) {
                $this->command->error("Gagal mengunduh dataset pelabuhan. Status: " . $response->status());
                return;
            }
            
            $data = $response->json();
            $ports = is_array($data['ports'] ?? null) ? $data['ports'] : (is_array($data) ? $data : []);
            
            if (empty($ports)) {
                $this->command->error("Data ports kosong di JSON dataset.");
                return;
            }
            
            $totalCount = count($ports);
            $this->command->info("Berhasil mengunduh {$totalCount} data pelabuhan. Memproses import ke DB secara optimal...");
            
            // Bersihkan tabel ports
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('ports')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Cache data negara yang sudah ada untuk mempercepat lookup
            $countriesMap = Country::pluck('id', 'name')->toArray();
            $assignedCodes = Country::pluck('country_code')->map(fn($c) => strtoupper($c))->flip()->toArray();
            
            $countryNameMapping = [
                'United States' => 'United States',
                'UK' => 'United Kingdom',
                'Great Britain' => 'United Kingdom',
                'South Korea' => 'South Korea',
                'Korea, South' => 'South Korea',
                'China, People\'s Republic of' => 'China',
            ];

            // Step 1: Pre-process untuk mengidentifikasi negara baru dan membuat datanya terlebih dahulu
            $newCountriesToInsert = [];
            foreach ($ports as $portData) {
                $rawCountryName = trim($portData['country'] ?? '');
                if (empty($rawCountryName)) continue;
                
                $countryName = $countryNameMapping[$rawCountryName] ?? $rawCountryName;
                
                if (!isset($countriesMap[$countryName]) && !isset($newCountriesToInsert[$countryName])) {
                    // Generate unique country code di memori
                    $cleanName = preg_replace('/[^A-Za-z]/', '', $countryName);
                    $codeBase = strtoupper(substr($cleanName, 0, 2));
                    if (strlen($codeBase) < 2) {
                        $codeBase = 'XX';
                    }
                    
                    $code = $codeBase;
                    $counter = 1;
                    $attempts = 0;
                    while (isset($assignedCodes[$code]) && $attempts < 100) {
                        if ($counter <= 9) {
                            $code = substr($codeBase, 0, 1) . $counter;
                            $counter++;
                        } else {
                            // Generate random 2 uppercase letters
                            $code = chr(rand(65, 90)) . chr(rand(65, 90)); 
                        }
                        $attempts++;
                    }
                    
                    // Daftarkan di cache lokal
                    $assignedCodes[$code] = true;
                    
                    $newCountriesToInsert[$countryName] = [
                        'name' => $countryName,
                        'country_code' => $code,
                        'currency' => 'USD',
                        'region' => $portData['state'] ?? 'Global',
                        'language' => 'English',
                        'gdp' => null,
                        'inflation' => null,
                        'population' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Simpan negara baru jika ada
            if (!empty($newCountriesToInsert)) {
                $this->command->info("Menambahkan " . count($newCountriesToInsert) . " negara baru...");
                Country::insert(array_values($newCountriesToInsert));
                // Reload countriesMap
                $countriesMap = Country::pluck('id', 'name')->toArray();
            }

            // Step 2: Bulk insert data ports
            $importedCount = 0;
            $chunks = array_chunk($ports, 500); // chunk lebih besar untuk speed
            
            foreach ($chunks as $chunkIndex => $chunk) {
                $insertData = [];
                
                foreach ($chunk as $portData) {
                    $rawCountryName = trim($portData['country'] ?? '');
                    if (empty($rawCountryName)) continue;
                    
                    $countryName = $countryNameMapping[$rawCountryName] ?? $rawCountryName;
                    $countryId = $countriesMap[$countryName] ?? null;
                    
                    if (!$countryId) continue;
                    
                    $portName = trim($portData['wpi_port_name'] ?? '');
                    if (empty($portName)) {
                        $portName = trim($portData['point_of_interest'] ?? 'Unknown Port');
                    }
                    
                    $latitude = floatval($portData['latitude'] ?? 0);
                    $longitude = floatval($portData['longitude'] ?? 0);
                    $portSize = trim($portData['port_size'] ?? 'Medium');
                    
                    $insertData[] = [
                        'country_id' => $countryId,
                        'name' => $portName,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'type' => $portSize . ' Seaport',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                if (!empty($insertData)) {
                    Port::insert($insertData);
                    $importedCount += count($insertData);
                }
            }
            
            $totalCountries = Country::count();
            $this->command->info("SELESAI! Berhasil mengimport {$importedCount} pelabuhan dari {$totalCountries} negara.");
            
        } catch (\Exception $e) {
            $this->command->error("Terjadi error saat import data: " . $e->getMessage());
            Log::error("PortsSeeder Error: " . $e->getMessage());
        }
    }
}
