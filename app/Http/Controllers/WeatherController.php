<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'city' => 'sometimes|string',
            'unit' => 'sometimes|in:metric,imperial'
        ]);

        $city = $validated['city'] ?? 'Nairobi';
        $unit = $validated['unit'] ?? 'metric';
        $apiKey = env('OPENWEATHER_API_KEY');

        // Step 1: Geocoding API call - NEW
        $geocodeUrl = "http://api.openweathermap.org/geo/1.0/direct?q={$city}&limit=1&appid={$apiKey}";
        $geocodeResponse = Http::get($geocodeUrl);

        if ($geocodeResponse->failed() || empty($geocodeResponse->json())) {
            return response()->json(['error' => 'City not found'], 404);
        }

        $location = $geocodeResponse->json()[0];
        $lat = $location['lat'];
        $lon = $location['lon'];
        $exactCityName = $location['name'] . ($location['state'] ?? '' ? ', ' . $location['state'] : '');

        // Step 2: Get current weather - MODIFIED to use coordinates
        $currentWeather = $this->getCurrentWeather($lat, $lon, $unit, $apiKey);
        if (!$currentWeather) {
            return response()->json(['error' => 'Failed to fetch weather data'], 500);
        }

        // Step 3: Get forecast - MODIFIED to use coordinates
        $forecast = $this->getWeatherForecast($lat, $lon, $unit, $apiKey);

        return [
            'city' => $exactCityName, // Using the precise name from geocoding
            'unit' => $unit === 'imperial' ? 'F' : 'C',
            'temperature' => round($currentWeather['main']['temp']),
            'description' => $currentWeather['weather'][0]['description'],
            'icon' => $currentWeather['weather'][0]['icon'],
            'humidity' => $currentWeather['main']['humidity'],
            'wind_speed' => $currentWeather['wind']['speed'],
            'wind_direction' => $currentWeather['wind']['deg'] ?? null,
            'forecast' => $forecast,
        ];
    }

    // MODIFIED to use coordinates instead of city name
    protected function getCurrentWeather($lat, $lon, $unit, $apiKey)
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&units={$unit}&appid={$apiKey}";
        $response = Http::timeout(10)->get($url);
        return $response->successful() ? $response->json() : null;
    }

    // MODIFIED to use coordinates instead of city name
    protected function getWeatherForecast($lat, $lon, $unit, $apiKey)
    {
        try {
            $url = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&units={$unit}&cnt=40&appid={$apiKey}";
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                return $this->processForecastData($response->json()['list'] ?? []);
            }
        } catch (\Exception $e) {
            \Log::error("Forecast error: " . $e->getMessage());
        }
        return [];
    }

    // UNCHANGED - your existing forecast processing
    protected function processForecastData(array $forecastItems): array
    {
        $dailyData = [];
        
        foreach ($forecastItems as $item) {
            $date = Carbon::createFromTimestamp($item['dt'])->format('d M');
            
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [
                    'high' => $item['main']['temp_max'],
                    'low' => $item['main']['temp_min'],
                    'icon' => $item['weather'][0]['icon'],
                    'description' => $item['weather'][0]['description'],
                ];
            } else {
                $dailyData[$date]['high'] = max($dailyData[$date]['high'], $item['main']['temp_max']);
                $dailyData[$date]['low'] = min($dailyData[$date]['low'], $item['main']['temp_min']);
                
                // Update to midday weather if available
                $hour = Carbon::createFromTimestamp($item['dt'])->hour;
                if ($hour >= 10 && $hour <= 14) {
                    $dailyData[$date]['icon'] = $item['weather'][0]['icon'];
                    $dailyData[$date]['description'] = $item['weather'][0]['description'];
                }
            }
        }

        // Skip today and get next 3 days
        return array_map(
            fn($day) => [
                'day' => $day,
                'icon' => $dailyData[$day]['icon'],
                'high' => round($dailyData[$day]['high']),
                'low' => round($dailyData[$day]['low']),
                'description' => $dailyData[$day]['description'],
            ],
            array_slice(array_keys($dailyData), 1, 3)
        );
    }
}