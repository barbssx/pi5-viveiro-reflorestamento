<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SensorReading::query()
            ->with('device')
            ->orderByDesc('collected_at');

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->integer('device_id'));
        }

        if ($request->filled('device_code')) {
            $query->whereHas('device', function (Builder $builder) use ($request) {
                $builder->where('code', $request->string('device_code')->toString());
            });
        }

        if ($request->filled('from')) {
            $query->where('collected_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->where('collected_at', '<=', $request->date('to'));
        }

        $limit = min((int) $request->integer('limit', 50), 500);

        $readings = $query->limit($limit)->get();

        return response()->json($readings);
    }

    public function latest(Request $request): JsonResponse
    {
        $query = SensorReading::query()
            ->with('device')
            ->orderByDesc('collected_at')
            ->orderByDesc('id');

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->integer('device_id'));
        }

        if ($request->filled('device_code')) {
            $query->whereHas('device', function (Builder $builder) use ($request) {
                $builder->where('code', $request->string('device_code')->toString());
            });
        }

        $latestReadings = $query->first();

        if (! $latestReadings) {
            return response()->json([
                'message' => 'Nenhuma leitura encontrada para os filtros informados.',
            ], 404);
        }

        return response()->json($latestReadings);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'device_id' => ['nullable', 'integer', 'exists:devices,id', 'required_without:device_code'],
            'device_code' => ['nullable', 'string', 'exists:devices,code', 'required_without:device_id'],
            'soil_moisture' => ['nullable', 'numeric', 'between:0,100'],
            'air_temperature' => ['nullable', 'numeric', 'between:-40,100'],
            'air_humidity' => ['nullable', 'numeric', 'between:0,100'],
            'irrigation_status' => ['sometimes', 'boolean'],
            'source' => ['sometimes', 'string', 'max:255'],
            'collected_at' => ['required', 'date'],
        ]);

        $deviceId = $payload['device_id'] ?? Device::query()
            ->where('code', $payload['device_code'])
            ->value('id');

        $reading = SensorReading::create([
            'device_id' => $deviceId,
            'soil_moisture' => $payload['soil_moisture'] ?? null,
            'air_temperature' => $payload['air_temperature'] ?? null,
            'air_humidity' => $payload['air_humidity'] ?? null,
            'irrigation_status' => $payload['irrigation_status'] ?? false,
            'source' => $payload['source'] ?? 'adafruit',
            'collected_at' => $payload['collected_at'],
        ]);

        return response()->json($reading->load('device'), 201);
    }
}
