<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'user_id';
    protected $connection = 'mysql2';

    // Add these lines to specify which fields are used for authentication
    protected $fillable = ['email', 'password'];

    // Override the default password attribute to prevent hashing
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function Departement()
    {
        return $this->belongsToMany(Departement::class, 'department_user', 'user_id', 'department_id');
    }
    public function Jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id');
    }
}
