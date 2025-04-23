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

        // Get current weather
        $currentData = $this->getCurrentWeather($city, $unit, $apiKey);
        if (!$currentData) {
            return response()->json(['error' => 'Failed to fetch current weather data'], 500);
        }

        // Get forecast (fails gracefully if unavailable)
        $forecast = $this->getWeatherForecast($city, $unit, $apiKey);

        return [
            'city' => $currentData['name'],
            'unit' => $unit === 'imperial' ? 'F' : 'C',
            'temperature' => round($currentData['main']['temp']),
            'description' => $currentData['weather'][0]['description'],
            'icon' => $currentData['weather'][0]['icon'],
            'humidity' => $currentData['main']['humidity'],
            'wind_speed' => $currentData['wind']['speed'],
            'forecast' => $forecast,
        ];
    }

    protected function getCurrentWeather($city, $unit, $apiKey)
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&units={$unit}&appid={$apiKey}";
        $response = Http::timeout(10)->get($url);
        
        return $response->successful() ? $response->json() : null;
    }

    protected function getWeatherForecast($city, $unit, $apiKey)
    {
        try {
            $url = "https://api.openweathermap.org/data/2.5/forecast?q={$city}&units={$unit}&cnt=40&appid={$apiKey}";
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                return $this->processForecastData($response->json()['list'] ?? []);
            }
        } catch (\Exception $e) {
            \Log::error("Forecast error: " . $e->getMessage());
        }
        
        return [];
    }

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