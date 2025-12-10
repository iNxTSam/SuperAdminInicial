@extends('layouts.headerAdmin')

@section('title', 'Vehículos ingresados')

@section('content')



  <!-- Contenido principal -->
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>Gestión de vehículos</h4>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoVehiculoModal">+ Nuevo
        vehículo</button>
    </div>

    <div class="card shadow-sm">
      <div class="card-body p-0">
        <table class="table mb-0 text-center align-middle">
          <thead>
            <tr>
              <th>Documento de propietario</th>
              <th>Placa</th>
              <th>Tipo de vehículo</th>
              <th>Fecha de registro</th>
              <th>Color</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($vehiculos as $vehiculo)
              <tr class="bg-light">
                <td>{{$vehiculo->propietario_id }}</td>
                <td>{{$vehiculo->placa }}</td>
                <td>{{$vehiculo->tipo_vehiculo_nombre }}</td>
                <td>{{$vehiculo->created_at }}</td>
                <td>{{$vehiculo->color }}</td>
                <td>{{$vehiculo->marca }}</td>
                <td>{{$vehiculo->modelo }}</td>
                <td>
                  <button class="btn btn-warning editar-vehiculo" data-bs-toggle="modal"
                    data-bs-target="#editarVehiculoModal" data-placa="{{ $vehiculo->placa }}"
                    data-tipo-vehiculo="{{ $vehiculo->tipo_vehiculo_id}}" data-propietario="{{ $vehiculo->propietario_id }}"
                    data-color="{{ $vehiculo->color }}" data-marca="{{ $vehiculo->marca }}"
                    data-modelo="{{ $vehiculo->modelo }}">Editar</button></td>
              </tr>

            @empty
              <tr>
                <td colspan="8" class="text-center text-muted">No se encontraron vehiculos.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal: Editar Vehículo -->
  <div class="modal fade" id="editarVehiculoModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formEditVehiculo" method="POST">
          @csrf @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Editar vehículo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">

            <div class="mb-2">
              <label class="form-label">Documento propietario</label>
              <input type="text" class="form-control" name="propietario" id="edit-propietario"
                placeholder="Ingrese documento">
            </div>
            <div class="mb-2">
              <label class="form-label">Placa</label>
              <input type="text" class="form-control" id="edit-placa" name="placa" placeholder="Ingrese placa">
            </div>
            <div class="mb-2">
              <label class="form-label">Tipo de vehículo</label>
              <select class="form-select" id="edit-vehiculo" name="tipoVehiculo">
                <option>Seleccione tipo de vehículo</option>
                @foreach ($tipos_vehiculo as $tipo)
                  <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Color</label>
              <input type="text" class="form-control" id="edit-color" name="color" placeholder="Color del vehículo">
            </div>
            <div class="mb-2">
              <label class="form-label">Marca</label>
              <input type="text" class="form-control" id="edit-marca" name="marca" placeholder="Marca">
            </div>
            <div class="mb-2">
              <label class="form-label">Modelo</label>
              <input type="text" class="form-control" id="edit-modelo" name="modelo" placeholder="Modelo">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn" data-bs-dismiss="modal">Aceptar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Nuevo Vehículo -->
  <div class="modal fade" id="nuevoVehiculoModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('admin.nuevo.vehiculo') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Nuevo vehículo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
            <div class="mb-2">
              <label class="form-label">Documento propietario</label>
              <input type="text" class="form-control" name="cedula" placeholder="Ingrese documento" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Placa</label>
              <input type="text" class="form-control" name="placa" placeholder="Ingrese placa" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Tipo de vehículo</label>
              <select name="tipo_vehiculo_id" class="form-select">
                <option>Seleccione tipo de vehículo</option>
                @foreach ($tipos_vehiculo as $tipo)
                  <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Color</label>
              <input type="text" class="form-control" name="color" placeholder="Color del vehículo" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Marca</label>
              <input type="text" class="form-control" name="marca" placeholder="Marca" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Modelo</label>
              <input type="text" class="form-control" name="modelo" placeholder="Modelo" required>
            </div>

          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.querySelectorAll('.editar-vehiculo').forEach(btn => {
      btn.addEventListener('click', function () {
        let id = this.dataset.placa;
        document.getElementById('formEditVehiculo').action = `/admin/vehiculos/${id}`;
        document.getElementById('edit-placa').value = this.dataset.placa;
        document.getElementById('edit-propietario').value = this.dataset.propietario;
        document.getElementById('edit-vehiculo').value = this.dataset.tipoVehiculo;
        document.getElementById('edit-color').value = this.dataset.color;
        document.getElementById('edit-marca').value = this.dataset.marca;
        document.getElementById('edit-modelo').value = this.dataset.modelo;
      })
    });
  </script>

@endsection