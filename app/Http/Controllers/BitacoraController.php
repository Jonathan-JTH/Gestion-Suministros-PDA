<?php

namespace App\Http\Controllers;

use App\Models\BitacoraMovimiento;

class BitacoraController extends Controller
{
    public function index()
    {
        return BitacoraMovimiento::with('usuario')->get();
    }
}