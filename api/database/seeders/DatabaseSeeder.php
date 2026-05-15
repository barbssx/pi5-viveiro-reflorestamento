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
        $devices = [
            [
                'code' => 'viveiro-a-01',
                'name' => 'Sensor Viveiro A',
                'location' => 'Viveiro principal',
                'is_active' => true,
            ],
            [
                'code' => 'viveiro-b-01',
                'name' => 'Sensor Viveiro B',
                'location' => 'Setor de mudas nativas',
                'is_active' => true,
            ],
            [
                'code' => 'estufa-01',
                'name' => 'Sensor Estufa 01',
                'location' => 'Estufa de germinacao',
                'is_active' => true,
            ],
            [
                'code' => 'campo-teste-01',
                'name' => 'Sensor Campo Teste',
                'location' => 'Area externa experimental',
                'is_active' => false,
            ],
        ];

        foreach ($devices as $deviceData) {
            $device = Device::query()->updateOrCreate(
                ['code' => $deviceData['code']],
                $deviceData
            );

            $device->sensorReadings()
                ->where('source', 'seed')
                ->delete();

            foreach ($this->readingsFor($deviceData['code']) as $reading) {
                SensorReading::query()->create([
                    ...$reading,
                    'device_id' => $device->id,
                    'source' => 'seed',
                ]);
            }
        }
    }

    private function readingsFor(string $deviceCode): array
    {
        $profiles = [
            'viveiro-a-01' => [58.2, 27.1, 76.4],
            'viveiro-b-01' => [64.8, 26.5, 81.2],
            'estufa-01' => [72.5, 29.4, 85.7],
            'campo-teste-01' => [43.1, 31.2, 58.9],
        ];

        [$soilMoisture, $airTemperature, $airHumidity] = $profiles[$deviceCode];

        return collect(range(23, 0))->map(function (int $hoursAgo) use ($soilMoisture, $airTemperature, $airHumidity) {
            $variation = (($hoursAgo % 6) - 2.5) * 1.8;
            $temperatureVariation = (($hoursAgo % 8) - 3.5) * 0.55;

            return [
                'soil_moisture' => round(max(0, min(100, $soilMoisture + $variation)), 2),
                'air_temperature' => round($airTemperature + $temperatureVariation, 2),
                'air_humidity' => round(max(0, min(100, $airHumidity - ($variation / 2))), 2),
                'irrigation_status' => $soilMoisture + $variation < 50,
                'collected_at' => now()->subHours($hoursAgo),
            ];
        })->all();
    }
}
