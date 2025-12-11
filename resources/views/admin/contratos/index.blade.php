@extends('layouts.headerAdmin')

@section('title', 'Gestión de Contratos')

@section('content')
  <div class="container py-4">
    <h3 class="mb-4">Gestión de Contratos</h3>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <form action="{{ route('admin.contratos') }}" method="GET" class="d-flex">
        <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar contrato..."
          value="{{ request('buscar') }}">
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
                <span class="badge bg-secondary">{{ ucfirst($contrato->estado) }}</span>
              </td>
              <td>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarContrato"
                  data-id="{{ $contrato->id }}" data-observaciones="{{ $contrato->observaciones }}"
                  data-valor="{{ $contrato->valor_total}}" data-estado="{{ $contrato->estado_id }}"
                   data-vehiculo="{{ $contrato->vehiculo }}">
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
        <form id="form" action="{{ route('admin.contratos.store') }}" method="POST">
          @csrf
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">Nuevo Contrato</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
              onclick="limpiarFormulario()"></button>
          </div>
          <div class="modal-body">
            <div class="p-1">
              <label for="propietario">Numero de propietario</label>
              <input type="text" id="propietario" name="propietario" class="form-control"
                placeholder="Ingrese el número de documento" required>
            </div>

            <div class="p-2">
              <button class="btn btn-dark" onclick="verficarUsuario()" id="btnVerificar">Verificar</button>
              <label for="propietario">Verificar usuario</label>
            </div>
            <div id="textMsg" class="alert mt-2" style="display: none;"></div>
            <div class="mb-3">
              <label class="form-label">Vehículo</label>
              <select name="vehiculo" id="vehiculo" class="form-select" required>
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
            <input type="hidden" name="vehiculo" id="editVehiculo">
            <div class="mb-3">
              <label class="form-label">Valor Total</label>
              <input type="number" name="valor_total" id="editValor" class="form-control" required>
            </div>
            <div class="mb-3">
              <select type="number" name="estado" id="editEstado" class="form-select" required>
                @foreach ($estados as $estado)
                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                @endforeach
              </select>
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
    function verficarUsuario() {
      const msg = document.getElementById('textMsg');
      const input = document.getElementById('propietario').value;
      const btn = document.getElementById('btnVerificar');
      const select = document.getElementById('vehiculo');
      if (!input.trim()) {
        msg.className = 'text text-danger';
        msg.textContent = 'Por favor ingrese un numero de documento';
        msg.style.display = 'block';
        return;
      }

      btn.disabled = true;
      msg.className = 'text';
      msg.style.display = 'block';
      msg.textContent = 'Verificando...';
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

      fetch('/admin/contratos/verificar-usuario', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          propietario: input
        })

      }).then(response => response.json())
        .then(data => {
          if (data.existe) {
            msg.className = 'text text-success';
            msg.textContent = 'Usuario verificado';
            msg.style.display = 'block';

            select.innerHTML = '<option value="">Seleccione el tipo de vehiculo</option>'
            if (data.vehiculos) {
              data.vehiculos.forEach((tipo, index) => {
                const option = document.createElement('option');
                option.value = data.placas[index];
                option.textContent = data.vehiculos[index].nombre + " | " + (data.placas[index] || 'Sin placa');
                select.appendChild(option);

              });
            } else {
              select.innerHTML = '<option>No se econtraron vehículos</option>'
            }
          } else {
            msg.className = 'text text-danger';
            msg.textContent = 'Usuario no encontrado';
            msg.style.display = 'block';
            select.innerHTML = '<option>No se econtraron vehículos</option>'
          }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
          btn.disabled = false;
        });
    }
    document.querySelectorAll('[data-bs-target="#modalEditarContrato"]').forEach(btn => {
      btn.addEventListener('click', function () {
        document.getElementById('formEditarContrato').action = `/admin/contratos/${this.dataset.id}`;
        document.getElementById('editVehiculo').value = this.dataset.vehiculo;
        document.getElementById('editValor').value = this.dataset.valor;
        document.getElementById('editEstado').value = this.dataset.estado;
        document.getElementById('editObservaciones').value = this.dataset.observaciones;
      });
    });

  </script>
  <script src="{{ asset('js/limpiarFormulario.js') }}"></script>
@endsection