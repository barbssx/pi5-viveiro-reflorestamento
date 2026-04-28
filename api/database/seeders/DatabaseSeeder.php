<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $device = Device::query()->updateOrCreate(
            ['code' => 'viveiro-a-01'],
            [
                'name' => 'Sensor Viveiro A',
                'location' => 'Viveiro principal',
                'is_active' => true,
            ]
        );

        $readings = [
            [
                'soil_moisture' => 62.35,
                'air_temperature' => 27.40,
                'air_humidity' => 78.10,
                'irrigation_status' => false,
                'collected_at' => now()->subMinutes(30),
            ],
            [
                'soil_moisture' => 41.80,
                'air_temperature' => 29.10,
                'air_humidity' => 58.20,
                'irrigation_status' => true,
                'collected_at' => now()->subMinutes(10),
            ],
        ];

        foreach ($readings as $reading) {
            SensorReading::query()->updateOrCreate(
                [
                    'device_id' => $device->id,
                    'collected_at' => $reading['collected_at'],
                ],
                [
                    ...$reading,
                    'device_id' => $device->id,
                    'source' => 'seed',
                ]
            );
        }
    }
}
