<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table = 'recibos';
    protected $fillable = ['numero', 'contrato_id', 'valor', 'concepto', 'estado', 'emitido_por'];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function emitidoPor()
    {
        return $this->belongsTo(Usuario::class, 'emitido_por');
    }
}
