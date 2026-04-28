<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->decimal('soil_moisture', 5, 2)->nullable();
            $table->decimal('air_temperature', 5, 2)->nullable();
            $table->decimal('air_humidity', 5, 2)->nullable();
            $table->boolean('irrigation_status')->default(false);
            $table->timestamp('collected_at');
            $table->string('source')->default('adafruit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
