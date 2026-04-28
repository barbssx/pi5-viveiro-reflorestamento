<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_device(): void
    {
        $response = $this->postJson('/api/devices', [
            'name' => 'Sensor Viveiro A',
            'code' => 'viveiro-a-01',
            'location' => 'Talhao Norte',
            'is_active' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Sensor Viveiro A',
                'code' => 'viveiro-a-01',
            ]);

        $this->assertDatabaseHas('devices', [
            'code' => 'viveiro-a-01',
            'name' => 'Sensor Viveiro A',
        ]);
    }

    public function test_can_create_reading_using_device_code(): void
    {
        Device::create([
            'name' => 'Sensor Campo 1',
            'code' => 'campo-1',
            'location' => 'Area de plantio',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/readings', [
            'device_code' => 'campo-1',
            'soil_moisture' => 62.35,
            'air_temperature' => 27.40,
            'air_humidity' => 78.10,
            'irrigation_status' => false,
            'source' => 'esp32',
            'collected_at' => now()->toIso8601String(),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('device.code', 'campo-1')
            ->assertJsonPath('source', 'esp32');

        $this->assertDatabaseCount('sensor_readings', 1);
    }

    public function test_latest_reading_can_be_filtered_by_device_code(): void
    {
        $deviceA = Device::create([
            'name' => 'Sensor A',
            'code' => 'sensor-a',
            'location' => 'Area A',
            'is_active' => true,
        ]);

        $deviceB = Device::create([
            'name' => 'Sensor B',
            'code' => 'sensor-b',
            'location' => 'Area B',
            'is_active' => true,
        ]);

        SensorReading::create([
            'device_id' => $deviceA->id,
            'soil_moisture' => 40,
            'air_temperature' => 22,
            'air_humidity' => 75,
            'irrigation_status' => false,
            'source' => 'esp32',
            'collected_at' => now()->subMinutes(5),
        ]);

        SensorReading::create([
            'device_id' => $deviceB->id,
            'soil_moisture' => 55,
            'air_temperature' => 24,
            'air_humidity' => 70,
            'irrigation_status' => true,
            'source' => 'esp32',
            'collected_at' => now()->subMinutes(1),
        ]);

        SensorReading::create([
            'device_id' => $deviceA->id,
            'soil_moisture' => 45,
            'air_temperature' => 23,
            'air_humidity' => 72,
            'irrigation_status' => true,
            'source' => 'esp32',
            'collected_at' => now(),
        ]);

        $response = $this->getJson('/api/readings/latest?device_code=sensor-a');

        $response
            ->assertOk()
            ->assertJsonPath('device.code', 'sensor-a')
            ->assertJsonPath('soil_moisture', '45.00');
    }
}
