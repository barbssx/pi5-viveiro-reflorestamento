<?php

use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/teste', function () {
    return ['message' => 'API funcionando'];
});

Route::apiResource('devices', DeviceController::class);
Route::get('/devices/{device}/readings', [DeviceController::class, 'readings']);

Route::get('/readings', [ReadingController::class, 'index']);
Route::post('/readings', [ReadingController::class, 'store']);
Route::get('/readings/latest', [ReadingController::class, 'latest']);
