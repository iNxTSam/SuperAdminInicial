<?php
namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class Register extends Controller
{
    public function view()
    {
        return view('register');
    }
    public function crear(Request $request)
    {
        $id = $request->numeroDocumento;
        $correo = $request->correo;
        $nombre = $request->nombre;
        $numeroCarnet = $request->carnet;
        $telefono = $request->telefono;
        $rol = $request->rol;
        $password = $request->clave;
        $passwordConfirm = $request->claveConfirm;
        if (!$id || !$correo || !$nombre || !$numeroCarnet || !$telefono || !$rol || !$password || !$passwordConfirm) {
            return redirect()->route('register')->withErrors(['error' => 'Porfavor llene todos los campos']);
        }
        $userExist = Usuario::where('cedula', $request->numeroDocumento)
            ->first();

        if ($userExist) {
            return redirect()->route('register')->withErrors(['error' => 'Este usuario ya existe']);
        }

        if ($password !== $passwordConfirm) {
            return redirect()->route('register')->withErrors(['error' => 'Las contraseñas no coinciden']);
        }

        Usuario::create([
            'id' => 1,
            'nombre' => $nombre,
            'cedula' => $id,
            'carnet' => $numeroCarnet,
            'contacto' => $telefono,
            'email' => $correo,
            'password' => bcrypt($password),
            'rol_id' => $rol,
            'activo' => 1
        ]);

        return redirect()->route('login');
    }
}