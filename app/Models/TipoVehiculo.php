<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    protected $table = 'tipos_vehiculo';
    protected $fillable = ['nombre', 'descripcion'];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'tipo_vehiculo_id');
    }

    public function bahias()
    {
        return $this->hasMany(Bahia::class, 'tipo_vehiculo_id');
    }

    public function tarifas()
    {
        return $this->hasMany(Tarifa::class, 'tipo_vehiculo_id');
    }
}
