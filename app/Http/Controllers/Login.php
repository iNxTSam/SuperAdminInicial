<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function view()
    {
        return view('signIn');
    }
    public function loginUser(Request $request)
    {
        $user = Usuario::where('cedula', $request->numeroDocumento)
            ->first();

        if ($user && Hash::check($request->clave, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            if ($user->rol_id === 1) {
                return redirect()->route('superadmin.dashboard');
            }
            if ($user->rol_id === 2) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->rol_id === 3) {
                return redirect()->route('vigilante.dashboard');
            }

            return redirect()->route('login')->withErrors(['error' => 'No es posible iniciar sesión']);


        }
        return redirect()->route('login')->withErrors(['error' => 'Documento o contraseña incorrectos']);
    }
}