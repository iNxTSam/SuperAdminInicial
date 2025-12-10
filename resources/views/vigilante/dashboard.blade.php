@extends('layouts.headerVigilante')

@section('title', 'Dashboard Vigilante')

@section('content')
    <h4>Dashboard Vigilante</h4>
    <p>Estado de bahías:</p>

    <div class="row g-3">

        @foreach ($ocupadosPorTipo as $bahia)
            <div class="col-md-3">
                <div class="{{$bahia->ocupados >= $bahia->capacidad_maxima ? 'card text-bg-danger' : 'card text-bg-success'}}">
                    <div class="card-body">
                        <div class="stat-title">{{ $bahia->nombre }}</div>
                        <div class="stat-number">{{ $bahia->ocupados }}/{{ $bahia->capacidad_maxima }}</div>
                        <div class="stat-status">
                            @if ($bahia->ocupados >= $bahia->capacidad_maxima)
                                Lleno
                            @else
                                Disponible
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection