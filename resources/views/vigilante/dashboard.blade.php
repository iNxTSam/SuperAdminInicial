@extends('layouts.headerVigilante')

@section('title', 'Dashboard Vigilante')

@section('content')
    <h4>Dashboard Vigilante</h4>
    <p>Estado de bahías:</p>

    <div class="d-flex gap-3 flex-wrap">
        @foreach ( $bahias as $bahia )
        <div class="stat-card bg-success">
            <div class="stat-title">{{ $bahia->tipo_vehiculo_nombre }}</div>
            <div class="stat-number">{{ $bahia->ocupada }}/{{ $bahia->capacidad_maxima }}</div>
            <div class="stat-status">
                @if ($bahia->ocupada >= $bahia->capacidad_maxima)
                    Lleno
                @else
                    Disponible
                @endif
        </div>
        </div>
        @endforeach
    </div>
@endsection
