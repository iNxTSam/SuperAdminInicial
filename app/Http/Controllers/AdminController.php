<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Contrato;
use App\Models\Tarifa;
use App\Models\EstadoContrato;
use App\Models\Vehiculo;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    #VISTA DE DASHBOARD
    public function dashboard()
    {

        $totalUsuarios = Usuario::count();
        $totalContratos = Contrato::count();
        $contratosActivos = Contrato::whereHas('estado', fn($q) => $q->where('nombre', 'activo'))->count();


        $contratosVencidos = Contrato::with(['vehiculo.propietario', 'tarifa'])
            ->whereHas('estado', fn($q) => $q->where('nombre', 'vencido'))
            ->orderBy('fecha_fin', 'desc')
            ->get();


        $proximosVencimientos = DB::table('contratos')->where('estado_id', 1)
            ->join('vehiculos', 'contratos.vehiculo_id', '=', 'vehiculos.placa')
            ->join('clientes', 'vehiculos.propietario_id', '=', 'clientes.id')
            ->join('tarifas', 'contratos.tarifa_id', '=', 'tarifas.id')
            ->whereBetween('contratos.fecha_fin', [now(), now()->addDays(7)])
            ->select(
                'contratos.*',
                'clientes.nombre as propietario_nombre',
                'clientes.id as propietario_cedula',
                'vehiculos.placa',
                'tarifas.nombre as tarifa_nombre'
            )
            ->orderBy('contratos.fecha_fin')
            ->get();


        $bahias = \App\Models\Bahia::with('tipoVehiculo')
            ->where('activa', true)
            ->get()
            ->groupBy('tipo_vehiculo_id')
            ->map(function ($grupo) {
                $tipoVehiculo = $grupo->first()->tipoVehiculo->nombre ?? 'Sin tipo';
                $total = $grupo->sum('capacidad_maxima');
                $ocupadas = $grupo->sum('ocupada');
                return (object) [
                    'tipo_vehiculo' => $tipoVehiculo,
                    'total' => $total,
                    'ocupadas' => $ocupadas,
                ];
            })
            ->values();


        $notificaciones = collect([

        ]);

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'totalContratos',
            'contratosActivos',
            'contratosVencidos',
            'proximosVencimientos',
            'bahias',
            'notificaciones'
        ));
    }

    #VISTA DE USUARIOS
    public function usuarios(Request $request)
    {

        $buscar = $request->input('buscar');


        $query = Usuario::with('rol')->orderBy('created_at', 'desc');

        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                    ->orWhere('cedula', 'LIKE', "%{$buscar}%")
                    ->orWhere('carnet', 'LIKE', "%{$buscar}%")
                    ->orWhere('email', 'LIKE', "%{$buscar}%");
            });
        }

        $usuarios = $query->get();
        $roles = Rol::all();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'buscar'));
    }
    public function storeUsuario(Request $request)
    {

        if (!$request->nombre || !$request->cedula || !$request->password) {
            return redirect()->route('admin.usuarios')->with('error', 'Por favor llene los campos obligaorios');
        }
        $usuarioExistente = Usuario::where('cedula', $request->cedula)->first();

        if ($usuarioExistente) {
            return redirect()->route('admin.usuarios')->with('error', 'El numero de documento ya existe');
        }
        Usuario::create([
            'nombre' => $request->nombre,
            'cedula' => $request->cedula,
            'carnet' => $request->carnet,
            'contacto' => $request->contacto,
            'email' => $request->email,
            'rol_id' => $request->rol_id,
            'password' => bcrypt($request->password),
            'activo' => $request->activo ?? 1,
        ]);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario registrado exitosamente.');
    }

    public function updateUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuarios = DB::table('usuarios')->where('cedula', $request->cedula)->count();
        if ($usuarios > 0) {
            return redirect()->route('admin.usuarios')->with('error', 'El numero de documento ya esta registrado');
        }
        if (!$request->nombre || !$request->cedula || !$request->rol_id || !$request->password) {
            return redirect()->route('admin.usuarios')->with('error', 'Por favor llene los campos obligatorios');
        }


        $usuario->update([
            'nombre' => $request->nombre,
            'cedula' => $request->cedula,
            'carnet' => $request->carnet,
            'contacto' => $request->contacto,
            'email' => $request->email,
            'rol_id' => $request->rol_id,
            'password' => $request->password ? bcrypt($request->password) : $usuario->password,
            'activo' => $request->activo ?? $usuario->activo,
        ]);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleUsuario($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->activo = !$usuario->activo;
        $usuario->save();

        return redirect()->route('admin.usuarios')->with('success', 'Estado de usuario actualizado correctamente.');
    }

    //VISTA DE CONTRATOS
    public function contratos()
    {
        //Actualizar estado de los contratos
        $contratoss = DB::table('contratos')->get();
        $fechaActual = now();

        Contrato::where('fecha_fin', '<=', $fechaActual)
            ->where('estado_id', '!=', 2)
            ->update(['estado_id' => 3]);



        // Cargar contratos con sus relaciones
        $contratos = DB::table('contratos')
            ->join('vehiculos', 'contratos.vehiculo_id', '=', 'vehiculos.placa')
            ->join('clientes', 'vehiculos.propietario_id', '=', 'clientes.id')
            ->join('tarifas', 'contratos.tarifa_id', '=', 'tarifas.id')
            ->join('estados_contrato', 'contratos.estado_id', '=', 'estados_contrato.id')
            ->select(
                'contratos.id',
                'clientes.nombre as propietario',
                'clientes.id as cedula',
                'vehiculos.placa as vehiculo',
                'contratos.fecha_inicio',
                'contratos.fecha_fin',
                'contratos.valor as valor_total',
                'estados_contrato.nombre as estado',
                'contratos.observaciones',
                'estados_contrato.id as estado_id'
            )
            ->orderBy('contratos.id', 'desc')
            ->get();


        $vehiculos = Vehiculo::with('propietario')->get();
        $tarifas = Tarifa::where('activa', true)->get();
        $estados = EstadoContrato::all();

        return view('admin.contratos.index', compact('contratos', 'vehiculos', 'tarifas', 'estados'));
    }

    public function storeContrato(Request $request)
    {

        $tarifa = DB::table('tarifas')->where('id', $request->tarifa_id)->first();
        $fecha_inicio = now();
        $fecha_fin = $fecha_inicio->clone();

        $tipo_tarifa = $tarifa->tipo ?? 'Mes';

        $contratoActivo = DB::table('contratos')->where('vehiculo_id', $request->vehiculo)
            ->where('estado_id', 1)->count();
        if ($contratoActivo > 0) {
            return redirect()->route('admin.contratos')->with('error', 'El vehiculo ya tiene un contrato activo');
        }
        switch ($tipo_tarifa) {
            case 'Dia':
                $fecha_fin->addDay();
                break;
            case 'Semana':
                $fecha_fin->addDays(7);
                break;
            case 'Quincena':
                $fecha_fin->addDays(15);
                break;
            case 'Mes':
                $fecha_fin->addMonth();
                break;
            default:
                $fecha_fin->addMonth();
        }

        $valor_total = $tarifa->valor;

        try {
            Contrato::create([
                'propietario_id' => $request->propietario,

                'vehiculo_id' => $request->vehiculo,
                'tarifa_id' => $request->tarifa_id,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'estado_id' => EstadoContrato::where('nombre', 'activo')->value('id'),
                'observaciones' => $request->observaciones,
                'created_by' => Auth::id(),
                'valor' => $valor_total,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.contratos')->with('error', 'Error al crear el contrato: ' . $e->getMessage());
        }


        return redirect()->route('admin.contratos')->with('success', 'Contrato creado exitosamente.');
    }
    public function updateContrato(Request $request, $id)
    {
        $contrato = Contrato::findOrFail($id);
        $contratoActivo = DB::table('contratos')->where('vehiculo_id', $request->vehiculo)
            ->where('estado_id', 1)
            ->count();
        $fechaActual = now();
        if ($contrato->fecha_fin <= $fechaActual) {
            return redirect()->route('admin.contratos')->with('error', 'No se puede hacer esto');
        }

        if ($contratoActivo > 0 && $request->estado == 1) {
            return redirect()->route('admin.contratos')->with('error', 'No se puede hacer esto');
        }

        $contrato->update([
            'valor' => $request->valor_total,
            'estado_id' => $request->estado,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('admin.contratos')->with('success', 'Contrato actualizado correctamente.');
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

    #VISTA DE CLIENTES
    public function clientes()
    {
        $clientes = DB::table('clientes')
            ->join('rolclientes', 'clientes.rol', '=', 'rolclientes.idRol')
            ->select('clientes.*', 'rolclientes.nombre as rol_nombre')
            ->get();
        $roles = DB::table('rolclientes')->get();
        return view('admin.clientes.clientes', compact('clientes', 'roles'));
    }

    public function storeCliente(Request $request)
    {
        if (!$request->cedula || !$request->nombre || !$request->telefono || !$request->correo || !$request->rol) {
            return redirect()->route('admin.clientes')->with('error', 'Por favor llene todos los campos');
        }
        $usuario = DB::table('clientes')->where('id', $request->cedula)->count();
        if ($usuario > 0) {
            return redirect()->route('admin.clientes')->with('error', 'El cliente ya esta registrado');
        }
        DB::table('clientes')->insert([
            'id' => $request->cedula,
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'activo' => 1,
            'rol' => $request->rol
        ]);

        return redirect()->route('admin.clientes')->with('success', 'Cliente creado exitosamente.');
    }

    public function updateCliente(Request $request, $id)
    {
        if (!$request->cedula || !$request->nombre || !$request->correo || !$request->estado || !$request->telefono) {
            return redirect()->route('admin.clientes')->with('error', 'Por favor llene todos los campos');
        }
        $cliente = DB::table('clientes')->where('id', $request->cedula)->count();
        if ($cliente > 0) {
            return redirect()->route('admin.clientes')->with('error', 'El numero de documento ya se encuentra registrado');
        }
        DB::table('clientes')
            ->where('id', $id)
            ->update([
                'id' => $request->cedula,
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'activo' => $request->estado,
                'telefono' => $request->telefono,
            ]);

        return redirect()->route('admin.clientes')->with('success', 'Cliente actualizado correctamente.');
    }

    #VISTA GESTION DE VEHICULOS
    public function gestion()
    {
        $vehiculos = DB::table('vehiculos')
            ->join('tipos_vehiculo', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->select('vehiculos.*', 'tipos_vehiculo.nombre as tipo_vehiculo_nombre')
            ->orderBy('vehiculos.created_at')
            ->get();
        $tipos_vehiculo = DB::table('tipos_vehiculo')->get();

        return view('admin.vehiculos.vehiculos', compact('vehiculos', 'tipos_vehiculo'));
    }

    public function nuevoVehiculo(Request $request)
    {

        $cliente = DB::table('clientes')->where('id', $request->cedula)->first();
        $vehiculo = DB::table('vehiculos')->where('placa', $request->placa)->first();
        if (!$request->placa || !$request->tipo_vehiculo_id || !$request->cedula || !$request->color || !$request->marca || !$request->modelo) {
            return redirect()->route('admin.gestionvehiculos')->with("error", "Por favor llene todos los campos");
        }
        if (!$cliente) {
            return redirect()->route('admin.gestionvehiculos')->with("error", "El cliente no existe");
        }
        if ($vehiculo) {
            return redirect()->route('admin.gestionvehiculos')->with("error", "El vehiculo ya existe");
        }

        DB::table('vehiculos')->insert([
            'placa' => $request->placa,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'propietario_id' => $request->cedula,
            'autorizado_por_id' => $request->user_id,
            'color' => $request->color,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
        ]);

        return redirect()->route('admin.gestionvehiculos')->with("succes", "Vehiculo registrado correctamente");
    }

    public function updateVehiculo(Request $request, $id)
    {

        if (!$request->placa || !$request->tipoVehiculo || !$request->propietario || !$request->color || !$request->marca || !$request->modelo) {
            return redirect()->route('admin.gestionvehiculos')->with("error", "Por favor llene todos los campos");
        }
        $cliente = DB::table('clientes')->where('id', $request->propietario)->first();
        $vehiculo = DB::table('vehiculos')->where('placa', $request->placa)->first();
        if (!$cliente) {
            return redirect()->route('admin.gestionvehiculos')->with("error", "El cliente no existe");
        }
        if ($vehiculo) {
            return redirect()->route('admin.gestionvehiculos')->with("error", "La placa ya esta registrada");
        }

        DB::table('vehiculos')
            ->where('placa', $id)
            ->update([
                'placa' => $request->placa,
                'tipo_vehiculo_id' => $request->tipoVehiculo,
                'propietario_id' => $request->propietario,
                'color' => $request->color,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'updated_at' => now()->setTimezone('America/Bogota')
            ]);

        return redirect()->route('admin.gestionvehiculos')->with("succes", "Informacion actualizada correctamente correctamente");
    }
}
