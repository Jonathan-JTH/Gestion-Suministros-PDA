<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SuministroController;
use App\Http\Controllers\TrazabilidadController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user()->load('rol');

    return ($user->isAdmin() || $user->isSoporte())
        ? redirect('/dashboard')
        : redirect('/solicitudes');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/login/2fa/setup', [AuthController::class, 'showTwoFactorSetup'])->name('login.2fa.setup');
    Route::post('/login/2fa/setup', [AuthController::class, 'confirmTwoFactorSetup'])->middleware('throttle:6,1')->name('login.2fa.setup.submit');
    Route::get('/login/2fa', [AuthController::class, 'showTwoFactor'])->name('login.2fa');
    Route::post('/login/2fa', [AuthController::class, 'verifyTwoFactor'])->middleware('throttle:6,1')->name('login.2fa.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', '2fa', 'role:admin,soporte'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/suministros', [SuministroController::class, 'index'])->name('suministros.index');
    Route::get('/suministros/create', [SuministroController::class, 'create'])->name('suministros.create');
    Route::post('/suministros', [SuministroController::class, 'store'])->name('suministros.store');
    Route::get('/suministros/{suministro}/edit', [SuministroController::class, 'edit'])->name('suministros.edit');
    Route::put('/suministros/{suministro}', [SuministroController::class, 'update'])->name('suministros.update');

    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/{id}/entrada', [InventarioController::class, 'entrada'])->name('inventario.entrada');
    Route::post('/inventario/{id}/ajuste', [InventarioController::class, 'ajuste'])->name('inventario.ajuste');

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/generar', [ReporteController::class, 'generar'])->name('reportes.generar');
    Route::get('/reportes/pdf', [ReporteController::class, 'pdf'])->name('reportes.pdf');

    Route::get('/trazabilidad', [TrazabilidadController::class, 'index'])->name('trazabilidad.index');
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion/notificaciones', [ConfiguracionController::class, 'actualizarNotificaciones'])->name('configuracion.notificaciones');

    Route::get('/solicitudes/gestion', [SolicitudController::class, 'gestion'])->name('solicitudes.gestion');
    Route::post('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{id}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::post('/solicitudes/{id}/atender', [SolicitudController::class, 'atender'])->name('solicitudes.atender');
});

Route::middleware(['auth', '2fa', 'role:admin'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
});

Route::middleware(['auth', '2fa', 'role:sucursal'])->group(function () {
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
});

Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('/solicitudes/{solicitude}', [SolicitudController::class, 'show'])->name('solicitudes.show');
});
