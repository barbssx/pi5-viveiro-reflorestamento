<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Device::query();

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $limit = min((int) $request->integer('limit', 50), 200);

        $devices = $query
            ->withCount('sensorReadings')
            ->orderBy('name')
            ->paginate($limit);

        return response()->json($devices);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:devices,code'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $device = Device::create($payload);

        return response()->json($device, 201);
    }

    public function show(Device $device): JsonResponse
    {
        $device->loadCount('sensorReadings');

        return response()->json($device);
    }

    public function update(Request $request, Device $device): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('devices', 'code')->ignore($device->id),
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $device->update($payload);

        return response()->json($device->fresh());
    }

    public function destroy(Device $device): JsonResponse
    {
        $device->delete();

        return response()->json(null, 204);
    }

    public function readings(Request $request, Device $device): JsonResponse
    {
        $query = $device->sensorReadings()->orderByDesc('collected_at');

        if ($request->filled('from')) {
            $query->where('collected_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->where('collected_at', '<=', $request->date('to'));
        }

        $limit = min((int) $request->integer('limit', 100), 500);

        return response()->json($query->limit($limit)->get());
    }
}
