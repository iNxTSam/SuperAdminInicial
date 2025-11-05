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

class AdminController extends Controller
{

public function dashboard()
{

    $totalUsuarios = Usuario::count();
    $totalContratos = Contrato::count();
    $contratosActivos = Contrato::whereHas('estado', fn($q) => $q->where('nombre', 'activo'))->count();


    $contratosVencidos = Contrato::with(['vehiculo.propietario', 'tarifa'])
        ->whereHas('estado', fn($q) => $q->where('nombre', 'vencido'))
        ->orderBy('fecha_fin', 'desc')
        ->get();


    $proximosVencimientos = Contrato::with(['vehiculo.propietario', 'tarifa'])
        ->whereBetween('fecha_fin', [now(), now()->addDays(7)])
        ->orderBy('fecha_fin')
        ->get();


    $bahias = \App\Models\Bahia::with('tipoVehiculo')
        ->where('activa', true)
        ->get()
        ->groupBy('tipo_vehiculo_id')
        ->map(function ($grupo) {
            $tipoVehiculo = $grupo->first()->tipoVehiculo->nombre ?? 'Sin tipo';
            $total = $grupo->sum('capacidad_maxima');
            $ocupadas = $grupo->sum('ocupada');
            return (object)[
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


public function usuarios(Request $request)
{

    $buscar = $request->input('buscar');


    $query = Usuario::with('rol')->orderBy('created_at', 'desc');

    if (!empty($buscar)) {
        $query->where(function($q) use ($buscar) {
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
    $request->validate([
        'nombre' => 'required|string|max:100',
        'cedula' => 'required|string|max:20|unique:usuarios,cedula',
        'carnet' => 'nullable|string|max:50',
        'contacto' => 'nullable|string|max:100',
        'email' => 'nullable|email|max:100',
        'rol_id' => 'required|exists:roles,id',
        'password' => 'required|string|min:6',
        'password_confirm' => 'required|string|min:6|same:password',
        'activo' => 'nullable|boolean',
    ]);

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

    $request->validate([
        'nombre' => 'required|string|max:100',
        'cedula' => 'required|string|max:20|unique:usuarios,cedula,' . $usuario->id,
        'carnet' => 'nullable|string|max:50',
        'contacto' => 'nullable|string|max:100',
        'email' => 'nullable|email|max:100',
        'rol_id' => 'required|exists:roles,id',
        'password' => 'nullable|string|min:6',
        'password_confirm' => 'nullable|string|min:6|same:password',
        'activo' => 'nullable|boolean',
    ]);

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




// ===========================
// 🔹 GESTIÓN DE CONTRATOS
// ===========================
public function contratos()
{
    // Cargar contratos con sus relaciones
    $contratos = Contrato::with(['vehiculo.propietario', 'tarifa', 'estado'])
        ->orderBy('fecha_fin', 'desc')
        ->get()
        ->map(function ($c) {
            return (object)[
                'id' => $c->id,
                'propietario' => $c->vehiculo->propietario->nombre ?? '—',
                'cedula' => $c->vehiculo->propietario->cedula ?? '—',
                'vehiculo' => $c->vehiculo->placa ?? '—',
                'fecha_inicio' => $c->fecha_inicio,
                'fecha_fin' => $c->fecha_fin,
                'valor_total' => $c->valor_total,
                'estado' => $c->estado->nombre ?? '—',
                'observaciones' => $c->observaciones,
            ];
        });

    $vehiculos = Vehiculo::with('propietario')->get();
    $tarifas = Tarifa::where('activa', true)->get();
    $estados = EstadoContrato::all();

    return view('admin.contratos.index', compact('contratos', 'vehiculos', 'tarifas', 'estados'));
}

public function storeContrato(Request $request)
{
    $request->validate([
        'vehiculo_id' => 'required|exists:vehiculos,id',
        'tarifa_id' => 'required|exists:tarifas,id',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        'valor_total' => 'required|numeric|min:0',
        'observaciones' => 'nullable|string|max:255',
    ]);

    Contrato::create([
        'vehiculo_id' => $request->vehiculo_id,
        'tarifa_id' => $request->tarifa_id,
        'fecha_inicio' => $request->fecha_inicio,
        'fecha_fin' => $request->fecha_fin,
        'estado_id' => EstadoContrato::where('nombre', 'activo')->value('id'),
        'valor_total' => $request->valor_total,
        'observaciones' => $request->observaciones,
        'created_by' => Auth::id(),
    ]);

    return redirect()->route('admin.contratos')->with('success', 'Contrato creado exitosamente.');
}

public function updateContrato(Request $request, $id)
{
    $contrato = Contrato::findOrFail($id);

    $request->validate([
        'valor_total' => 'required|numeric|min:0',
        'observaciones' => 'nullable|string|max:255',
    ]);

    $contrato->update([
        'valor_total' => $request->valor_total,
        'observaciones' => $request->observaciones,
    ]);

    return redirect()->route('admin.contratos')->with('success', 'Contrato actualizado correctamente.');
}




}
