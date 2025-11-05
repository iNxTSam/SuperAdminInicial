@extends('layouts.headerAdmin')

@section('title', 'Dashboard Administrador')

@section('content')
<div class="container py-4">
  <h3 class="mb-4">Dashboard Administrador</h3>

  <h5 class="mb-3">Vencimientos de contratos hoy:</h5>
  <div class="table-responsive mb-5">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Documento</th>
          <th>Código</th>
          <th>Vehículo</th>
          <th>Valor</th>
          <th>Inicio</th>
          <th>Fin</th>
        </tr>
      </thead>
      <tbody>
        @forelse($contratosVencidos as $contrato)
          <tr>
            <td>{{ $contrato->propietario }}</td>
            <td>{{ $contrato->cedula }}</td>
            <td>{{ $contrato->id }}</td>
            <td>{{ $contrato->vehiculo }}</td>
            <td>${{ number_format($contrato->valor_total, 0, ',', '.') }}</td>
            <td>{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted">No hay contratos próximos a vencer hoy.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>


  <h5 class="mb-3">Estado de bahías:</h5>
  <div class="row g-3 mb-4">
    @forelse($bahias as $bahia)
      <div class="col-md-3">
        <div class="card text-center shadow-sm">
          <div class="card-header fw-bold">{{ $bahia->tipo_vehiculo }}</div>
          <div class="card-body">
            <h4 class="card-title">{{ $bahia->ocupadas }}/{{ $bahia->total }}</h4>
            <p class="card-text">
              @if($bahia->ocupadas >= $bahia->total)
                <span class="badge bg-danger">Lleno</span>
              @else
                <span class="badge bg-success">Disponible</span>
              @endif
            </p>
          </div>
        </div>
      </div>
    @empty
      <p class="text-muted">No hay bahías registradas.</p>
    @endforelse
  </div>


  <h5 class="mb-3">Notificaciones recientes:</h5>
  <div class="list-group shadow-sm">
    @forelse($notificaciones as $notif)
      <div class="list-group-item d-flex justify-content-between align-items-center">
        <span>{{ $notif->mensaje }}</span>
        <small class="text-muted">{{ \Carbon\Carbon::parse($notif->fecha_alerta)->format('h:i A') }}</small>
      </div>
    @empty
      <div class="list-group-item text-center text-muted">Sin notificaciones recientes.</div>
    @endforelse
  </div>
</div>
@endsection
