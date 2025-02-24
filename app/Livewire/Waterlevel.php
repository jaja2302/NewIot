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
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class Waterlevel extends Component implements HasForms, HasTable
{
    public $selectedWilayah = '';
    public $selectedStation;
    public $stations = [];
    public $today = [];
    public $week =  [];
    public $month =  [];
    public $stationGalery =  [];
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

    // Add new properties for chart data
    public $chartData = [];
    public $chartPeriod = 'today'; // today, week, month
    public $chartType = 'blok'; // blok, parit, sensor, rekap

    // Add this property to store gallery images
    public $galleryImages = [];

    // Add these properties at the top of the class
    public $selectedImage = null;
    public $imageToDelete = null;

    // Add these properties
    public $dateType = 'single';
    public $startDate = null;
    public $endDate = null;

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
        $this->updateGalery($stationId);
    }

    // Update mount method to ensure default state
    public function mount()
    {
        $this->form->fill();

        // Set default values if not already set
        $this->chartPeriod = $this->chartPeriod ?: 'today';
        $this->chartType = $this->chartType ?: 'blok';
        $this->stationGalery = [];

        // Dispatch initial state
        $this->dispatch('initChartState', [
            'period' => $this->chartPeriod,
            'type' => $this->chartType
        ]);
    }

    use InteractsWithTable;
    use InteractsWithForms;

    public function render()
    {
        $Estate = Estate::all();

        return view('livewire.waterlevel', [
            'Estate' => $Estate,
            'filteredEstates' => $this->filteredEstates,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }

    //  tabel 
    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                if (!$this->selectedStation) {
                    return ModelsWaterlevel::query()->where('idwl', 0);
                }
                return $this->getFilteredQuery();
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
                Select::make('WaterStation')
                    ->required()
                    ->options(Waterlevellist::where('status', 1)->pluck('location', 'id')),
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
            // Get initial count
            $initialCount = ModelsWaterlevel::count();

            // Pass the WaterStation ID to the import constructor
            Excel::import(new WaterlevelImport($data['WaterStation']), $data['file']);

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
                $this->refreshAllComponents();
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

        // dd($station, $waterlevel);
        $stationData = [
            'location' => $station->location ?? 1,
            'datetime' => '-',
            'level_blok' => $station->level_blok ?? 0,
            'level_parit' => $station->level_parit ?? 0,
            'sensor_distance' => $station->sensor_distance ?? 0,
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

        // dd($this->selectedStation);
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
        if (empty($this->selectedStation)) {
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

            $this->refreshAllComponents();
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

    private function getFilteredQuery()
    {
        $query = ModelsWaterlevel::query()
            ->where('idwl', $this->selectedStation);

        // Apply date filtering
        if ($this->startDate) {
            $query->whereDate('datetime', '>=', $this->startDate);

            if ($this->endDate) {
                $query->whereDate('datetime', '<=', $this->endDate);
            }
        }

        return $query;
    }

    private function updateMapMarker($station)
    {
        $query = $this->getFilteredQuery();
        $waterlevel = $query->get();

        $stationData = $this->processStationData(
            $station,
            $waterlevel
        );

        $this->dispatch('updateMapMarker', [
            'coordinates' => [
                'lat' => $station->lat ?? 0,
                'lon' => $station->lon ?? 0,
            ],
            'station' => $stationData,
        ]);
    }

    // Add method to fetch chart data
    public function getChartData()
    {
        if (!$this->selectedStation) {
            return ['series' => []];
        }

        $query = $this->getFilteredQuery()->orderBy('datetime', 'asc');
        $data = $query->get();

        $series = [];

        // Only add series if they should be visible
        if ($this->chartType === 'blok' || $this->chartType === 'rekap') {
            $series[] = [
                'name' => 'Level Blok',
                'data' => $data->map(function ($item) {
                    return [
                        'x' => Carbon::parse($item->datetime)->format('Y-m-d H:i:s'),
                        'y' => (float) $item->level_blok
                    ];
                })->toArray()
            ];
        }

        if ($this->chartType === 'parit' || $this->chartType === 'rekap') {
            $series[] = [
                'name' => 'Level Parit',
                'data' => $data->map(function ($item) {
                    return [
                        'x' => Carbon::parse($item->datetime)->format('Y-m-d H:i:s'),
                        'y' => (float) $item->level_parit
                    ];
                })->toArray()
            ];
        }

        if ($this->chartType === 'sensor' || $this->chartType === 'rekap') {
            $series[] = [
                'name' => 'Sensor Distance',
                'data' => $data->map(function ($item) {
                    return [
                        'x' => Carbon::parse($item->datetime)->format('Y-m-d H:i:s'),
                        'y' => (float) $item->sensor_distance
                    ];
                })->toArray()
            ];
        }

        return [
            'series' => $series
        ];
    }

    // Update the updateChart method to handle null values better
    public function updateChart($period = null, $type = null)
    {
        // Only update if value is provided
        if ($period !== null) {
            $this->chartPeriod = $period;
        }
        if ($type !== null) {
            $this->chartType = $type;
        }

        $this->chartData = $this->getChartData();

        // Always send the current state
        $this->dispatch('updateChart', [
            'data' => $this->chartData,
            'period' => $this->chartPeriod,
            'type' => $this->chartType
        ]);
    }

    // untuk galery
    public function updateGalery($stationId)
    {
        $station = Waterlevellist::find($stationId);
        if ($station && !empty($station->foto_lokasi)) {
            $decodedImages = json_decode($station->foto_lokasi, true);
            $this->galleryImages = is_array($decodedImages) ? array_filter($decodedImages) : [];
        } else {
            $this->galleryImages = [];
        }
    }

    // Add method to handle image deletion
    public function deleteImage()
    {
        if (!$this->imageToDelete || !$this->selectedStation) {
            return;
        }

        try {
            $station = Waterlevellist::find($this->selectedStation);
            if (!$station) {
                return;
            }

            $images = json_decode($station->foto_lokasi, true) ?? [];
            $images = array_filter($images, fn($img) => $img !== $this->imageToDelete);

            // Update the database
            $station->update([
                'foto_lokasi' => json_encode(array_values($images))
            ]);

            // Delete the actual file
            if (Storage::disk('public')->exists($this->imageToDelete)) {
                Storage::disk('public')->delete($this->imageToDelete);
            }

            // Update the gallery
            $this->updateGalery($this->selectedStation);

            // Show success notification
            Notification::make()
                ->title('Image deleted successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error deleting image')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        // Reset the imageToDelete property
        $this->imageToDelete = null;

        // Close the modal
        $this->dispatch('close-modal', id: 'delete-confirmation');
    }

    // Update the event handler
    #[On('dateFilterChanged')]
    public function handleDateFilter($data)
    {
        // Extract data from the event payload
        $type = $data['type'] ?? null;

        if ($type === 'single') {
            $this->startDate = $data['date'] ?? null;
            $this->endDate = null;
        } else if ($type === 'range') {
            $this->startDate = $data['startDate'] ?? null;
            $this->endDate = $data['endDate'] ?? null;
        } else {
            // Clear filter case
            $this->startDate = null;
            $this->endDate = null;
        }

        $this->refreshAllComponents();
    }

    // Add these methods
    public function applyDateFilter()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
        ]);

        $this->refreshAllComponents();
    }

    public function clearDateFilter()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->refreshAllComponents();
    }

    // Add this method to refresh all components
    private function refreshAllComponents()
    {
        if (!$this->selectedStation) {
            return;
        }
        $this->dispatch('showLoadingScreen');

        try {
            $station = Waterlevellist::find($this->selectedStation);
            if (!$station) {
                return;
            }

            // Get filtered data
            $query = $this->getFilteredQuery();
            $data = $query->get();

            // If no data found, reset chart and map
            if ($data->isEmpty()) {
                // Reset chart
                $this->dispatch('updateChart', [
                    'data' => ['series' => []],
                    'period' => $this->chartPeriod,
                    'type' => $this->chartType
                ]);

                // Reset map marker
                $this->dispatch('updateMapMarker', [
                    'coordinates' => [
                        'lat' => $station->lat ?? 0,
                        'lon' => $station->lon ?? 0,
                    ],
                    'station' => [
                        'location' => $station->location,
                        'datetime' => '-',
                        'level_blok' => 0,
                        'level_parit' => 0,
                        'sensor_distance' => 0,
                        'level_blok_avg' => 0,
                        'level_parit_avg' => 0,
                        'sensor_distance_avg' => 0,
                    ]
                ]);
            } else {
                // Update map marker
                $this->updateMapMarker($station);
                // Update chart
                $this->updateChart();
            }
            $this->dispatch('hideLoadingScreen');
        } catch (\Exception $e) {
            $this->dispatch('hideLoadingScreen');
            // Handle any errors
            Notification::make()
                ->title('Error refreshing data')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // Add these methods for quick date selection
    public function setLastWeek()
    {
        $this->startDate = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
        // $this->refreshAllComponents();
    }

    public function setLastMonth()
    {
        $this->startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        // $this->refreshAllComponents();
    }

    // Optional: Add method for current week/month if needed
    public function setCurrentWeek()
    {
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        // $this->refreshAllComponents();
    }

    public function setCurrentMonth()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        // $this->refreshAllComponents();
    }



    #[On('GeneratePDF')]
    public function GeneratePDF($dataURI)
    {
        try {
            if (!$this->selectedStation) {
                throw new \Exception('Please select a station first');
            }

            // Validate data URI
            if (!str_starts_with($dataURI, 'data:image/')) {
                throw new \Exception('Invalid chart data');
            }

            // Get the filtered data
            $query = $this->getFilteredQuery();
            $data = $query->get();

            if ($data->isEmpty()) {
                throw new \Exception('No data available for the selected period');
            }

            // Save the chart image and get the path
            $imagePath = $this->saveBase64Image($dataURI);

            // Get station name
            $stationName = Waterlevellist::find($this->selectedStation)->location ?? 'Unknown Station';

            // Create PDF
            $pdf = PDF::loadView('exports.pdf.water-level-report', [
                'imagePath' => $imagePath,
                'data' => $data,
                'station' => $stationName,
                'startDate' => $this->startDate ? Carbon::parse($this->startDate)->format('d M Y') : Carbon::now()->format('d M Y'),
                'endDate' => $this->endDate ? Carbon::parse($this->endDate)->format('d M Y') : null
            ]);

            // Set paper
            $pdf->setPaper('a4', 'landscape');

            // Generate filename
            $filename = "water-level-report-" . now()->format('Y-m-d-His') . ".pdf";

            // Return the PDF for download
            return response()->streamDownload(
                function () use ($pdf) {
                    echo $pdf->output();
                },
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]
            );
        } catch (\Exception $e) {
            // Handle any errors
            Notification::make()
                ->title('Error generating PDF')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    private function saveBase64Image($base64String)
    {
        try {
            // Extract the base64 data
            list($type, $data) = explode(';', $base64String);
            list(, $data) = explode(',', $data);

            // Generate unique filename
            $filename = 'chart_' . uniqid() . '_' . time() . '.png';
            $path = 'images/' . $filename;

            // Save the image
            if (!Storage::disk('public')->put($path, base64_decode($data))) {
                throw new \Exception('Failed to save image to storage');
            }

            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Failed to save chart image: ' . $e->getMessage());
        }
    }
}
