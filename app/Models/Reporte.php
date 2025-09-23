<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reportes';
    protected $fillable = ['nombre', 'tipo', 'formato', 'ruta_archivo', 'parametros', 'generado_por'];

    protected $casts = [
        'parametros' => 'array',
    ];

    public function generadoPor()
    {
        return $this->belongsTo(Usuario::class, 'generado_por');
    }
}
