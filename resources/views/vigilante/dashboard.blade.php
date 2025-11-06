@extends('layouts.headerVigilante')

@section('title', 'Dashboard Vigilante')

@section('content')
    <h4>Dashboard Vigilante</h4>
    <p>Estado de bahías:</p>

    <div class="d-flex gap-3 flex-wrap">
        <div class="stat-card bg-success">
            <div class="stat-title">Moto</div>
            <div class="stat-number">90/{{ ($bahiasMoto->first())->capacidad_maxima ?? 0 }}</div>
            <div class="stat-status">Lleno</div>
        </div>

        <div class="stat-card bg-primary">
            <div class="stat-title">Carro</div>
            <div class="stat-number">5/{{ ($bahiasAuto->first())->capacidad_maxima ?? 0}}</div>
            <div class="stat-status">Disponible</div>
        </div>

        <div class="stat-card bg-warning text-dark">
            <div class="stat-title">Bicicleta</div>
            <div class="stat-number">40/{{ ($bahiasBicicleta->first())->capacidad_maxima ?? 0 }}</div>
            <div class="stat-status">Disponible</div>
        </div>

        <div class="stat-card bg-danger">
            <div class="stat-title">Motos eléctricas</div>
            <div class="stat-number">3/{{ $bahiasElectricas->first()?->capacidad_maxima ?? 0 }}</div>
            <div class="stat-status">Disponible</div>
        </div>
    </div>
@endsection
