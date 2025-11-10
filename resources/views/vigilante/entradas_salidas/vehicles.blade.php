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
                    <label>Placa</label>
                    <input class="form-control mb-2" placeholder="Ej: ABC123">
                    <label>Vehículo</label>
                    <select class="form-control mb-2">
                        <option>Carro</option>
                        <option>Moto</option>
                        <option>Bicicleta</option>
                        <option>Moto eléctrica</option>
                    </select>
                    <label>Contacto</label>
                    <input class="form-control mb-2" placeholder="Teléfono o correo">
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
@endsection