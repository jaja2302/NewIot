<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Estate;
use App\Models\Wilayah;
use App\Models\Waterlevellist;

class Waterlevel extends Component
{
    public $selectedWilayah;
    public $stations = [];
    public $selectedDate;
    public $weatherstation;
    public function render()
    {
        $wilayah = Wilayah::all();
        return view('livewire.waterlevel', [
            'wilayah' => $wilayah
        ]);
    }

    public function updateSelectedStation($wilayahId)
    {
        $data = Estate::where('wil', $wilayahId)->pluck('est');

        $liststation = Waterlevellist::where(function ($query) use ($data) {
            foreach ($data as $estate) {
                $query->orWhere('location', 'like', $estate . '%');
            }
        })->get();
        // dd($liststation);
        $this->stations = $liststation;
    }
}
