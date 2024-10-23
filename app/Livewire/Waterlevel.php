<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Estate;
use App\Models\Wilayah;
use App\Models\Waterlevellist;

class Waterlevel extends Component
{
    public $selectedWilayah;
    public $selectedStation;
    public $stations = [];
    public $selectedDate;
    public $weatherstation;
    public $latlon;


    public function render()
    {
        $wilayah = Wilayah::all();
        return view('livewire.waterlevel', [
            'wilayah' => $wilayah
        ]);
    }

    public function updateSelectedStation($wilayahId)
    {
        $this->selectedWilayah = $wilayahId;
        $data = Estate::where('wil', $wilayahId)->pluck('est');

        $this->stations = Waterlevellist::where(function ($query) use ($data) {
            foreach ($data as $estate) {
                $query->orWhere('location', 'like', $estate . '%');
            }
        })->get();
    }

    public function onChangeStation($stationId)
    {
        $station = Waterlevellist::find($stationId);

        if ($station) {
            $this->latlon = [
                'lat' => $station->lat,
                'lon' => $station->lon
            ];
            $this->dispatch('updateMap', json_encode($this->latlon));
        }
    }
}
