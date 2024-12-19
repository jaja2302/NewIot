<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Models\Estate;
use App\Models\Waterlevel as ModelsWaterlevel;
use App\Models\Wilayah;
use App\Models\Waterlevellist;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WaterlevelExcel;
use Illuminate\Support\Carbon;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use App\Imports\WaterlevelImport;
use Filament\Notifications\Notification;

class Waterlevel extends Component implements HasForms, HasTable
{
    public $selectedWilayah = '';
    public $selectedStation;
    public $stations = [];
    public $today = [];
    public $week =  [];
    public $month =  [];
    public $weatherstation;
    public $latlon;

    // Add loading flags
    public $isLoadingStations = false;
    public $isLoadingMapMarker = false;
    use InteractsWithForms;

    public ?array $data = [];
    // Add wire:model binding for date
    public $selectedDate;

    // Add these properties
    public $selectedStationForCoordinates;
    public $selectedLat;
    public $selectedLon;

    // Add mount method to set default date
    public function mount()
    {
        $this->selectedDate = date('Y-m-d');
        $this->form->fill();
        // $permission = permission();
        // dd($permission);
    }

    use InteractsWithTable;
    use InteractsWithForms;
    public function render()
    {
        $wilayah = Wilayah::all();
        return view('livewire.waterlevel', [
            'wilayah' => $wilayah
        ]);
    }

    public function updateSelectedStation($wilayahId)
    {
        $this->isLoadingStations = true;

        try {
            $this->selectedStation = null;
            $this->selectedWilayah = $wilayahId;

            $data = Estate::where('wil', $wilayahId)->pluck('est');

            $this->stations = Waterlevellist::where(function ($query) use ($data) {
                foreach ($data as $estate) {
                    $query->orWhere('location', 'like', $estate . '%');
                }
            })->get();
        } finally {
            $this->isLoadingStations = false;
        }
    }

    // Add a watcher for date changes
    public function updatedSelectedDate($value)
    {
        if ($this->selectedStation) {
            $this->generateChartData($this->selectedStation);
        }
    }

    public function generateChartData($stationId)
    {
        // Initialize empty arrays for all parameters
        $level_data = [];
        $level_data_7days = [];
        $level_data_month = [];

        try {
            // 1. Get Today's Data (grouped by hour)
            $today_data = ModelsWaterlevel::where('idwl', $stationId)
                ->whereDate('datetime', $this->selectedDate)
                ->orderBy('datetime', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->datetime)->format('H:00');
                })
                ->map(function ($hourData) {
                    return [
                        'datetime' => $hourData->first()->datetime,
                        'lvl_in' => $hourData->avg('lvl_in'),
                        'lvl_out' => $hourData->avg('lvl_out'),
                        'lvl_act' => $hourData->avg('lvl_act'),
                        'batas_atas' => $hourData->first()->waterlevellist->batas_atas_air,
                        'batas_bawah' => $hourData->first()->waterlevellist->batas_bawah_air
                    ];
                });

            // Process today's data
            foreach ($today_data as $hour => $data) {
                $timestamp = strtotime($data['datetime']) * 1000;

                $level_data['levelIn'][] = [$timestamp, round((float)$data['lvl_in'], 1)];
                $level_data['levelOut'][] = [$timestamp, round((float)$data['lvl_out'], 1)];
                $level_data['levelActual'][] = [$timestamp, round((float)$data['lvl_act'], 1)];
                $level_data['batasAtas'][] = [$timestamp, round((float)$data['batas_atas'], 1)];
                $level_data['batasBawah'][] = [$timestamp, round((float)$data['batas_bawah'], 1)];
            }

