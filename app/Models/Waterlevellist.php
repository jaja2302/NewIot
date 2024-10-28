<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waterlevellist extends Model
{
    //
    protected $table = 'water_level_list';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function waterlevel()
    {
        return $this->hasMany(Waterlevel::class, 'idwl', 'id');
    }
}
