<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afdeling extends Model
{
    use HasFactory;
    protected $connection = 'mysql3';
    protected $table = 'afdeling';
    public $timestamps = false;
    protected $guarded = ['id'];
    public function estate()
    {
        return $this->belongsTo(Estate::class, 'estate', 'id');
    }

    // public function ombro_afdeling()
    // {
    //     return $this->hasMany(ombrodata::class, 'afd', 'id');
    // }
}
