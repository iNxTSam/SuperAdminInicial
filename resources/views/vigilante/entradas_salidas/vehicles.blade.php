@extends('layouts.headerVigilante')

@section('title', 'Vehículos ingresados')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Vehículos ingresados</h4>
        <div>
            <input class="form-control d-inline-block" style="width:260px" placeholder="Ingrese placa o documento">
            <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalNuevo">+ Ingresar nuevo
                vehículo</button>
        </div>
    </div>

    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Placa</th>
                <th>Documento de propietario</th>
                <th>Vehículo</th>
                <th>Contacto</th>
                <th>Fecha y hora de ingreso</th>
                <th>Fecha y hora de salida</th>


                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entradasSalidas as $registros)
                <tr>
                    <td>{{ $registros->vehiculo }} </td>
                    <td>{{ $registros->propietario }}</td>
                    <td>{{ $registros->tipo_vehiculo_nombre }}</td>
                    <td>{{ $registros->telefono }}</td>
                    <td>{{ $registros->fecha_entrada }}</td>
                    <td>{{ $registros->fecha_salida }}</td>
                    <td>
                        @if($registros->marcar_salida == 1)
                            Salida marcada
                        @else
                            <button class="btn btn-warning btn-sm marcar-salida" data-bs-toggle="modal" data-bs-target="#modalSalida" 
                            data-id="{{ $registros->id}}"
                            data-placa="{{ $registros->vehiculo}}">Marcar salida</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No se encontraron vehiculos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Modal Ingresar nuevo vehículo -->
    <div class="modal fade" id="modalNuevo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ingresar vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vigilante.registrarIngreso') }}" method="post">
                        @csrf
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

                        <label for="vehiculo" class="form-label">Vehiculo</label>
                        <select name="vehiculo" id="vehiculo" class="form-control" onchange="activarBtn()">

                        </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ingresarBtn" disabled>Aceptar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Marcar salida -->
    <div class="modal fade" id="modalSalida" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Marcar salida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="marcarSalidaForm" method="post">
                        @csrf @method('PUT')
                        <label>Número de ticket</label>
                        <input class="form-control" placeholder="Ingrese el número de ticket">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Validar salida</button>
                    </form>
                </div>
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

            fetch('/vigilante/entradas-salidas/verificar-usuario', {
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

                        select.innerHTML = '<option value="0" >Seleccione el tipo de vehiculo</option>'
                        if (data.vehiculos) {
                            data.vehiculos.forEach((tipo, index) => {
                                const option = document.createElement('option');
                                option.value = data.placas[index];
                                option.textContent =data.vehiculos[index].nombre + " | " + (data.placas[index] || 'Sin placa');
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

        function activarBtn() {
            const btnGuardar = document.getElementById('ingresarBtn');
            const select = document.getElementById('vehiculo').value;

            if (select == 0) {
                btnGuardar.disabled = true;
            } else {
                btnGuardar.disabled = false;
            }
        }



        document.querySelectorAll('.marcar-salida').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id;
                let placa = this.dataset.placa;
                document.getElementById('marcarSalidaForm').action = `/vigilante/entradas-salidas/registrarSalida/${id}/${placa}`;
            })
        });


    </script>
@endsection