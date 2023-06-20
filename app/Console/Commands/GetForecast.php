<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetForecast extends Command
{
    protected $signature = 'forecast {location?} {--days=1} {--units=metric}';

    protected $description = 'Get the weather forecast for max 5 days for the given location';

    public function handle()
    {
        $location = $this->argument('location') ?? 'Santander,ES';
        $days = min(max(1, $this->option('days')), 5);
        $units = $this->option('units');

        $response = Http::get("http://api.openweathermap.org/data/2.5/forecast", [
            'q' => $location,
            'units' => $units,
            'appid' => '6335631660abaed3fd3063941c7f147d',
        ]);

        $data = $response->json();

        if ($response->ok()) {
            $this->displayForecast($data, $days);
        } else {
            $this->error($data['message']);
        }
    }

    private function displayForecast($data, $days)
    {
        $city = $data['city']['name'];
        $country = $data['city']['country'];

        $this->info("$city ($country)");

        $forecasts = $data['list'];

        for ($i = 0; $i < $days; $i++) {
            $forecast = $forecasts[$i];
            $date = date('M d, Y', $forecast['dt']);
            $weather = $forecast['weather'][0]['description'];
            $temperature = $forecast['main']['temp'];

            $this->line("$date");
            $this->line("> Weather: $weather");
            $this->line("> Temperature: $temperature °" . ($country === 'US' ? 'F' : 'C'));
        }
    }
}
