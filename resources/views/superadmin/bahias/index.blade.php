@extends('layouts.app')

@section('title', 'Gestión de Bahías')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Bahías</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaBahiaModal">+ Nueva Bahía</button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Número</th>
                    <th>Tipo Vehículo</th>
                    <th>Capacidad Máxima</th>
                    <th>Ubicación</th>
                    <th>Ocupada</th>
                    <th>Activa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bahias as $bahia)
                <tr>
                    <td>{{ $bahia->numero }}</td>
                    <td>{{ $bahia->tipo_vehiculo_nombre }}</td>
                    <td>{{ $bahia->capacidad_maxima }}</td>
                    <td>{{ $bahia->ubicacion }}</td>
                    <td>
                        @if($bahia->ocupada)
                            <span class="badge bg-danger">Sí</span>
                        @else
                            <span class="badge bg-success">No</span>
                        @endif
                    </td>
                    <td>
                        @if($bahia->activa)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm editar-bahia"
                            data-bs-toggle="modal"
                            data-bs-target="#editarBahiaModal"
                            data-id="{{ $bahia->id }}"
                            data-numero="{{ $bahia->numero }}"
                            data-tipo-vehiculo="{{ $bahia->tipo_vehiculo_id }}"
                            data-capacidad="{{ $bahia->capacidad_maxima }}"
                            data-ubicacion="{{ $bahia->ubicacion }}"
                            data-activa="{{ $bahia->activa }}">
                            Editar
                        </button>

                        <button class="btn btn-danger btn-sm editar-bahia"
                            data-bs-toggle="modal"
                            data-bs-target="#eliminarBahiaModal"
                            data-id="{{ $bahia->id }}">
                            Eliminar
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nueva Bahía -->
<div class="modal fade" id="nuevaBahiaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('superadmin.bahias.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Bahía</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="numero" class="form-control mb-2" placeholder="Número" required>
                    <select name="tipo_vehiculo_id" class="form-control mb-2" required>
                        <option value="">Seleccione tipo de vehículo</option>
                        @foreach($tipos_vehiculo as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="capacidad_maxima" class="form-control mb-2" placeholder="Capacidad Máxima" required>
                    <input type="text" name="ubicacion" class="form-control mb-2" placeholder="Ubicación (opcional)">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Bahía -->
<div class="modal fade" id="editarBahiaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditarBahia" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Bahía</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id">
                    <input type="text" id="edit-numero" name="numero" class="form-control mb-2" required>
                    <select id="edit-tipo-vehiculo" name="tipo_vehiculo_id" class="form-control mb-2" required>
                        @foreach($tipos_vehiculo as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    <input type="number" id="edit-capacidad" name="capacidad_maxima" class="form-control mb-2" required>
                    <input type="text" id="edit-ubicacion" name="ubicacion" class="form-control mb-2">
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

<!-- Modal Eliminar Bahía -->
<div class="modal fade" id="eliminarBahiaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar bahia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="edit-id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('.editar-bahia').forEach(btn => {
    btn.addEventListener('click', function() {
        let id = this.dataset.id;
        document.getElementById('formEditarBahia').action = `/superadmin/bahias/${id}`;
        document.getElementById('edit-numero').value = this.dataset.numero;
        document.getElementById('edit-tipo-vehiculo').value = this.dataset.tipoVehiculo;
        document.getElementById('edit-capacidad').value = this.dataset.capacidad;
        document.getElementById('edit-ubicacion').value = this.dataset.ubicacion;
        document.getElementById('edit-activa').checked = this.dataset.activa == 1;
    });
});
</script>
@endsection
