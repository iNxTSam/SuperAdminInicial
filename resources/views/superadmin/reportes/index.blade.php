@extends('layouts.app')

@section('title', 'Generar Reportes')

@section('content')
<h2>Generación de Reportes</h2>

<form action="{{ route('superadmin.reportes.generar') }}" method="POST" class="card p-3 shadow">
    @csrf
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Tipo de Reporte</label>
            <select name="tipo_reporte" class="form-control" required>
                <option value="ocupacion">Ocupación</option>
                <option value="ingresos">Ingresos</option>
                <option value="contratos">Contratos</option>
                <option value="usuarios">Usuarios</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Formato</label>
            <select name="formato" class="form-control" required>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Desde</label>
            <input type="date" name="fecha_inicio" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Hasta</label>
            <input type="date" name="fecha_fin" class="form-control">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-success w-100">Generar</button>
        </div>
    </div>
</form>
@endsection
