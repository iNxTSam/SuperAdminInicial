<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VigilanteController extends Controller
{
    public function dashboard()
    {
        $bahias = DB::table('bahias')
            ->join('tipos_vehiculo', 'bahias.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select('bahias.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
            ->get();
            $ocupadosPorTipo = DB::table('tipos_vehiculo')
                ->leftJoin('vehiculos', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
                ->select(
                    'tipos_vehiculo.id',
                    'tipos_vehiculo.nombre',
                    DB::raw('SUM(CASE WHEN vehiculos.estado = 1 THEN 1 ELSE 0 END) as ocupados')
                )
                ->groupBy('tipos_vehiculo.id', 'tipos_vehiculo.nombre')
                ->get();
        return view('vigilante.dashboard', compact('bahias'));
    }
    public function entradas_salidas()
    {
        $entradasSalidas = DB::table('entrada_salida')
        ->join('clientes', 'entrada_salida.propietario', '=', 'clientes.id')
        ->join('vehiculos', 'entrada_salida.vehiculo', '=', 'vehiculos.placa')
        ->join('tipos_vehiculo', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
        ->select('entrada_salida.*', 'clientes.telefono', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
        ->orderBy('entrada_salida.fecha_entrada', 'desc')
        ->get();
        
        return view('vigilante.entradas_salidas.vehicles', compact('entradasSalidas',));
    }

    public function verificarUsuario(Request $request)
    {
        $id = $request->input('propietario');
        $usuario = DB::table('clientes')->where('id', $id)->first();

        if (!$usuario) {
            return response()->json([
                'existe' => false,
                'tipo_vehiculo' => collect()
            ]);
        }

        $vehiculo = DB::table('vehiculos')->where('propietario_id', $usuario->id)->get();
            $placas = DB::table('vehiculos')->where('propietario_id', $usuario->id)->pluck('placa')->filter()->values()->all();
        $tipo_ids = $vehiculo->pluck('tipo_vehiculo_id')->filter()->values()->all();
        $tipo_vehiculo = count($tipo_ids) ? DB::table('tipos_vehiculo')->whereIn('id', $tipo_ids)->get() : collect();
        return response()->json([
            'existe' => true,
            'tipo_vehiculo' => $tipo_vehiculo,
            'placas' => $placas
        ]);
    }
    public function registrarIngreso(Request $request){
        DB::table('entrada_salida')->insert([
            'propietario'=> $request->propietario,
            'vehiculo'=> $request->vehiculo
            ]
        );

        return redirect()->route('vigilante.vehicles')->with('success','Registro de ingreso satisfactorio');
    }
    public function registrarSalida($id){
        DB::table('entrada_salida')->where('id',$id)->update([
            'fecha_salida'=> now()->setTimezone('America/Bogota')
            ]
        );

        return redirect()->route('vigilante.vehicles')->with('success','Registro de saliday satisfactorio');
    }

    public function gestion()
    {
        $vehiculos = DB::table('vehiculos')
            ->join('tipos_vehiculo', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select('vehiculos.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
            ->orderBy('vehiculos.created_at')
            ->get();
        $tipos_vehiculo = DB::table('tipos_vehiculo')->get();

        return view('vigilante.vehiculos.vehiculos', compact('vehiculos', 'tipos_vehiculo'));
    }

    public function nuevoVehiculo(Request $request)
    {
        DB::table('vehiculos')->insert([
            'placa' => $request->placa,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'propietario_id' => $request->cedula,
            'autorizado_por_id' => $request->user_id,
            'color' => $request->color,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('vigilante.gestionvehiculos')->with("succes", "Vehiculo registrado correctamente");
    }

    public function updateVehiculo(Request $request, $id)
    {
        DB::table('vehiculos')
            ->where('placa', $id)
            ->update([
                'placa' => $request->placa,
                'tipo_vehiculo_id' => $request->tipoVehiculo,
                'propietario_id' => $request->propietario,
                'color' => $request->color,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'updated_at' => now()
            ]);

        return redirect()->route('vigilante.gestionvehiculos')->with("succes", "Informacion actualizada correctamente correctamente");
    }
}