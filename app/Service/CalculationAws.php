<?php

namespace App\Service;

use App\Models\Weatherstationdata;
use Carbon\Carbon;

class CalculationAws
{
    // Weather Condition Calculations
    public function getWeatherCondition($data)
    {
        if (!$data) {
            return 'Unknown';
        }

        if ($data->rain_rate > 0) {
            if ($data->rain_rate >= 7.6) {
                return 'Hujan Lebat';
            } elseif ($data->rain_rate >= 2.5) {
                return 'Hujan Sedang';
            } else {
                return 'Hujan Ringan';
            }
        }

        $solar_radiation = (float)$data->solar_radiation;
        $humidity = (float)$data->hum_out;

        if ($solar_radiation > 600) {
            return 'Cerah';
        } elseif ($solar_radiation > 200) {
            if ($humidity >= 85) {
                return 'Berawan';
            }
            return 'Cerah Berawan';
        } elseif ($humidity >= 90) {
            return 'Berkabut';
        } else {
            return 'Berawan';
        }
    }

    // Weather Animation
    public function getWeatherAnimation($weatherData)
    {
        $rainRate = (float)$weatherData['rain']['rate'] ?? 0;
        $solarRadiation = (float)$weatherData['solar']['radiation'] ?? 0;
        $humidity = (float)$weatherData['temperature']['humidity'] ?? 0;
        $condition = $weatherData['temperature']['condition'] ?? '';

        $isDay = $solarRadiation > 0;

        if ($rainRate > 0) {
            if ($rainRate >= 7.6) {
                return $isDay ? 'rainstormdaylight' : 'rainstormnight';
            } elseif ($rainRate >= 2.5) {
                return $isDay ? 'raindaylight' : 'rainnight';
            } else {
                return $isDay ? 'raindaylight' : 'rainnight';
            }
        }

        if ($condition === 'Cerah') {
            return $isDay ? 'sunnydaylight' : 'sunnynight';
        } elseif ($condition === 'Cerah Berawan') {
            return $isDay ? 'cloudydaylight' : 'cloudnight';
        } elseif (in_array($condition, ['Berawan', 'Berkabut'])) {
            return $isDay ? 'cloudydaylight' : 'cloudnight';
        }

        return $isDay ? 'sunnydaylight' : 'sunnynight';
    }

    // Comfort Calculations
    public function calculateComfortLevel($temperature, $humidity)
    {
        $discomfortIndex = ($temperature * 1.8 + 32) - (0.55 - 0.0055 * $humidity) * (($temperature * 1.8 + 32) - 58);

        if ($discomfortIndex < 70) {
            return [
                'label' => 'Comfortable',
                'icon' => '😊',
                'color' => 'text-green-500'
            ];
        } elseif ($discomfortIndex < 80) {
            return [
                'label' => 'Slightly Warm',
                'icon' => '😐',
                'color' => 'text-yellow-500'
            ];
        } else {
            return [
                'label' => 'Uncomfortable',
                'icon' => '😓',
                'color' => 'text-red-500'
            ];
        }
    }

    public function calculateDewPoint($temperature, $humidity)
    {
        $a = 17.27;
        $b = 237.7;

        $alpha = (($a * $temperature) / ($b + $temperature)) + log($humidity / 100);
        return ($b * $alpha) / ($a - $alpha);
    }

    public function calculateHeatIndex($temperature, $humidity)
    {
        $tempF = ($temperature * 9 / 5) + 32;
        $heatIndexF = 0.5 * ($tempF + 61.0 + (($tempF - 68.0) * 1.2) + ($humidity * 0.094));

        if ($tempF >= 80) {
            $heatIndexF = -42.379 + (2.04901523 * $tempF) + (10.14333127 * $humidity)
                - (0.22475541 * $tempF * $humidity) - (6.83783 * pow(10, -3) * $tempF * $tempF)
                - (5.481717 * pow(10, -2) * $humidity * $humidity)
                + (1.22874 * pow(10, -3) * $tempF * $tempF * $humidity)
                + (8.5282 * pow(10, -4) * $tempF * $humidity * $humidity)
                - (1.99 * pow(10, -6) * $tempF * $tempF * $humidity * $humidity);
        }

        return ($heatIndexF - 32) * 5 / 9;
    }

