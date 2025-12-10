<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';
    protected $fillable = [
        'placa', 'tipo_vehiculo_id', 'propietario_id', 'autorizado_por_id',
        'color', 'marca', 'modelo', 'activo'
    ];

    protected $primaryKey = 'placa';

    public function tipo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id');
    }

    public function propietario()
    {
        return $this->belongsTo(Cliente::class, 'propietario_id');
    }

    public function autorizadoPor()
    {
        return $this->belongsTo(Usuario::class, 'autorizado_por_id');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'vehiculo_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'vehiculo_id');
    }
}