            // 2. Get Last 7 Days Data (grouped by day)
            $seven_days_data = ModelsWaterlevel::where('idwl', $stationId)
                ->whereDate('datetime', '>=', Carbon::parse($this->selectedDate)->subDays(7))
                ->whereDate('datetime', '<=', $this->selectedDate)
                ->orderBy('datetime', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->datetime)->format('Y-m-d');
                })
                ->map(function ($dayData) {
                    return [
                        'datetime' => $dayData->first()->datetime,
                        'lvl_in' => $dayData->avg('lvl_in'),
                        'lvl_out' => $dayData->avg('lvl_out'),
                        'lvl_act' => $dayData->avg('lvl_act'),
                        'batas_atas' => $dayData->first()->waterlevellist->batas_atas_air,
                        'batas_bawah' => $dayData->first()->waterlevellist->batas_bawah_air
                    ];
                });

            // Process 7 days data
            foreach ($seven_days_data as $day => $data) {
                $timestamp = strtotime($data['datetime']) * 1000;

                $level_data_7days['levelIn'][] = [$timestamp, round((float)$data['lvl_in'], 1)];
                $level_data_7days['levelOut'][] = [$timestamp, round((float)$data['lvl_out'], 1)];
                $level_data_7days['levelActual'][] = [$timestamp, round((float)$data['lvl_act'], 1)];
                $level_data_7days['batasAtas'][] = [$timestamp, round((float)$data['batas_atas'], 1)];
                $level_data_7days['batasBawah'][] = [$timestamp, round((float)$data['batas_bawah'], 1)];
            }

            // 3. Get Monthly Data (grouped by day)
            $month_data = ModelsWaterlevel::where('idwl', $stationId)
                ->whereDate('datetime', '>=', Carbon::parse($this->selectedDate)->startOfMonth())
                ->whereDate('datetime', '<=', Carbon::parse($this->selectedDate)->endOfMonth())
                ->orderBy('datetime', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->datetime)->format('Y-m-d');
                })
                ->map(function ($dayData) {
                    return [
                        'datetime' => $dayData->first()->datetime,
                        'lvl_in' => $dayData->avg('lvl_in'),
                        'lvl_out' => $dayData->avg('lvl_out'),
                        'lvl_act' => $dayData->avg('lvl_act'),
                        'batas_atas' => $dayData->first()->waterlevellist->batas_atas_air,
                        'batas_bawah' => $dayData->first()->waterlevellist->batas_bawah_air
                    ];
                });

            // Process monthly data
            foreach ($month_data as $day => $data) {
                $timestamp = strtotime($data['datetime']) * 1000;

                $level_data_month['levelIn'][] = [$timestamp, round((float)$data['lvl_in'], 1)];
                $level_data_month['levelOut'][] = [$timestamp, round((float)$data['lvl_out'], 1)];
                $level_data_month['levelActual'][] = [$timestamp, round((float)$data['lvl_act'], 1)];
                $level_data_month['batasAtas'][] = [$timestamp, round((float)$data['batas_atas'], 1)];
                $level_data_month['batasBawah'][] = [$timestamp, round((float)$data['batas_bawah'], 1)];
            }

            // Add default points if no data found
            if (empty($level_data)) {
                $timestamp = strtotime($this->selectedDate) * 1000;
                $default_point = [[$timestamp, 0]];
                $level_data = [
                    'levelIn' => $default_point,
                    'levelOut' => $default_point,
                    'levelActual' => $default_point,
                    'batasAtas' => $default_point,
                    'batasBawah' => $default_point
                ];
            }
            if (empty($level_data_7days)) {
                $timestamp = strtotime($this->selectedDate) * 1000;
                $default_point = [[$timestamp, 0]];
                $level_data_7days = [
                    'levelIn' => $default_point,
                    'levelOut' => $default_point,
                    'levelActual' => $default_point,
                    'batasAtas' => $default_point,
                    'batasBawah' => $default_point
                ];
            }
            if (empty($level_data_month)) {
                $timestamp = strtotime($this->selectedDate) * 1000;
                $default_point = [[$timestamp, 0]];
                $level_data_month = [
                    'levelIn' => $default_point,
                    'levelOut' => $default_point,
                    'levelActual' => $default_point,
                    'batasAtas' => $default_point,
                    'batasBawah' => $default_point
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Error generating chart data: ' . $e->getMessage());
            $timestamp = strtotime($this->selectedDate) * 1000;
            $default_point = [[$timestamp, 0]];
            $default_data = [
                'levelIn' => $default_point,
                'levelOut' => $default_point,
                'levelActual' => $default_point,
                'batasAtas' => $default_point,
                'batasBawah' => $default_point
            ];
            $level_data = $level_data_7days = $level_data_month = $default_data;
        }


        $this->today = $level_data;
        $this->week = $level_data_7days;
        $this->month = $level_data_month;

        // dd([
        //     'today' => $level_data,
        //     'week' => $level_data_7days,
        //     'month' => $level_data_month
        // ]);
        $this->dispatch('updateChartData', [
            'today' => $level_data,
            'week' => $level_data_7days,
            'month' => $level_data_month
        ]);
    }

    public function onChangeStation($stationId)
    {
        if (!$stationId) return;
        $this->isLoadingMapMarker = true;

        try {
            $station = Waterlevellist::find($stationId);
            if ($station) {
                $coordinates = [
                    'lat' => (float)$station->lat,
                    'lon' => (float)$station->lon,
                ];

                // Generate chart data
                $this->generateChartData($stationId);

                // Process station data for current readings
                $stationData = $this->processStationData($station, ModelsWaterlevel::where('idwl', $stationId)
                    ->whereDate('datetime', $this->selectedDate)
                    ->get());

                // Update map marker
                $this->dispatch('updateMapMarker', [
                    'coordinates' => $coordinates,
                    'station' => $stationData
                ]);
            }
        } finally {
            $this->isLoadingMapMarker = false;
        }
    }

    private function processStationData($station, $waterlevel)
    {
        $stationData = [
            'location' => $station->location,
            'level_in' => 0,
            'level_out' => 0,
            'level_actual' => 0,
            'level_in_avg' => 0,
            'level_out_avg' => 0,
            'level_actual_avg' => 0,
            'batas_atas_air' => $station->batas_atas_air,
            'batas_bawah_air' => $station->batas_bawah_air,
        ];

        if (!$waterlevel->isEmpty()) {
            $sumlvl_in = $waterlevel->sum('lvl_in');
            $sumlvl_out = $waterlevel->sum('lvl_out');
            $sumlvl_act = $waterlevel->sum('lvl_act');
            $count = $waterlevel->count();

            $stationData['level_in_avg'] = round($sumlvl_in / $count, 2);
            $stationData['level_out_avg'] = round($sumlvl_out / $count, 2);
            $stationData['level_actual_avg'] = round($sumlvl_act / $count, 2);

            $latest = $waterlevel->first();
            if ($latest) {
                $stationData['level_in'] = $latest->lvl_in;
                $stationData['level_out'] = $latest->lvl_out;
                $stationData['level_actual'] = $latest->lvl_act;
            }
        }

        return $stationData;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $data = [];
                if ($this->selectedStation) {
                    // dd($this->selectedStation, $this->selectedDate);
                    $data = ModelsWaterlevel::query()->where('idwl', $this->selectedStation)->where('datetime', 'like', '%' . $this->selectedDate . '%');
                    // dd($data, $this->selectedStation, $this->selectedDate);
                    return $data;
                }
                return ModelsWaterlevel::query()->where('idwl', $this->selectedStation);
            })
            ->columns([
                TextColumn::make('waterlevellist.location')->label('Station'),
                TextColumn::make('datetime')->label('Date'),
                TextColumn::make('lvl_in')->label('In'),
                TextColumn::make('lvl_out')->label('Out'),
                TextColumn::make('lvl_act')->label('Actual'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // Action::make('edit')
                //     // ->url(fn(ModelsWaterlevel $record): string => route('waterlevel.edit', $record))
                //     ->openUrlInNewTab()
            ])
            ->bulkActions([
                // BulkAction::make('delete')
                //     ->requiresConfirmation()
                //     ->action(fn(Collection $records) => $records->each->delete()),
                BulkAction::make('export')
                    ->label('Export to Excel')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new WaterlevelExcel($records),
                            'waterlevel-data-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    })
            ]);
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->acceptedFileTypes(['text/csv', 'application/csv'])
                    ->storeFiles(false)
                    ->required(),
                // ...
            ])
            ->statePath('data');
    }
    public function saveForm(): void
    {
        $data = $this->form->getState();

        try {
            // Show loading notification
            Notification::make()
                ->title('Uploading...')
                ->info()
                ->send();

            // Get initial count
            $initialCount = ModelsWaterlevel::count();

            Excel::import(new WaterlevelImport, $data['file']);

            // Get final count
            $finalCount = ModelsWaterlevel::count();
            $newRecords = $finalCount - $initialCount;

            // Reset the form
            $this->form->fill();

            if ($newRecords > 0) {
                // Show success notification with count
                Notification::make()
                    ->title('Data imported successfully!')
                    ->body("Added {$newRecords} new records.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('No records added')
                    ->body('Duplicate data found.')
                    ->info()
                    ->send();
            }

            // \Log::info('Import completed', [
            //     'initial_count' => $initialCount,
            //     'final_count' => $finalCount,
            //     'new_records' => $newRecords
            // ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error importing data')
                ->body($e->getMessage())
                ->danger()
                ->send();

            // \Log::error('Import error: ' . $e->getMessage());
        }
    }

    // Add this function to handle coordinate updates
    public function updateStationCoordinates()
    {
        // Check if user is SuperAdmin
        if (!SuperAdmin()) {
            Notification::make()
                ->title('Access Denied')
                ->body('Only SuperAdmin can update coordinates.')
                ->danger()
                ->send();
            return;
        }

        // Check if wilayah and station are selected
        if (empty($this->selectedWilayah) || empty($this->selectedStation)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select both Wilayah and Station before updating coordinates.')
                ->danger()
                ->send();
            return;
        }

        $this->validate([
            'selectedLat' => 'required|numeric',
            'selectedLon' => 'required|numeric',
        ]);

        try {
            $station = Waterlevellist::find($this->selectedStation);
            $station->update([
                'lat' => $this->selectedLat,
                'lon' => $this->selectedLon,
            ]);

            // After updating coordinates, refresh the map marker
            $stationData = $this->processStationData($station, ModelsWaterlevel::where('idwl', $this->selectedStation)
                ->whereDate('datetime', $this->selectedDate)
                ->get());

            $this->dispatch('updateMapMarker', [
                'coordinates' => [
                    'lat' => (float)$this->selectedLat,
                    'lon' => (float)$this->selectedLon,
                ],
                'station' => $stationData
            ]);

            Notification::make()
                ->title('Coordinates updated successfully!')
                ->success()
                ->send();

            // Close the modal after successful update
            $this->dispatch('close-modal', ['id' => 'maps-coordinates']);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error updating coordinates')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    #[On('set-coordinates')]
    public function setCoordinates($lat, $lng)
    {
        $this->selectedLat = $lat;
        $this->selectedLon = $lng;
    }
}
