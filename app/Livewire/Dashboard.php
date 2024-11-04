<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public $weatherData = [];
    public $lat = '-6.2088'; // Default to Jakarta
    public $lon = '106.8456';
    public $locationError = null;
    public $searchQuery = '';
    public $searchResults = [];
    public $isSearching = false;
    public $weatheranimation = null;
    public $chartUvindex = [];

    public function mount()
    {
        $this->loadCachedLocation();
        $this->fetchWeatherData();
        // dd($this->loadCachedLocation());
    }

    private function loadCachedLocation()
    {
        $cachedLocation = Cache::get('user_location');
        if ($cachedLocation) {
            $this->lat = $cachedLocation['lat'];
            $this->lon = $cachedLocation['lon'];
            $this->searchQuery = $cachedLocation['city'];
        }
    }

    public function fetchWeatherData()
    {
        $cacheKey = "weather_data_{$this->lat}_{$this->lon}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            $this->weatherData = $cachedData;
        } else {
            $response = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $this->lat,
                'longitude' => $this->lon,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,rain,showers,snowfall,weather_code,cloud_cover,pressure_msl,surface_pressure,wind_speed_10m,wind_direction_10m,wind_gusts_10m',
                'hourly' => 'temperature_2m,relative_humidity_2m,weather_code',
                'daily' => 'weather_code,sunrise,sunset,daylight_duration,sunshine_duration,uv_index_max,uv_index_clear_sky_max,precipitation_sum,rain_sum',
                'timezone' => 'auto'
            ]);

            $this->weatherData = $response->json();
            Cache::put($cacheKey, $this->weatherData, now()->addHour());
        }




        // Update the weather animation based on the latest weather data
        $this->weatheranimation = $this->getWeatherAnimation();
        $this->chartUvindex();
        $this->dispatch('weatherAnimationUpdated', $this->weatheranimation);
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
        $this->fetchWeatherData();
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

    public function render()
    {
        return view('livewire.dashboard');
    }

    public function getWeatherAnimation()
    {
        $weatherCode = $this->weatherData['current']['weather_code'] ?? 0;
        $isDay = $this->weatherData['current']['is_day'] ?? 1;

        if ($weatherCode === 0) {
            return $isDay ? 'sunnydaylight' : 'sunnynight';
        } elseif (in_array($weatherCode, [1, 2, 3])) {
            return $isDay ? 'cloudydaylight' : 'cloudnight';
        } elseif (in_array($weatherCode, [51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82])) {
            return $isDay ? 'raindaylight' : 'rainnight';
        } elseif (in_array($weatherCode, [95, 96, 99])) {
            return $isDay ? 'rainstormdaylight' : 'rainstormnight';
        }

        return $isDay ? 'sunnydaylight' : 'sunnynight';
    }

    public function chartUvindex()
    {
        $data  = $this->weatherData;
        $dates = $data["daily"]["time"];
        $uvIndexes = $data["daily"]["uv_index_max"];

        $formattedData = [];

        foreach ($dates as $index => $date) {
            $formattedData[] = [
                strtotime($date) * 1000, // Convert date to Unix timestamp in milliseconds
                $uvIndexes[$index]
            ];
        }

        $this->chartUvindex = $formattedData;
        $this->dispatch('chartDataUpdated', $formattedData);
    }

    public function setLocation($lat, $lon, $cityName)
    {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->locationError = null;
        $this->searchQuery = $cityName;
        $this->searchResults = [];

        Cache::put('user_location', [
            'lat' => $lat,
            'lon' => $lon,
            'city' => $cityName
        ], now()->addDays(30));

        $this->fetchWeatherData();

        // Dispatch event for map update
        $this->dispatch('locationUpdated', [
            'lat' => $lat,
            'lon' => $lon,
            'cityName' => $cityName
        ]);
    }

    public function updatedSearchQuery()
    {
        $this->searchCity();
    }


    public function calculateSunPosition()
    {
        $now = now(); // Current time
        $sunrise = isset($this->weatherData['daily']['sunrise'][0]) ? \Carbon\Carbon::parse($this->weatherData['daily']['sunrise'][0]) : null;
        $sunset = isset($this->weatherData['daily']['sunset'][0]) ? \Carbon\Carbon::parse($this->weatherData['daily']['sunset'][0]) : null;

        if (!$sunrise || !$sunset) {
            return 0; // No sunrise/sunset data available
        }

        if ($now < $sunrise || $now > $sunset) {
            return -1; // Nighttime
        }

        $totalDuration = $sunset->diffInSeconds($sunrise);
        $elapsed = $now->diffInSeconds($sunrise);
        $position = ($elapsed / $totalDuration) * 100; // Percentage of the sun's path
        return $position;
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
}
