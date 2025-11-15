<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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
        $vehiculo = DB::table('vehiculos')
        ->where('estado_parqueo', 1)
        ->get();
        return view('vigilante.entradas_salidas.vehicles',compact('vehiculo'));
    }

    public function verificarUsuario(Request $request){
        $id= $request->input('propietario');
        $usuario = DB::table('clientes')->where('id', $id)->first();
        $vehiculo = DB::table('vehiculos')->where('propietario_id',$usuario->id)->get();
            $tipo_ids = $vehiculo->pluck('tipo_vehiculo_id')->filter()->values()->all();
            $tipo_vehiculo = count($tipo_ids) ? DB::table('tipos_vehiculo')->whereIn('id', $tipo_ids)->get() : collect();
        return response()->json([  
            'existe'=> $usuario ? true : false,
            'tipo_vehiculo' => $tipo_vehiculo
        ]);
    }

    public function gestion()
    {
        $vehiculos = DB::table('vehiculos')
         ->join('tipos_vehiculo', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
         ->select('vehiculos.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
        ->orderBy('vehiculos.created_at')
        ->get();
        $tipos_vehiculo = DB::table('tipos_vehiculo')->get();

        return view('vigilante.vehiculos.vehiculos',compact('vehiculos', 'tipos_vehiculo'));
    }

    public function nuevoVehiculo(Request $request)
    {
        DB::table('vehiculos')-> insert([
            'placa'=> $request->placa,
            'tipo_vehiculo_id'=>$request->tipo_vehiculo_id,
            'propietario_id'=> $request->cedula,
            'autorizado_por_id' => $request->user_id,
            'color'=> $request->color,
            'marca'=> $request->marca,
            'modelo'=> $request->modelo,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('vigilante.gestionvehiculos')->with("succes","Vehiculo registrado correctamente");
    }
    
    public function updateVehiculo(Request $request, $id)
    {
        DB::table('vehiculos') 
        ->where('id',$id) 
        ->update([
            'placa'=>$request->placa,
            'tipo_vehiculo_id'=> $request->tipoVehiculo,
            'propietario_id'=>$request->propietario,
            'color'=>$request->color,
            'marca'=>$request->marca,
            'modelo'=>$request->modelo,
            'updated_at' => now()
        ]);

        return redirect()->route('vigilante.gestionvehiculos')->with("succes","Informacion actualizada correctamente correctamente");
    }
}