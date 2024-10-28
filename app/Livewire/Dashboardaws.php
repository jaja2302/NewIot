<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WeatherStation;
use App\Models\Weatherstationdata;
use Carbon\Carbon;

class Dashboardaws extends Component
{
    public $list_station;
    public $weather_data;
    public $selectedstation = 10; // Add default station ID
    public $tempChartData;
    public $rainChartData;
    public $selectedDate; // Remove the initialization here

    public function mount()
    {
        // $this->selectedDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $this->selectedDate = '2024-10-18';
        $this->list_station = WeatherStation::all();
        $this->getLatestData($this->selectedstation);
        $this->generateChartData($this->selectedstation);
    }

    public function render()
    {
        return view('livewire.dashboardaws');
    }

    public function updateSelectedStation($station_id)
    {
        $this->selectedstation = $station_id;
        $this->getLatestData($station_id);
        $this->generateChartData($station_id);
    }

    private function generateChartData($station_id)
    {
        // Get data for the selected date with proper time formatting
        $historical_data = Weatherstationdata::where('idws', $station_id)
            ->whereDate('date', $this->selectedDate)
            ->orderBy('date', 'asc')
            ->get();

        $temp_data = [];
        $rain_data = [];

        foreach ($historical_data as $data) {
            // Convert to UTC timestamp in milliseconds
            $timestamp = strtotime($data->date) * 1000;

            // Temperature data
            if ($data->temp_out !== null) {
                $temp_data[] = [
                    $timestamp,
                    round((float)$data->temp_out, 1)
                ];
            }

            // Rainfall data
            if ($data->rain_rate !== null) {
                $rain_data[] = [
                    $timestamp,
                    round((float)$data->rain_rate, 2)
                ];
            }
        }

        $this->tempChartData = $temp_data;
        $this->rainChartData = $rain_data;

        // Emit event with new data
        $this->dispatch('chartDataUpdated', [
            'tempData' => $temp_data,
            'rainData' => $rain_data
        ]);
    }

    private function getLatestData($id)
    {
        // Get latest record
        $latest_data = Weatherstationdata::where('idws', $id)
            ->latest('date')
            ->first();

        // Get today's data for calculations
        $today_data = Weatherstationdata::where('idws', $id)
            ->whereDate('date', $this->selectedDate)
            ->get();

        if (!$latest_data) {
            $this->setDefaultWeatherData();
            return;
        }

        // Calculate daily statistics
        $daily_stats = [
            'max_temp' => $today_data->max('temp_out'),
            'min_temp' => $today_data->min('temp_out'),
            'max_wind' => $today_data->max('windspeedkmh'),
            'total_rain' => $today_data->sum('rain_rate'),
            'avg_pressure' => $today_data->avg('air_press_rel'),
            'max_uv' => $today_data->max('uv'),
            'avg_solar' => $today_data->avg('solar_radiation'),
        ];

        // Get weekly and monthly rain data from latest record
        $weekly_rain = $latest_data->weeklyrainmm ?? 0;
        $monthly_rain = $latest_data->monthlyrainmm ?? 0;

        $this->weather_data = [
            'temperature' => [
                'current' => $latest_data->temp_out ?? 'N/A',
                'indoor' => $latest_data->temp_in ?? 'N/A',
                'humidity' => $latest_data->hum_out ?? 'N/A',
                'pressure' => number_format($latest_data->air_press_rel, 1) ?? 'N/A', // Using latest pressure
                'condition' => $this->getWeatherCondition($latest_data),
                'max' => number_format($daily_stats['max_temp'], 1),
                'min' => number_format($daily_stats['min_temp'], 1)
            ],
            'wind' => [
                'speed' => $latest_data->windspeedkmh ?? 'N/A',
                'direction' => $this->getWindDirection($latest_data->winddir ?? ''),
                'gust' => number_format($daily_stats['max_wind'], 1) ?? 'N/A'
            ],
            'uv' => [
                'value' => $latest_data->uv ?? 'N/A', // Current UV
                'level' => $this->getUVLevel($latest_data->uv ?? 0),
                'description' => $this->getUVDescription($latest_data->uv ?? 0),
                'max_today' => number_format($daily_stats['max_uv'], 1)
            ],
            'rain' => [
                'rate' => $latest_data->rain_rate ?? '0', // Current rain rate
                'today' => number_format($daily_stats['total_rain'], 1) ?? '0',
                'weekly' => number_format($weekly_rain, 1) ?? '0',
                'monthly' => number_format($monthly_rain, 1) ?? '0'
            ],
            'solar' => [
                'radiation' => number_format($latest_data->solar_radiation, 1) ?? 'N/A', // Current radiation
                'battery' => $latest_data->wh65batt ?? 'N/A',
                'avg_today' => number_format($daily_stats['avg_solar'], 1)
            ]
        ];
    }

    private function setDefaultWeatherData()
    {
        $this->weather_data = [
            'temperature' => [
                'current' => 'N/A',
                'indoor' => 'N/A',
                'humidity' => 'N/A',
                'pressure' => 'N/A',
                'condition' => 'N/A',
                'max' => 'N/A',
                'min' => 'N/A'
            ],
            'wind' => [
                'speed' => 'N/A',
                'direction' => 'N/A',
                'gust' => 'N/A'
            ],
            'uv' => [
                'value' => 'N/A',
                'level' => 'N/A',
                'description' => 'N/A',
                'max_today' => 'N/A'
            ],
            'rain' => [
                'rate' => '0',
                'today' => '0',
                'weekly' => '0',
                'monthly' => '0'
            ],
            'solar' => [
                'radiation' => 'N/A',
                'battery' => 'N/A',
                'avg_today' => 'N/A'
            ]
        ];
    }

    private function getWindDirection($dir)
    {
        $directions = [
            'N' => 'North',
            'NE' => 'Northeast',
            'E' => 'East',
            'SE' => 'Southeast',
            'S' => 'South',
            'SW' => 'Southwest',
            'W' => 'West',
            'NW' => 'Northwest'
        ];

        return $directions[$dir] ?? $dir;
    }

    private function getUVLevel($uvIndex)
    {
        if ($uvIndex <= 2) return 'Low';
        if ($uvIndex <= 5) return 'Moderate';
        if ($uvIndex <= 7) return 'High';
        return 'Very High';
    }

    private function getUVDescription($uvIndex)
    {
        if ($uvIndex <= 2) return 'Low risk from UV rays';
        if ($uvIndex <= 5) return 'Moderate risk from UV rays';
        if ($uvIndex <= 7) return 'High risk from UV rays';
        return 'Very high risk from UV rays';
    }

    private function getWeatherCondition($data)
    {
        // You can implement your own logic here based on temperature, humidity, etc.
        return 'Partly Cloudy'; // Placeholder
    }
}
