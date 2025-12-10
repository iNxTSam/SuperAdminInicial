<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $fillable = [
        'nombre', 'cedula', 'carnet', 'contacto', 'email', 'password', 'rol_id', 'activo'
    ];

    protected $hidden = ['password'];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'propietario_id');
    }

    
}
