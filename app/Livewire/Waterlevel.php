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

    // Add new property for search
    public $searchEstate = '';
    public $filteredEstates = [];


    // Add new method to handle search
    public function updatedSearchEstate($value)
    {
        if (empty($value)) {
            $this->filteredEstates = [];
            return;
        }

        // Search in Estate table
        $estates = Estate::where('est', 'like', "%{$value}%")->get();

        // dd($estates);
        $this->filteredEstates = [];
        foreach ($estates as $estate) {
            // Find matching water level stations
            $stations = Waterlevellist::where('status', 1)
                ->where('location', 'like', "%{$estate->est}%")
                ->get();

            foreach ($stations as $station) {
                // Get latest water level data for each station
                $latestData = ModelsWaterlevel::where('idwl', $station->id)
                    ->latest('datetime')
                    ->first();

                if ($latestData) {
                    $this->filteredEstates[] = [
                        'id' => $station->id,
                        'name' => $station->location,
                        'level_blok' => $latestData->level_blok ?? 0,
                        'level_parit' => $latestData->level_parit ?? 0,
                        'sensor_distance' => $latestData->sensor_distance ?? 0,
                        'datetime' => $latestData->datetime,
                        'is_active' => false
                    ];
                }
            }
        }
    }

    public function selectEstate($stationId)
    {
        // Update active state
        foreach ($this->filteredEstates as &$estate) {
            $estate['is_active'] = ($estate['id'] == $stationId);
        }

        // Update selected station and trigger data loading
        $this->selectedStation = $stationId;
        $this->onChangeStation($stationId);
    }



    // Add mount method to set default date
    public function mount()
    {
        $this->form->fill();
    }

    use InteractsWithTable;
    use InteractsWithForms;

    public function render()
    {
        $Estate = Estate::all();

        return view('livewire.waterlevel', [
            'Estate' => $Estate,
            'filteredEstates' => $this->filteredEstates
        ]);
    }

    //  tabel 
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
                TextColumn::make('level_blok')->label('Level Blok'),
                TextColumn::make('level_parit')->label('Level Parit'),
                TextColumn::make('sensor_distance')->label('Sensor Distance'),
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


    // untuk import data excel 
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
    // maps station 
    public function updateSelectedStation($wilayahId)
    {
        $this->isLoadingStations = true;

        try {
            $this->selectedStation = null;
            $this->selectedWilayah = $wilayahId;
        } finally {
            $this->isLoadingStations = false;
        }
    }

    // Add a watcher for date changes
    public function updatedSelectedDate($value)
    {
        if ($this->selectedStation) {
            $this->onChangeStation($this->selectedStation);
        }
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
                if ($coordinates['lat'] == 0 && $coordinates['lon'] == 0) {
                    Notification::make()
                        ->title('Coordinates water level not found')
                        ->body('Please update coordinates')
                        ->danger()
                        ->send();
                }
                $this->updateMapMarker($station);
            }
        } finally {
            $this->isLoadingMapMarker = false;
        }
    }

    #[On('set-coordinates')]
    public function setCoordinates($lat, $lng)
    {
        $this->selectedLat = $lat;
        $this->selectedLon = $lng;
    }

    private function processStationData($station, $waterlevel)
    {
        $stationData = [
            'location' => $station->location ?? 1,
            'datetime' => '-',
            'level_blok' => 0,
            'level_parit' => 0,
            'sensor_distance' => 0,
            'level_blok_avg' => 0,
            'level_parit_avg' => 0,
            'sensor_distance_avg' => 0,
            'batas_atas_air' => $station->batas_atas_air ?? 0,
            'batas_bawah_air' => $station->batas_bawah_air ?? 0,
        ];

        if (!$waterlevel->isEmpty()) {
            $sum_level_blok = $waterlevel->sum('level_blok');
            $sum_level_parit = $waterlevel->sum('level_parit');
            $sum_sensor_distance = $waterlevel->sum('sensor_distance');
            $count = $waterlevel->count();

            $stationData['level_blok_avg'] = round($sum_level_blok / $count, 2);
            $stationData['level_parit_avg'] = round($sum_level_parit / $count, 2);
            $stationData['sensor_distance_avg'] = round($sum_sensor_distance / $count, 2);

            $latest = $waterlevel->first();
            if ($latest) {
                $stationData['datetime'] = Carbon::parse($latest->datetime)->format('Y-m-d');
                $stationData['level_blok'] = $latest->level_blok;
                $stationData['level_parit'] = $latest->level_parit;
                $stationData['sensor_distance'] = $latest->sensor_distance;
            }
        }

        return $stationData;
    }

    // untuk mengupdate titik lokasi water level 
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

            $this->updateMapMarker($station);
            Notification::make()
                ->title('Coordinates updated successfully!')
                ->success()
                ->send();

            // Close the modal after successful update
            $this->dispatch('close-modal', id: 'mapscordinates');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error updating coordinates')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function updateMapMarker($station)
    {
        $stationData = $this->processStationData($station, ModelsWaterlevel::where('idwl', $this->selectedStation)
            ->whereDate('datetime', $this->selectedDate)
            ->get());

        $this->dispatch('updateMapMarker', [
            'coordinates' => [
                'lat' => $station->lat ?? 0,
                'lon' => $station->lon ?? 0,
            ],
            'station' => $stationData
        ]);
    }
}
