<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
Route::get('/ping', function () {
    return 'pong';
});

Route::get('/weather', [WeatherController::class, 'getWeather']);
