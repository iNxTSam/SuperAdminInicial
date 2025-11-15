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
                <th>Fecha y hora de ingreso</th>
                <th>Contacto</th>
                <th>Fecha y hora de salida</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehiculo as $vehiculos)

            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No se encontraron vehiculos</td>
                </tr>
            @endforelse
            <tr>
                <td>12345</td>
                <td>111111111111</td>
                <td>Carro</td>
                <td>10/20/2025 12:30 PM</td>
                <td>987654321</td>
                <td>10/21/2025 4:30 PM</td>
                <td><button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSalida">Marcar
                        salida</button></td>
            </tr>
            <tr>
                <td>54321</td>
                <td>222222222222</td>
                <td>Moto</td>
                <td>10/20/2025 12:30 PM</td>
                <td>987654321</td>
                <td>-</td>
                <td><button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSalida">Marcar
                        salida</button></td>
            </tr>
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

                    <div class="p-1">
                        <label for="propietario">Numero de propietario</label>
                        <input type="text" id="propietario" name="propietario" class="form-control"
                            placeholder="Ingrese el número de documento">
                    </div>

                    <div class="p-2">
                        <button class="btn btn-dark" onclick="verficarUsuario()" id="btnVerificar">Verificar</button>
                        <label for="propietario">Verificar usuario</label>
                    </div>
                    <div id="textMsg" class="alert mt-2" style="display: none;"></div>

                    <label for="vehiculo" class="form-label">Vehiculo</label>
                    <select name="vehiculo" id="vehiculo" class="form-control">

                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Aceptar</button>
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
                    <label>Número de ticket</label>
                    <input class="form-control" placeholder="Ingrese el número de ticket">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Validar salida</button>
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
                msg.textContent = 'Profavor ingrese un numero de documento';
                msg.style.display = 'block';
                return;
            }

            btn.disabled = true;
            msg.className = 'text';
            msg.style.display = 'block';
            msg.textContent = 'Verificando...';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

            fetch('/vigilante/verificar-usuario', {
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
                        select.innerHTML = '<option>Seleccione el tipo de vehiculo</option>'
                        if (data.tipos_vehiculos && data.tipos_vahiculos > 0) {
                            data.tipos_vahiculos.array.forEach(tipo => {
                                const option = document.createElement('option');
                                option.value = tipo.id;
                                option.textContent = tipo.nombre;
                                select.appendChild(option);
                            });
                        } else {
                            select.innerHTML = '<option>Seleccione el tipo de vehiculo</option>'
                        }
                    } else {
                        msg.className = 'text text-danger';
                        msg.textContent = 'Usuario no encontrado';
                        msg.style.display = 'block';
                    } -
                        })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    btn.disabled = false;
                });
        }
    </script>
@endsection