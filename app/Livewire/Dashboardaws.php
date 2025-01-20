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
use App\Service\CalculationAws;

class Dashboardaws extends Component implements HasForms, HasTable
{
    public $list_station;
    public $weather_data;
    public $selectedstation = 10; // Add default station ID
    public $tempChartData;
    public $rainChartData;
    public $windChartData;
    public $humidityChartData;
    public $tempChartData_7days;
    public $rainChartData_7days;
    public $windChartData_7days;
    public $humidityChartData_7days;
    public $tempChartData_month;
    public $rainChartData_month;
    public $windChartData_month;
    public $humidityChartData_month;
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
    public $heatIndex;
    use InteractsWithTable;
    use InteractsWithForms;


    protected CalculationAws $calculationService;

    public function boot(CalculationAws $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    public function mount(CalculationAws $calculationService)
    {
        $this->calculationService = $calculationService;
        $this->selectedDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        // $this->selectedDate = '2025-01-19';
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

        // Get initial data
        $this->getLatestData($this->selectedstation);

        // Generate and immediately dispatch chart data
        $data = $this->calculationService->generateChartData($this->selectedstation, $this->selectedDate);
        $this->tempChartData = $data['tempData_today'];
        $this->rainChartData = $data['rainData_today'];
        $this->windChartData = $data['windData_today'];
        $this->humidityChartData = $data['humidityData_today'];
        $this->tempChartData_7days = $data['tempData_7days'];
        $this->rainChartData_7days = $data['rainData_7days'];
        $this->windChartData_7days = $data['windData_7days'];
        $this->humidityChartData_7days = $data['humidityData_7days'];
        $this->tempChartData_month = $data['tempData_month'];
        $this->rainChartData_month = $data['rainData_month'];
        $this->windChartData_month = $data['windData_month'];
        $this->humidityChartData_month = $data['humidityData_month'];
    }

    public function generateChartData($station_id)
    {
        $data = $this->calculationService->generateChartData($station_id, $this->selectedDate);
        logger()->debug('Chart Data Generated:', $data);

        // Remove local property assignments since we'll use dispatch directly
        $this->dispatch('chartDataUpdated', [
            'tempChartData' => $data['tempData_today'],
            'rainChartData' => $data['rainData_today'],
            'windChartData' => $data['windData_today'],
            'humidityChartData' => $data['humidityData_today'],
            'tempChartData_7days' => $data['tempData_7days'],
            'rainChartData_7days' => $data['rainData_7days'],
            'windChartData_7days' => $data['windData_7days'],
            'humidityChartData_7days' => $data['humidityData_7days'],
            'tempChartData_month' => $data['tempData_month'],
            'rainChartData_month' => $data['rainData_month'],
            'windChartData_month' => $data['windData_month'],
            'humidityChartData_month' => $data['humidityData_month']
        ]);
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
        // $this->fetchLatestData();
        // $this->fetchTodayData();
        // $this->fetchFiveDaysAheadData();
        $this->dispatch('hideLoadingScreen');
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

    public function updatedSelectedDate($value)
    {
        $this->selectedDate = $value;
        $this->getLatestData($this->selectedstation);
        $this->generateChartData($this->selectedstation);
        // $this->fetchLatestData();
        // $this->fetchTodayData();
        // $this->fetchFiveDaysAheadData();
    }

    public function getLatestData($id)
    {
        $this->weather_data = $this->calculationService->weatherData($id, $this->selectedDate);
        $this->weatheranimation = $this->calculationService->getWeatherAnimation($this->weather_data);
        $this->heatIndex = $this->calculationService->calculateHeatIndex($this->weather_data['temperature']['current'], $this->weather_data['temperature']['humidity']);
        $this->dispatch('weatherAnimationUpdated', $this->weatheranimation);
    }

    public function fetchLatestData()
    {
        $this->latest_data = $this->calculationService->fetchData($this->selectedstation, 'latest');
    }


    public function fetchTodayData()
    {
        $this->today_data = $this->calculationService->fetchData($this->selectedstation, 'today');
    }

    public function fetchFiveDaysAheadData()
    {
        $this->five_days_ahead_data = $this->calculationService->fetchData($this->selectedstation, 'five_days_ahead');
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
}
