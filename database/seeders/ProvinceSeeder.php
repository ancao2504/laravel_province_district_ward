<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class ProvinceSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        try {
            $response = \Http::withoutVerifying()->get('https://provinces.open-api.vn/api/p/');

            if ($response->status() === 200) {
                $jsonData = $response->json();
                $provinces = $jsonData;
                foreach ($provinces as $province) {
                    Province::query()->createOrFirst([
                        'name' => $province['name'],
                        'code' => $province['code'],
                        'division_type' => $province['division_type'],
                        'codename' => $province['codename'],
                        'phone_code' => $province['phone_code'],
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
