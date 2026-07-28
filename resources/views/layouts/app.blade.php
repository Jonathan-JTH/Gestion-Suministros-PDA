<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Gestión de Suministros')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ auth()->check() ? (auth()->user()->rol === 'admin' ? route('dashboard') : route('solicitudes.index')) : url('/login') }}">
                Suministros
            </a>

            @auth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    @if(auth()->user()->rol === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('solicitudes.gestion') ? 'active' : '' }}" href="{{ route('solicitudes.gestion') }}">Solicitudes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}" href="{{ route('inventario.index') }}">Inventario</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">Reportes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('bitacora.*') ? 'active' : '' }}" href="{{ route('bitacora.index') }}">Bitácora</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('solicitudes.*') ? 'active' : '' }}" href="{{ route('solicitudes.index') }}">Mis Solicitudes</a>
                        </li>
                    @endif
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-light me-3">{{ auth()->user()->nombre }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
