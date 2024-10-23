<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Dashboard extends Component
{
    public $weatherData = [];
    public $lat = '-6.2088'; // Default to Jakarta
    public $lon = '106.8456';
    public $locationError = null;
    public $searchQuery = '';
    public $searchResults = [];
    public $isSearching = false;

    public function mount()
    {
        $this->fetchWeatherData();
        // dd($this->weatherData);
    }

    public function fetchWeatherData()
    {
        // Fetch weather data from Open-Meteo API
        $response = Http::get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $this->lat,
            'longitude' => $this->lon,
            'current' => 'temperature_2m,apparent_temperature,is_day',
            'hourly' => 'temperature_2m,relative_humidity_2m,weather_code',
            'daily' => 'weather_code,sunrise,sunset,daylight_duration,sunshine_duration,uv_index_max,uv_index_clear_sky_max,precipitation_sum,rain_sum',
            'timezone' => 'auto'
        ]);

        $this->weatherData = $response->json();
    }

    public function updateLocation($lat, $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->locationError = null;
        $this->fetchWeatherData();
    }

    public function setLocationError($error)
    {
        $this->locationError = $error;
        // Fetch weather data with default coordinates when there's an error
        $this->fetchWeatherData();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }

    public function getWeatherIcon($code)
    {
        $icons = [
            0 => '☀️', // Clear sky
            1 => '🌤️', // Mainly clear
            2 => '⛅', // Partly cloudy
            3 => '☁️', // Overcast
            45 => '🌫️', // Fog
            48 => '🌫️', // Depositing rime fog
            51 => '🌧️', // Light drizzle
            53 => '🌧️', // Moderate drizzle
            55 => '🌧️', // Dense drizzle
            56 => '🌧️', // Light freezing drizzle
            57 => '🌧️', // Dense freezing drizzle
            61 => '🌦️', // Slight rain
            63 => '🌧️', // Moderate rain
            65 => '🌧️', // Heavy rain
            66 => '🌧️', // Light freezing rain
            67 => '🌧️', // Heavy freezing rain
            71 => '🌨️', // Slight snow fall
            73 => '🌨️', // Moderate snow fall
            75 => '🌨️', // Heavy snow fall
            77 => '❄️', // Snow grains
            80 => '🌦️', // Slight rain showers
            81 => '🌧️', // Moderate rain showers
            82 => '🌧️', // Violent rain showers
            85 => '🌨️', // Slight snow showers
            86 => '🌨️', // Heavy snow showers
            95 => '⛈️', // Thunderstorm
            96 => '⛈️', // Thunderstorm with slight hail
            99 => '⛈️', // Thunderstorm with heavy hail
        ];

        return $icons[$code] ?? '❓';
    }

    public function updatedSearchQuery()
    {
        $this->searchCity();
    }

    public function searchCity()
    {
        $this->isSearching = true;

        if (strlen($this->searchQuery) < 3) {
            $this->searchResults = [];
            $this->isSearching = false;
            return;
        }

        $response = Http::get("https://api.opencagedata.com/geocode/v1/json", [
            'q' => $this->searchQuery . ', Indonesia',
            'key' => env('OPENCAGE_API_KEY'),
            'language' => 'en',
            'limit' => 5,
        ]);

        if ($response->successful()) {
            $this->searchResults = $response->json()['results'];
        } else {
            $this->locationError = "Error searching for cities. Please try again.";
        }

        $this->isSearching = false;
    }

    public function setLocation($lat, $lon, $cityName)
    {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->locationError = null;
        $this->searchQuery = $cityName;
        $this->searchResults = [];
        $this->fetchWeatherData();
    }
}
