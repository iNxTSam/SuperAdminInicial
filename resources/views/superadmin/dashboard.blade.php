@extends('layouts.app')

@section('title', 'Dashboard SuperAdmin')

@section('content')
<h2 class="mb-4">Dashboard Super Administrador</h2>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card text-bg-primary shadow">
            <div class="card-body">
                <h5>Total Usuarios</h5>
                <p class="fs-3">{{ $stats['total_usuarios'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning shadow">
            <div class="card-body">
                <h5>Total Clientes</h5>
                <p class="fs-3">{{ $stats['total_clientes'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success shadow">
            <div class="card-body">
                <h5>Total Vehículos</h5>
                <p class="fs-3">{{ $stats['total_vehiculos'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info shadow">
            <div class="card-body">
                <h5>Contratos Activos</h5>
                <p class="fs-3">{{ $stats['contratos_activos'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <h4>Ocupación por tipo de vehículo</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tipo Vehículo</th>
                <th>Total</th>
                <th>Ocupadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ocupacion_por_tipo as $item)
            <tr>
                <td>{{ $item->nombre }}</td>
                <td>{{ $item->capacidad_maxima }}</td>
                <td>{{ $item->vehiculos_en_parqueo }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
