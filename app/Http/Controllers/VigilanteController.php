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
            ->select('entrada_salida.*', 'clientes.telefono', 'clientes.rol', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
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
            'usuario' => $usuario,
            'placas' => $placas,
            'vehiculos' => $vehiculo
        ]);
    }
    public function verificarContrato(Request $request)
    {
        $placa = $request->vehiculo;
        $fechaActual = now();

        DB::table('contratos')->where('vehiculo_id', $placa)
            ->where('estado_id', 1)
            ->where('fecha_fin', '<=', $fechaActual)->update([
                    'estado_id' => 3
                ]);

        $contratoActivo = DB::table('contratos')
            ->where('vehiculo_id', $placa)
            ->where('estado_id', 1)->count();
        if ($contratoActivo > 0) {
            return response()->json([
                'existe' => true,
            ]);
        }
        return response()->json([
            'existe' => false,
        ]);
    }
    public function registrarIngreso(Request $request)
    {
        $cliente = DB::table('clientes')->where('id', $request->propietario)->first();
        $vehiculo = DB::table('vehiculos')->where('placa', $request->vehiculo)->first();
        $vehiculosEnParqueo = DB::table('vehiculos')
            ->where('estado_parqueo', 1)
            ->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)
            ->count();
        $bahia = DB::table('bahias')->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)->first();

        
        $clienteTicket = DB::table('clientes')->where('id', $request->propietario)->first();

        if ($vehiculo->estado_parqueo == 1) {
            return redirect()->route('vigilante.vehicles')->with('error', 'El vehiculo ya se encuentra en el parqueadero');
        }

        if ($vehiculosEnParqueo >= $bahia->capacidad_maxima) {
            return redirect()->route('vigilante.vehicles')->with('error', 'No hay cupo disponible para este tipo de vehiculo');
        }
        $nuevaEntrada = DB::table('entrada_salida')->insertGetId(
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

        if ($cliente->rol === 1) {
            $ticketsDisponible = DB::table('tickets_salida')->where('estado', 0)->pluck('id')->all();
            if (count($ticketsDisponible) > 0) {
                $ticketAleatorio = $ticketsDisponible[array_rand($ticketsDisponible)];
                DB::table(table: 'entrada_salida')->where('id', $nuevaEntrada)->update([
                    'ticket_salida' => $ticketAleatorio
                ]);
                DB::table('tickets_salida')->where('id', $ticketAleatorio)->update([
                    'estado' => 1
                ]);

                $entrada=DB::table('entrada_salida')->where('id', $nuevaEntrada)->first();
                return redirect()->route('vigilante.vehicles')->with('success', "Registro de ingreso satisfactorio. Ticket: {$entrada->ticket_salida}");
            } else {
                return redirect()->route('vigilante.vehicles')->with('error', 'No hay tickets disponibles');
            }
        }


        return redirect()->route('vigilante.vehicles')->with('success', "Registro de ingreso satisfactorio. Ticket: {$clienteTicket->ticket_salida}");
    }
    public function registrarSalida(Request $request, $id, $placa,$propietario)
    {
        $salida= DB::table('entrada_salida')->where('id', $id)->first();
        $cliente= DB::table('clientes')->where('id', $propietario)->first();

        $vehiculo = DB::table('vehiculos')->where('placa', $placa)->first();
        $vehiculosEnParqueo = DB::table('vehiculos')
            ->where('estado_parqueo', 1)
            ->where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)
            ->count();
        if($cliente->rol == 1){
            if($request->ticket != $salida->ticket_salida){
                return redirect()->route('vigilante.vehicles')->with('error', 'El ticket no coincide');
            }
            DB::table('tickets_salida')->where('id', $salida->ticket_salida)->update([
                'estado'=>0
            ]);
            DB::table('entrada_salida')->where('id', $id)->update([
                'ticket_salida'=>null
            ]);
        }
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