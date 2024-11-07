<?php

namespace App\Livewire;

use App\Exports\Weatherexceldaterange;
use App\Exports\Weatherexcelperhours;
use App\Exports\Weatherexcelyear;
use App\Exports\Weatherexcelyearmonth;
use App\Exports\WeatherstationExcel;
use Livewire\Component;
use App\Models\WeatherStation;
use App\Models\Weatherstationdata;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Set;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class Dashboardaws extends Component implements HasForms, HasTable
{
    public $list_station;
    public $weather_data;
    public $selectedstation = 10; // Add default station ID
    public $tempChartData;
    public $rainChartData;
    public $selectedDate; // Remove the initialization here
    public $latest_data;
    public $today_data;
    public $five_days_ahead_data;
    public $wind_statistics;
    public $humidity_levels;
    public $pressure_levels;
    public $rainfall_statistics;
    public $uv_index;
    public $data_toggle_by_year;
    public $data_toggle_by_year_month;
    public $data_toggle_by_date_range;
    public $toggle_by_year = true;
    public $toggle_by_year_month = false;
    public $toggle_by_date_range = false;
    public $station_lat;
    public $station_lon;
    public $station_loc;
    public $weatheranimation = null;

    use InteractsWithTable;
    use InteractsWithForms;


    public function mount()
    {
        $this->selectedDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        // $this->selectedDate = '2024-11-05';
        $list_station = DB::connection('mysql')->table('weather_station_list')->where('flags', 1)->get();
        $this->list_station = $list_station;

        // Get the initial station coordinates
        $station = DB::connection('mysql')
            ->table('weather_station_list')
            ->where('id', $this->selectedstation)
            ->first();

        $this->station_lat = $station->lat;
        $this->station_lon = $station->lon;
        $this->station_loc = $station->loc;

        $this->getLatestData($this->selectedstation);
        $this->fetchLatestData();
        $this->fetchTodayData();
        $this->fetchFiveDaysAheadData();
        $this->fetchWindStatistics();
        $this->fetchHumidityLevels();
        $this->fetchPressureLevels();
        $this->fetchRainfallStatistics();
        $this->fetchUVIndex();

        // dd($this->getLatestData($this->selectedstation));
        $this->generateChartData($this->selectedstation);
    }

    public function render()
    {
        $this->dispatch('hideLoadingScreen');
        return view('livewire.dashboardaws');
    }

    public function updateSelectedStation($station_id)
    {
        // Show loading screen immediately
        $this->dispatch('showLoadingScreen');

        $this->selectedstation = $station_id;

        // Update station coordinates when station changes
        $station = DB::connection('mysql')
            ->table('weather_station_list')
            ->where('id', $station_id)
            ->first();

        $this->station_lat = $station->lat;
        $this->station_lon = $station->lon;
        $this->station_loc = $station->loc;

        $this->getLatestData($station_id);
        $this->generateChartData($station_id);

        // Hide loading screen after data is loaded
        $this->dispatch('hideLoadingScreen');

        // Dispatch event to update map
        $this->dispatch('updateMapMarker', [
            'lat' => $this->station_lat,
            'lon' => $this->station_lon,
            'loc' => $this->station_loc
        ]);
    }

    public function updateSelectedDate($date)
    {
        // Show loading screen immediately
        $this->dispatch('showLoadingScreen');

        $this->selectedDate = $date;
        $this->getLatestData($this->selectedstation);
        $this->generateChartData($this->selectedstation);
        $this->fetchLatestData();
        $this->fetchTodayData();
        $this->fetchFiveDaysAheadData();
        $this->fetchWindStatistics();
        $this->fetchHumidityLevels();
        $this->fetchPressureLevels();
        $this->fetchRainfallStatistics();
        $this->fetchUVIndex();

        // Hide loading screen after all data is loaded
        $this->dispatch('hideLoadingScreen');
    }

    private function generateChartData($station_id)
    {
        // Get data for the selected date and group by hour
        $historical_data = Weatherstationdata::where('idws', $station_id)
            ->whereDate('date', $this->selectedDate)
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('H:00'); // Group by hour
            })
            ->map(function ($hourData) {
                // Calculate average values for each hour
                return [
                    'date' => $hourData->first()->date, // Get first record's timestamp
                    'temp_out' => $hourData->avg('temp_out'),
                    'rain_rate' => $hourData->sum('rain_rate') // Sum for rainfall
                ];
            });

        // // Fill missing hours with dummy data
        // $complete_data = collect();
        // for ($hour = 0; $hour < 24; $hour++) {
        //     $hour_key = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';

        //     if ($historical_data->has($hour_key)) {
        //         $complete_data[$hour_key] = $historical_data[$hour_key];
        //     } else {
        //         $complete_data[$hour_key] = [
        //             'date' => Carbon::parse($this->selectedDate)->format('Y-m-d') . ' ' . $hour_key . ':00',
        //             'temp_out' => 0,
        //             'rain_rate' => 0
        //         ];
        //     }
        // }

        // $historical_data = $complete_data;
        // dd($historical_data);
        $temp_data = [];
        $rain_data = [];

        foreach ($historical_data as $hour => $data) {
            // Convert to UTC timestamp in milliseconds
            $timestamp = strtotime($data['date']) * 1000;

            // Temperature data
            if ($data['temp_out'] !== null) {
                $temp_data[] = [
                    $timestamp,
                    round((float)$data['temp_out'], 1)
                ];
            }

            // Rainfall data
            if ($data['rain_rate'] !== null) {
                $rain_data[] = [
                    $timestamp,
                    round((float)$data['rain_rate'], 2)
                ];
            }
        }
        // dd($temp_data, $rain_data);
        $this->tempChartData = $temp_data;

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
        // dd($latest_data);
        // Get today's data for calculations
        $today_data = Weatherstationdata::where('idws', $id)
            ->whereDate('date', $this->selectedDate)
            ->get();

        if (!$latest_data) {
            $this->setDefaultWeatherData();
            return;
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

        $this->weatheranimation = $this->getWeatherAnimation();
        $this->dispatch('weatherAnimationUpdated', $this->weatheranimation);
        // $this->dispatch('hideLoadingScreen');
    }
    public function getWeatherAnimation()
    {
        // Get relevant weather data
        $rainRate = (float)$this->weather_data['rain']['rate'] ?? 0;
        $solarRadiation = (float)$this->weather_data['solar']['radiation'] ?? 0;
        $humidity = (float)$this->weather_data['temperature']['humidity'] ?? 0;
        $condition = $this->weather_data['temperature']['condition'] ?? '';

        // Determine if it's day or night based on solar radiation
        $isDay = $solarRadiation > 0;

        // Determine weather animation based on conditions
        if ($rainRate > 0) {
            if ($rainRate >= 7.6) {
                return $isDay ? 'rainstormdaylight' : 'rainstormnight';
            } elseif ($rainRate >= 2.5) {
                return $isDay ? 'raindaylight' : 'rainnight';
            } else {
                return $isDay ? 'raindaylight' : 'rainnight';
            }
        }

        // Check other weather conditions
        if ($condition === 'Cerah') {
            return $isDay ? 'sunnydaylight' : 'sunnynight';
        } elseif ($condition === 'Cerah Berawan') {
            return $isDay ? 'cloudydaylight' : 'cloudnight';
        } elseif (in_array($condition, ['Berawan', 'Berkabut'])) {
            return $isDay ? 'cloudydaylight' : 'cloudnight';
        }

        // Default animation
        return $isDay ? 'sunnydaylight' : 'sunnynight';
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

    private function getUVLevel($uvIndex)
    {
        if ($uvIndex <= 2) return 'Low';
        if ($uvIndex <= 5) return 'Moderate';
        if ($uvIndex <= 7) return 'High';
        return 'Very High';
    }

    private function getUVDescription($uvIndex)
    {
        if ($uvIndex <= 2) return 'Risiko rendah dari sinar UV';
        if ($uvIndex <= 5) return 'Risiko sedang dari sinar UV';
        if ($uvIndex <= 7) return 'Risiko tinggi dari sinar UV';
        return 'Risiko sangat tinggi dari sinar UV';
    }

    private function getWeatherCondition($data)
    {
        // If no data is provided, return 'Unknown'
        if (!$data) {
            return 'Unknown';
        }

        // Check for rain first
        if ($data->rain_rate > 0) {
            if ($data->rain_rate >= 7.6) {
                return 'Hujan Lebat';
            } elseif ($data->rain_rate >= 2.5) {
                return 'Hujan Sedang';
            } else {
                return 'Hujan Ringan';
            }
        }

        // Check temperature and humidity combinations
        $temp = (float)$data->temp_out;
        $humidity = (float)$data->hum_out;
        $solar_radiation = (float)$data->solar_radiation;

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

    private function fetchLatestData()
    {
        $this->latest_data = Weatherstationdata::where('idws', $this->selectedstation)
            ->latest('date')
            ->first();
    }

    private function fetchTodayData()
    {
        $this->today_data = Weatherstationdata::where('idws', $this->selectedstation)
            ->whereDate('date', Carbon::today())
            ->get();
    }

    private function fetchFiveDaysAheadData()
    {
        $this->five_days_ahead_data = Weatherstationdata::where('idws', $this->selectedstation)
            ->whereDate('date', '>=', Carbon::today())
            ->whereDate('date', '<=', Carbon::today()->addDays(5))
            ->get();
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
    // Fetch Wind Statistics
    private function fetchWindStatistics()
    {
        $this->wind_statistics = Weatherstationdata::where('idws', $this->selectedstation)
            ->whereDate('date', Carbon::today())
            ->get();
    }

    // Fetch Humidity Levels
    private function fetchHumidityLevels()
    {
        $this->humidity_levels = Weatherstationdata::where('idws', $this->selectedstation)
            ->whereDate('date', Carbon::today())
            ->get();
    }

    // Fetch Pressure Levels
    private function fetchPressureLevels()
    {
        $this->pressure_levels = Weatherstationdata::where('idws', $this->selectedstation)
            ->whereDate('date', Carbon::today())
            ->get();
    }

    // Fetch Rainfall Statistics
    private function fetchRainfallStatistics()
    {
        $this->rainfall_statistics = Weatherstationdata::where('idws', $this->selectedstation)
            ->whereDate('date', Carbon::today())
            ->get();
    }

    // Fetch UV Index
    private function fetchUVIndex()
    {
        $this->uv_index = Weatherstationdata::where('idws', $this->selectedstation)
            ->whereDate('date', Carbon::today())
            ->get();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $data = Weatherstationdata::query()
                    ->where('idws', $this->selectedstation)
                    ->orderBy('id', 'desc');
                return $data;
            })
            ->columns([
                TextColumn::make('weatherstation.loc'),
                TextColumn::make('date')->label('Tanggal')->sortable(),
                TextColumn::make('temp_out')->label('Suhu Luar')->sortable(),
                TextColumn::make('hum_out')->label('Kelembaban Luar')->sortable(),
                TextColumn::make('windspeedkmh')->label('Kecepatan Angin (km/h)')->sortable(),
                TextColumn::make('winddir')->label('Arah Angin')->sortable(),
                TextColumn::make('rain_rate')->label('Tingkat Hujan')->sortable(),
                TextColumn::make('rain_today')->label('Hujan Hari Ini')->sortable(),
                TextColumn::make('temp_in')->label('Suhu Dalam')->sortable(),
                TextColumn::make('hum_in')->label('Kelembaban Dalam')->sortable(),
                TextColumn::make('uv')->label('Indeks UV')->sortable(),
                TextColumn::make('wind_gust')->label('Hembusan Angin')->sortable(),
                TextColumn::make('air_press_rel')->label('Tekanan Udara Relatif')->sortable(),
                TextColumn::make('air_press_abs')->label('Tekanan Udara Absolut')->sortable(),
                TextColumn::make('solar_radiation')->label('Radiasi Matahari')->sortable(),
                TextColumn::make('dailyRainIn')->label('Hujan Harian (Inci)')->sortable(),
                TextColumn::make('dailyrainmm')->label('Hujan Harian (mm)')->sortable(),
                TextColumn::make('raintodaymm')->label('Hujan Hari Ini (mm)')->sortable(),
                TextColumn::make('totalrainmm')->label('Total Hujan (mm)')->sortable(),
                TextColumn::make('weeklyrainmm')->label('Hujan Mingguan (mm)')->sortable(),
                TextColumn::make('monthlyrainmm')->label('Hujan Bulanan (mm)')->sortable(),
                TextColumn::make('yearlyrainmm')->label('Hujan Tahunan (mm)')->sortable(),
                TextColumn::make('maxdailygust')->label('Hembusan Angin Harian Maksimum')->sortable(),
                // TextColumn::make('wh65batt')->label('Baterai WH65')->sortable(),
            ])
            ->filters([
                Filter::make('by_year')
                    ->form([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tabs\Tab::make('by_year_month')
                                    ->label('Cari berdasarkan Tahun dan Bulan')
                                    ->visible(fn($get) => !$get('toggle_by_year') && !$get('toggle_by_date_range'))
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $set('toggle_by_year_month', true);
                                            $set('toggle_by_year', false);
                                            $set('toggle_by_date_range', false);
                                            $this->toggle_by_year = false;
                                            $this->toggle_by_year_month = true;
                                            $this->toggle_by_date_range = false;
                                        }
                                    })
                                    ->schema([
                                        Section::make('Berdasarkan Tahun dan Bulan')
                                            ->translateLabel()
                                            ->headerActions([
                                                FormAction::make('Export_excel')
                                                    ->label('Export Excel Per 5 Menit')
                                                    ->color('success')
                                                    ->tooltip('Export Excel Per 5 Menit')
                                                    ->action(function ($state, FormAction $action) {
                                                        // dd($state);
                                                        if ($state['year_month'] == null) {
                                                            Notification::make()
                                                                ->title('Perhatian')
                                                                ->warning()
                                                                ->body('Bulan tidak boleh kosong')
                                                                ->send();
                                                            $action->halt();
                                                        }

                                                        $data_bulanan = $this->databulanan($state);
                                                        // dd($data_bulanan);
                                                        $data_mingguan = $this->datamingguan($state);
                                                        $data_harian = $this->dataharian($state);

                                                        $data = array_merge(
                                                            [
                                                                'Bulanan' => [
                                                                    'title' => 'Bulanan',
                                                                    'data' => $data_bulanan,
                                                                ],
                                                                'Mingguan' => [
                                                                    'title' => 'Mingguan',
                                                                    'data' => $data_mingguan,
                                                                ],
                                                            ],
                                                            $data_harian
                                                        );


                                                        // dd($data);

                                                        return Excel::download(
                                                            new Weatherexcelyearmonth($data),
                                                            'Rekap-Data-Perbulan_5menit' . $state['year_month'] . '.xlsx'
                                                        );
                                                    }),
                                                FormAction::make('Export_excels')
                                                    ->label('Export Excel Per 1 Jam')
                                                    ->tooltip('Export Excel Per 1 Jam')
                                                    ->action(function ($state, FormAction $action) {
                                                        // dd($state);
                                                        if ($state['year_month'] == null) {
                                                            Notification::make()
                                                                ->title('Perhatian')
                                                                ->warning()
                                                                ->body('Bulan tidak boleh kosong')
                                                                ->send();
                                                            $action->halt();
                                                        }
                                                        $data_perjam = $this->dataperjam($state);
                                                        return Excel::download(
                                                            new Weatherexcelperhours($data_perjam),
                                                            'Rekap-Data-Perbulan_perjam' . $state['year_month'] . '.xlsx'
                                                        );
                                                    }),
                                            ])
                                            ->description('Jangan lupa checklist semua data sebelum export ke excel')
                                            ->schema([
                                                TextInput::make('year_month')
                                                    ->type('month')
                                                    ->afterStateUpdated(fn($state, Set $set) => $set('toggle_by_year_month', true)),
                                            ]),
                                    ]),
                                Tabs\Tab::make('by_year')
                                    ->label('Cari berdasarkan Tahun')
                                    ->live()
                                    ->visible(fn($get) => !$get('toggle_by_year_month') && !$get('toggle_by_date_range'))
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $set('toggle_by_year', true);
                                            $set('toggle_by_year_month', false);
                                            $set('toggle_by_date_range', false);

                                            $this->toggle_by_year = true;
                                            $this->data_toggle_by_year = $state;
                                            $this->toggle_by_year_month = false;
                                            $this->data_toggle_by_year_month = $state;
                                            $this->toggle_by_date_range = false;
                                            $this->data_toggle_by_date_range = $state;
                                        }
                                    })
                                    ->schema([

                                        Section::make('Berdasarkan Tahun')
                                            ->translateLabel()
                                            ->headerActions([
                                                FormAction::make('Export_excel')
                                                    ->label('Export Excel')
                                                    ->tooltip('Dalam Perbaikan')
                                                    ->disabled()
                                                    ->action(function ($state) {
                                                        // dd($state);
                                                        // $data['data'] = $state;
                                                        // $data['idws'] = $this->selectedstation;
                                                        $data = Weatherstationdata::where('idws',  $this->selectedstation)
                                                            ->whereYear('date', $state['year'])
                                                            ->orderBy('date', 'asc')
                                                            ->get()
                                                            ->groupBy(function ($item) {
                                                                return Carbon::parse($item->date)->timezone('Asia/Jakarta')->isoFormat('MMMM Y');
                                                            });
                                                        $data =   json_decode(json_encode($data), true);

                                                        $avarage_data = $this->averagedata($data);
                                                        $avarage_year = $this->avarage_year($avarage_data);
                                                        // dd($avarage_year);
                                                        $data['Avarage'] = $avarage_year;
                                                        // dd($data['Avarage'], $data);
                                                        // dd($data);
                                                        return Excel::download(
                                                            new Weatherexcelyear($data),
                                                            'Rekap-Data-Pertahun ' . $state['year'] . '.xlsx'
                                                        );
                                                    }),
                                            ])
                                            ->description('Jangan lupa checklist semua data sebelum export ke excel')
                                            ->schema([
                                                Select::make('year')
                                                    ->options(array_combine(
                                                        range(2020, now()->year),
                                                        range(2020, now()->year)
                                                    ))
                                                    ->default(now()->year)
                                                    ->placeholder('Pilih Tahun')
                                                    ->afterStateUpdated(fn($state, Set $set) => $set('toggle_by_year', true)),
                                            ]),

                                    ]),

                            ]),
                    ])
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data): Builder {
                        // Check which filter is active and apply the corresponding query
                        if (isset($data['toggle_by_year']) && $data['toggle_by_year']) {
                            return $query->when(
                                $data['year'],
                                fn(Builder $query, $year) => $query->whereYear('date', $year)
                            );
                        } elseif (isset($data['toggle_by_year_month']) && $data['toggle_by_year_month']) {
                            return $query->when(
                                $data['year_month'],
                                function (Builder $query) use ($data) {
                                    $date = \Carbon\Carbon::createFromFormat('Y-m', $data['year_month']);
                                    return $query->whereYear('date', $date->year)
                                        ->whereMonth('date', $date->month);
                                }
                            );
                        } elseif (isset($data['toggle_by_date_range']) && $data['toggle_by_date_range']) {
                            return $query->when(
                                $data['from_date'] && $data['to_date'],
                                fn(Builder $query) => $query->whereBetween('date', [$data['from_date'], $data['to_date']])
                            );
                        } else {
                            return $query->when(
                                $data['year'],
                                fn(Builder $query, $year) => $query->whereYear('date', $year)
                            );
                        }

                        return $query; // Return the original query if no filters are applied
                    }),
            ], layout: FiltersLayout::AboveContent)
            // ->filtersFormColumns(2)
            // ->deferFilters()
            ->deselectAllRecordsWhenFiltered(true)
            ->actions([
                // ...
            ])
            ->bulkActions([
                // BulkAction::make('export')
                //     ->label('Excel Database')
                //     ->action(function (Collection $records) {
                //         return Excel::download(
                //             new WeatherstationExcel($records),
                //             'Weatherdata-data-' . now()->format('Y-m-d') . '.xlsx'
                //         );
                //     }),
            ]);
    }


    private function averagedata($data)
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


    private function avarage_year($avarage_data)
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


    private function dataharian($state)
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

    private function dataperjam($state)
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


    private function getavaragebyfiltermonth($data)
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

    private function datamingguan($state)
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

    private function databulanan($state)
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
    private function getavaragebyfiltermonth_revision($data)
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
    public function updatedSelectedDate($value)
    {
        $this->selectedDate = $value;
        $this->getLatestData($this->selectedstation);
        $this->generateChartData($this->selectedstation);
        $this->fetchLatestData();
        $this->fetchTodayData();
        $this->fetchFiveDaysAheadData();
        $this->fetchWindStatistics();
        $this->fetchHumidityLevels();
        $this->fetchPressureLevels();
        $this->fetchRainfallStatistics();
        $this->fetchUVIndex();
    }

    private function calculateComfortLevel($temperature, $humidity)
    {
        // Simple comfort level calculation based on temperature and humidity
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

    private function calculateDewPoint($temperature, $humidity)
    {
        // Magnus formula for dew point calculation
        $a = 17.27;
        $b = 237.7;

        $alpha = (($a * $temperature) / ($b + $temperature)) + log($humidity / 100);
        return ($b * $alpha) / ($a - $alpha);
    }

    private function calculateHeatIndex($temperature, $humidity)
    {
        // Simplified heat index calculation
        $tempF = ($temperature * 9 / 5) + 32;
        $heatIndexF = 0.5 * ($tempF + 61.0 + (($tempF - 68.0) * 1.2) + ($humidity * 0.094));

        if ($tempF >= 80) {
            // More accurate calculation for higher temperatures
            $heatIndexF = -42.379 + (2.04901523 * $tempF) + (10.14333127 * $humidity)
                - (0.22475541 * $tempF * $humidity) - (6.83783 * pow(10, -3) * $tempF * $tempF)
                - (5.481717 * pow(10, -2) * $humidity * $humidity)
                + (1.22874 * pow(10, -3) * $tempF * $tempF * $humidity)
                + (8.5282 * pow(10, -4) * $tempF * $humidity * $humidity)
                - (1.99 * pow(10, -6) * $tempF * $tempF * $humidity * $humidity);
        }

        // Convert back to Celsius
        return ($heatIndexF - 32) * 5 / 9;
    }
}
