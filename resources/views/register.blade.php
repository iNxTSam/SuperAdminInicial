@extends('layouts.header')

@section('title', 'Registrarse')

@section('content')
    @if($errors->any())
        <div class="errores" style="color:red;">
            {{ $errors->first('error') }}
        </div>
    @endif
    <form action="{{ route('registrarUsuario') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="numeroDocumento" class="form-label">Número de documento</label>
            <input type="text" class="form-control" id="numeroDocumento" aria-describedby="emailHelp" name="numeroDocumento"
                required>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" aria-describedby="emailHelp" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="rol">Seleccione un rol</label>
            <select class="form-select" aria-label="rol" name="rol" id="rol">
                <option selected>Seleccionar</option>
                <option value="1">Super Administrador</option>
                <option value="2">Administrador</option>
                <option value="3">Vigilante</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="clave" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="clave" name="clave" required>
        </div>
        <div class="mb-3">
            <label for="claveConfirm" class="form-label">Confirmar contraseña</label>
            <input type="password" class="form-control" id="claveConfirm" name="claveConfirm" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
@endsection