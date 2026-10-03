<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->tienePermiso('bitacora.ver')) {
            abort(403);
        }

        $query = Bitacora::with('usuario')->latest('created_at');

        if ($request->filled('q')) {
            $term = $request->string('q')->trim();
            $like = '%'.$term.'%';
            $query->where(function ($w) use ($like) {
                $w->where('detalle', 'like', $like)
                    ->orWhere('modulo', 'like', $like)
                    ->orWhere('accion', 'like', $like)
                    ->orWhere('ip_address', 'like', $like);
            });
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $bitacora = $query->paginate(20)->withQueryString();

        $modulos = Bitacora::query()->distinct()->orderBy('modulo')->pluck('modulo');
        $acciones = Bitacora::query()->distinct()->orderBy('accion')->pluck('accion');

        return view('bitacora.index', compact('bitacora', 'modulos', 'acciones'));
    }
}
