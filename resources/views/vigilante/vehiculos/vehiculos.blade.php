@extends('layouts.headerVigilante')

@section('title', 'Vehículos ingresados')

@section('content')



  <!-- Contenido principal -->
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>Gestión de vehículos</h4>
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

@endsection