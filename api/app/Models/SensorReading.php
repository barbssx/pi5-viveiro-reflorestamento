<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'soil_moisture',
        'irrigation_status',
        'device_id',
        'collected_at',
        'air_temperature',
        'air_humidity',
    ];

    protected $casts = [
        'soil_moisture' => 'decimal:2',
        'air_temperature' => 'decimal:2',
        'air_humidity' => 'decimal:2',
        'irrigation_status' => 'boolean',
        'collected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
}