    public function getWindDirection($dir)
    {
        $directions = [
            'N' => 'Utara',
            'NE' => 'Timur Laut',
            'E' => 'Timur',
            'SE' => 'Tenggara',
            'S' => 'Selatan',
            'SW' => 'Barat Daya',
            'W' => 'Barat',
            'NW' => 'Barat Laut'
        ];

        return $directions[$dir] ?? $dir;
    }

    public function getUVLevel($uvIndex)
    {
        if ($uvIndex <= 2) return 'Low';
        if ($uvIndex <= 5) return 'Moderate';
        if ($uvIndex <= 7) return 'High';
        return 'Very High';
    }

    public function getUVDescription($uvIndex)
    {
        if ($uvIndex <= 2) return 'Risiko rendah dari sinar UV';
        if ($uvIndex <= 5) return 'Risiko sedang dari sinar UV';
        if ($uvIndex <= 7) return 'Risiko tinggi dari sinar UV';
        return 'Risiko sangat tinggi dari sinar UV';
    }

    public function setDefaultWeatherData()
    {
        return [
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

    public function weatherData($id, $selectedDate)
    {
        $latest_data = Weatherstationdata::where('idws', $id)
            ->latest('date')
            ->first();
        // dd($latest_data);
        // Get today's data for calculations
        $today_data = Weatherstationdata::where('idws', $id)
            ->whereDate('date', $selectedDate)
            ->get();

        if (!$latest_data) {
            return $this->setDefaultWeatherData();
        }
        // dd($latest_data);
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
        return [
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


    public function fetchData($id, $type)
    {
        switch ($type) {
            case 'latest':
                return Weatherstationdata::where('idws', $id)
                    ->latest('date')
                    ->first();
            case 'today':
                return Weatherstationdata::where('idws', $id)
                    ->whereDate('date', Carbon::today())
                    ->get();
            case 'five_days_ahead':
                return Weatherstationdata::where('idws', $id)
                    ->whereDate('date', '>=', Carbon::today())
                    ->whereDate('date', '<=', Carbon::today()->addDays(5))
                    ->get();
        }
    }


    public function averagedata($data)
    {
        $avarage_data  = [];
        foreach ($data as $key => $value) {
            $data_count = count($value);
            $windspeedkmhp = 0;
            $winddir_sum = 0;
            $rain_rate_sum = 0;
            $rain_today_sum = 0;
            $temp_in_sum = 0;
            $temp_out_sum = 0;
            $hum_in_sum = 0;
            $hum_out_sum = 0;
            $uv_sum = 0;
            $wind_gust_sum = 0;
            $air_press_rel_sum = 0;
            $air_press_abs_sum = 0;
            $solar_radiation_sum = 0;

            foreach ($value as $key1 => $value1) {
                $windspeedkmhp += $value1['windspeedkmh'] ?? 0;
                $winddir_sum += $value1['winddir'] ?? 0;
                $rain_rate_sum += $value1['rain_rate'] ?? 0;
                if ($value1['rain_today'] == null) {
                    $rain_today = 0;
                } else {
                    $rain_today = $value1['rain_today'];
                }
                $rain_today_sum += $rain_today;
                $temp_in_sum += $value1['temp_in'] ?? 0;
                $temp_out_sum += $value1['temp_out'] ?? 0;
                $hum_in_sum += $value1['hum_in'] ?? 0;
                $hum_out_sum += $value1['hum_out'] ?? 0;
                $uv_sum += $value1['uv'] ?? 0;
                $wind_gust_sum += $value1['wind_gust'] ?? 0;
                $air_press_rel_sum += $value1['air_press_rel'] ?? 0;
                $air_press_abs_sum += $value1['air_press_abs'] ?? 0;
                $solar_radiation_sum += $value1['solar_radiation'] ?? 0;
            }

            $avarage_data[$key]['Date'] = $key;
            $avarage_data[$key]['windspeedkmh'] = $windspeedkmhp / $data_count;
            $avarage_data[$key]['winddir'] = $winddir_sum / $data_count;
            $avarage_data[$key]['rain_rate'] = $rain_rate_sum / $data_count;
            $avarage_data[$key]['rain_today'] = $rain_today_sum / $data_count;
            $avarage_data[$key]['temp_in'] = $temp_in_sum / $data_count;
            $avarage_data[$key]['temp_out'] = $temp_out_sum / $data_count;
            $avarage_data[$key]['hum_in'] = $hum_in_sum / $data_count;
            $avarage_data[$key]['hum_out'] = $hum_out_sum / $data_count;
            $avarage_data[$key]['uv'] = $uv_sum / $data_count;
            $avarage_data[$key]['wind_gust'] = $wind_gust_sum / $data_count;
            $avarage_data[$key]['air_press_rel'] = $air_press_rel_sum / $data_count;
            $avarage_data[$key]['air_press_abs'] = $air_press_abs_sum / $data_count;
            $avarage_data[$key]['solar_radiation'] = $solar_radiation_sum / $data_count;
        }
        // Implement your logic to calculate average data
        // For example, you might want to average the values for each key
        return $avarage_data;
    }


    public function avarage_year($avarage_data)
    {
        $avarage_year = [];
        $windspeedkmhp = 0;
        $winddir_sum = 0;
        $rain_rate_sum = 0;
        $rain_today_sum = 0;
        $temp_in_sum = 0;
        $temp_out_sum = 0;
        $hum_in_sum = 0;
        $hum_out_sum = 0;
        $uv_sum = 0;
        $wind_gust_sum = 0;
        $air_press_rel_sum = 0;
        $air_press_abs_sum = 0;
        $solar_radiation_sum = 0;
        $data_count = count($avarage_data);
        foreach ($avarage_data as $key => $value) {
            $windspeedkmhp += $value['windspeedkmh'];
            $winddir_sum += $value['winddir'];
            $rain_rate_sum += $value['rain_rate'];
            $rain_today_sum += $value['rain_today'];
            $temp_in_sum += $value['temp_in'];
            $temp_out_sum += $value['temp_out'];
            $hum_in_sum += $value['hum_in'];
            $hum_out_sum += $value['hum_out'];
            $uv_sum += $value['uv'];
            $wind_gust_sum += $value['wind_gust'];
            $air_press_rel_sum += $value['air_press_rel'];
            $air_press_abs_sum += $value['air_press_abs'];
            $solar_radiation_sum += $value['solar_radiation'];
        }

        $avarage_year[0]['date'] = 'Rata-rata dalam setahun';
        $avarage_year[0]['windspeedkmh'] = $windspeedkmhp / $data_count;
        $avarage_year[0]['winddir'] = $winddir_sum / $data_count;
        $avarage_year[0]['rain_rate'] = $rain_rate_sum / $data_count;
        $avarage_year[0]['rain_today'] = $rain_today_sum / $data_count;
        $avarage_year[0]['temp_in'] = $temp_in_sum / $data_count;
        $avarage_year[0]['temp_out'] = $temp_out_sum / $data_count;
        $avarage_year[0]['hum_in'] = $hum_in_sum / $data_count;
        $avarage_year[0]['hum_out'] = $hum_out_sum / $data_count;
        $avarage_year[0]['uv'] = $uv_sum / $data_count;
        $avarage_year[0]['wind_gust'] = $wind_gust_sum / $data_count;
        $avarage_year[0]['air_press_rel'] = $air_press_rel_sum / $data_count;
        $avarage_year[0]['air_press_abs'] = $air_press_abs_sum / $data_count;
        $avarage_year[0]['solar_radiation'] = $solar_radiation_sum / $data_count;

        return $avarage_year;
    }


    public function dataharian($state)
    {
        $data_harian = Weatherstationdata::where('idws', $this->selectedstation)
            ->where('date', 'like', '%' . $state['year_month'] . '%')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function ($item) {
                // Group by the date part in UTC
                return Carbon::parse($item->date)->isoFormat('D MMMM YYYY');
            });

        $data_harian = json_decode(json_encode($data_harian), true);



        $data_harian = $this->getavaragebyfiltermonth($data_harian);

        // dd($data_harian);

        return $data_harian;
    }

    public function dataperjam($state)
    {
        $data_harian = Weatherstationdata::select([
            '*',
            DB::raw("DATE_FORMAT(date, '%y-%m-%d') as Tanggal"),
            DB::raw("DATE_FORMAT(date, '%H') as Jam")
        ])
            ->where('idws', $this->selectedstation)
            ->where('date', 'like', '%' . $state['year_month'] . '%')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(['Tanggal', 'Jam']);
        // dd($data_harian);

        $data_harian = json_decode(json_encode($data_harian), true);
        //   
        // dd($data_harian);
        $data = [];
        foreach ($data_harian as $date => $hours) {
            $rain_today = 0;
            foreach ($hours as $hour => $records) {
                $avarage = count($records);
                $windspeedkmh = 0;
                $winddir_sum = 0;
                $rain_rate_sum = 0;
                $temp_in_sum = 0;
                $temp_out_sum = 0;
                $hum_in_sum = 0;
                $hum_out_sum = 0;
                $uv_sum = 0;
                $wind_gust_sum = 0;
                $air_press_rel_sum = 0;
                $air_press_abs_sum = 0;
                $solar_radiation_sum = 0;
                $dailyrainmm = 0;
                foreach ($records as $key => $value) {
                    $windspeedkmh += $value['windspeedkmh'];
                    $winddir_sum += $value['winddir'];
                    $rain_rate_sum += $value['rain_rate'];
                    $temp_in_sum += $value['temp_in'];
                    $temp_out_sum += $value['temp_out'];
                    $hum_in_sum += $value['hum_in'];
                    $hum_out_sum += $value['hum_out'];
                    $uv_sum += $value['uv'];
                    $wind_gust_sum += $value['wind_gust'];
                    $air_press_rel_sum += $value['air_press_rel'];
                    $air_press_abs_sum += $value['air_press_abs'];
                    $solar_radiation_sum += $value['solar_radiation'];
                    $dailyrainmm += $value['dailyrainmm'];
                }
                $data[$date]['Jam ke-' . $hour . ':00']['windspeedkmh'] = $windspeedkmh / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['winddir'] = $winddir_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['rain_rate'] = $rain_rate_sum;
                $data[$date]['Jam ke-' . $hour . ':00']['rain_today'] = $rain_today += $rain_rate_sum;
                $data[$date]['Jam ke-' . $hour . ':00']['temp_in'] = $temp_in_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['temp_out'] = $temp_out_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['hum_in'] = $hum_in_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['hum_out'] = $hum_out_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['uv'] = $uv_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['wind_gust'] = $wind_gust_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['air_press_rel'] = $air_press_rel_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['air_press_abs'] = $air_press_abs_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['solar_radiation'] = $solar_radiation_sum / $avarage;
                $data[$date]['Jam ke-' . $hour . ':00']['dailyrainmm'] = $dailyrainmm;
            }
        }
        // dd($data);
        return $data;
    }


    public function getavaragebyfiltermonth($data)
    {
        // dd($data);
        foreach ($data as $key => &$value) {

            $data_count = count($value);
            $windspeedkmhp = 0;
            $winddir_sum = 0;
            $rain_rate_sum = 0;
            $rain_today_sum = 0;
            $temp_in_sum = 0;
            $temp_out_sum = 0;
            $hum_in_sum = 0;
            $hum_out_sum = 0;
            $uv_sum = 0;
            $wind_gust_sum = 0;
            $air_press_rel_sum = 0;
            $air_press_abs_sum = 0;
            $solar_radiation_sum = 0;

            foreach ($value as $key1 => $value1) {
                // dd($value1);
                $windspeedkmhp += $value1['windspeedkmh'] ?? 0;
                $winddir_sum += $value1['winddir'] ?? 0;
                $rain_rate_sum += $value1['rain_rate'] ?? 0;
                if ($value1['rain_today'] == null) {
                    $rain_today = 0;
                } else {
                    $rain_today = $value1['rain_today'];
                }
                $rain_today_sum += $rain_today;
                $temp_in_sum += $value1['temp_in'] ?? 0;
                $temp_out_sum += $value1['temp_out'] ?? 0;
                $hum_in_sum += $value1['hum_in'] ?? 0;
                $hum_out_sum += $value1['hum_out'] ?? 0;
                $uv_sum += $value1['uv'] ?? 0;
                $wind_gust_sum += $value1['wind_gust'] ?? 0;
                $air_press_rel_sum += $value1['air_press_rel'] ?? 0;
                $air_press_abs_sum += $value1['air_press_abs'] ?? 0;
                $solar_radiation_sum += $value1['solar_radiation'] ?? 0;
                $dailyRainIn = $value1['dailyRainIn'];
                $dailyrainmm = $value1['dailyrainmm'];
                $raintodaymm = $value1['raintodaymm'];
                $totalrainmm = $value1['totalrainmm'];
                $weeklyrainmm = $value1['weeklyrainmm'];
                $monthlyrainmm = $value1['monthlyrainmm'];
                $yearlyrainmm = $value1['yearlyrainmm'];
                $maxdailygust = $value1['maxdailygust'];
                $iddata = $value1['id'];
            }
            $value['avarage']['id'] = $iddata;
            $value['avarage']['date'] = 'Avarage';
            $value['avarage']['windspeedkmh'] = round($windspeedkmhp / $data_count, 3);
            $value['avarage']['winddir'] = round($winddir_sum / $data_count, 3);
            $value['avarage']['rain_rate'] = round($dailyrainmm, 3);
            $value['avarage']['rain_today'] = round($rain_today_sum / $data_count, 3);
            $value['avarage']['temp_in'] = round($temp_in_sum / $data_count, 3);
            $value['avarage']['temp_out'] = round($temp_out_sum / $data_count, 3);
            $value['avarage']['hum_in'] = round($hum_in_sum / $data_count, 3);
            $value['avarage']['hum_out'] = round($hum_out_sum / $data_count, 3);
            $value['avarage']['uv'] = round($uv_sum / $data_count, 3);
            $value['avarage']['wind_gust'] = round($wind_gust_sum / $data_count, 3);
            $value['avarage']['air_press_rel'] = round($air_press_rel_sum / $data_count, 3);
            $value['avarage']['air_press_abs'] = round($air_press_abs_sum / $data_count, 3);
            $value['avarage']['solar_radiation'] = round($solar_radiation_sum / $data_count, 3);
            $value['avarage']['dailyRainIn'] = $dailyRainIn;
            $value['avarage']['dailyrainmm'] = $dailyrainmm;
            $value['avarage']['raintodaymm'] = $raintodaymm;
            $value['avarage']['totalrainmm'] = $totalrainmm;
            $value['avarage']['weeklyrainmm'] = $weeklyrainmm;
            $value['avarage']['monthlyrainmm'] = $monthlyrainmm;
            $value['avarage']['yearlyrainmm'] = $yearlyrainmm;
            $value['avarage']['maxdailygust'] = $maxdailygust;
        }

        $newdata = [];
        foreach ($data as $key => $value) {
            $newdata[$key]['title'] = 'Harian';
            $newdata[$key]['data'] = $value;
        }

        return $newdata;
    }

    public function datamingguan($state)
    {
        // dd($state['year_month']);
        // $test = '2024-10';
        $data_mingguan = Weatherstationdata::where('idws', $this->selectedstation)
            ->where('date', 'like', '%' . $state['year_month'] . '%')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function ($item) {
                $date = Carbon::parse($item->date);

                // Move the start of the week to the nearest previous Sunday
                $weekStart = $date->copy()->startOfWeek(Carbon::SUNDAY);

                // Calculate the end of the week, which will be the following Saturday
                $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SATURDAY);

                // Format output to identify each week uniquely
                return sprintf(
                    'Week %d (%s-%s %s)',
                    $weekStart->weekOfMonth,
                    $weekStart->format('d'),
                    $weekEnd->format('d'),
                    $weekStart->format('M')
                );
            });

        $data_mingguan = json_decode(json_encode($data_mingguan), true);
        // dd($data_mingguan);
        $data_mingguan = $this->getavaragebyfiltermonth_revision($data_mingguan);
        // dd($data_mingguan);
        return $data_mingguan;
    }

    public function databulanan($state)
    {
        $data_bulanan['bulanan'] = Weatherstationdata::where('idws', $this->selectedstation)
            ->where('date', 'like', '%' . $state['year_month'] . '%')
            ->orderBy('date', 'asc')
            ->get();

        // dd($data_bulanan);


        $data_bulanan = json_decode(json_encode($data_bulanan), true);
        // dd($data_bulanan);
        $data_bulanan = $this->getavaragebyfiltermonth_revision($data_bulanan);
        // dd($data_bulanan, 'mamam');

        return $data_bulanan;
    }
    public function getavaragebyfiltermonth_revision($data)
    {
        // dd($data);
        $newdata = [];
        foreach ($data as $key => &$value) {

            $data_count = count($value);
            $windspeedkmhp = 0;
            $winddir_sum = 0;
            $rain_rate_sum = 0;
            $rain_today_sum = 0;
            $temp_in_sum = 0;
            $temp_out_sum = 0;
            $hum_in_sum = 0;
            $hum_out_sum = 0;
            $uv_sum = 0;
            $wind_gust_sum = 0;
            $air_press_rel_sum = 0;
            $air_press_abs_sum = 0;
            $solar_radiation_sum = 0;

            foreach ($value as $key1 => $value1) {
                // dd($value1);
                $windspeedkmhp += $value1['windspeedkmh'] ?? 0;
                $winddir_sum += $value1['winddir'] ?? 0;
                $rain_rate_sum += $value1['rain_rate'] ?? 0;
                if ($value1['rain_today'] == null) {
                    $rain_today = 0;
                } else {
                    $rain_today = $value1['rain_today'];
                }
                $rain_today_sum += $rain_today;
                $temp_in_sum += $value1['temp_in'] ?? 0;
                $temp_out_sum += $value1['temp_out'] ?? 0;
                $hum_in_sum += $value1['hum_in'] ?? 0;
                $hum_out_sum += $value1['hum_out'] ?? 0;
                $uv_sum += $value1['uv'] ?? 0;
                $wind_gust_sum += $value1['wind_gust'] ?? 0;
                $air_press_rel_sum += $value1['air_press_rel'] ?? 0;
                $air_press_abs_sum += $value1['air_press_abs'] ?? 0;
                $solar_radiation_sum += $value1['solar_radiation'] ?? 0;
                $dailyRainIn = $value1['dailyRainIn'];
                $dailyrainmm = $value1['dailyrainmm'];
                $raintodaymm = $value1['raintodaymm'];
                $totalrainmm = $value1['totalrainmm'];
                $weeklyrainmm = $value1['weeklyrainmm'];
                $monthlyrainmm = $value1['monthlyrainmm'];
                $yearlyrainmm = $value1['yearlyrainmm'];
                $maxdailygust = $value1['maxdailygust'];
                $iddata = $value1['id'];
            }
            $newdata[$key]['id'] = $iddata;
            $newdata[$key]['date'] = 'Avarage';
            $newdata[$key]['windspeedkmh'] = round($windspeedkmhp / $data_count, 3);
            $newdata[$key]['winddir'] = round($winddir_sum / $data_count, 3);
            $newdata[$key]['rain_rate'] = round($dailyrainmm, 3);
            $newdata[$key]['rain_today'] = round($rain_today_sum / $data_count, 3);
            $newdata[$key]['temp_in'] = round($temp_in_sum / $data_count, 3);
            $newdata[$key]['temp_out'] = round($temp_out_sum / $data_count, 3);
            $newdata[$key]['hum_in'] = round($hum_in_sum / $data_count, 3);
            $newdata[$key]['hum_out'] = round($hum_out_sum / $data_count, 3);
            $newdata[$key]['uv'] = round($uv_sum / $data_count, 3);
            $newdata[$key]['wind_gust'] = round($wind_gust_sum / $data_count, 3);
            $newdata[$key]['air_press_rel'] = round($air_press_rel_sum / $data_count, 3);
            $newdata[$key]['air_press_abs'] = round($air_press_abs_sum / $data_count, 3);
            $newdata[$key]['solar_radiation'] = round($solar_radiation_sum / $data_count, 3);
            $newdata[$key]['dailyRainIn'] = $dailyRainIn;
            $newdata[$key]['dailyrainmm'] = $dailyrainmm;
            $newdata[$key]['raintodaymm'] = $raintodaymm;
            $newdata[$key]['totalrainmm'] = $totalrainmm;
            $newdata[$key]['weeklyrainmm'] = $weeklyrainmm;
            $newdata[$key]['monthlyrainmm'] = $monthlyrainmm;
            $newdata[$key]['yearlyrainmm'] = $yearlyrainmm;
            $newdata[$key]['maxdailygust'] = $maxdailygust;
        }

        return $newdata;
    }


    public function generateChartData($station_id, $selectedDate)
    {
        // Initialize empty arrays for all parameters
        $temp_data = [];
        $rain_data = [];
        $wind_data = [];
        $humidity_data = [];
        $temp_data_7days = [];
        $rain_data_7days = [];
        $wind_data_7days = [];
        $humidity_data_7days = [];
        $temp_data_month = [];
        $rain_data_month = [];
        $wind_data_month = [];
        $humidity_data_month = [];

        try {
            // 1. Get Today's Data (grouped by hour)
            $today_data = Weatherstationdata::where('idws', $station_id)
                ->whereDate('date', $selectedDate)
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->date)->format('H:00');
                })
                ->map(function ($hourData) {
                    return [
                        'date' => $hourData->first()->date,
                        'temp_out' => $hourData->avg('temp_out'),
                        'rain_rate' => $hourData->sum('rain_rate'),
                        'windspeedkmh' => $hourData->avg('windspeedkmh'),
                        'hum_out' => $hourData->avg('hum_out')
                    ];
                });

