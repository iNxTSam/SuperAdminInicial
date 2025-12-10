<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'contratos';
    protected $fillable = [
        'vehiculo_id', 'tarifa_id', 'fecha_inicio', 'fecha_fin',
        'estado_id', 'valor_total', 'observaciones', 'created_by','propietario_id','valor'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'placa');
    }

    public function tarifa()
    {
        return $this->belongsTo(Tarifa::class, 'tarifa_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoContrato::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function recibos()
    {
        return $this->hasMany(Recibo::class, 'contrato_id');
    }
}
