<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'location',
        'name',
        'code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class, 'device_id', 'id');
    }
}
