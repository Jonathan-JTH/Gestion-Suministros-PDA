<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\Suministro;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ======================
        // DASHBOARD ADMIN
        // ======================
        if ($user->rol == 'admin') {

            return view('dashboard', [
                'totalSolicitudes' => Solicitud::count(),
                'pendientes' => Solicitud::where('estado', 'pendiente')->count(),
                'aprobadas' => Solicitud::where('estado', 'aprobado')->count(),
                'inventarioBajo' => Suministro::whereColumn('cantidad_actual', '<=', 'minimo')->count(),
                'usuarios' => User::count()
            ]);
        }

        // ======================
        // DASHBOARD USUARIO
        // ======================
        return view('dashboard', [
            'misSolicitudes' => Solicitud::where('usuario_id', $user->id)->count(),
            'pendientes' => Solicitud::where('usuario_id', $user->id)
                            ->where('estado', 'pendiente')->count(),
            'aprobadas' => Solicitud::where('usuario_id', $user->id)
                            ->where('estado', 'aprobado')->count()
        ]);
    }
}