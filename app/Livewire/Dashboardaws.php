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
        $this->generateChartData($this->selectedstation);
        // $this->fetchLatestData();
        // $this->fetchTodayData();
        // $this->fetchFiveDaysAheadData();
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

    public function generateChartData($station_id)
    {
        $data = $this->calculationService->generateChartData($station_id, $this->selectedDate);
        // dd($data);
        // dd($data['tempData_today']);
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
        $this->dispatch('chartDataUpdated', [
            'tempData' => $data['tempData_today'],
            'rainData' => $data['rainData_today'],
            'windData' => $data['windData_today'],
            'humidityData' => $data['humidityData_today'],
            'tempData_7days' => $data['tempData_7days'],
            'rainData_7days' => $data['rainData_7days'],
            'windData_7days' => $data['windData_7days'],
            'humidityData_7days' => $data['humidityData_7days'],
            'tempData_month' => $data['tempData_month'],
            'rainData_month' => $data['rainData_month'],
            'windData_month' => $data['windData_month'],
            'humidityData_month' => $data['humidityData_month']
        ]);
    }
}
