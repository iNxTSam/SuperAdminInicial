<?php

namespace App\Http\Controllers;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VigilanteController extends Controller
{
    #VISTA DE DASHBOARD
    public function dashboard()
    {
        $bahias = DB::table('bahias')
            ->join('tipos_vehiculo', 'bahias.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select('bahias.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
            ->get();
        $ocupadosPorTipo = DB::table('tipos_vehiculo')
            ->leftJoin('vehiculos', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->join('bahias', 'bahias.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select(
                'tipos_vehiculo.id',
                'tipos_vehiculo.nombre',
                'bahias.capacidad_maxima',
                DB::raw('SUM(CASE WHEN vehiculos.estado_parqueo = 1 THEN 1 ELSE 0 END) as ocupados')
            )
            ->groupBy('tipos_vehiculo.id', 'tipos_vehiculo.nombre', 'bahias.capacidad_maxima')
            ->get();

        return view('vigilante.dashboard', compact('bahias', 'ocupadosPorTipo'));
    }
    #VISTA DE GESTION DE ENTRADAS Y SALIDAS
    public function entradas_salidas()
    {
        $entradasSalidas = DB::table('entrada_salida')
            ->join('clientes', 'entrada_salida.propietario', '=', 'clientes.id')
            ->join('vehiculos', 'entrada_salida.vehiculo', '=', 'vehiculos.placa')
            ->join('tipos_vehiculo', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select('entrada_salida.*', 'clientes.telefono', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
            ->orderBy('entrada_salida.fecha_entrada', 'desc')
            ->get();

        return view('vigilante.entradas_salidas.vehicles', compact('entradasSalidas', ));
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

        $vehiculo = DB::table('vehiculos')
            ->join('tipos_vehiculo', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->where('vehiculos.propietario_id', $usuario->id)
            ->select('vehiculos.*', 'tipos_vehiculo.nombre')
            ->get();
        $placas = DB::table('vehiculos')->where('propietario_id', $usuario->id)->pluck('placa')->filter()->values()->all();

        return response()->json([
            'existe' => true,
            'placas' => $placas,
            'vehiculos' => $vehiculo
        ]);
    }
    public function registrarIngreso(Request $request)
    {

        $vehiculo = DB::table('vehiculos')->where('placa', $request->vehiculo)->first();
        $vehiculosEnParqueo = DB::table('vehiculos')
            ->where('estado_parqueo', 1)
            ->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)
            ->count();
        $bahia = DB::table('bahias')->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)->first();

        if ($vehiculo->estado_parqueo == 1) {
            return redirect()->route('vigilante.vehicles')->with('error', 'El vehiculo ya se encuentra en el parqueadero');
        }

        if ($vehiculosEnParqueo >= $bahia->capacidad_maxima) {
            return redirect()->route('vigilante.vehicles')->with('error', 'No hay cupo disponible para este tipo de vehiculo');
        }

        DB::table('entrada_salida')->insert(
            [
                'propietario' => $request->propietario,
                'vehiculo' => $request->vehiculo
            ]
        );
        DB::table('vehiculos')->where('placa', $request->vehiculo)->update(
            [
                'estado_parqueo' => 1,
            ]
        );

        DB::table('bahias')->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)->update(
            [
                'ocupada' => $vehiculosEnParqueo + 1,
            ]
        );

        return redirect()->route('vigilante.vehicles')->with('success', 'Registro de ingreso satisfactorio');
    }
    public function registrarSalida($id, $placa)
    {
        $vehiculo = DB::table('vehiculos')->where('placa', $placa)->first();
        $vehiculosEnParqueo = DB::table('vehiculos')
            ->where('estado_parqueo', 1)
            ->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)
            ->count();
        $bahia = DB::table('bahias')->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)->first();

        DB::table('entrada_salida')->where('id', $id)->update(
            [
                'fecha_salida' => now()->setTimezone('America/Bogota')
            ]
        );

        DB::table('vehiculos')->where('placa', $placa)->update(
            [
                'estado_parqueo' => 0,
            ]
        );
        DB::table('entrada_salida')->where('id', $id)->update(
            [
                'marcar_salida' => 1,
            ]
        );
        DB::table('bahias')->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)->update(
            [
                'ocupada' => $vehiculosEnParqueo - 1,
            ]
        );
        return redirect()->route('vigilante.vehicles')->with('success', 'Registro de salida satisfactorio');
    }

    #VISTA DE GESTION DE VEHICULOS

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
}