@extends('layouts.app')

@section('title', 'Configuración Global')

@section('content')
    <h2>Configuración Global</h2>

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

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tipo de vehiculo</th>
                <th>Descripción</th>
                <th>Acción</th>
            </tr>
        <tbody>
            @foreach($configuraciones['tipos_vehiculo'] as $tipo)
                <tr>
                    <td>{{ $tipo->nombre }}</td>
                    <td>{{ $tipo->descripcion }}</td>
                    <td><button class="btn btn-warning btn-sm actualizar-tipoVehiculo" data-bs-toggle="modal"
                            data-bs-target="#actualizarTipoVehiculoModal" data-id="{{ $tipo->id }}"
                            data-nombre="{{ $tipo->nombre }}"
                            data-descripcion="{{$tipo->descripcion}}">Editar</button></td>
                </tr>
            @endforeach
        </tbody>
        </thead>
    </table>
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

    <h4 class="mt-4">Otros datos</h4>
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

    <div class="modal fade" id="actualizarTipoVehiculoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formActualizarTipoVehiculo" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar bahia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="delete-id" name="id" value="">
                        <input type="text" id="edit-nombre" name="nombre" class="form-control mb-2" required>
                        <input type="text" id="edit-descripcion" name="descripcion" class="form-control mb-2" >
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.actualizar-tipoVehiculo').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id;
                document.getElementById('formActualizarTipoVehiculo').action = `/superadmin/configuracion/tipo-vehiculo/update/${id}`;
                document.getElementById('delete-id').value = id;
                document.getElementById('edit-nombre').value = this.dataset.nombre;
                document.getElementById('edit-descripcion').value = this.dataset.descripcion;
            })
        })
    </script>

@endsection