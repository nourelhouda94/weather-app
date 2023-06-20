<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getCurrentWeather($location = 'Santander,ES')
    {
        $units = request()->input('units', 'metric');

        $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'q' => $location,
            'units' => $units,
            'appid' => '6335631660abaed3fd3063941c7f147d',
        ]);

        $data = $response->json();

        if ($response->ok()) {
            return $this->formatWeatherResponse($data);
        } else {
            return response()->json(['error' => $data['message']], $response->status());
        }
    }

    public function getWeatherForecast($location = 'Santander,ES', $days = 1)
    {
        $days = min(max(1, $days), 5);
        $units = request()->input('units', 'metric');

        $response = Http::get("http://api.openweathermap.org/data/2.5/forecast", [
            'q' => $location,
            'units' => $units,
            'appid' => '6335631660abaed3fd3063941c7f147d',
        ]);

        $data = $response->json();

        if ($response->ok()) {
            return $this->formatForecastResponse($data, $days);
        } else {
            return response()->json(['error' => $data['message']], $response->status());
        }
    }

    private function formatWeatherResponse($data)
    {
        $city = $data['name'];
        $date = date('M d, Y', $data['dt']);
        $weather = $data['weather'][0]['description'];
        $temperature = $data['main']['temp'];

        return response()->json([
            'city' => $city,
            'date' => $date,
            'weather' => $weather,
            'temperature' => $temperature,
        ]);
    }

    private function formatForecastResponse($data, $days)
    {
        $city = $data['city']['name'];
        $country = $data['city']['country'];

        $forecasts = $data['list'];

        $response = [
            'city' => $city,
            'country' => $country,
            'forecasts' => [],
        ];

        for ($i = 0; $i < $days; $i++) {
            $forecast = $forecasts[$i];
            $date = date('M d, Y', $forecast['dt']);
            $weather = $forecast['weather'][0]['description'];
            $temperature = $forecast['main']['temp'];

            $response['forecasts'][] = [
                'date' => $date,
                'weather' => $weather,
                'temperature' => $temperature,
            ];
        }

        return response()->json($response);
    }
}
