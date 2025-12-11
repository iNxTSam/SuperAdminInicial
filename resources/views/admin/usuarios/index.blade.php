@extends('layouts.headerAdmin')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container py-4">
  <h3 class="mb-4">Gestión de Usuarios</h3>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <form action="{{ route('admin.usuarios') }}" method="GET" class="d-flex">
      <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar usuario..." value="{{ request('buscar') }}">
      <button type="submit" class="btn btn-outline-primary">Buscar</button>
    </form>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" id="btnNuevoUsuario">+ Nuevo Usuario</button>
  </div>

  <div class="table-responsive">
    <table class="table table-striped align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Cédula</th>
          <th>Carnet</th>
          <th>Correo</th>
          <th>Contacto</th>
          <th>Rol</th>
          <th>Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($usuarios as $usuario)
        <tr>
          <td>{{ $usuario->nombre }}</td>
          <td>{{ $usuario->cedula }}</td>
          <td>{{ $usuario->carnet ?? '—' }}</td>
          <td>{{ $usuario->email ?? '—' }}</td>
          <td>{{ $usuario->contacto ?? '—' }}</td>
          <td>{{ $usuario->rol->nombre ?? '—' }}</td>
          <td>
            @if($usuario->activo)
              <span class="badge bg-success">Sí</span>
            @else
              <span class="badge bg-danger">No</span>
            @endif
          </td>
          <td>
            <button 
              class="btn btn-warning btn-sm editarUsuario"
              data-id="{{ $usuario->id }}"
              data-nombre="{{ $usuario->nombre }}"
              data-cedula="{{ $usuario->cedula }}"
              data-carnet="{{ $usuario->carnet }}"
              data-email="{{ $usuario->email }}"
              data-contacto="{{ $usuario->contacto }}"
              data-rol="{{ $usuario->rol_id }}"
              data-activo="{{ $usuario->activo }}"
              data-bs-toggle="modal"
              data-bs-target="#modalUsuario">
              Editar
            </button>
          </td>
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


<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="form" method="POST">
        @csrf
        <input type="hidden" name="id" id="usuarioId">

        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title" id="tituloModal">Nuevo Usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre" id="usuarioNombre" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Número de Documento *</label>
            <input type="text" name="cedula" id="usuarioCedula" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="email" id="usuarioEmail" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Contacto</label>
            <input type="text" name="contacto" id="usuarioContacto" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Rol *</label>
            <select name="rol_id" id="usuarioRol" class="form-select" required>
              <option value="">Seleccion el rol</option>
              @foreach($roles as $rol)
                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Activo *</label>
            <select name="activo" id="usuarioActivo" class="form-select">
              <option value="1">Sí</option>
              <option value="0">No</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Contraseña *</label>
            <input type="password" name="password" id="usuarioPassword" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('modalUsuario');
  const form = document.getElementById('form');
  const titulo = document.getElementById('tituloModal');

  modal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;


    if (button && button.dataset.id) {
      titulo.textContent = 'Editar Usuario';
      form.action = `/admin/usuarios/${button.dataset.id}`;
      form.method = 'POST';
      if (!form.querySelector('input[name="_method"]')) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_method';
        input.value = 'PUT';
        form.appendChild(input);
      }
      form.querySelector('#usuarioId').value = button.dataset.id;
      form.querySelector('#usuarioNombre').value = button.dataset.nombre || '';
      form.querySelector('#usuarioCedula').value = button.dataset.cedula || '';
      form.querySelector('#usuarioCarnet').value = button.dataset.carnet || '';
      form.querySelector('#usuarioEmail').value = button.dataset.email || '';
      form.querySelector('#usuarioContacto').value = button.dataset.contacto || '';
      form.querySelector('#usuarioRol').value = button.dataset.rol || '';
      form.querySelector('#usuarioActivo').value = button.dataset.activo || '1';
      form.querySelector('#usuarioPassword').removeAttribute('required');
    } else {
      titulo.textContent = 'Nuevo Usuario';
      form.action = "{{ route('admin.usuarios.store') }}";
      const methodInput = form.querySelector('input[name="_method"]');
      if (methodInput) methodInput.remove();
      form.reset();
      form.querySelector('#usuarioPassword').setAttribute('required', true);

    }
  });
});
</script>
<script src="{{ asset('js/app.js') }}"></script>

@endsection
