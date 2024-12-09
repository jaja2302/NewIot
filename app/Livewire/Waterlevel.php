<?php

namespace App\Livewire;

use Livewire\Component;

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

class Waterlevel extends Component implements HasForms, HasTable
{
    public $selectedWilayah = '';
    public $selectedStation;
    public $stations = [];

    public $weatherstation;
    public $latlon;

    // Add loading flags
    public $isLoadingStations = false;
    public $isLoadingMapMarker = false;

    // Add wire:model binding for date
    public $selectedDate;

    // Add mount method to set default date
    public function mount()
    {
        $this->selectedDate = date('Y-m-d');
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

                // Get data and sort by datetime
                $waterlevel = ModelsWaterlevel::where('idwl', $stationId)
                    ->where('datetime', 'like', $this->selectedDate . '%')
                    ->orderBy('datetime', 'asc') // Add this line to sort
                    // ->limit(10)
                    ->get();

                // Initialize arrays
                $levelIn = [];
                $levelOut = [];
                $levelActual = [];
                $datetime = [];
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
                    // Calculate sums manually
                    $sumlvl_in = 0;
                    $sumlvl_out = 0;
                    $sumlvl_act = 0;

                    foreach ($waterlevel as $item) {
                        $sumlvl_in += $item->lvl_in;
                        $sumlvl_out += $item->lvl_out;
                        $sumlvl_act += $item->lvl_act;
                        $levelIn[] = $item->lvl_in;
                        $levelOut[] = $item->lvl_out;
                        $levelActual[] = $item->lvl_act;
                        $datetime[] = Carbon::parse($item->datetime)->format('H:i:s');
                    }

                    // Calculate averages
                    $count = count($waterlevel);
                    $stationData['level_in_avg'] = round($sumlvl_in / $count, 2);
                    $stationData['level_out_avg'] = round($sumlvl_out / $count, 2);
                    $stationData['level_actual_avg'] = round($sumlvl_act / $count, 2);

                    // Get latest readings
                    $latest = $waterlevel->first();
                    if ($latest) {
                        $stationData['level_in'] = $latest->lvl_in;
                        $stationData['level_out'] = $latest->lvl_out;
                        $stationData['level_actual'] = $latest->lvl_act;
                    }
                }

                // Dispatch both map and chart data
                $this->dispatch('updateMapMarker', [
                    'coordinates' => $coordinates,
                    'station' => $stationData
                ]);

                $this->dispatch('updateChartData', [
                    'levelIn' => $levelIn,
                    'levelOut' => $levelOut,
                    'datetime' => $datetime,
                    'levelActual' => $levelActual
                ]);
            }
        } finally {
            $this->isLoadingMapMarker = false;
        }
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
}
