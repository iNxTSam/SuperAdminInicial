<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class VigilanteController extends Controller
{
    public function dashboard()
    {
        $bahias = DB::table('bahias')
            ->join('tipos_vehiculo', 'bahias.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select('bahias.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
            ->orderBy('bahias.numero')
            ->get();
        return view('vigilante.dashboard', compact('bahias'));
    }
    public function vehicles()
    {
        return view('vigilante.vehicles');
    }
}