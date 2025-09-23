@extends('layouts.app')

@section('title', 'Configuración Global')

@section('content')
<h2>Configuración Global</h2>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow text-bg-primary">
            <div class="card-body">
                <h6>Capacidad Total</h6>
                <p class="fs-4">{{ $configuraciones['capacidad_total'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow text-bg-success">
            <div class="card-body">
                <h6>Tarifas Activas</h6>
                <p class="fs-4">{{ $configuraciones['tarifas_activas'] }}</p>
            </div>
        </div>
    </div>
</div>

<h4 class="mt-4">Usuarios por Rol</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Rol</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($configuraciones['usuarios_por_rol'] as $rol)
        <tr>
            <td>{{ $rol->nombre }}</td>
            <td>{{ $rol->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4 class="mt-4">Tipos de Vehículo</h4>
<form action="{{ route('superadmin.configuracion.tipoVehiculo') }}" method="POST" class="card p-3 shadow">
    @csrf
    <div class="row g-3">
        <div class="col-md-5">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
        </div>
        <div class="col-md-5">
            <input type="text" name="descripcion" class="form-control" placeholder="Descripción">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Agregar</button>
        </div>
    </div>
</form>

<ul class="list-group mt-3">
    @foreach($configuraciones['tipos_vehiculo'] as $tipo)
        <li class="list-group-item">{{ $tipo->nombre }} - {{ $tipo->descripcion }}</li>
    @endforeach
</ul>
@endsection
