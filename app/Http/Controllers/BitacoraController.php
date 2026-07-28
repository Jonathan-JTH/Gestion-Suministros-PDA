<?php

namespace App\Http\Controllers;

use App\Models\BitacoraMovimiento;

class BitacoraController extends Controller
{
    public function index()
    {
        $bitacora = BitacoraMovimiento::with('usuario')
            ->latest('created_at')
            ->get();

        return view('bitacora.index', compact('bitacora'));
    }
}
