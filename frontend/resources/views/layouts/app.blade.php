<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestión de Suministros')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #eef2f7; min-height: 100vh; position: relative; }
        .brand-background {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .brand-background::before {
            content: '';
            position: absolute;
            inset: -8px;
            background-image: var(--brand-bg-image);
            background-size: cover;
            background-position: center 35%;
            background-repeat: no-repeat;
            opacity: 0.2;
            filter: saturate(0.7) blur(2px);
            transform: scale(1.03);
        }
        .brand-background::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(165deg, rgba(238,242,247,0.88) 0%, rgba(244,246,249,0.96) 45%, rgba(248,250,252,0.98) 100%);
        }
        .app-shell {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }
        .sidebar {
            width: 260px;
            background: rgba(31, 41, 55, 0.97);
            color: #e5e7eb;
            flex-shrink: 0;
            backdrop-filter: blur(6px);
        }
        .sidebar .brand {
            padding: 1.25rem 1rem;
            font-weight: 700;
            border-bottom: 1px solid #374151;
        }
        .sidebar .brand-line {
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .sidebar .brand-sub {
            font-size: 0.68rem;
            font-weight: 500;
            color: #9ca3af;
            margin-top: 0.35rem;
            letter-spacing: 0.02em;
        }
        .sidebar a {
            color: #d1d5db;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .75rem 1rem;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #374151;
            color: #fff;
            border-left-color: #3b82f6;
        }
        .content-wrap { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e5e7eb;
            padding: .85rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .main-content { padding: 1.5rem; }
        .kpi-card {
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            background: rgba(255, 255, 255, 0.97);
            color: #111827;
        }
        .kpi-card .kpi-label {
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .kpi-card .kpi-value { font-size: 1.75rem; font-weight: 700; line-height: 1.2; }
        .kpi-card .kpi-accent { width: 4px; border-radius: 4px; align-self: stretch; min-height: 2.5rem; }
        .form-card {
            border: 1px solid #e5e7eb !important;
            background: rgba(255, 255, 255, 0.98);
        }
        .form-card .form-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.35rem;
        }
        .detail-dl dt {
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .detail-dl dd { margin-bottom: 0.75rem; }
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, rgba(17,24,39,0.93), rgba(31,41,55,0.9));
        }
        body.has-brand-bg .auth-wrapper {
            background:
                linear-gradient(135deg, rgba(17,24,39,0.88), rgba(31,41,55,0.85)),
                var(--brand-bg-image, none) center/cover no-repeat fixed;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.98);
        }
        .auth-card.auth-card-wide { max-width: 480px; }
        .auth-brand-caption {
            font-size: 0.75rem;
            color: #6b7280;
            letter-spacing: 0.03em;
        }
        .modal-pro .modal-header {
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }
        .modal-pro .modal-footer {
            border-top: 1px solid #e5e7eb;
            background: #f8fafc;
        }
        .modal-pro .form-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        @media (max-width: 991px) {
            .app-shell { flex-direction: column; }
            .sidebar { width: 100%; }
        }
        .page-title { font-weight: 600; color: #111827; }
        .page-meta { color: #64748b; font-size: 0.875rem; }
        .filter-card {
            border: 1px solid #e5e7eb !important;
            background: rgba(255, 255, 255, 0.98);
        }
        .filter-card .form-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }
        .table-pro {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        .table-pro thead th {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-color: #374151 !important;
            white-space: nowrap;
        }
        .table-pro tbody td { vertical-align: middle; }
        .card-list-table {
            border: 1px solid #e5e7eb !important;
            background: rgba(255, 255, 255, 0.98);
        }
        .card-list-table .card-footer {
            background: rgba(255, 255, 255, 0.98);
            border-top: 1px solid #e5e7eb;
        }
    </style>
    @stack('styles')
</head>
@php
    $hasBrandBg = is_file(public_path('images/fabrigas-planta.png'));
    $brandBgInline = $hasBrandBg ? asset('images/fabrigas-planta.png') : null;
@endphp
<body class="@if($hasBrandBg) has-brand-bg @endif" @if($brandBgInline) style="--brand-bg-image: url('{{ $brandBgInline }}');" @endif>
@include('partials.brand-background')

@if(auth()->check() && session('2fa_verified'))
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-line">
                    <i class="bi bi-printer-fill fs-4 text-primary"></i>
                    <span>Gestión de Suministros</span>
                </div>
                <div class="brand-sub">Grupo Fabrigas · Planta industrial</div>
            </div>
            <nav class="py-2">
                @if(auth()->user()->isAdmin() || auth()->user()->isSoporte())
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-house"></i> Inicio</a>
                    <a href="{{ route('solicitudes.gestion') }}" class="{{ request()->routeIs('solicitudes.gestion') ? 'active' : '' }}"><i class="bi bi-inboxes"></i> Solicitudes</a>
                    <a href="{{ route('suministros.index') }}" class="{{ request()->routeIs('suministros.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Suministros</a>
                    <a href="{{ route('inventario.index') }}" class="{{ request()->routeIs('inventario.*') ? 'active' : '' }}"><i class="bi bi-archive"></i> Inventario</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Usuarios</a>
                    @endif
                    <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i> Reportes</a>
                    <a href="{{ route('trazabilidad.index') }}" class="{{ request()->routeIs('trazabilidad.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> Trazabilidad</a>
                    <a href="{{ route('bitacora.index') }}" class="{{ request()->routeIs('bitacora.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Bitácora</a>
                    <a href="{{ route('configuracion.index') }}" class="{{ request()->routeIs('configuracion.*') ? 'active' : '' }}"><i class="bi bi-gear"></i> Configuración</a>
                @elseif(auth()->user()->isSucursal())
                    <a href="{{ route('solicitudes.index') }}" class="{{ request()->routeIs('solicitudes.index') ? 'active' : '' }}"><i class="bi bi-search"></i> Consulta solicitudes</a>
                    <a href="{{ route('solicitudes.create') }}" class="{{ request()->routeIs('solicitudes.create') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Nueva solicitud</a>
                @endif
            </nav>
        </aside>
        <div class="content-wrap">
            <header class="topbar">
                <div class="fw-semibold">@yield('title', 'Panel')</div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small">{{ auth()->user()->nombre }}</span>
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Cerrar sesión</button>
                    </form>
                </div>
            </header>
            <main class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show">{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
@else
    @yield('content')
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
