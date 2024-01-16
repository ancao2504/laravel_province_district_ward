<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\District;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class DistrictSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        try {
            $response = Http::withoutVerifying()->get('https://provinces.open-api.vn/api/d/');

            if($response->status() === 200) {
                $jsonData = $response->json();
                $districts = $jsonData;
                foreach ($districts as $district) {
                    District::query()->createOrFirst([
                        'name' => $district['name'],
                        'code' => $district['code'],
                        'division_type' => $district['division_type'],
                        'codename' => $district['codename'],
                        'province_code' => $district['province_code'],
                    ]);
                }
            }
        } catch (RequestException $requestException) {
            // Handle HTTP request exceptions separately, providing more details about the exception
            dd("HTTP Request Exception", ['message' => $requestException->getMessage()]);
            \Log::error('HTTP Request Exception', ['message' => $requestException->getMessage()]);
            throw $requestException;
        } catch (\Throwable $th) {
            dd("Unexpected Exception", ['message' => $th->getMessage()]);
            // Catch any other throwable exceptions and log them
            \Log::error('Unexpected Exception', ['message' => $th->getMessage()]);
            throw $th;
        }
    }
}
