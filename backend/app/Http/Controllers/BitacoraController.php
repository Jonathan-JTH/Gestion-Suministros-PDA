<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;

class BitacoraController extends Controller
{
    public function index()
    {
        if (! auth()->user()->tienePermiso('bitacora.ver')) {
            abort(403);
        }

        $bitacora = Bitacora::with('usuario')
            ->latest('created_at')
            ->paginate(25);

        return view('bitacora.index', compact('bitacora'));
    }
}
