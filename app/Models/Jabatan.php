<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;
    protected $table = 'jabatan';
    protected $connection = 'mysql2';
    public function Departement()
    {
        return $this->hasMany(Pengguna::class, 'new_jabatan', 'id');
    }
    public function Pengguna()
    {
        return $this->hasMany(Pengguna::class, 'id', 'id_jabatan');
    }
}
