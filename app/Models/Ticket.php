<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = [
        'numero_ticket', 'vehiculo_id', 'bahia_id', 'fecha_entrada',
        'fecha_salida', 'valor_pagado', 'estado', 'registrado_por', 'observaciones'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function bahia()
    {
        return $this->belongsTo(Bahia::class, 'bahia_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
