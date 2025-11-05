@extends('layouts.headerAdmin')

@section('title', 'Gestión de Contratos')

@section('content')
<div class="container py-4">
  <h3 class="mb-4">Gestión de Contratos</h3>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <form action="{{ route('admin.contratos') }}" method="GET" class="d-flex">
      <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar contrato..." value="{{ request('buscar') }}">
      <button type="submit" class="btn btn-outline-primary">Buscar</button>
    </form>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoContrato">
      + Nuevo Contrato
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>Propietario</th>
          <th>Documento</th>
          <th>Vehículo</th>
          <th>Inicio</th>
          <th>Fin</th>
          <th>Valor</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($contratos as $contrato)
        <tr>
          <td>{{ $contrato->propietario }}</td>
          <td>{{ $contrato->cedula }}</td>
          <td>{{ $contrato->vehiculo }}</td>
          <td>{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</td>
          <td>{{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}</td>
          <td>${{ number_format($contrato->valor_total, 0, ',', '.') }}</td>
          <td>
            @if($contrato->estado == 'activo')
              <span class="badge bg-success">Activo</span>
            @elseif($contrato->estado == 'vencido')
              <span class="badge bg-warning text-dark">Vencido</span>
            @elseif($contrato->estado == 'cancelado')
              <span class="badge bg-danger">Cancelado</span>
            @else
              <span class="badge bg-secondary">{{ ucfirst($contrato->estado) }}</span>
            @endif
          </td>
          <td>
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarContrato"
              data-id="{{ $contrato->id }}"
              data-observaciones="{{ $contrato->observaciones }}"
              data-valor="{{ $contrato->valor_total }}">
              Editar
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted">No hay contratos registrados.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>



<div class="modal fade" id="modalNuevoContrato" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.contratos.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">Nuevo Contrato</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Vehículo</label>
            <select name="vehiculo_id" class="form-select" required>
              @foreach($vehiculos as $v)
                <option value="{{ $v->id }}">{{ $v->placa }} - {{ $v->propietario->nombre ?? 'Sin propietario' }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tarifa</label>
            <select name="tarifa_id" class="form-select" required>
              @foreach($tarifas as $t)
                <option value="{{ $t->id }}">{{ $t->nombre }} ({{ $t->tipo ?? '—' }})</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha Fin</label>
            <input type="date" name="fecha_fin" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Valor Total</label>
            <input type="number" name="valor_total" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar Contrato</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="modalEditarContrato" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formEditarContrato" method="POST">
        @csrf @method('PUT')
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">Editar Contrato</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Valor Total</label>
            <input type="number" name="valor_total" id="editValor" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" id="editObservaciones" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('[data-bs-target="#modalEditarContrato"]').forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById('formEditarContrato').action = `/admin/contratos/${this.dataset.id}`;
    document.getElementById('editValor').value = this.dataset.valor;
    document.getElementById('editObservaciones').value = this.dataset.observaciones;
  });
});
</script>
@endsection
