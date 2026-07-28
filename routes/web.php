<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\BitacoraController;

Route::get('/', function () {
    return Auth::check()
        ? redirect(auth()->user()->rol === 'admin' ? '/dashboard' : '/solicitudes')
        : redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/{id}/{cantidad}', [InventarioController::class, 'updateStock'])->name('inventario.update');

    Route::get('/reportes', [ReporteController::class, 'resumen'])->name('reportes.index');
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');

    Route::get('/solicitudes/gestion', [SolicitudController::class, 'gestion'])->name('solicitudes.gestion');
    Route::post('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{id}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::post('/solicitudes/{id}/despachar', [SolicitudController::class, 'despachar'])->name('solicitudes.despachar');
});

Route::middleware(['auth', 'role:usuario'])->group(function () {
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/solicitudes/{solicitude}', [SolicitudController::class, 'show'])->name('solicitudes.show');
});
