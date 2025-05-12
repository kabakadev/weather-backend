<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use Illuminate\Http\Request; // Add this import
use Illuminate\Support\Facades\Http; // You'll also need this for the Http facade

Route::get('/', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/ping', function () {
    return 'pong';
});

Route::get('/weather', [WeatherController::class, 'getWeather']);
Route::get('/city-suggestions', function (Request $request) {
    $query = $request->query('q');
    $apiKey = env('OPENWEATHER_API_KEY');
    
    $response = Http::get("http://api.openweathermap.org/geo/1.0/direct", [
        'q' => $query,
        'limit' => 5,
        'appid' => $apiKey
    ]);
    
    if ($response->failed()) {
        return response()->json([], 200);
    }
    
    $suggestions = collect($response->json())
        ->map(function ($location) {
            $name = $location['name'];
            if (!empty($location['state'])) {
                $name .= ', ' . $location['state'];
            }
            return $name . ', ' . $location['country'];
        })
        ->toArray();
    
    return response()->json($suggestions);
});