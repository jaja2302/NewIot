<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waterlevel extends Model
{
    //
    protected $table = 'water_level_new';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function waterlevellist()
    {
        return $this->belongsTo(Waterlevellist::class, 'idwl', 'id');
    }
}
