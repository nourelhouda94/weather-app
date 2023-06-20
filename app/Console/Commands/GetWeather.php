<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetWeather extends Command
{
    protected $signature = 'current {location?} {--units=metric}';

    protected $description = 'Get the current weather data for the given location';

    public function handle()
    {
        $location = $this->argument('location') ?? 'Santander,ES';
        $units = $this->option('units');

        $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'q' => $location,
            'units' => $units,
            'appid' => '6335631660abaed3fd3063941c7f147d',
        ]);

        $data = $response->json();

        if ($response->ok()) {
            $this->displayWeather($data);
        } else {
            $this->error($data['message']);
        }
    }

    private function displayWeather($data)
    {
        $city = $data['name'];
        $date = date('M d, Y', $data['dt']);
        $weather = $data['weather'][0]['description'];
        $temperature = $data['main']['temp'];

        $this->info("$city");
        $this->line("$date");
        $this->line("> Weather: $weather");
        $this->line("> Temperature: $temperature °" . ($data['sys']['country'] === 'US' ? 'F' : 'C'));
    }
}
