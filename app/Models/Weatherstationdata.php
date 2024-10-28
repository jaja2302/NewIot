<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weatherstationdata extends Model
{
    //
    protected $table = 'weather_station';
    protected $guarded = ['id'];

    public function weatherstation()
    {
        return $this->belongsTo(Weatherstation::class, 'idws', 'id');
    }
}
