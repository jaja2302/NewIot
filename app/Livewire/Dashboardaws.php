<?php

namespace App\Livewire;

use App\Exports\WeatherstationExcel;
use Livewire\Component;
use App\Models\WeatherStation;
use App\Models\Weatherstationdata;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class Dashboardaws extends Component implements HasForms, HasTable
{
    public $list_station;
    public $weather_data;
    public $selectedstation = 10; // Add default station ID
    public $tempChartData;
    public $rainChartData;
    public $selectedDate; // Remove the initialization here
    use InteractsWithTable;
    use InteractsWithForms;


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


    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $data = Weatherstationdata::query()->where('idws', $this->selectedstation);
                return $data;
            })
            ->columns([
                TextColumn::make('weatherstation.loc'),
                TextColumn::make('date')->sortable(),
                TextColumn::make('temp_out'),
                TextColumn::make('hum_out'),
                TextColumn::make('windspeedkmh'),
                TextColumn::make('winddir'),
            ])
            ->filters([
                Filter::make('by_year')
                    ->form([
                        Select::make('year')
                            ->options(array_combine(
                                range(2020, now()->year),
                                range(2020, now()->year)
                            ))
                            // ->default(now()->year)
                            ->placeholder('Select Year')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['year'],
                                function (Builder $query, $year) use ($data) {
                                    return $query->whereYear('date', $year);
                                }
                            );
                    }),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from_date')
                            ->default(now()->subDays(30))
                            ->label('From Date'),
                        DatePicker::make('to_date')
                            ->default(now())
                            ->label('To Date')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'] && $data['to_date'],
                                function (Builder $query) use ($data) {
                                    return $query->whereBetween('date', [$data['from_date'], $data['to_date']]);
                                }
                            );
                    })
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // BulkAction::make('delete')
                //     ->label('Delete')
                //     ->icon('heroicon-o-trash')
                //     ->color('danger')
                //     ->requiresConfirmation()
                //     ->action(function (Collection $records) {
                //         $records->each(function (Weatherstationdata $record) {
                //             $record->delete();
                //         });
                //     }),
                BulkAction::make('export')
                    ->label('Export to Excel')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new WeatherstationExcel($records),
                            'Weatherdata-data-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    })
            ]);
    }
}
