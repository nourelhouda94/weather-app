<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('weather/current/{location?}', [WeatherController::class, 'getCurrentWeather']);
Route::get('weather/forecast/{location?}/{days?}', [WeatherController::class, 'getWeatherForecast']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
