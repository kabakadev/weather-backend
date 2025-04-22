<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $city = $request->query('city', 'Nairobi');
        $unit = $request->query('unit','metric'); // defaults to °C when no units are provided
        $apiKey = env('OPENWEATHER_API_KEY');
        $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&&units=$unit&appid=$apiKey";

        $response = Http::get($url);

        return $response->json();
    }
}
