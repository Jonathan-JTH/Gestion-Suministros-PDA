@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<h2 class="mb-4">Panel Principal</h2>

@if(auth()->user()->rol == 'admin')

<!-- ================= ADMIN ================= -->
<div class="row">

    <div class="col-md-3">
        <div class="card bg-primary text-white mb-3">
            <div class="card-body">
                <h5>Total Solicitudes</h5>
                <h3>{{ $totalSolicitudes }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white mb-3">
            <div class="card-body">
                <h5>Pendientes</h5>
                <h3>{{ $pendientes }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white mb-3">
            <div class="card-body">
                <h5>Aprobadas</h5>
                <h3>{{ $aprobadas }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-danger text-white mb-3">
            <div class="card-body">
                <h5>Inventario Bajo</h5>
                <h3>{{ $inventarioBajo }}</h3>
            </div>
        </div>
    </div>

</div>

<div class="card mt-3">
    <div class="card-body">
        <h5>Usuarios en el sistema: {{ $usuarios }}</h5>
    </div>
</div>

@else

<!-- ================= USUARIO ================= -->
<div class="row">

    <div class="col-md-4">
        <div class="card bg-primary text-white mb-3">
            <div class="card-body">
                <h5>Mis Solicitudes</h5>
                <h3>{{ $misSolicitudes }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-warning text-white mb-3">
            <div class="card-body">
                <h5>Pendientes</h5>
                <h3>{{ $pendientes }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-success text-white mb-3">
            <div class="card-body">
                <h5>Aprobadas</h5>
                <h3>{{ $aprobadas }}</h3>
            </div>
        </div>
    </div>

</div>

<a href="/solicitudes/create" class="btn btn-primary mt-3">
    Nueva Solicitud
</a>

@endif

@endsection