            // Process today's data
            foreach ($today_data as $hour => $data) {
                $timestamp = strtotime($data['date']) * 1000; // Using exact timestamp of max rain

                if ($data['temp_out'] !== null) {
                    $temp_data[] = [$timestamp, round((float)$data['temp_out'], 1)];
                }
                if ($data['rain_rate'] !== null) {
                    $rain_data[] = [$timestamp, round((float)$data['rain_rate'], 2)];
                }
                if ($data['windspeedkmh'] !== null) {
                    $wind_data[] = [$timestamp, round((float)$data['windspeedkmh'], 1)];
                }
                if ($data['hum_out'] !== null) {
                    $humidity_data[] = [$timestamp, round((float)$data['hum_out'], 1)];
                }
            }

            // 2. Get Last 7 Days Data (grouped by day)
            $seven_days_data = Weatherstationdata::where('idws', $station_id)
                ->whereDate('date', '>=', Carbon::parse($selectedDate)->subDays(7))
                ->whereDate('date', '<=', $selectedDate)
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                })
                ->map(function ($dayData) {
                    return [
                        'date' => $dayData->first()->date,
                        'temp_out' => $dayData->avg('temp_out'),
                        'rain_rate' => $dayData->max('dailyrainmm'),
                        'windspeedkmh' => $dayData->avg('windspeedkmh'),
                        'hum_out' => $dayData->avg('hum_out')
                    ];
                });

            // Process 7 days data
            foreach ($seven_days_data as $day => $data) {
                $timestamp = strtotime($data['date']) * 1000;

                if ($data['temp_out'] !== null) {
                    $temp_data_7days[] = [$timestamp, round((float)$data['temp_out'], 1)];
                }
                if ($data['rain_rate'] !== null) {
                    $rain_data_7days[] = [$timestamp, round((float)$data['rain_rate'], 2)];
                }
                if ($data['windspeedkmh'] !== null) {
                    $wind_data_7days[] = [$timestamp, round((float)$data['windspeedkmh'], 1)];
                }
                if ($data['hum_out'] !== null) {
                    $humidity_data_7days[] = [$timestamp, round((float)$data['hum_out'], 1)];
                }
            }

            // 3. Get Monthly Data (grouped by day)
            $month_data = Weatherstationdata::where('idws', $station_id)
                ->whereDate('date', '>=', Carbon::parse($selectedDate)->startOfMonth())
                ->whereDate('date', '<=', Carbon::parse($selectedDate)->endOfMonth())
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                })
                ->map(function ($dayData) {
                    return [
                        'date' => $dayData->first()->date,
                        'temp_out' => $dayData->avg('temp_out'),
                        'rain_rate' => $dayData->max('dailyrainmm'),
                        'windspeedkmh' => $dayData->avg('windspeedkmh'),
                        'hum_out' => $dayData->avg('hum_out')
                    ];
                });

            // Process monthly data
            foreach ($month_data as $day => $data) {
                $timestamp = strtotime($data['date']) * 1000;

                if ($data['temp_out'] !== null) {
                    $temp_data_month[] = [$timestamp, round((float)$data['temp_out'], 1)];
                }
                if ($data['rain_rate'] !== null) {
                    $rain_data_month[] = [$timestamp, round((float)$data['rain_rate'], 2)];
                }
                if ($data['windspeedkmh'] !== null) {
                    $wind_data_month[] = [$timestamp, round((float)$data['windspeedkmh'], 1)];
                }
                if ($data['hum_out'] !== null) {
                    $humidity_data_month[] = [$timestamp, round((float)$data['hum_out'], 1)];
                }
            }

            // Add default points if no data found
            if (empty($temp_data)) {
                $timestamp = strtotime($selectedDate) * 1000;
                $temp_data = $rain_data = $wind_data = $humidity_data = [[$timestamp, 0]];
            }
            if (empty($temp_data_7days)) {
                $timestamp = strtotime($selectedDate) * 1000;
                $temp_data_7days = $rain_data_7days = $wind_data_7days = $humidity_data_7days = [[$timestamp, 0]];
            }
            if (empty($temp_data_month)) {
                $timestamp = strtotime($selectedDate) * 1000;
                $temp_data_month = $rain_data_month = $wind_data_month = $humidity_data_month = [[$timestamp, 0]];
            }
        } catch (\Exception $e) {
            \Log::error('Error generating chart data: ' . $e->getMessage());
            $timestamp = strtotime($selectedDate) * 1000;
            $default_point = [[$timestamp, 0]];
            $temp_data = $temp_data_7days = $temp_data_month = $default_point;
            $rain_data = $rain_data_7days = $rain_data_month = $default_point;
            $wind_data = $wind_data_7days = $wind_data_month = $default_point;
            $humidity_data = $humidity_data_7days = $humidity_data_month = $default_point;
        }
        // dd([
        //     'temp_data' => $temp_data,
        //     'rain_data' => $rain_data,
        //     'wind_data' => $wind_data,
        // ]);
        return [
            'tempData_today' => $temp_data,
            'rainData_today' => $rain_data,
            'windData_today' => $wind_data,
            'humidityData_today' => $humidity_data,
            'tempData_7days' => $temp_data_7days,
            'rainData_7days' => $rain_data_7days,
            'windData_7days' => $wind_data_7days,
            'humidityData_7days' => $humidity_data_7days,
            'tempData_month' => $temp_data_month,
            'rainData_month' => $rain_data_month,
            'windData_month' => $wind_data_month,
            'humidityData_month' => $humidity_data_month
        ];
    }
}
