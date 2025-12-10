@extends('layouts.app')

@section('title', 'Gestión de Tarifas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Tarifas</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaTarifaModal">+ Nueva Tarifa</button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Vehículo</th>
                    <th>Valor</th>
                    <th>Activa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tarifas as $tarifa)
                <tr>
                    <td>{{ $tarifa->nombre }}</td>
                    <td>{{ $tarifa->tipo }}</td>
                    <td>{{ $tarifa->tipo_vehiculo_nombre }}</td>
                    <td>${{ number_format($tarifa->valor, 0, ',', '.') }}</td>
                    <td>
                        @if($tarifa->activa)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm editar-tarifa"
                            data-bs-toggle="modal"
                            data-bs-target="#editarTarifaModal"
                            data-id="{{ $tarifa->id }}"
                            data-nombre="{{ $tarifa->nombre }}"
                            data-tipo="{{ $tarifa->tipo }}"
                            data-tipo-vehiculo="{{ $tarifa->tipo_vehiculo_id }}"
                            data-valor="{{ $tarifa->valor }}"
                            data-activa="{{ $tarifa->activa }}">
                            Editar
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nueva Tarifa -->
<div class="modal fade" id="nuevaTarifaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('superadmin.tarifas.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Tarifa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
                    <select name="tipo" class="form-control mb-2" required>
                        <option>Seleccione el tipo (dia,mes, etc)</option>
                        <option value="Mes">Mensual</option>
                        <option value="Quincenal">Quincenal</option>
                        <option value="Semanal">Semanal</option>
                        <option value="Dia">Día</option>
                    </select>
                    <select name="tipo_vehiculo_id" class="form-control mb-2" required>
                        <option value="">Seleccione tipo de vehículo</option>
                        @foreach($tipos_vehiculo as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="valor" class="form-control mb-2" placeholder="Valor" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Tarifa -->
<div class="modal fade" id="editarTarifaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditarTarifa" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tarifa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id">
                    <input type="text" id="edit-nombre" name="nombre" class="form-control mb-2" required>
                    <input type="text" id="edit-tipo" name="tipo" class="form-control mb-2" required>
                    <select id="edit-tipo-vehiculo" name="tipo_vehiculo_id" class="form-control mb-2" required>
                        @foreach($tipos_vehiculo as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    <input type="number" id="edit-valor" name="valor" class="form-control mb-2" required>
                    <div class="form-check">
                        <input type="checkbox" id="edit-activa" name="activa" value="1" class="form-check-input">
                        <label for="edit-activa" class="form-check-label">Activa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.editar-tarifa').forEach(btn => {
    btn.addEventListener('click', function() {
        let id = this.dataset.id;
        document.getElementById('formEditarTarifa').action = `/superadmin/tarifas/${id}`;
        document.getElementById('edit-nombre').value = this.dataset.nombre;
        document.getElementById('edit-tipo').value = this.dataset.tipo;
        document.getElementById('edit-tipo-vehiculo').value = this.dataset.tipoVehiculo;
        document.getElementById('edit-valor').value = this.dataset.valor;
        document.getElementById('edit-activa').checked = (this.dataset.activa == 1);
    });
});
</script>
@endsection
