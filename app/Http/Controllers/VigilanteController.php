<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class VigilanteController extends Controller
{
    public function dashboard()
    {
        $bahiasAuto = DB::table('bahias')->where('tipo_vehiculo_id', 1)
            ->get();
        $bahiasMoto = DB::table('bahias')->where('tipo_vehiculo_id', 2)
            ->get();
        $bahiasBicicleta = DB::table('bahias')->where('tipo_vehiculo_id', 3)
            ->get();
        $bahiasElectricas = DB::table('bahias')->where('tipo_vehiculo_id', 4)
            ->get();

        return view('vigilante.dashboard', compact('bahiasAuto', 'bahiasMoto', 'bahiasBicicleta', 'bahiasElectricas'));
    }
    public function vehicles()
    {
        return view('vigilante.vehicles');
    }
}