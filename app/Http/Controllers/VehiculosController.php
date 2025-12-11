<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehiculosController extends Controller
{
    // #VISTA GESTION DE VEHICULOS
    // public function gestion()
    // {
    //     $usuario->id = DB::table('usuarios')->where('id', Auth::id())->first();
    //     $rol= '';
    //     $usuario->id == 1? $rol = 'superadmin' : ($usuario->id == 2 ? $rol= 'admin':'vigilante');
        
    //     $vehiculos = DB::table('vehiculos')
    //         ->join('tipos_vehiculo', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
    //         ->select('vehiculos.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
    //         ->orderBy('vehiculos.created_at')
    //         ->get();
    //     $tipos_vehiculo = DB::table('tipos_vehiculo')->get();

    //     return view(`{$rol}.vehiculos.vehiculos`, compact('vehiculos', 'tipos_vehiculo'));
    // }

    // public function nuevoVehiculo(Request $request)
    // {
    //     $usuario->id = DB::table('usuarios')->where('id', Auth::id())->first();
    //     $rol= '';
    //     $usuario->id == 1? $rol = 'superadmin' : ($usuario->id == 2 ? $rol= 'admin':'vigilante');
        
    //     $cliente = DB::table('clientes')->where('id', $request->cedula)->first();
    //     $vehiculo = DB::table('vehiculos')->where('placa', $request->placa)->first();
    //     if (!$cliente) {
    //         return redirect()->route(`{$rol}.gestionvehiculos`)->with("error", "El cliente no existe");
    //     }
    //     if ($vehiculo) {
    //         return redirect()->route(`{$rol}.gestionvehiculos`)->with("error", "El vehiculo ya existe");
    //     }

    //     DB::table('vehiculos')->insert([
    //         'placa' => $request->placa,
    //         'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
    //         'propietario_id' => $request->cedula,
    //         'autorizado_por_id' => $request->user_id,
    //         'color' => $request->color,
    //         'marca' => $request->marca,
    //         'modelo' => $request->modelo,
    //     ]);

    //     return redirect()->route(`{$rol}.gestionvehiculos`)->with("succes", "Vehiculo registrado correctamente");
    // }

    // public function updateVehiculo(Request $request, $id)
    // {
    //     $usuario->id = DB::table('usuarios')->where('id', Auth::id())->first();
    //     $rol= '';
    //     $usuario->id == 1? $rol = 'superadmin' : ($usuario->id == 2 ? $rol= 'admin':'vigilante');
    //     $cliente = DB::table('clientes')->where('id', $request->propietario)->first();

    //     $vehiculo = DB::table('vehiculos')->where('placa', $request->placa)->first();
    //     if (!$cliente) {
    //         return redirect()->route(`{$rol}.gestionvehiculos`)->with("error", "El cliente no existe");
    //     }
    //     if ($vehiculo) {
    //         return redirect()->route(`{$rol}.gestionvehiculos`)->with("error", "No se puede editar porque la placa ya existe");
    //     }

    //     DB::table('vehiculos')
    //         ->where('placa', $id)
    //         ->update([
    //             'placa' => $request->placa,
    //             'tipo_vehiculo_id' => $request->tipoVehiculo,
    //             'propietario_id' => $request->propietario,
    //             'color' => $request->color,
    //             'marca' => $request->marca,
    //             'modelo' => $request->modelo,
    //             'updated_at' => now()->setTimezone('America/Bogota')
    //         ]);

    //     return redirect()->route(`{$rol}.gestionvehiculos`)->with("succes", "Informacion actualizada correctamente correctamente");
    // }
}
