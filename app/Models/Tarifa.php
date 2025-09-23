<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $table = 'tarifas';
    protected $fillable = ['nombre', 'tipo', 'tipo_vehiculo_id', 'valor', 'activa'];

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'tarifa_id');
    }
}
