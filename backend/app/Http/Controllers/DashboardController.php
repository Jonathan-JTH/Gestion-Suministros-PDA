<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Solicitud;
use App\Models\Suministro;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('rol');

        if (! $user->tienePermiso('dashboard.ver')) {
            abort(403);
        }

        $estadosLabels = ['pendiente', 'en_proceso', 'atendida', 'rechazada'];
        $estadosCounts = Solicitud::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');
        $estadosValues = array_map(fn ($e) => (int) ($estadosCounts[$e] ?? 0), $estadosLabels);

        $movimientosMes = MovimientoInventario::select(
            DB::raw("DATE_FORMAT(fecha_movimiento, '%Y-%m') as mes"),
            'tipo',
            DB::raw('SUM(ABS(cantidad)) as total')
        )
            ->where('fecha_movimiento', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('mes', 'tipo')
            ->orderBy('mes')
            ->get();

        $meses = [];
        $entradas = [];
        $salidas = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i)->format('Y-m');
            $meses[] = now()->subMonths($i)->format('M');
            $entradas[] = (int) $movimientosMes->where('mes', $mes)->where('tipo', 'entrada')->sum('total');
            $salidas[] = (int) $movimientosMes->where('mes', $mes)->where('tipo', 'salida')->sum('total');
        }

        $inventarioBajo = Inventario::with('suministro')
            ->get()
            ->filter(fn ($i) => $i->bajoStockMinimo())
            ->count();

        return view('dashboard', [
            'pendientes' => Solicitud::where('estado', 'pendiente')->count(),
            'enProceso' => Solicitud::where('estado', 'en_proceso')->count(),
            'atendidas' => Solicitud::where('estado', 'atendida')->count(),
            'rechazadas' => Solicitud::where('estado', 'rechazada')->count(),
            'totalSuministros' => Suministro::where('activo', true)->count(),
            'totalUsuarios' => User::where('activo', true)->count(),
            'inventarioBajo' => $inventarioBajo,
            'estadosLabels' => $estadosLabels,
            'estadosValues' => $estadosValues,
            'chartMeses' => $meses,
            'chartEntradas' => $entradas,
            'chartSalidas' => $salidas,
        ]);
    }
}
