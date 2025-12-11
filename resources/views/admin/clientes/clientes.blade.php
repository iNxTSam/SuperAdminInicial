@extends('layouts.headerAdmin')
@section('title', 'Dashboard Administrador')
@section('content')
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>Gestión de vehículos</h4>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoClienteModal">+ Nuevo cliente</button>
    </div>

    <div class="card shadow-sm">
      <div class="card-body p-0">
        <table class="table mb-0 text-center align-middle">
          <thead>
            <tr>
              <th>Numero de documento</th>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Estado</th>
              <th>Rol</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            @forelse($clientes as $cliente)
              <tr class="bg-light">
                <td>{{$cliente->id }}</td>
                <td>{{$cliente->nombre }}</td>
                <td>{{$cliente->correo }}</td>
                <td>{{$cliente->activo == 1 ? 'Activo' : 'Inactivo' }}</td>
                <td>{{$cliente->rol_nombre}}</td>
                <td><button class="btn btn-warning editar-cliente" data-bs-toggle="modal"
                    data-bs-target="#editarClienteModal" data-id="{{ $cliente->id }}" data-nombre="{{ $cliente->nombre }}"
                    data-correo="{{ $cliente->correo }}" data-telefono="{{ $cliente->telefono }}" data-estado="{{ $cliente->activo }}">Editar</button></td>
              </tr>

            @empty
              <tr>
                <td colspan="8" class="text-center text-muted">No se encontraron usuarios.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Modal para nuevo cliente -->
  <div class="modal fade" id="nuevoClienteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="form" action="{{ route('admin.clientes.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Nuevo cliente</h5>
            <button type="button" onclick="limpiarFormulario()" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-2">
              <label class="form-label">Numero de documento</label>
              <input type="text" class="form-control" name="cedula" placeholder="Ingrese numero de documento" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre" placeholder="Ingrese nombre" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Télefono</label>
              <input type="text" class="form-control" name="telefono" placeholder="Ingrese numero de telefono" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Correo</label>
              <input type="text" class="form-control" name="correo" placeholder="Ingrese correo" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Rol</label>
              <select class="form-control" name="rol"  required>
                <option value="">Seleccione un rol</option>
                @foreach ($roles as $rol)
                  <option value="{{ $rol->idRol }}">{{ $rol->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal para editar cliente -->
   <div class="modal fade" id="editarClienteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formEditCliente" method="POST">
          @csrf @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Nuevo cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-2">
              <label class="form-label">Numero de documento</label>
              <input type="text" class="form-control" name="cedula" id="editarClienteId" placeholder="Ingrese numero de documento" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre" id="editarClienteNombre" placeholder="Ingrese nombre" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Télefono</label>
              <input type="text" class="form-control" name="telefono" id="editarClienteTelefono" placeholder="Ingrese numero de telefono" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Correo</label>
              <input type="text" class="form-control" name="correo" id="editarClienteCorreo" placeholder="Ingrese correo" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Estado</label>
              <select name="estado" id="editarClienteEstado">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
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
    document.querySelectorAll('.editar-cliente').forEach(btn => {
      btn.addEventListener('click', () => {
        let id = btn.getAttribute('data-id');
        document.getElementById('formEditCliente').action = `/admin/clientes/${id}`;
        const clienteId = btn.getAttribute('data-id');
        const clienteNombre = btn.getAttribute('data-nombre');
        const clienteEstado = btn.getAttribute('data-estado');
        const clienteTelefono = btn.getAttribute('data-telefono');
        const clienteCorreo = btn.getAttribute('data-correo');

        document.getElementById('editarClienteId').value = clienteId;
        document.getElementById('editarClienteNombre').value = clienteNombre;
        document.getElementById('editarClienteEstado').value = clienteEstado;
        document.getElementById('editarClienteTelefono').value = clienteTelefono;
        document.getElementById('editarClienteCorreo').value = clienteCorreo;
      })
    })
  </script>

  <script src="{{ asset('js/limpiarFormulario.js') }}"></script>

@endsection