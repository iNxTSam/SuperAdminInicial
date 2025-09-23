<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahia extends Model
{
    protected $table = 'bahias';
    protected $fillable = ['numero', 'tipo_vehiculo_id', 'capacidad_maxima', 'ocupada', 'activa', 'ubicacion'];

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'bahia_id');
    }
}
