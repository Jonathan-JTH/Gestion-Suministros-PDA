<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\BitacoraController;

// ------------------------------
// PÁGINA PRINCIPAL
// ------------------------------
Route::get('/', function () {
    return view('welcome');
});

// ------------------------------
// LOGIN / LOGOUT
// ------------------------------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ------------------------------
// RUTAS ADMIN
// ------------------------------
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventario completo
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/{id}/{cantidad}', [InventarioController::class, 'updateStock'])->name('inventario.update');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'resumen'])->name('reportes.index');

    // Bitácora de movimientos
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');

    // Aprobar solicitudes (solo admin)
    Route::post('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar'])->name('solicitudes.aprobar');
});

// ------------------------------
// RUTAS USUARIO NORMAL
// ------------------------------
Route::middleware(['auth', 'role:usuario'])->group(function () {

    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');

    // Opcional: ver detalles de su solicitud
    Route::get('/solicitudes/{solicitude}', [SolicitudController::class, 'show'])->name('solicitudes.show');
});