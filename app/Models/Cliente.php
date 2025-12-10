<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = [
        'nombre', 'telefono', 'email', 'activo', 'created_at', 'updated_at','rol'
    ];
    protected $primaryKey = 'id';

    public function contratosCreados()
    {
        return $this->hasMany(Contrato::class, 'created_by');
    }
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id');
    }
}
