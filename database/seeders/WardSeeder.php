<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ward;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class WardSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $chunkSize = 500;
        try {
            DB::beginTransaction();
            $response = \Http::withoutVerifying()->get('https://provinces.open-api.vn/api/w/');

            if ($response->status() === 200) {
                $jsonData = $response->json();
                $provinces = $jsonData;
                collect($provinces)->chunk($chunkSize)->each(function ($chunkedProvinces) {
                    // Process each chunk of provinces
                    foreach ($chunkedProvinces as $province) {
                        Ward::query()->createOrFirst([
                            'name' => $province['name'],
                            'code' => $province['code'],
                            'division_type' => $province['division_type'],
                            'codename' => $province['codename'],
                            'district_code' => $province['district_code'],
                        ]);
                    }
                });
                DB::commit(); // Commit the transaction if everything is successful
            }
        } catch (RequestException $requestException) {
            // Handle HTTP request exceptions separately, providing more details about the exception
            dd("HTTP Request Exception", ['message' => $requestException->getMessage()]);
            \Log::error('HTTP Request Exception', ['message' => $requestException->getMessage()]);
            throw $requestException;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd("Unexpected Exception", ['message' => $th->getMessage()]);
            // Catch any other throwable exceptions and log them
            \Log::error('Unexpected Exception', ['message' => $th->getMessage()]);
            throw $th;
        }
    }
}
