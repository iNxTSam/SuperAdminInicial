<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoContrato extends Model
{
    protected $table = 'estados_contrato';
    protected $fillable = ['nombre', 'descripcion'];

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'estado_id');
    }
}
