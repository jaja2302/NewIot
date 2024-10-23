<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    use HasFactory;
    protected $connection = 'mysql3';
    protected $table = 'estate';

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wil', 'id');
    }

    public function afdelings()
    {
        return $this->hasMany(Afdeling::class, 'estate', 'id');
    }

    // public function estate_plots()
    // {
    //     return $this->hasMany(EstatePlot::class, 'est', 'est');
    // }

    // public function estate_plots_ombro()
    // {
    //     return $this->hasMany(ombrodata::class, 'est', 'id');
    // }

    // public function crontabs()
    // {
    //     return $this->hasMany(Crontab::class, 'est', 'estate');
    // }
}
