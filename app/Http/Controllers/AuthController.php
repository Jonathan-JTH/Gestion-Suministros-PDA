<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // MOSTRAR LOGIN (IMPORTANTE PARA VISTA)
    public function showLogin()
    {
        return view('auth.login');
    }

    // LOGIN DEL SISTEMA
    public function login(Request $request)
    {
        // Validar datos
        $credentials = $request->validate([
            'correo' => 'required|email',
            'password' => 'required'
        ]);

        // Intentar autenticar usuario usando columna 'correo'
        if (Auth::attempt([
            'correo' => $credentials['correo'], // mapear al campo 'correo'
            'password' => $credentials['password']
        ])) {

            // Regenerar sesión
            $request->session()->regenerate();

            // Obtener usuario autenticado
            $user = Auth::user();

            // REDIRECCIÓN POR ROL
            if ($user->rol === 'admin') {
                return redirect('/dashboard'); // Admin ve todo
            }

            if ($user->rol === 'usuario') {
                return redirect('/solicitudes'); // Usuario solo solicitudes
            }
        }

        // Credenciales incorrectas
        return back()->withErrors([
            'correo' => 'Credenciales incorrectas'
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}