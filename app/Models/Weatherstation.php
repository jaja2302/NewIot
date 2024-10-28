<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weatherstation extends Model
{
    //
    protected $table = 'weather_station_list';
    protected $guarded = ['id'];

    public function weatherstationdata()
    {
        return $this->hasMany(Weatherstationdata::class, 'idws', 'id');
    }
}
